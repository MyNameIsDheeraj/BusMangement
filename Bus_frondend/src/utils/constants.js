// Role IDs
export const ROLES = {
  ADMIN: 1,
  TEACHER: 2,
  PARENT: 3,
  STUDENT: 4,
  DRIVER: 5,
  CLEANER: 6,
};

// Role Names
export const ROLE_NAMES = {
  [ROLES.ADMIN]: 'admin',
  [ROLES.TEACHER]: 'teacher',
  [ROLES.PARENT]: 'parent',
  [ROLES.STUDENT]: 'student',
  [ROLES.DRIVER]: 'driver',
  [ROLES.CLEANER]: 'cleaner',
};

// API Base URL
export const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1';

// Storage Keys
export const STORAGE_KEYS = {
  ACCESS_TOKEN: 'access_token',
  REFRESH_TOKEN: 'refresh_token',
  USER: 'user',
};

// Permission Names
export const PERMISSIONS = {
  VIEW_STUDENT: 'view_student',
  CREATE_STUDENT: 'create_student',
  EDIT_STUDENT: 'edit_student',
  DELETE_STUDENT: 'delete_student',
  VIEW_PAYMENT: 'view_payment',
  CREATE_PAYMENT: 'create_payment',
  EDIT_PAYMENT: 'edit_payment',
  DELETE_PAYMENT: 'delete_payment',
  VIEW_BUS_ROUTE: 'view_bus_route',
  VIEW_ATTENDANCE: 'view_attendance',
  MARK_ATTENDANCE: 'mark_attendance',
  CREATE_ALERT: 'create_alert',
  VIEW_ALERT: 'view_alert',
  VIEW_ANNOUNCEMENT: 'view_announcement',
  VIEW_USERS: 'view_users',
  CREATE_USERS: 'create_users',
  EDIT_USERS: 'edit_users',
  DELETE_USERS: 'delete_users',
  VIEW_STAFF: 'view_staff',
  CREATE_STAFF: 'create_staff',
  EDIT_STAFF: 'edit_staff',
  DELETE_STAFF: 'delete_staff',
};

