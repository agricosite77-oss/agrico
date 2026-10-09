import axios from 'axios';
import { Platform } from 'react-native';
import * as Keychain from 'react-native-keychain';
import { ENV } from '../config/env';

const SERVICE_KEY = 'agrico_user_token';

// Candidate API hosts in order of priority:
// 1. Configured ENV.API_URL (http://10.0.2.2:8080/api for emulator)
// 2. 127.0.0.1:8080 (via adb reverse tcp:8080 tcp:8080 or iOS Simulator)
// 3. 192.168.100.37:8080 (Host PC Wi-Fi IP for physical phones on LAN)
// 4. 10.0.2.2:8080 (Android Emulator fallback)
const CANDIDATE_BASE_URLS = [
  ENV.API_URL,
  'http://10.0.2.2:8080/api',
  'http://127.0.0.1:8080/api',
  'http://192.168.100.37:8080/api',
].filter((url, index, self) => Boolean(url) && self.indexOf(url) === index);

let activeBaseUrl = ENV.API_URL || 'http://10.0.2.2:8080/api';

const api = axios.create({
  baseURL: activeBaseUrl,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Bypass-Tunnel-Reminder': 'true',
  },
  timeout: 4000,
});

export const getActiveBaseUrl = () => activeBaseUrl;

let onUnauthorizedCallback = null;

export const setUnauthorizedHandler = (handler) => {
  onUnauthorizedCallback = handler;
};

// Request Interceptor: Attach Bearer Token from react-native-keychain
api.interceptors.request.use(
  async (config) => {
    try {
      let token = null;
      const creds = await Keychain.getGenericPassword({ service: SERVICE_KEY });
      if (creds && creds.password) {
        token = creds.password;
      } else {
        const fallback = await Keychain.getGenericPassword();
        if (fallback && fallback.password) {
          token = fallback.password;
        }
      }

      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
    } catch (err) {
      console.warn('[AgriCo API] Error reading keychain in request interceptor:', err);
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response Interceptor: Handle HTTP 401 Unauthorized & Network Fallback
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const config = error.config;

    // If request failed with network error (server unreachable) and has not tried fallbacks yet:
    if (
      (!error.response || error.code === 'ERR_NETWORK' || error.message?.includes('Network Error')) &&
      config &&
      !config._fallbackAttempted
    ) {
      config._fallbackAttempted = true;

      for (const candidate of CANDIDATE_BASE_URLS) {
        if (candidate === activeBaseUrl) continue;
        try {
          console.log(`[AgriCo API] Retrying request via fallback host: ${candidate}`);
          config.baseURL = candidate;
          const retryRes = await axios(config);
          activeBaseUrl = candidate;
          api.defaults.baseURL = candidate;
          console.log(`[AgriCo API] Connected successfully to fallback host: ${candidate}`);
          return retryRes;
        } catch (fallbackErr) {
          // Continue trying next candidate
        }
      }
    }

    if (error.response && error.response.status === 401) {
      const url = error.config?.url || '';
      // Only clear session if the core session check (/user) specifically failed
      if (url.includes('/user') || url.includes('/logout')) {
        try {
          await Keychain.resetGenericPassword({ service: SERVICE_KEY });
          await Keychain.resetGenericPassword();
        } catch (err) {
          console.warn('[AgriCo API] Error resetting keychain on 401:', err);
        }
        if (typeof onUnauthorizedCallback === 'function') {
          onUnauthorizedCallback();
        }
      }
    }

    console.warn(`[AgriCo API Error] ${config?.method?.toUpperCase()} ${config?.url} failed: ${error.message} (Base: ${config?.baseURL})`);
    return Promise.reject(error);
  }
);

export default api;
