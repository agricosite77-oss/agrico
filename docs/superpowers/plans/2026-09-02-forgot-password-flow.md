# End-to-End Forgot Password Flow Implementation Plan (Web & Mobile)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement a complete, end-to-end "Forgot Password" flow with live email checking against the database, 6-digit emailed verification code, square 6-box verification UI, and password reset — unified across both Laravel Web and React Native Mobile.

**Architecture:**
- **Backend API & Web Controller:** `App\Http\Controllers\Auth\PasswordResetCodeController` handling code generation, email dispatch via `Mail`, token persistence in `password_reset_tokens`, code validation (with 15-minute expiration), and password update.
- **Web UI:**
  - `forgot-password.blade.php`: Email input with live DB check (`validateLiveEmail(this, 'forgot')`).
  - `verify-code.blade.php`: 6 square auto-advancing boxes with cooldown timer and AgriCo card styling.
  - `reset-password.blade.php`: New password & confirmation with green pill submit button.
- **Mobile UI (`rn-cli-app`):**
  - `ForgotPasswordScreen.js`: Email input with live checking $\to$ calls `/api/forgot-password/send-code`.
  - `VerifyCodeScreen.js`: 6 square code boxes (using delivered `.agents/VerifyCodeScreen.js`) with auto-advance and cooldown $\to$ calls `/api/forgot-password/verify-code`.
  - `ResetPasswordScreen.js`: Password reset inputs $\to$ calls `/api/forgot-password/reset`.
  - Registered in `AppNavigator.js` and wired from `SignInScreen.js`.

---

## File Structure

- **`app/Http/Controllers/Auth/PasswordResetCodeController.php`** [NEW]: Controller managing code generation, mailing, verification, and password updating.
- **`routes/web.php`** [MODIFY]: Registers `password.request`, `password.send-code`, `password.verify`, `password.verify-code`, `password.reset.form`, `password.update`.
- **`routes/api.php`** [MODIFY]: Exposes `/auth/check-email`, `/forgot-password/send-code`, `/forgot-password/verify-code`, `/forgot-password/reset`.
- **`resources/views/website/auth/forgot-password.blade.php`** [MODIFY]: Aligns form action to `route('password.send-code')` with live DB check.
- **`resources/views/website/auth/verify-code.blade.php`** [NEW]: 6 square boxes web verification screen.
- **`resources/views/website/auth/reset-password.blade.php`** [MODIFY]: Clean AgriCo card reset password view.
- **`rn-cli-app/src/screens/ForgotPasswordScreen.js`** [NEW]: Mobile email entry with live validation.
- **`rn-cli-app/src/screens/VerifyCodeScreen.js`** [NEW]: Mobile 6 square boxes screen (from `.agents/VerifyCodeScreen.js`).
- **`rn-cli-app/src/screens/ResetPasswordScreen.js`** [NEW]: Mobile reset password screen.
- **`rn-cli-app/src/screens/SignInScreen.js`** [MODIFY]: Connects "Forgot password?" touchable to navigate to `ForgotPassword`.
- **`rn-cli-app/src/navigation/AppNavigator.js`** [MODIFY]: Registers the 3 new auth screens in the unauthenticated stack.
- **`tests/Feature/PasswordResetCodeTest.php`** [NEW]: Feature tests covering the full flow.

---

## Task 1: Backend PasswordResetCodeController & Routes

- [ ] **Step 1: Create `PasswordResetCodeController.php`**
Implement methods:
1. `showForgotPasswordForm()`
2. `sendResetCode(Request $request)` (validates email, stores 6-digit code in `password_reset_tokens`, sends email)
3. `showVerifyCodeForm(Request $request)`
4. `verifyCode(Request $request)` (verifies 6-digit code with 15-min expiration)
5. `showResetPasswordForm(Request $request)`
6. `resetPassword(Request $request)` (updates password, clears token)

- [ ] **Step 2: Register routes in `routes/web.php` & `routes/api.php`**
Ensure `route('password.request')` resolves to `showForgotPasswordForm`.
Ensure `/api/forgot-password/send-code`, `/api/forgot-password/verify-code`, and `/api/forgot-password/reset` are accessible to mobile.

- [ ] **Step 3: Create automated feature test `tests/Feature/PasswordResetCodeTest.php`**
Test send code, verify code, and password update.

---

## Task 2: Web Forgot Password Flow Views

- [ ] **Step 1: Update `forgot-password.blade.php`**
Wire form action to `route('password.send-code')` with `validateLiveEmail(this, 'forgot')`.
- [ ] **Step 2: Create `verify-code.blade.php`**
Build 6 square input boxes with automatic focus advance, backspace handling, resend cooldown, and AgriCo card styling.
- [ ] **Step 3: Update `reset-password.blade.php`**
Wire form to `route('password.update')` with new password & confirm password inputs.

---

## Task 3: Mobile React Native Forgot Password Flow

- [ ] **Step 1: Create `rn-cli-app/src/screens/ForgotPasswordScreen.js`**
Email input with live check, calls `POST /api/forgot-password/send-code`, navigates to `VerifyCode`.
- [ ] **Step 2: Install and adapt `VerifyCodeScreen.js` in `rn-cli-app/src/screens/`**
Incorporate delivered `.agents/VerifyCodeScreen.js`, dynamically resolve API URL via `api.js` or `env.js`, and match `PureGradient` / card styling.
- [ ] **Step 3: Create `rn-cli-app/src/screens/ResetPasswordScreen.js`**
Password & confirmation inputs, calls `POST /api/forgot-password/reset`, redirects to `SignIn`.
- [ ] **Step 4: Update `SignInScreen.js` & `AppNavigator.js`**
Link `Forgot password?` to `ForgotPassword`, add routes to `AppNavigator`.

---

## Task 4: End-to-End Verification

- [ ] **Step 1: Run feature tests via `php artisan test`**
- [ ] **Step 2: Verify Web Flow**
- [ ] **Step 3: Verify Mobile Flow on Android Emulator**
Capture live emulator screenshot of `ForgotPasswordScreen` and `VerifyCodeScreen`.
