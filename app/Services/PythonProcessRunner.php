<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PythonProcessRunner
{
    /**
     * Runs a Python script with SCAN_SOIL_ROOT explicitly pinned, regardless of
     * the calling process's current working directory.
     *
     * @param string $scriptPath Absolute path to the .py script (e.g. base_path('python/colorimetry_service.py'))
     * @param array<string> $args Already-escaped arguments (use escapeshellarg on each before passing in)
     * @return array|null Decoded JSON output, or null if the script produced no valid JSON
     */
    public function run(string $scriptPath, array $args): ?array
    {
        $sysTempDir = sys_get_temp_dir();
        $systemRoot = getenv('SystemRoot') ?: 'C:\Windows';
        $path       = getenv('PATH') ?: '';

        $env = array_merge($_ENV, getenv(), [
            'SystemRoot'            => $systemRoot,
            'PATH'                  => $path,
            'TEMP'                  => $sysTempDir,
            'TMP'                   => $sysTempDir,
            'SCAN_SOIL_ROOT'        => config('soil.scan_soil_root'),
            'MUNSELL_REFERENCE_CSV' => config('services.soil_ml.munsell_csv', base_path('python/munsell_reference_sample.csv')),
            'STAGE2_MODE'           => config('soil.stage2_mode', 'classical'),
        ]);

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $python = static::getPythonBinary();
        $command = escapeshellcmd($python) . ' ' . escapeshellarg($scriptPath) . ' ' . implode(' ', $args);

        $options = PHP_OS_FAMILY === 'Windows' ? ['bypass_shell' => true] : [];
        $process = proc_open($command, $descriptorSpec, $pipes, base_path(), $env, $options);
        if (!is_resource($process)) {
            return null;
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        if ($stderr) {
            Log::channel('single')->info("[PythonProcessRunner] stderr: {$stderr}");
        }

        return json_decode($stdout, true);
    }

    /**
     * Fire a Python script in the background (non-blocking).
     *
     * The process writes its own progress/result files to the system temp directory and exits
     * independently. This method returns immediately without blocking.
     * All output (stdout + stderr) is redirected to a readable log file in the temp directory.
     *
     * @param string         $scriptPath Absolute path to the .py script
     * @param array<string>  $args       Already-escaped arguments
     * @param string|null    $scanId     Optional scan ID for log naming
     */
    public function runDetached(string $scriptPath, array $args, ?string $scanId = null): void
    {
        // Extract scanId from args if not explicitly provided
        if (!$scanId) {
            foreach ($args as $idx => $arg) {
                if ($arg === '--scan-id' && isset($args[$idx + 1])) {
                    $scanId = trim($args[$idx + 1], "\"'");
                    break;
                }
            }
        }

        $scanIdSafe = $scanId ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $scanId) : 'detached_' . time();
        $logFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "scan_python_{$scanIdSafe}.log";

        // Full environment merge to preserve Windows Winsock, SystemDrive, and UserProfile
        $sysTempDir = sys_get_temp_dir();
        $systemRoot = getenv('SystemRoot') ?: 'C:\Windows';
        $path       = getenv('PATH') ?: '';

        $env = array_merge($_ENV, getenv(), [
            'SystemRoot'            => $systemRoot,
            'PATH'                  => $path,
            'TEMP'                  => $sysTempDir,
            'TMP'                   => $sysTempDir,
            'SCAN_SOIL_ROOT'        => config('soil.scan_soil_root'),
            'MUNSELL_REFERENCE_CSV' => config('services.soil_ml.munsell_csv', base_path('python/munsell_reference_sample.csv')),
            'STAGE2_MODE'           => config('soil.stage2_mode', 'classical'),
        ]);

        $python = static::getPythonBinary();
        $command = escapeshellcmd($python) . ' ' . escapeshellarg($scriptPath) . ' ' . implode(' ', $args);

        if (PHP_OS_FAMILY === 'Windows') {
            $logHandle = fopen($logFile, 'a');
            if ($logHandle === false) {
                Log::error("[PythonProcessRunner] Failed to open log file for writing: {$logFile}");
                return;
            }

            $descriptors = [
                0 => ['file', 'NUL', 'r'],
                1 => $logHandle,
                2 => $logHandle,
            ];

            // bypass_shell prevents cmd.exe from corrupting quoted paths with spaces
            $proc = proc_open($command, $descriptors, $pipes, base_path(), $env, [
                'bypass_shell' => true,
            ]);
            fclose($logHandle);

            if (is_resource($proc)) {
                Log::info("[PythonProcessRunner] Started detached Python process on Windows (log: {$logFile})");
            } else {
                Log::error("[PythonProcessRunner] Failed to start detached Python process on Windows");
            }
        } else {
            // Linux/Mac: launch detached via nohup with inline environment variables
            $envPrefix = '';
            foreach ($env as $k => $v) {
                if (is_scalar($v) && $v !== '') {
                    $envPrefix .= $k . '=' . escapeshellarg((string)$v) . ' ';
                }
            }
            $bgCommand = 'nohup ' . $envPrefix . $command . ' >> ' . escapeshellarg($logFile) . ' 2>&1 &';
            exec($bgCommand);
            Log::info("[PythonProcessRunner] Started detached Python process on Unix (log: {$logFile})");
        }
    }

    /**
     * Resolves the Python executable binary, preferring python3 on Unix/Linux systems.
     */
    public static function getPythonBinary(): string
    {
        $python = config('services.soil_ml.python_binary');
        if (!empty($python) && is_string($python) && (PHP_OS_FAMILY === 'Windows' || file_exists($python))) {
            return $python;
        }

        // Docker container virtual environment
        if (PHP_OS_FAMILY !== 'Windows' && file_exists('/opt/venv/bin/python')) {
            return '/opt/venv/bin/python';
        }

        if (PHP_OS_FAMILY !== 'Windows') {
            $py3 = trim((string) @shell_exec('which python3 2>/dev/null'));
            if ($py3) {
                return $py3;
            }
            $py = trim((string) @shell_exec('which python 2>/dev/null'));
            if ($py) {
                return $py;
            }
            return 'python3';
        }

        return 'python';
    }
}
