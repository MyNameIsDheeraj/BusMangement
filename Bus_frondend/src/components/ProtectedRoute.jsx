import { Navigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { ROLES } from '../utils/constants';

const ProtectedRoute = ({ children, allowedRoles = [] }) => {
  const { isAuthenticated, user, loading } = useAuth();

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="text-lg">Loading...</div>
      </div>
    );
  }

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  // If allowedRoles is specified, check if user has one of the allowed roles
  if (allowedRoles.length > 0 && user?.role) {
    const userRoleId = user.role.id;
    if (!allowedRoles.includes(userRoleId)) {
      // Redirect to appropriate dashboard based on role
      const roleRoutes = {
        [ROLES.ADMIN]: '/admin/dashboard',
        [ROLES.TEACHER]: '/teacher/dashboard',
        [ROLES.PARENT]: '/parent/dashboard',
        [ROLES.STUDENT]: '/student/dashboard',
        [ROLES.DRIVER]: '/driver/dashboard',
        [ROLES.CLEANER]: '/cleaner/dashboard',
      };
      return <Navigate to={roleRoutes[userRoleId] || '/dashboard'} replace />;
    }
  }

  return children;
};

export default ProtectedRoute;

