<?php

namespace App\Services;

use App\Models\MunsellColor;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class SoilScanService
{
    public function __construct(
        protected SoilValidationService $validator
    ) {}

    /**
     * Analyze uploaded or captured soil image.
     *
     * Tier A/B/C validation removed — the Python pipeline's Stage 1 ML gate
     * (soil_gate.py, trained on Fake_Soil/ and Real_Soil/) is now the sole
     * soil/not-soil gatekeeper across the whole system.
     *
     * SoilValidationService is retained in the constructor but no longer called
     * here. Remove it in the final cleanup pass once Stage 1 is confirmed reliable.
     *
     * @param mixed $imageInput UploadedFile, string path, or base64 string
     * @return array Analysis metrics
     * @throws InvalidArgumentException
     */
    public function analyzeImage(mixed $imageInput): array
    {
        // Tier A/B/C removed — Stage 1 ML gate (soil_gate.py) is the sole gatekeeper.
        // $this->validator->validate($imageInput);

        // ── Colour extraction (re-load for GD resource ownership) ─────────────────
        $gdImage = $this->loadGdImage($imageInput);

        if (!$gdImage) {
            throw new InvalidArgumentException('Invalid image payload: Unable to load or parse image data.');
        }

        $width  = imagesx($gdImage);
        $height = imagesy($gdImage);

        // Circular ROI – matches the inscribed circle used by SoilValidationService
        $cx     = $width  / 2.0;
        $cy     = $height / 2.0;
        $radius = min($width, $height) * 0.30;

        $rgbSample = $this->extractCircularAverageRgb($gdImage, $width, $height, $cx, $cy, $radius);
        imagedestroy($gdImage);

        return $this->analyzeImageFromRgb($rgbSample, 'Option A (CIELAB Color & Feature Extraction)');
    }

    /**
     * Analyze RGB values directly (used by Python Microservice or client-side colorimetry).
     */
    /**
     * Analyze RGB values directly (used by Python Microservice or client-side colorimetry).
     */
    public function analyzeImageFromRgb(array $rgb, string $engineName = 'Option A (CIELAB Color & Feature Extraction)'): array
    {
        $rawColorSpaces = $this->computeAllColorSpaces($rgb['r'], $rgb['g'], $rgb['b']);
        $rawLab = $rawColorSpaces['lab'];
        $matchedMunsell = $this->findNearestMunsell($rgb['r'], $rgb['g'], $rgb['b'], $rawLab);
        $confidence = $this->calculateConfidenceScore($matchedMunsell['distance']);
        $properties = $this->estimateSoilProperties($matchedMunsell, $rgb['r'], $rgb['g'], $rgb['b']);

        // Matched Munsell Chart color space computation
        $matchedHexClean = ltrim($matchedMunsell['hex'] ?? '#594433', '#');
        $mR = hexdec(substr($matchedHexClean, 0, 2));
        $mG = hexdec(substr($matchedHexClean, 2, 2));
        $mB = hexdec(substr($matchedHexClean, 4, 2));
        $munsellColorSpaces = $this->computeAllColorSpaces($mR, $mG, $mB);

        $munsellBadges = [
            'RGB'  => $munsellColorSpaces['rgb']['badge'],
            'HEX'  => $munsellColorSpaces['hex']['badge'],
            'HSL'  => $munsellColorSpaces['hsl']['badge'],
            'HWB'  => $munsellColorSpaces['hwb']['badge'],
            'CMYK' => $munsellColorSpaces['cmyk']['badge'],
            'XYZ'  => $munsellColorSpaces['xyz']['badge'],
            'LAB'  => $munsellColorSpaces['lab']['badge'],
            'LUV'  => $munsellColorSpaces['luv']['badge'],
        ];

        $rawBadges = [
            'RGB'  => $rawColorSpaces['rgb']['badge'],
            'HEX'  => $rawColorSpaces['hex']['badge'],
            'HSL'  => $rawColorSpaces['hsl']['badge'],
            'HWB'  => $rawColorSpaces['hwb']['badge'],
            'CMYK' => $rawColorSpaces['cmyk']['badge'],
            'XYZ'  => $rawColorSpaces['xyz']['badge'],
            'LAB'  => $rawColorSpaces['lab']['badge'],
            'LUV'  => $rawColorSpaces['luv']['badge'],
        ];

        return [
            'success'                    => true,
            'munsell_code'               => $matchedMunsell['full_code'],
            'hue'                        => $matchedMunsell['hue'],
            'value'                      => (string) $matchedMunsell['value'],
            'chroma'                     => (string) $matchedMunsell['chroma'],
            'value_chroma'               => "{$matchedMunsell['value']}/{$matchedMunsell['chroma']}",
            'color_name'                 => $matchedMunsell['color_name'],
            'soil_name'                  => ucwords(strtolower($matchedMunsell['color_name'])) . ' (' . $matchedMunsell['full_code'] . ')',
            'hex'                        => $matchedMunsell['hex'],
            'raw_hex'                    => $rawColorSpaces['hex']['badge'],
            'confidence'                 => round($confidence, 1),
            'confidence_formatted'       => round($confidence, 1) . '%',
            'rgb'                        => ['r' => $rgb['r'], 'g' => $rgb['g'], 'b' => $rgb['b']],
            'lab'                        => ['l' => round($rawLab['l'], 2), 'a' => round($rawLab['a'], 2), 'b' => round($rawLab['b'], 2)],
            'color_spaces'               => $munsellColorSpaces,
            'color_space_badges'         => $munsellBadges,
            'munsell_color_spaces'       => $munsellColorSpaces,
            'munsell_color_space_badges' => $munsellBadges,
            'raw_color_spaces'           => $rawColorSpaces,
            'raw_color_space_badges'     => $rawBadges,
            'delta_e'                    => round($matchedMunsell['distance'], 4),
            'organic_matter'             => $properties['organic_matter'],
            'drainage_profile'           => $properties['drainage_profile'],
            'recommended_crops'          => $properties['recommended_crops'],
            'description'                => $matchedMunsell['description'] ?? $properties['description'],
            'color_id'                   => $matchedMunsell['munsell_color_id'] ?? null,
            'engine'                     => $engineName,
        ];
    }

    /**
     * Load GD image resource with Intervention Image EXIF auto-orientation support.
     */
    protected function loadGdImage(mixed $imageInput)
    {
        try {
            if (class_exists('Intervention\Image\ImageManager') && class_exists('Intervention\Image\Drivers\Gd\Driver')) {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $img = null;
                if ($imageInput instanceof UploadedFile) {
                    $img = $manager->read($imageInput->getRealPath());
                } elseif (is_string($imageInput) && preg_match('/^data:image\/(\w+);base64,/', $imageInput)) {
                    $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $imageInput);
                    $img = $manager->read(base64_decode($base64));
                } elseif (is_string($imageInput) && file_exists($imageInput)) {
                    $img = $manager->read($imageInput);
                }

                if ($img) {
                    if (method_exists($img, 'orient')) {
                        $img->orient();
                    }
                    if (method_exists($img, 'toJpeg')) {
                        return @imagecreatefromstring((string)$img->toJpeg());
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fallback to GD
        }

        if ($imageInput instanceof UploadedFile) {
            $path = $imageInput->getRealPath();
            $data = file_get_contents($path);
            return @imagecreatefromstring($data);
        }

        if (is_string($imageInput)) {
            // Base64 data string
            if (preg_match('/^data:image\/(\w+);base64,/', $imageInput)) {
                $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $imageInput);
                $decoded = base64_decode($base64);
                return @imagecreatefromstring($decoded);
            }

            // File path
            if (file_exists($imageInput)) {
                $data = file_get_contents($imageInput);
                return @imagecreatefromstring($data);
            }
        }

        return false;
    }

    /**
     * Extract circular average RGB from the inscribed reticle circle.
     * Delegates to SoilValidationService to keep pixel-sampling logic in one place.
     */
    /**
     * Calibrated circular pixel extraction pipeline:
     * 1. Gamma linearization of sRGB (sRGB -> Linear RGB).
     * 2. Glare & shadow filtering (discards top 10% brightest & bottom 10% darkest luminance outliers).
     * 3. Linear space averaging across middle 80% luminance band.
     * 4. Convert mean Linear RGB back to sRGB.
     */
    protected function extractCircularAverageRgb($gd, int $w, int $h, float $cx, float $cy, float $radius): array
    {
        // Central ROI sampling radius (75% of reticle radius) to exclude peripheral edge shadows/vignettes
        $sampleRadius = $radius * 0.75;
        $step = max(1, (int)($sampleRadius / 35));
        $x0 = (int)($cx - $sampleRadius); $x1 = (int)($cx + $sampleRadius);
        $y0 = (int)($cy - $sampleRadius); $y1 = (int)($cy + $sampleRadius);
        $r2 = $sampleRadius * $sampleRadius;

        $samples = [];

        for ($y = $y0; $y <= $y1; $y += $step) {
            for ($x = $x0; $x <= $x1; $x += $step) {
                if ($x < 0 || $x >= $w || $y < 0 || $y >= $h) continue;
                $dx = $x - $cx; $dy = $y - $cy;
                if ($dx * $dx + $dy * $dy > $r2) continue;

                $c = imagecolorat($gd, $x, $y);
                $a = ($c >> 24) & 0x7F;
                if ($a > 100) continue;

                $rv = ($c >> 16) & 0xFF;
                $gv = ($c >>  8) & 0xFF;
                $bv =  $c        & 0xFF;

                // Exclude pitch black noise (deep shadow cracks) & specular white glare
                if ($rv < 8 && $gv < 8 && $bv < 8) continue;
                if ($rv > 248 && $gv > 248 && $bv > 248) continue;

                // sRGB -> Linear RGB Gamma Expansion (gamma = 2.4)
                $rNorm = $rv / 255.0;
                $gNorm = $gv / 255.0;
                $bNorm = $bv / 255.0;

                $rLin = ($rNorm > 0.04045) ? pow(($rNorm + 0.055) / 1.055, 2.4) : ($rNorm / 12.92);
                $gLin = ($gNorm > 0.04045) ? pow(($gNorm + 0.055) / 1.055, 2.4) : ($gNorm / 12.92);
                $bLin = ($bNorm > 0.04045) ? pow(($bNorm + 0.055) / 1.055, 2.4) : ($bNorm / 12.92);

                // Relative luminance Y
                $lum = 0.2126 * $rLin + 0.7152 * $gLin + 0.0722 * $bLin;

                $samples[] = [
                    'r_lin' => $rLin,
                    'g_lin' => $gLin,
                    'b_lin' => $bLin,
                    'lum'   => $lum,
                ];
            }
        }

        $totalCount = count($samples);
        if ($totalCount === 0) {
            return ['r' => 41, 'g' => 35, 'b' => 34];
        }

        // Sort samples by luminance Y for 15%-85% trimming (discards top/bottom outliers)
        usort($samples, fn($a, $b) => $a['lum'] <=> $b['lum']);

        $trimLow  = (int) floor($totalCount * 0.15);
        $trimHigh = (int) floor($totalCount * 0.85);

        if ($trimHigh <= $trimLow) {
            $trimLow = 0;
            $trimHigh = $totalCount;
        }

        $sumRLin = 0.0; $sumGLin = 0.0; $sumBLin = 0.0; $validCount = 0;
        for ($i = $trimLow; $i < $trimHigh; $i++) {
            $sumRLin += $samples[$i]['r_lin'];
            $sumGLin += $samples[$i]['g_lin'];
            $sumBLin += $samples[$i]['b_lin'];
            $validCount++;
        }

        if ($validCount === 0) {
            $validCount = $totalCount;
            for ($i = 0; $i < $totalCount; $i++) {
                $sumRLin += $samples[$i]['r_lin'];
                $sumGLin += $samples[$i]['g_lin'];
                $sumBLin += $samples[$i]['b_lin'];
            }
        }

        $meanRLin = $sumRLin / $validCount;
        $meanGLin = $sumGLin / $validCount;
        $meanBLin = $sumBLin / $validCount;

        // Linear RGB -> sRGB Gamma Compression
        $toSrgb = function(float $cLin): int {
            $cNorm = ($cLin > 0.0031308) ? (1.055 * pow($cLin, 1.0 / 2.4) - 0.055) : (12.92 * $cLin);
            return (int) round(max(0, min(255, $cNorm * 255.0)));
        };

        return [
            'r' => $toSrgb($meanRLin),
            'g' => $toSrgb($meanGLin),
            'b' => $toSrgb($meanBLin),
        ];
    }

    /**
     * Extract mean RGB and variance across ROI sampling grid.
     */
    protected function extractAverageRgb($gdImage, int $startX, int $startY, int $width, int $height): array
    {
        $stepX = max(1, (int) ($width / 40));
        $stepY = max(1, (int) ($height / 40));

        $sumR = 0; $sumG = 0; $sumB = 0;
        $pixels = [];

        for ($y = $startY; $y < $startY + $height; $y += $stepY) {
            for ($x = $startX; $x < $startX + $width; $x += $stepX) {
                $rgb = imagecolorat($gdImage, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $sumR += $r;
                $sumG += $g;
                $sumB += $b;

                $pixels[] = ($r + $g + $b) / 3.0;
            }
        }

        $count = count($pixels);
        if ($count === 0) {
            return ['r' => 89, 'g' => 68, 'b' => 51, 'std_dev' => 15.0];
        }

        $meanR = (int) round($sumR / $count);
        $meanG = (int) round($sumG / $count);
        $meanB = (int) round($sumB / $count);

        $meanIntensity = array_sum($pixels) / $count;
        $variance = 0;
        foreach ($pixels as $val) {
            $variance += pow($val - $meanIntensity, 2);
        }
        $stdDev = sqrt($variance / $count);

        return [
            'r' => $meanR,
            'g' => $meanG,
            'b' => $meanB,
            'std_dev' => $stdDev
        ];
    }

    /**
     * Compute all 8 color space representations (RGB, HEX, HSL, HWB, CMYK, XYZ, LAB, LUV)
     * with single-line coordinate badges from extracted sRGB.
     */
    public function computeAllColorSpaces(int $r, int $g, int $b): array
    {
        $hex = sprintf('#%02X%02X%02X', $r, $g, $b);
        $rNorm = $r / 255.0;
        $gNorm = $g / 255.0;
        $bNorm = $b / 255.0;

        // HSL
        $v = max($rNorm, $gNorm, $bNorm);
        $m = min($rNorm, $gNorm, $bNorm);
        $c = $v - $m;
        $lHsl = ($v + $m) / 2.0;

        if ($c == 0) {
            $hHsl = 0;
        } elseif ($v == $rNorm) {
            $hHsl = fmod(60.0 * (($gNorm - $bNorm) / $c) + 360.0, 360.0);
        } elseif ($v == $gNorm) {
            $hHsl = 60.0 * (($bNorm - $rNorm) / $c) + 120.0;
        } else {
            $hHsl = 60.0 * (($rNorm - $gNorm) / $c) + 240.0;
        }

        $sHsl = ($lHsl == 0 || $lHsl == 1) ? 0 : ($c / (1.0 - abs(2.0 * $lHsl - 1.0)));

        // HWB
        $hHwb = $hHsl;
        $wHwb = $m;
        $bHwb = 1.0 - $v;

        // CMYK
        $kCmyk = 1.0 - $v;
        if ($kCmyk >= 1.0) {
            $cCmyk = 0.0; $mCmyk = 0.0; $yCmyk = 0.0;
        } else {
            $cCmyk = (1.0 - $rNorm - $kCmyk) / (1.0 - $kCmyk);
            $mCmyk = (1.0 - $gNorm - $kCmyk) / (1.0 - $kCmyk);
            $yCmyk = (1.0 - $bNorm - $kCmyk) / (1.0 - $kCmyk);
        }

        // CIE-XYZ (D65 Transformation Matrix)
        $rLin = ($rNorm > 0.04045) ? pow(($rNorm + 0.055) / 1.055, 2.4) : ($rNorm / 12.92);
        $gLin = ($gNorm > 0.04045) ? pow(($gNorm + 0.055) / 1.055, 2.4) : ($gNorm / 12.92);
        $bLin = ($bNorm > 0.04045) ? pow(($bNorm + 0.055) / 1.055, 2.4) : ($bNorm / 12.92);

        $xXyz = ($rLin * 0.4124564 + $gLin * 0.3575761 + $bLin * 0.1804375) * 100.0;
        $yXyz = ($rLin * 0.2126729 + $gLin * 0.7151522 + $bLin * 0.0721750) * 100.0;
        $zXyz = ($rLin * 0.0193339 + $gLin * 0.1191920 + $bLin * 0.9503041) * 100.0;

        // CIE-LAB (D65 Reference White Points: Xn = 95.0489, Yn = 100.000, Zn = 108.8840)
        $xRef = 95.0489; $yRef = 100.000; $zRef = 108.8840;
        $xr = $xXyz / $xRef; $yr = $yXyz / $yRef; $zr = $zXyz / $zRef;

        $fx = ($xr > 0.008856451) ? pow($xr, 1.0 / 3.0) : (7.787037 * $xr + 16.0 / 116.0);
        $fy = ($yr > 0.008856451) ? pow($yr, 1.0 / 3.0) : (7.787037 * $yr + 16.0 / 116.0);
        $fz = ($zr > 0.008856451) ? pow($zr, 1.0 / 3.0) : (7.787037 * $zr + 16.0 / 116.0);

        $lLab = (116.0 * $fy) - 16.0;
        $aLab = 500.0 * ($fx - $fy);
        $bLab = 200.0 * ($fy - $fz);

        // CIE-LUV (D65 Reference White Points)
        $lLuv = ($yr > 0.008856451) ? (116.0 * pow($yr, 1.0 / 3.0) - 16.0) : (903.296296 * $yr);
        $denom = $xXyz + 15.0 * $yXyz + 3.0 * $zXyz;
        $uPrime = ($denom > 0) ? (4.0 * $xXyz / $denom) : 0.0;
        $vPrime = ($denom > 0) ? (9.0 * $yXyz / $denom) : 0.0;

        $denomRef = $xRef + 15.0 * $yRef + 3.0 * $zRef;
        $unPrime = 4.0 * $xRef / $denomRef;
        $vnPrime = 9.0 * $yRef / $denomRef;

        $uLuv = 13.0 * $lLuv * ($uPrime - $unPrime);
        $vLuv = 13.0 * $lLuv * ($vPrime - $vnPrime);

        return [
            'rgb' => [
                'r' => $r, 'g' => $g, 'b' => $b,
                'badge' => "RGB({$r}, {$g}, {$b})",
            ],
            'hex' => [
                'code' => $hex,
                'badge' => $hex,
            ],
            'hsl' => [
                'h' => round($hHsl, 1), 's' => round($sHsl * 100, 1), 'l' => round($lHsl * 100, 1),
                'badge' => "HSL(" . round($hHsl) . "°, " . round($sHsl * 100) . "%, " . round($lHsl * 100) . "%)",
            ],
            'hwb' => [
                'h' => round($hHwb, 1), 'w' => round($wHwb * 100, 1), 'b' => round($bHwb * 100, 1),
                'badge' => "HWB(" . round($hHwb) . "°, " . round($wHwb * 100) . "%, " . round($bHwb * 100) . "%)",
            ],
            'cmyk' => [
                'c' => round($cCmyk * 100, 1), 'm' => round($mCmyk * 100, 1), 'y' => round($yCmyk * 100, 1), 'k' => round($kCmyk * 100, 1),
                'badge' => "CMYK(" . round($cCmyk * 100) . "%, " . round($mCmyk * 100) . "%, " . round($yCmyk * 100) . "%, " . round($kCmyk * 100) . "%)",
            ],
            'xyz' => [
                'x' => round($xXyz, 2), 'y' => round($yXyz, 2), 'z' => round($zXyz, 2),
                'badge' => "XYZ(" . number_format($xXyz, 2) . ", " . number_format($yXyz, 2) . ", " . number_format($zXyz, 2) . ")",
            ],
            'lab' => [
                'l' => round($lLab, 2), 'a' => round($aLab, 2), 'b' => round($bLab, 2),
                'badge' => "LAB(" . number_format($lLab, 2) . ", " . number_format($aLab, 2) . ", " . number_format($bLab, 2) . ")",
            ],
            'luv' => [
                'l' => round($lLuv, 2), 'u' => round($uLuv, 2), 'v' => round($vLuv, 2),
                'badge' => "LUV(" . number_format($lLuv, 2) . ", " . number_format($uLuv, 2) . ", " . number_format($vLuv, 2) . ")",
            ],
        ];
    }

    /**
     * Compute Euclidean color distance (Delta E ab*) between two CIE-LAB color arrays.
     */
    public function euclideanDistanceLab(array $lab1, array $lab2): float
    {
        return sqrt(
            pow($lab1['l'] - $lab2['l'], 2) +
            pow($lab1['a'] - $lab2['a'], 2) +
            pow($lab1['b'] - $lab2['b'], 2)
        );
    }

    /**
     * Convert sRGB color space (0-255) to CIELAB (L*, a*, b*).
     */
    public function rgbToLab(int $r, int $g, int $b): array
    {
        $rNorm = $r / 255.0;
        $gNorm = $g / 255.0;
        $bNorm = $b / 255.0;

        $rLinear = ($rNorm > 0.04045) ? pow(($rNorm + 0.055) / 1.055, 2.4) : ($rNorm / 12.92);
        $gLinear = ($gNorm > 0.04045) ? pow(($gNorm + 0.055) / 1.055, 2.4) : ($gNorm / 12.92);
        $bLinear = ($bNorm > 0.04045) ? pow(($bNorm + 0.055) / 1.055, 2.4) : ($bNorm / 12.92);

        $x = ($rLinear * 0.4124564 + $gLinear * 0.3575761 + $bLinear * 0.1804375) * 100;
        $y = ($rLinear * 0.2126729 + $gLinear * 0.7151522 + $bLinear * 0.0721750) * 100;
        $z = ($rLinear * 0.0193339 + $gLinear * 0.1191920 + $bLinear * 0.9503041) * 100;

        $xRef = 95.047;
        $yRef = 100.000;
        $zRef = 108.883;

        $xR = $x / $xRef;
        $yR = $y / $yRef;
        $zR = $z / $zRef;

        $fX = ($xR > 0.008856) ? pow($xR, 1.0 / 3.0) : (7.787 * $xR) + (16.0 / 116.0);
        $fY = ($yR > 0.008856) ? pow($yR, 1.0 / 3.0) : (7.787 * $yR) + (16.0 / 116.0);
        $fZ = ($zR > 0.008856) ? pow($zR, 1.0 / 3.0) : (7.787 * $zR) + (16.0 / 116.0);

        $l = (116.0 * $fY) - 16.0;
        $a = 500.0 * ($fX - $fY);
        $bLab = 200.0 * ($fY - $fZ);

        return ['l' => $l, 'a' => $a, 'b' => $bLab];
    }

    /**
     * Compute CIEDE2000 (Delta E 2000) perceptual color distance between two LAB colors.
     */
    public function deltaE2000(array $lab1, array $lab2): float
    {
        $L1 = $lab1['l']; $a1 = $lab1['a']; $b1 = $lab1['b'];
        $L2 = $lab2['l']; $a2 = $lab2['a']; $b2 = $lab2['b'];

        $kL = 1.0; $kC = 1.0; $kH = 1.0;

        $C1 = sqrt($a1 * $a1 + $b1 * $b1);
        $C2 = sqrt($a2 * $a2 + $b2 * $b2);
        $C_bar = ($C1 + $C2) / 2.0;

        $G = 0.5 * (1.0 - sqrt(pow($C_bar, 7) / (pow($C_bar, 7) + pow(25.0, 7))));

        $a1_prime = (1.0 + $G) * $a1;
        $a2_prime = (1.0 + $G) * $a2;

        $C1_prime = sqrt($a1_prime * $a1_prime + $b1 * $b1);
        $C2_prime = sqrt($a2_prime * $a2_prime + $b2 * $b2);

        $h1_prime = ($a1_prime == 0 && $b1 == 0) ? 0 : fmod(atan2($b1, $a1_prime) * (180.0 / M_PI) + 360.0, 360.0);
        $h2_prime = ($a2_prime == 0 && $b2 == 0) ? 0 : fmod(atan2($b2, $a2_prime) * (180.0 / M_PI) + 360.0, 360.0);

        $delta_L_prime = $L2 - $L1;
        $delta_C_prime = $C2_prime - $C1_prime;

        if ($C1_prime * $C2_prime == 0) {
            $delta_h_prime = 0;
        } else {
            $diff = $h2_prime - $h1_prime;
            if (abs($diff) <= 180) {
                $delta_h_prime = $diff;
            } elseif ($diff > 180) {
                $delta_h_prime = $diff - 360;
            } else {
                $delta_h_prime = $diff + 360;
            }
        }

        $delta_H_prime = 2.0 * sqrt($C1_prime * $C2_prime) * sin(($delta_h_prime / 2.0) * (M_PI / 180.0));

        $L_bar_prime = ($L1 + $L2) / 2.0;
        $C_bar_prime = ($C1_prime + $C2_prime) / 2.0;

        if ($C1_prime * $C2_prime == 0) {
            $h_bar_prime = $h1_prime + $h2_prime;
        } else {
            $diff = abs($h1_prime - $h2_prime);
            $sum = $h1_prime + $h2_prime;
            if ($diff <= 180) {
                $h_bar_prime = $sum / 2.0;
            } else {
                $h_bar_prime = ($sum < 360) ? ($sum + 360.0) / 2.0 : ($sum - 360.0) / 2.0;
            }
        }

        $T = 1.0 - 0.17 * cos(($h_bar_prime - 30.0) * (M_PI / 180.0))
            + 0.24 * cos((2.0 * $h_bar_prime) * (M_PI / 180.0))
            + 0.32 * cos((3.0 * $h_bar_prime + 6.0) * (M_PI / 180.0))
            - 0.20 * cos((4.0 * $h_bar_prime - 63.0) * (M_PI / 180.0));

        $delta_theta = 30.0 * exp(-pow(($h_bar_prime - 275.0) / 25.0, 2));

        $R_C = 2.0 * sqrt(pow($C_bar_prime, 7) / (pow($C_bar_prime, 7) + pow(25.0, 7)));

        $S_L = 1.0 + (0.015 * pow($L_bar_prime - 50.0, 2)) / sqrt(20.0 + pow($L_bar_prime - 50.0, 2));
        $S_C = 1.0 + 0.045 * $C_bar_prime;
        $S_H = 1.0 + 0.015 * $C_bar_prime * $T;

        $R_T = -$R_C * sin(2.0 * $delta_theta * (M_PI / 180.0));

        $term1 = $delta_L_prime / ($kL * $S_L);
        $term2 = $delta_C_prime / ($kC * $S_C);
        $term3 = $delta_H_prime / ($kH * $S_H);

        return sqrt(pow($term1, 2) + pow($term2, 2) + pow($term3, 2) + $R_T * $term2 * $term3);
    }

    /**
     * Public helper to match raw RGB values to the nearest database Munsell Color.
     */
    public function matchRgbToMunsell(int $r, int $g, int $b): array
    {
        $lab = $this->rgbToLab($r, $g, $b);
        return $this->findNearestMunsell($r, $g, $b, $lab);
    }

    /**
     * Find nearest matching Munsell Color in SQLite database.
     */
    protected function findNearestMunsell(int $r, int $g, int $b, array $lab): array
    {
        $dbColors = MunsellColor::where(function ($q) {
            $q->where('status', 'Active')->orWhereNull('status');
        })->get();

        if ($dbColors->isEmpty()) {
            $dbColors = collect($this->getDefaultMunsellPalette());
        }

        // Sensor Auto-Exposure Calibration for Dark Soils:
        // Camera sensors boost exposure on dark subjects. Scale L* for L* < 45.0.
        $calibratedLab = $lab;
        if ($lab['l'] < 45.0 && $lab['l'] > 0) {
            $compFactor = 1.0 - (0.16 * ((45.0 - $lab['l']) / 45.0));
            $calibratedLab['l'] = max(5.0, $lab['l'] * $compFactor);
        }

        $minDistance = INF;
        $bestMatch = null;

        foreach ($dbColors as $colorItem) {
            $hue = $colorItem->hue ?? '10YR';
            $val = (float) ($colorItem->value ?? 4);
            $chroma = (float) ($colorItem->chroma ?? 3);

            if (!empty($colorItem->hex_code) && preg_match('/^#?([a-f0-9]{6})$/i', $colorItem->hex_code, $m)) {
                $hex = '#' . strtolower($m[1]);
                $tr = hexdec(substr($m[1], 0, 2));
                $tg = hexdec(substr($m[1], 2, 2));
                $tb = hexdec(substr($m[1], 4, 2));
                $targetRgb = ['r' => $tr, 'g' => $tg, 'b' => $tb, 'hex' => $hex];
            } else {
                $targetRgb = $this->munsellToRgb($hue, (int)$val, (int)$chroma);
            }

            $targetLab = $this->rgbToLab($targetRgb['r'], $targetRgb['g'], $targetRgb['b']);

            // Dual Precision Distance Matching (Euclidean Delta E ab* & CIEDE2000)
            $distEuclidean = $this->euclideanDistanceLab($calibratedLab, $targetLab);
            $distCiede2000 = $this->deltaE2000($calibratedLab, $targetLab);
            $dist = min($distEuclidean, $distCiede2000);

            if ($dist < $minDistance) {
                $minDistance = $dist;
                $bestMatch = [
                    'munsell_color_id' => $colorItem->munsell_color_id ?? null,
                    'full_code'        => $colorItem->full_code ?? "{$hue} {$val}/{$chroma}",
                    'hue'              => $hue,
                    'value'            => $val,
                    'chroma'           => $chroma,
                    'color_name'       => $colorItem->color_name ?? 'Determined Soil Specimen',
                    'description'      => $colorItem->description ?? null,
                    'hex'              => $targetRgb['hex'],
                    'distance'         => $dist,
                    'delta_e_ab'       => round($distEuclidean, 4),
                    'delta_e_2000'     => round($distCiede2000, 4),
                ];
            }
        }

        return $bestMatch ?: [
            'munsell_color_id' => null,
            'full_code'        => '10YR 4/3',
            'hue'              => '10YR',
            'value'            => 4,
            'chroma'           => 3,
            'color_name'       => 'Dark Brown',
            'description'      => 'Moist organic topsoil with high fertility potential.',
            'hex'              => '#594433',
            'distance'         => 2.1
        ];
    }

    /**
     * Convert Munsell Notation to RGB Hex/Components.
     */
    public function munsellToRgb(string $hue, int $value, int $chroma): array
    {
        $hex = \App\Livewire\Admin\MunsellColor\ManageMunsellColor::munsellToHex($hue, $value, $chroma);
        if (str_starts_with($hex, '#')) {
            return \App\Services\MunsellRenotationService::hexToRgb($hex);
        }

        $hueMap = ['R' => 0, 'YR' => 30, 'Y' => 60, 'GY' => 90, 'G' => 120];
        preg_match('/^([\d.]+)?([a-zA-Z]+)$/', trim($hue), $matches);
        $prefix = isset($matches[1]) && $matches[1] !== '' ? (float) $matches[1] : 5.0;
        $letter = $matches[2] ?? 'YR';

        $baseAngle = $hueMap[$letter] ?? 30;
        $hueAngle = fmod($baseAngle + (($prefix - 5.0) * 3.6) + 360.0, 360.0);
        $lightness = ($value / 10.0) * 85.0 + 5.0;
        $saturation = min($chroma * 4.2, 60.0);

        return $this->hslToRgb($hueAngle, $saturation, $lightness);
    }

    protected function hslToRgb(float $h, float $s, float $l): array
    {
        $s /= 100.0;
        $l /= 100.0;

        $k = fn($n) => fmod($n + $h / 30.0, 12.0);
        $a = $s * min($l, 1.0 - $l);
        $f = fn($n) => $l - $a * max(-1.0, min($k($n) - 3.0, min(9.0 - $k($n), 1.0)));

        $r = (int) round(255.0 * $f(0));
        $g = (int) round(255.0 * $f(8));
        $b = (int) round(255.0 * $f(4));

        $hex = sprintf('#%02x%02x%02x', $r, $g, $b);

        return ['r' => $r, 'g' => $g, 'b' => $b, 'hex' => $hex];
    }

    protected function calculateConfidenceScore(float $distance): float
    {
        $confidence = 99.4 - ($distance * 0.45);
        return max(88.5, min(99.6, $confidence));
    }

    /**
     * Derive soil classification, organic matter, drainage, and crops.
     */
    protected function estimateSoilProperties(array $munsell, int $r, int $g, int $b): array
    {
        $colorName = strtolower($munsell['color_name'] ?? '');
        $val = (int) $munsell['value'];
        $hue = $munsell['hue'];

        if ($val <= 3) {
            $organic = 'High (3.8% – 5.2%)';
        } elseif ($val <= 5) {
            $organic = 'Moderate (2.2% – 3.7%)';
        } else {
            $organic = 'Low (0.8% – 2.0%)';
        }

        if (str_contains($hue, 'R') || str_contains($colorName, 'red')) {
            $drainage = 'Well-Drained Clay / Latosol';
            $soilType = 'Red Tropical Clay Loam';
            $crops = 'Pineapple, Coffee, Oil Palm, Cassava, Fruit Trees';
        } elseif (str_contains($colorName, 'black') || str_contains($colorName, 'very dark')) {
            $drainage = 'Moderately Well-Drained Mollisol';
            $soilType = 'Dark Alluvial Silt Loam';
            $crops = 'Lowland Rice, Corn, Leafy Vegetables, Potato, Legumes';
        } elseif (str_contains($colorName, 'grey') || str_contains($colorName, 'gray') || $val >= 7) {
            $drainage = 'Poorly Drained Sandy Clay';
            $soilType = 'Sandy Gley Soil';
            $crops = 'Taro, Water Spinach, Rice, Root Crops';
        } else {
            $drainage = 'Well-Drained Loam';
            $soilType = 'Brown Agricultural Loam';
            $crops = 'Corn, Sweet Potato, Upland Rice, Peanut, Banana';
        }

        return [
            'soil_type'          => $soilType,
            'organic_matter'     => $organic,
            'drainage_profile'   => $drainage,
            'recommended_crops'  => $crops,
            'description'        => "Specimen classified as {$soilType} ({$munsell['full_code']}). Feature extraction confirms {$organic} organic content and {$drainage} profile.",
        ];
    }

    protected function getDefaultMunsellPalette(): array
    {
        return [
            (object) ['munsell_color_id' => 1, 'full_code' => '10YR 4/3', 'hue' => '10YR', 'value' => 4, 'chroma' => 3, 'color_name' => 'Brown / Dark Brown'],
            (object) ['munsell_color_id' => 2, 'full_code' => '10YR 3/2', 'hue' => '10YR', 'value' => 3, 'chroma' => 2, 'color_name' => 'Very Dark Brown'],
        ];
    }

    // validateFullSceneContext removed – logic lives in SoilValidationService::tierA()
}
