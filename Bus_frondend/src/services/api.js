import axios from 'axios';
import { API_BASE_URL, STORAGE_KEYS } from '../utils/constants';

// Create axios instance for authenticated requests
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Create axios instance for auth endpoints (login, refresh) - no token needed
const authApi = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor to add auth token (only for authenticated endpoints)
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem(STORAGE_KEYS.ACCESS_TOKEN);
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    } else {
      console.warn('No access token found in storage');
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle token refresh and errors
let isRefreshing = false;
let failedQueue = [];

const processQueue = (error, token = null) => {
  failedQueue.forEach((prom) => {
    if (error) {
      prom.reject(error);
    } else {
      prom.resolve(token);
    }
  });
  
  failedQueue = [];
};

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    if (!originalRequest) {
      return Promise.reject(error);
    }

    // Don't retry if it's already a retry or if it's an auth endpoint
    const requestUrl = originalRequest.url || '';
    const isAuthEndpoint = requestUrl.includes('/login') || 
                          requestUrl.includes('/logout') || 
                          requestUrl.includes('/refresh') ||
                          requestUrl.includes('/register');

    // Only handle 401 errors for authenticated endpoints
    // Don't retry if already retried or if it's an auth endpoint
    if (error.response?.status === 401 && !originalRequest._retry && !isAuthEndpoint) {
      
      // Check if we have an access token to refresh
      // JWT library uses the expired access token itself for refresh
      const accessToken = localStorage.getItem(STORAGE_KEYS.ACCESS_TOKEN);
      if (!accessToken) {
        console.warn('No access token available, cannot refresh');
        // Clear tokens and redirect to login
        localStorage.removeItem(STORAGE_KEYS.ACCESS_TOKEN);
        localStorage.removeItem(STORAGE_KEYS.REFRESH_TOKEN);
        localStorage.removeItem(STORAGE_KEYS.USER);
        const currentPath = window.location.pathname;
        if (currentPath !== '/login' && !currentPath.includes('/login')) {
          window.location.href = '/login';
        }
        return Promise.reject(error);
      }

      if (isRefreshing) {
        // If already refreshing, queue this request
        return new Promise((resolve, reject) => {
          failedQueue.push({ resolve, reject });
        })
          .then((token) => {
            // Update the request headers with new token
            originalRequest.headers = originalRequest.headers || {};
            originalRequest.headers.Authorization = `Bearer ${token}`;
            return api(originalRequest);
          })
          .catch((err) => {
            return Promise.reject(err);
          });
      }

      originalRequest._retry = true;
      isRefreshing = true;

      try {
        console.log('Attempting to refresh access token...');
        
        // Get the expired access token from storage
        const expiredToken = localStorage.getItem(STORAGE_KEYS.ACCESS_TOKEN);
        if (!expiredToken) {
          throw new Error('No access token found for refresh');
        }
        
        // Send the expired token in Authorization header for refresh
        // JWT library refreshes using the expired access token itself
        const response = await authApi.post('/refresh', {}, {
          headers: {
            'Authorization': `Bearer ${expiredToken}`
          }
        });

        const data = response.data;
        const access_token = data.access_token || data.token || data.accessToken;
        
        if (access_token) {
          console.log('Token refreshed successfully');
          localStorage.setItem(STORAGE_KEYS.ACCESS_TOKEN, access_token);
          
          // Update user data if provided
          if (data.user) {
            localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(data.user));
          }
          
          // Update the original request with new token
          originalRequest.headers = originalRequest.headers || {};
          originalRequest.headers.Authorization = `Bearer ${access_token}`;
          
          processQueue(null, access_token);
          isRefreshing = false;
          
          // Retry the original request with new token
          return api(originalRequest);
        } else {
          console.error('Refresh response missing access_token:', data);
          throw new Error('No access token in refresh response');
        }
      } catch (refreshError) {
        console.error('Token refresh failed:', {
          status: refreshError.response?.status,
          data: refreshError.response?.data,
          message: refreshError.message,
        });
        
        processQueue(refreshError, null);
        isRefreshing = false;
        
        // Only clear tokens and logout if refresh actually failed (not network error)
        // Check if it's a real auth failure (401, 403) vs network/server error
        const isAuthFailure = refreshError.response?.status === 401 || 
                             refreshError.response?.status === 403;
        
        if (isAuthFailure) {
          console.warn('Refresh token invalid, clearing session');
          // Clear tokens on auth failure
          localStorage.removeItem(STORAGE_KEYS.ACCESS_TOKEN);
          localStorage.removeItem(STORAGE_KEYS.REFRESH_TOKEN);
          localStorage.removeItem(STORAGE_KEYS.USER);
          
          // Only redirect if we're not already on login page
          const currentPath = window.location.pathname;
          if (currentPath !== '/login' && !currentPath.includes('/login')) {
            // Use a small delay and check again to avoid redirect loops
            setTimeout(() => {
              if (window.location.pathname !== '/login') {
                window.location.href = '/login';
              }
            }, 100);
          }
        } else {
          // For network errors or server errors, don't logout
          // Just reject the request - let the component handle it
          console.warn('Refresh failed due to network/server error, keeping session');
        }
        
        return Promise.reject(refreshError);
      }
    }

    // For non-401 errors or other cases, just reject normally
    return Promise.reject(error);
  }
);

