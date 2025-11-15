// Check if user has a specific role
export const hasRole = (user, roleId) => {
  if (!user || !user.role) return false;
  return user.role.id === roleId;
};

// Check if user is admin
export const isAdmin = (user) => hasRole(user, 1);

// Check if user is teacher
export const isTeacher = (user) => hasRole(user, 2);

// Check if user is parent
export const isParent = (user) => hasRole(user, 3);

// Check if user is student
export const isStudent = (user) => hasRole(user, 4);

// Check if user is driver
export const isDriver = (user) => hasRole(user, 5);

// Check if user is cleaner
export const isCleaner = (user) => hasRole(user, 6);

// Format date
export const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

// Format datetime
export const formatDateTime = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

// Format currency
export const formatCurrency = (amount) => {
  if (!amount && amount !== 0) return '';
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount);
};

// Get role display name
export const getRoleDisplayName = (roleId) => {
  const roleNames = {
    1: 'Admin',
    2: 'Teacher',
    3: 'Parent',
    4: 'Student',
    5: 'Driver',
    6: 'Cleaner',
  };
  return roleNames[roleId] || 'Unknown';
};

// Check if user can access resource based on context
export const canAccessResource = (user, resource, context) => {
  if (isAdmin(user)) return true;
  
  // Teacher: can only access students in their classes
  if (isTeacher(user) && resource === 'student') {
    return context?.classId && user.classes?.some(c => c.id === context.classId);
  }
  
  // Parent: can only access their children
  if (isParent(user) && resource === 'student') {
    return context?.parentId === user.id;
  }
  
  // Student: can only access their own data
  if (isStudent(user) && resource === 'student') {
    return context?.studentId === user.student?.id;
  }
  
  // Driver/Cleaner: can only access students on their route
  if ((isDriver(user) || isCleaner(user)) && resource === 'student') {
    return context?.routeId && user.bus?.route?.id === context.routeId;
  }
  
  return false;
};

// Check if user has a specific permission
export const hasPermission = (user, permission) => {
  if (!user) return false;
  // Admin has all permissions
  if (isAdmin(user)) return true;

  // Permissions could be present directly on user.permissions or on user.role.permissions
  const perms = user.permissions || user.role?.permissions || [];

  // Normalize structure: permissions may be array of strings or objects with 'name'
  const names = perms.map((p) => (typeof p === 'string' ? p : p?.name)).filter(Boolean);
  return names.includes(permission);
};


