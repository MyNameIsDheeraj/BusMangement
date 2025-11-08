import { createContext, useContext, useState, useEffect } from 'react';
import { apiService } from '../services/api';
import { STORAGE_KEYS } from '../utils/constants';

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

  // Logout function - defined early to avoid dependency issues
  const logout = async () => {
    try {
      const token = localStorage.getItem(STORAGE_KEYS.ACCESS_TOKEN);
      if (token) {
        // Only call logout API if we have a token
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
          const userData = JSON.parse(storedUser);
          setUser(userData);
          setIsAuthenticated(true);
          // Don't verify token on initial load to avoid immediate logout on network issues
          // Token validation will be handled by API calls and interceptors
        } catch (error) {
          // Failed to parse user data
          console.error('Error parsing user data:', error);
          localStorage.removeItem(STORAGE_KEYS.ACCESS_TOKEN);
          localStorage.removeItem(STORAGE_KEYS.REFRESH_TOKEN);
          localStorage.removeItem(STORAGE_KEYS.USER);
          setUser(null);
          setIsAuthenticated(false);
        }
      }
      setLoading(false);
    };

    loadUser();
  }, []);

  // Login function
  const login = async (email, password) => {
    try {
      const response = await apiService.login(email, password);
      const data = response.data;

      console.log('Login response:', data);

      // Handle different response structures
      const access_token = data.access_token || data.token;
      const userData = data.user || data;

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
  const updateUser = (userData) => {
    setUser(userData);
    localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(userData));
  };

  // Get current user
  const getCurrentUser = async () => {
    try {
      const response = await apiService.getMe();
      const userData = response.data;
      updateUser(userData);
      return userData;
    } catch (error) {
      console.error('Get current user error:', error);
      // Only log out if it's a 401/403 error (auth failure)
      // For network errors or other issues, keep the user session
      if (error.response?.status === 401 || error.response?.status === 403) {
        logout();
      }
      return null;
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
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