// API Service methods
export const apiService = {
  // Authentication - use authApi (no token required)
  login: (email, password) => authApi.post('/login', { email, password }),
  refresh: (expiredToken) => authApi.post('/refresh', {}, {
    headers: {
      'Authorization': `Bearer ${expiredToken}`
    }
  }),
  // Logout and getMe need token, so use api instance
  logout: () => api.post('/logout'),
  getMe: () => api.get('/me'),

  // Users
  getUsers: (params) => api.get('/users', { params }),
  getUser: (id) => api.get(`/users/${id}`),
  createUser: (data) => api.post('/users', data),
  updateUser: (id, data) => api.put(`/users/${id}`, data),
  deleteUser: (id) => api.delete(`/users/${id}`),

  // Students
  getStudents: (params) => api.get('/students', { params }),
  getStudent: (id) => api.get(`/students/${id}`),
  createStudent: (data) => api.post('/students', data),
  updateStudent: (id, data) => api.put(`/students/${id}`, data),
  deleteStudent: (id) => api.delete(`/students/${id}`),
  bulkDeleteStudents: (ids) => api.post('/students/bulk-delete', { ids }),
  getStudentsByClass: (classId) => api.get(`/students/class/${classId}`),
  assignParentToStudent: (studentId, parentId) => api.post(`/students/${studentId}/assign-parent`, { parent_id: parentId }),
  removeParentFromStudent: (studentId, parentId) => api.delete(`/students/${studentId}/remove-parent/${parentId}`),

  // Student-Parent Relationships
  getStudentParents: (params) => api.get('/student-parents', { params }),
  getStudentParent: (id) => api.get(`/student-parents/${id}`),
  createStudentParent: (data) => api.post('/student-parents', data),
  deleteStudentParent: (id) => api.delete(`/student-parents/${id}`),
  getParentsForStudent: (studentId) => api.get(`/student-parents/student/${studentId}`),
  getStudentsForParent: (parentId) => api.get(`/student-parents/parent/${parentId}`),

  // Buses
  getBuses: (params) => api.get('/buses', { params }),
  getBus: (id) => api.get(`/buses/${id}`),
  createBus: (data) => api.post('/buses', data),
  updateBus: (id, data) => api.put(`/buses/${id}`, data),
  deleteBus: (id) => api.delete(`/buses/${id}`),

  // Routes
  getRoutes: (params) => api.get('/routes', { params }),
  getRoute: (id) => api.get(`/routes/${id}`),
  createRoute: (data) => api.post('/routes', data),
  updateRoute: (id, data) => api.put(`/routes/${id}`, data),
  deleteRoute: (id) => api.delete(`/routes/${id}`),

  // Stops
  getStops: (params) => api.get('/stops', { params }),
  getStop: (id) => api.get(`/stops/${id}`),
  createStop: (data) => api.post('/stops', data),
  updateStop: (id, data) => api.put(`/stops/${id}`, data),
  deleteStop: (id) => api.delete(`/stops/${id}`),

  // Payments
  getPayments: (params) => api.get('/payments', { params }),
  getPayment: (id) => api.get(`/payments/${id}`),
  createPayment: (data) => api.post('/payments', data),
  updatePayment: (id, data) => api.put(`/payments/${id}`, data),
  deletePayment: (id) => api.delete(`/payments/${id}`),

  // Attendances
  getAttendances: (params) => api.get('/attendances', { params }),
  getAttendance: (id) => api.get(`/attendances/${id}`),
  createAttendance: (data) => api.post('/attendances', data),
  updateAttendance: (id, data) => api.put(`/attendances/${id}`, data),
  deleteAttendance: (id) => api.delete(`/attendances/${id}`),

  // Alerts
  getAlerts: (params) => api.get('/alerts', { params }),
  getAlert: (id) => api.get(`/alerts/${id}`),
  createAlert: (data) => api.post('/alerts', data),
  updateAlert: (id, data) => api.put(`/alerts/${id}`, data),
  deleteAlert: (id) => api.delete(`/alerts/${id}`),

  // Announcements
  getAnnouncements: (params) => api.get('/announcements', { params }),
  getAnnouncement: (id) => api.get(`/announcements/${id}`),
  createAnnouncement: (data) => api.post('/announcements', data),
  updateAnnouncement: (id, data) => api.put(`/announcements/${id}`, data),
  deleteAnnouncement: (id) => api.delete(`/announcements/${id}`),

  // Staff
  getStaffProfiles: (params) => api.get('/staff-profiles', { params }),
  getStaffProfile: (id) => api.get(`/staff-profiles/${id}`),
  createStaffProfile: (data) => api.post('/staff-profiles', data),
  updateStaffProfile: (id, data) => api.put(`/staff-profiles/${id}`, data),
  deleteStaffProfile: (id) => api.delete(`/staff-profiles/${id}`),

  // Teachers
  getTeachers: (params) => api.get('/teachers', { params }),
  getTeacher: (id) => api.get(`/teachers/${id}`),
  createTeacher: (data) => api.post('/teachers', data),
  updateTeacher: (id, data) => api.put(`/teachers/${id}`, data),
  deleteTeacher: (id) => api.delete(`/teachers/${id}`),
  getMyClasses: () => api.get('/teachers/me/classes'),
  getMyStudents: () => api.get('/teachers/me/students'),

  // Parents
  getParents: (params) => api.get('/parents', { params }),
  getParent: (id) => api.get(`/parents/${id}`),
  createParent: (data) => api.post('/parents', data),
  updateParent: (id, data) => api.put(`/parents/${id}`, data),
  deleteParent: (id) => api.delete(`/parents/${id}`),
  getParentMyStudents: () => api.get('/parents/me/students'),
};

export default api;
