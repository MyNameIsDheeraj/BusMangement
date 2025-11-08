// Debugging utilities for authentication
import { STORAGE_KEYS } from './constants';

export const checkAuthStatus = () => {
  const accessToken = localStorage.getItem(STORAGE_KEYS.ACCESS_TOKEN);
  const refreshToken = localStorage.getItem(STORAGE_KEYS.REFRESH_TOKEN);
  const user = localStorage.getItem(STORAGE_KEYS.USER);

  console.group('🔐 Authentication Status');
  console.log('Access Token:', accessToken ? '✅ Present' : '❌ Missing');
  console.log('Refresh Token:', refreshToken ? '✅ Present' : '❌ Missing');
  console.log('User Data:', user ? '✅ Present' : '❌ Missing');
  
  if (accessToken) {
    try {
      // Try to decode JWT (basic check - not validating signature)
      const payload = JSON.parse(atob(accessToken.split('.')[1]));
      const expiry = new Date(payload.exp * 1000);
      const now = new Date();
      console.log('Token Expiry:', expiry.toLocaleString());
      console.log('Token Valid:', expiry > now ? '✅ Valid' : '❌ Expired');
      console.log('Time Until Expiry:', Math.round((expiry - now) / 1000), 'seconds');
    } catch (e) {
      console.warn('Could not decode token:', e);
    }
  }
  
  if (user) {
    try {
      const userData = JSON.parse(user);
      console.log('User:', userData);
    } catch (e) {
      console.warn('Could not parse user data:', e);
    }
  }
  
  console.groupEnd();
  
  return {
    hasAccessToken: !!accessToken,
    hasRefreshToken: !!refreshToken,
    hasUser: !!user,
  };
};

// Call this in browser console: window.checkAuthStatus()
if (typeof window !== 'undefined') {
  window.checkAuthStatus = checkAuthStatus;
}

