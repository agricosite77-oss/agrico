# AgriCo Mobile (Bare React Native CLI)

Clean React Native CLI application integrated with Laravel Sanctum token authentication for **Email Log In** & **Account Sign Up**, with persistent token security via `react-native-keychain`.

---

## 🛠️ Tech Stack

- **Framework:** React Native CLI 0.73.6 (Non-Expo)
- **Navigation:** `@react-navigation/native` + `@react-navigation/native-stack`
- **Secure Token Storage:** `react-native-keychain`
- **HTTP Client:** `axios` with automatic Bearer token interceptors & 401 handler
- **Backend API:** Laravel Sanctum (`/api/login`, `/api/register`, `/api/user`, `/api/logout`)

---

## 📱 App Flow

1. **Splash Screen:** Auto-checks `react-native-keychain` for saved Sanctum tokens and validates against `GET /api/user`.
2. **Sign In Screen:** Allows existing users to log in with Email & Password (`POST /api/login`). Includes navigation link to **Sign Up**.
3. **Sign Up Screen:** Allows new users to create an account with Name, Email, Password, and Password Confirmation (`POST /api/register`).
4. **Home Screen Dashboard:** Displays authenticated user profile, active Sanctum session status, and Sign Out button.

---

## 🚀 How to Run the App

### Step 1: Install Node Dependencies
```bash
cd rn-cli-app
npm install
```

### Step 2: Start Metro Bundler
```bash
npm start
```

### Step 3: Launch on Android Emulator
Make sure your Android Emulator (or connected physical device) is running, then execute:
```bash
npm run android
```
