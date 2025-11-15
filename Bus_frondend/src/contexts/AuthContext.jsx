/* eslint-disable react-refresh/only-export-components */
import { createContext, useContext, useState, useEffect } from 'react';
import { apiService } from '../services/api';
import { STORAGE_KEYS } from '../utils/constants';

// Helper function to decode JWT token and check if it's expired
const isTokenExpired = (token) => {
  if (!token) return true;
  
  try {
    const base64Url = token.split('.')[1];
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    const payload = JSON.parse(atob(base64));
    const currentTime = Math.floor(Date.now() / 1000);
    return payload.exp < currentTime;
  } catch (error) {
    console.error('Error decoding token:', error);
    return true;
  }
};

const AuthContext = createContext(null);

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [isAuthenticated, setIsAuthenticated] = useState(false);

  // Normalize user shape helper (ensure `role` object exists if backend returned role_id)
  const normalizeUser = (u) => {
    if (!u) return u;
    const userCopy = { ...u };
    if (!userCopy.role && userCopy.role_id) {
      // coerce role id to number when possible
      const rid = Number(userCopy.role_id);
      userCopy.role = { id: Number.isNaN(rid) ? userCopy.role_id : rid, name: userCopy.role_name || null };
    } else if (userCopy.role && userCopy.role.id) {
      // ensure numeric id if possible
      const rid = Number(userCopy.role.id);
      userCopy.role.id = Number.isNaN(rid) ? userCopy.role.id : rid;
    }
    if (!userCopy.permissions && userCopy.role?.permissions) {
      userCopy.permissions = userCopy.role.permissions;
    }
    return userCopy;
  };

  // Logout function - defined early to avoid dependency issues
  const logout = async (silent = false) => {
    try {
      const token = localStorage.getItem(STORAGE_KEYS.ACCESS_TOKEN);
      if (token && !silent) {
        // Only call logout API if we have a token and aren't doing a silent logout
        try {
          await apiService.logout();
        } catch (error) {
          // Ignore logout API errors - we still want to clear local storage
          console.error('Logout API error:', error);
        }
      }
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      // Clear storage regardless of API call result
      localStorage.removeItem(STORAGE_KEYS.ACCESS_TOKEN);
      localStorage.removeItem(STORAGE_KEYS.REFRESH_TOKEN);
      localStorage.removeItem(STORAGE_KEYS.USER);
      setUser(null);
      setIsAuthenticated(false);
    }
  };

  // Load user from storage on mount
  useEffect(() => {
    const loadUser = async () => {
      const storedUser = localStorage.getItem(STORAGE_KEYS.USER);
      const token = localStorage.getItem(STORAGE_KEYS.ACCESS_TOKEN);

      if (storedUser && token) {
        try {
          const rawUser = JSON.parse(storedUser);
          // Normalize user shape: ensure user.role exists when backend returns role_id
          const normalizeUser = (u) => {
            if (!u) return u;
            const userCopy = { ...u };
            if (!userCopy.role && userCopy.role_id) {
              userCopy.role = { id: userCopy.role_id, name: userCopy.role_name || null };
            }
            // ensure permissions array is present (can be on role or user)
            if (!userCopy.permissions && userCopy.role?.permissions) {
              userCopy.permissions = userCopy.role.permissions;
            }
            return userCopy;
          };

          const userData = normalizeUser(rawUser);
          
          // Check if token is expired before using it
          if (isTokenExpired(token)) {
            console.log('Token expired, attempting refresh...');
            try {
              // Try to refresh the token using apiService.refresh (API wrapper)
              const response = await apiService.refresh(token);
              const data = response.data;
              const access_token = data?.access_token || data?.token || data?.accessToken;
              if (access_token) {
                console.log('Token refreshed successfully');
                localStorage.setItem(STORAGE_KEYS.ACCESS_TOKEN, access_token);
                // Update stored user if provided
                if (data.user) {
                  localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(data.user));
                }
                setUser(userData);
                setIsAuthenticated(true);
                return;
              }
            } catch (refreshError) {
              console.error('Token refresh failed:', refreshError);
              await logout(true);
              return;
            }
          } else {
            setUser(userData);
            setIsAuthenticated(true);
          }
        } catch (error) {
          // Failed to parse user data or other error
          console.error('Error loading user data:', error);
          await logout(true);
        }
      }
    };

    loadUser().finally(() => {
      // Ensure loading is set to false only after the async operation completes
      setLoading(false);
    });
  }, []);

  // Login function
  const login = async (email, password) => {
    try {
      const response = await apiService.login(email, password);
      const data = response.data;

      console.log('Login response:', data);

      // Handle different response structures
      const access_token = data.access_token || data.token;
  const userDataRaw = data.user || data;
  const userData = normalizeUser(userDataRaw);

      if (!access_token) {
        console.error('No access token in login response:', data);
        throw new Error('No access token received');
      }

      // Store token and user data
      // Note: JWT library uses the expired access token itself for refresh,
      // so we don't need a separate refresh token
  localStorage.setItem(STORAGE_KEYS.ACCESS_TOKEN, access_token);
  localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(userData));

  setUser(userData);
      setIsAuthenticated(true);

      return { success: true, user: userData };
    } catch (error) {
      console.error('Login error:', error);
      const errorMessage = error.response?.data?.message || 
                          error.response?.data?.error || 
                          error.message || 
                          'Login failed. Please check your credentials.';
      return {
        success: false,
        error: errorMessage,
      };
    }
  };

  // Update user data
  const updateUser = (userDataRaw) => {
    const userData = normalizeUser(userDataRaw);
    setUser(userData);
    localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(userData));
  };

  // Get current user
  const getCurrentUser = async () => {
    try {
      const response = await apiService.getMe();
  const userData = normalizeUser(response.data);
  updateUser(userData);
  return userData;
    } catch (error) {
      console.error('Get current user error:', error);
      // Don't logout immediately on 401/403 when we already have an API service
      // with proper token refresh logic - this prevents false logouts
      // The API interceptor handles token refresh and logout when necessary
      return null;
    }
  };

  // Check authentication status without making API calls
  const checkAuthStatus = () => {
    const token = localStorage.getItem(STORAGE_KEYS.ACCESS_TOKEN);
    const storedUser = localStorage.getItem(STORAGE_KEYS.USER);
    
    if (!token || !storedUser) {
      return { isAuthenticated: false, user: null };
    }
    
    if (isTokenExpired(token)) {
      // Token is expired, clear it
      localStorage.removeItem(STORAGE_KEYS.ACCESS_TOKEN);
      localStorage.removeItem(STORAGE_KEYS.REFRESH_TOKEN);
      localStorage.removeItem(STORAGE_KEYS.USER);
      return { isAuthenticated: false, user: null };
    }
    
    try {
      const userData = JSON.parse(storedUser);
      return { isAuthenticated: true, user: userData };
    } catch (error) {
      console.error('Error parsing stored user data:', error);
      return { isAuthenticated: false, user: null };
    }
  };

  const value = {
    user,
    loading,
    isAuthenticated,
    login,
    logout,
    updateUser,
    getCurrentUser,
    checkAuthStatus,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

