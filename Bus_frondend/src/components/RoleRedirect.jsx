import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { ROLES } from '../utils/constants';

const RoleRedirect = () => {
  const { user, loading } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (!loading && user?.role) {
      const roleRoutes = {
        [ROLES.ADMIN]: '/admin/dashboard',
        [ROLES.TEACHER]: '/teacher/dashboard',
        [ROLES.PARENT]: '/parent/dashboard',
        [ROLES.STUDENT]: '/student/dashboard',
        [ROLES.DRIVER]: '/driver/dashboard',
        [ROLES.CLEANER]: '/cleaner/dashboard',
      };

      const route = roleRoutes[user.role.id];
      if (route) {
        navigate(route, { replace: true });
      } else {
        navigate('/login', { replace: true });
      }
    } else if (!loading && !user) {
      navigate('/login', { replace: true });
    }
  }, [user, loading, navigate]);

  return (
    <div className="flex items-center justify-center min-h-screen">
      <div className="text-lg">Redirecting...</div>
    </div>
  );
};

export default RoleRedirect;

