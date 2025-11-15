import { useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import Sidebar from './common/Sidebar';
import { useState } from 'react';
import { Bars3Icon, UserCircleIcon, ArrowRightOnRectangleIcon } from '@heroicons/react/24/outline';

const Layout = ({ children }) => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const [mobileOpen, setMobileOpen] = useState(false);

  

  return (
    <div className="min-h-screen bg-gray-50">
      <nav className="bg-white shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="h-16 flex items-center">
            <div className="flex items-center gap-4">
              <div className="hidden md:block text-2xl font-bold text-blue-600">Bus Management System</div>
            </div>
            <div className="flex-1" />
            <div className="flex items-center gap-3">
              {/* user avatar/name */}
              <div className="flex items-center gap-2">
                {user?.avatar ? (
                  <img
                    src={user.avatar}
                    alt={user.name || 'User'}
                    className="h-8 w-8 rounded-full object-cover border border-gray-300"
                    onError={(e) => {
                      e.target.style.display = 'none';
                      e.target.nextElementSibling.style.display = 'inline-flex';
                    }}
                  />
                ) : null}
                <UserCircleIcon
                  className="h-8 w-8 text-gray-700"
                  style={user?.avatar ? { display: 'none' } : {}}
                />
                {/* user name/role removed per request - only the icon shows in the top nav */}
              </div>

              {/* logout icon/button */}
              {user && (
                <button
                  onClick={handleLogout}
                  title="Logout"
                  className="ml-2 inline-flex items-center p-2 bg-red-600 text-white rounded hover:bg-red-700"
                >
                  <ArrowRightOnRectangleIcon className="h-5 w-5" />
                </button>
              )}
            </div>
          </div>
        </div>
      </nav>
      <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div className="flex">
          <Sidebar mobileOpen={mobileOpen} onMobileClose={() => setMobileOpen(false)} />
          <main className="flex-1">
            {/* small top spacing/header inside main for page title or breadcrumbs if needed */}
            <div className="mb-4">
              <div className="flex items-center justify-between">
                <div className="md:hidden mr-2">
                  <button onClick={() => setMobileOpen(true)} className="p-2 rounded bg-white shadow-sm">
                    <Bars3Icon className="h-5 w-5 text-gray-700" />
                  </button>
                </div>
                <h2 className="text-2xl font-semibold text-gray-800">{/* page title inserted by pages */}</h2>
                {/* user name/role removed from main header */}
              </div>
            </div>
            {children}
          </main>
        </div>
      </div>
    </div>
  );
};

export default Layout;

