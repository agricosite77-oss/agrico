<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCodeMail;
use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class PasswordResetCodeController extends Controller
{
    /**
     * Show the web forgot-password view.
     */
    public function showForgotPasswordForm()
    {
        return view('website.auth.forgot-password');
    }

    /**
     * Send a 6-digit verification code to the given email.
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $email = strtolower(trim((string) $request->input('email')));

        // Verify account exists in database (case-insensitive for Postgres)
        $userExists = User::whereRaw('LOWER(email) = ?', [$email])->exists();
        $adminExists = Admin::whereRaw('LOWER(email) = ?', [$email])->exists();

        if (!$userExists && !$adminExists) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This email address is not registered in our records.',
                    'errors' => [
                        'email' => ['This email address is not registered in our records.']
                    ]
                ], 422);
            }

            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'This email address is not registered in our records.'
            ]);
        }

        // Generate 6-digit numeric verification code
        $code = sprintf('%06d', random_int(100000, 999999));

        // Persist code in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $code,
                'created_at' => now(),
            ]
        );

        $sent = false;
        $sendError = null;

        // 1. Try Resend HTTP API if key is configured (avoids blocked SMTP ports on cloud hosts like Railway)
        $resendKey = config('services.resend.key');
        if (is_string($resendKey) && trim($resendKey) !== '' && class_exists(\Resend::class)) {
            try {
                $resend = \Resend::client(trim($resendKey));
                $fromEmail = (string) config('services.resend.from', 'AgriCo Support <onboarding@resend.dev>');
                $resend->emails->send([
                    'from' => $fromEmail,
                    'to' => [$email],
                    'subject' => "Your AgriCo Password Reset Code: {$code}",
                    'html' => (string) view('emails.password-reset-code', ['code' => $code, 'email' => $email])->render(),
                ]);
                $sent = true;
                Log::info("AgriCo Password Reset Code sent via Resend HTTP API to {$email}");
            } catch (\Throwable $re) {
                Log::warning("Resend HTTP API failed: " . $re->getMessage() . ", falling back to standard Mail driver");
            }
        }

        // 2. Standard Laravel Mail driver (SMTP / Gmail)
        if (!$sent) {
            try {
                Mail::to($email)->send(new PasswordResetCodeMail($code, $email));
                $sent = true;
                Log::info("AgriCo Password Reset Code sent via Mail driver to {$email}");
            } catch (\Throwable $e) {
                $sendError = $e->getMessage();
                Log::error("Failed to send password reset code email to {$email}: " . $sendError);
            }
        }

        if (!$sent) {
            $userMsg = 'Unable to send verification email to your Gmail account. ' . ($sendError ?: 'Please verify mail settings or contact support.');
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $userMsg,
                ], 500);
            }

            return back()->withInput($request->only('email'))->withErrors([
                'email' => $userMsg
            ]);
        }

        Log::info("AgriCo Password Reset Code generated for {$email}: {$code}");

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'A 6-digit verification code has been sent to your email address.',
                'email' => $email,
            ]);
        }

        return redirect()->route('password.verify', ['email' => $email])
            ->with('status', 'A 6-digit verification code has been sent to your email address.');
    }

    /**
     * Show the web 6-square-box verify code view.
     */
    public function showVerifyCodeForm(Request $request)
    {
        $email = $request->query('email', '');
        return view('website.auth.verify-code', compact('email'));
    }

    /**
     * Verify the entered 6-digit code.
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'code'  => ['required', 'string', 'size:6'],
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $code  = trim((string) $request->input('code'));

        $record = DB::table('password_reset_tokens')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (!$record || $record->token !== $code) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'That verification code is invalid.',
                ], 422);
            }

            return back()->withInput($request->only('email'))->withErrors([
                'code' => 'That verification code is invalid.',
            ]);
        }

        // Check 15-minute expiration
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'That verification code has expired. Please request a new one.',
                ], 422);
            }

            return back()->withInput($request->only('email'))->withErrors([
                'code' => 'That verification code has expired. Please request a new one.',
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Verification code confirmed.',
                'email' => $email,
                'code' => $code,
            ]);
        }

        return redirect()->route('password.reset.form', [
            'email' => $email,
            'code'  => $code,
        ]);
    }

    /**
     * Show the web reset password view.
     */
    public function showResetPasswordForm(Request $request)
    {
        $email = $request->query('email', '');
        $code  = $request->query('code', '');

        return view('website.auth.reset-password', compact('email', 'code'));
    }

    /**
     * Reset the password in the database.
     */
    public function resetPassword(Request $request)
    {
        $inputCode = trim($request->input('code') ?? $request->input('token') ?? '');
        $request->merge(['code' => $inputCode]);

        $request->validate([
            'email' => ['required', 'string', 'email'],
            'code'  => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'confirmed',
                function ($attribute, $value, $fail) {
                    $missing = [];
                    if (strlen($value) < 8) {
                        $missing[] = '8+ characters';
                    }
                    if (!preg_match('/[0-9]/', $value)) {
                        $missing[] = 'at least one number';
                    }
                    if (!preg_match('/[A-Z]/', $value)) {
                        $missing[] = 'at least one uppercase letter';
                    }
                    if (!preg_match('/[a-z]/', $value)) {
                        $missing[] = 'at least one lowercase letter';
                    }
                    if (!preg_match('/[^A-Za-z0-9]/', $value)) {
                        $missing[] = 'at least one special character';
                    }

                    if (!empty($missing)) {
                        $fail('Password must meet all security requirements. Missing: ' . implode(', ', $missing) . '.');
                    }
                },
            ],
        ], [
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $code  = trim((string) $request->input('code'));
        $password = (string) $request->input('password');

        $record = DB::table('password_reset_tokens')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (!$record || $record->token !== $code) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid or expired verification session. Please restart.',
                ], 422);
            }

            return back()->withErrors([
                'password' => 'Invalid or expired verification session. Please restart.',
            ]);
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Verification code expired. Please request a new one.',
                ], 422);
            }

            return back()->withErrors([
                'password' => 'Verification code expired. Please request a new one.',
            ]);
        }

        $hashedPassword = Hash::make($password);

        // Update User if found (case-insensitive)
        $updatedUser = User::whereRaw('LOWER(email) = ?', [$email])->update([
            'password' => $hashedPassword,
        ]);

        // Update Admin if found (case-insensitive)
        $updatedAdmin = Admin::whereRaw('LOWER(email) = ?', [$email])
            ->update([
                'password' => $hashedPassword,
            ]);

        // Fail loudly if nothing was actually updated
        if (!$updatedUser && !$updatedAdmin) {
            Log::error("Password reset claimed success but updated 0 rows for {$email}");
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'We could not reset your password. Please try again or contact support.',
                ], 422);
            }
            return back()->withErrors([
                'password' => 'We could not reset your password. Please try again or contact support.',
            ]);
        }

        // Clear used reset token
        DB::table('password_reset_tokens')->whereRaw('LOWER(email) = ?', [$email])->delete();

        Log::info("Password successfully reset for {$email} (user rows: {$updatedUser}, admin rows: {$updatedAdmin})");

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Your password has been reset successfully. You can now log in.',
            ]);
        }

        return redirect()->route('login')->with('status', 'Your password has been reset successfully. You can now log in.');
    }
}
