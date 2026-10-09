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

        $email = trim($request->input('email'));

        // Verify account exists in database
        $userExists = User::where('email', $email)->exists();
        $adminExists = Admin::where('email', $email)->exists();

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

        // Send email with 6-digit code via Mailable
        try {
            Mail::to($email)->send(new PasswordResetCodeMail($code, $email));
        } catch (\Throwable $e) {
            Log::error("Failed to send password reset code email to {$email}: " . $e->getMessage());
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

        $email = trim($request->input('email'));
        $code  = trim($request->input('code'));

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
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

        $email = trim($request->input('email'));
        $code  = trim($request->input('code'));
        $password = $request->input('password');

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
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

        // Update User if found
        $updatedUser = User::where('email', $email)->update([
            'password' => $hashedPassword,
        ]);

        // Update Admin if found
        $updatedAdmin = Admin::where('email', $email)
            ->update([
                'password' => $hashedPassword,
            ]);

        // NEW: fail loudly if nothing was actually updated
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
        DB::table('password_reset_tokens')->where('email', $email)->delete();

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
