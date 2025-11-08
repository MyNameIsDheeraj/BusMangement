import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { ROLES } from '../utils/constants';
import { getRoleDisplayName } from '../utils/helpers';

const Layout = ({ children }) => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const getDashboardPath = () => {
    if (!user?.role) return '/dashboard';
    const roleRoutes = {
      [ROLES.ADMIN]: '/admin/dashboard',
      [ROLES.TEACHER]: '/teacher/dashboard',
      [ROLES.PARENT]: '/parent/dashboard',
      [ROLES.STUDENT]: '/student/dashboard',
      [ROLES.DRIVER]: '/driver/dashboard',
      [ROLES.CLEANER]: '/cleaner/dashboard',
    };
    return roleRoutes[user.role.id] || '/dashboard';
  };

  const getNavLinks = () => {
    if (!user?.role) return [];
    
    const roleId = user.role.id;
    const links = [];

    // Common links
    if ([ROLES.ADMIN, ROLES.TEACHER, ROLES.PARENT, ROLES.STUDENT, ROLES.DRIVER, ROLES.CLEANER].includes(roleId)) {
      links.push({ path: getDashboardPath(), label: 'Dashboard' });
    }

    // Admin links
    if (roleId === ROLES.ADMIN) {
      links.push(
        { path: '/admin/students', label: 'Students' },
        { path: '/admin/users', label: 'Users' },
        { path: '/admin/buses', label: 'Buses' },
        { path: '/admin/routes', label: 'Routes' },
        { path: '/admin/payments', label: 'Payments' },
        { path: '/admin/attendances', label: 'Attendances' },
        { path: '/admin/alerts', label: 'Alerts' },
        { path: '/admin/announcements', label: 'Announcements' }
      );
    }

    // Teacher links
    if (roleId === ROLES.TEACHER) {
      links.push(
        { path: '/teacher/students', label: 'My Students' },
        { path: '/teacher/attendances', label: 'Attendances' },
        { path: '/teacher/alerts', label: 'Alerts' },
        { path: '/teacher/payments', label: 'Payments' }
      );
    }

    // Parent links
    if (roleId === ROLES.PARENT) {
      links.push(
        { path: '/parent/children', label: 'My Children' },
        { path: '/parent/payments', label: 'Payments' },
        { path: '/parent/attendances', label: 'Attendances' }
      );
    }

    // Student links
    if (roleId === ROLES.STUDENT) {
      links.push(
        { path: '/student/profile', label: 'Profile' },
        { path: '/student/attendance', label: 'Attendance' },
        { path: '/student/payments', label: 'Payments' }
      );
    }

    // Driver/Cleaner links
    if (roleId === ROLES.DRIVER || roleId === ROLES.CLEANER) {
      links.push(
        { path: '/driver/route', label: 'My Route' },
        { path: '/driver/students', label: 'Students' },
        { path: '/driver/attendances', label: 'Attendances' },
        { path: '/driver/alerts', label: 'Alerts' }
      );
    }

    return links;
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <nav className="bg-white shadow-lg">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-16">
            <div className="flex">
              <div className="flex-shrink-0 flex items-center">
                <Link to={getDashboardPath()} className="text-xl font-bold text-blue-600">
                  Bus Management System
                </Link>
              </div>
              <div className="hidden sm:ml-6 sm:flex sm:space-x-8">
                {getNavLinks().map((link) => (
                  <Link
                    key={link.path}
                    to={link.path}
                    className="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 hover:text-blue-600"
                  >
                    {link.label}
                  </Link>
                ))}
              </div>
            </div>
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <span className="text-sm text-gray-700 mr-4">
                  {user?.name} ({getRoleDisplayName(user?.role?.id)})
                </span>
                <button
                  onClick={handleLogout}
                  className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                >
                  Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </nav>

      <main className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        {children}
      </main>
    </div>
  );
};

export default Layout;

