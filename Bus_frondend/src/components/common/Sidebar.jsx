import { NavLink, Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import { ROLES } from '../../utils/constants';
import { getRoleDisplayName } from '../../utils/helpers';
import { useEffect, useState } from 'react';
import * as Icons from '@heroicons/react/24/outline';
import Tooltip from './Tooltip';

export default function Sidebar({ mobileOpen = false, onMobileClose = () => {} }) {
  const { user } = useAuth();
  const [expanded, setExpanded] = useState(true);

  useEffect(() => {
    const val = localStorage.getItem('sidebar-expanded');
    if (val !== null) setExpanded(val === 'true');
    else setExpanded(window.innerWidth >= 768);
  }, []);

  useEffect(() => {
    localStorage.setItem('sidebar-expanded', expanded ? 'true' : 'false');
  }, [expanded]);

  if (!user?.role) return null;

  const roleId = user.role.id;

  const links = [];

  if ([ROLES.ADMIN, ROLES.TEACHER, ROLES.PARENT, ROLES.STUDENT, ROLES.DRIVER, ROLES.CLEANER].includes(roleId)) {
    links.push({ path: '/dashboard', label: 'Dashboard' });
  }

  if (roleId === ROLES.ADMIN) {
    links.push(
      { path: '/admin/students', label: 'Students' },
      { path: '/admin/users', label: 'Users' },
      { path: '/admin/teachers', label: 'Teachers' },
      { path: '/admin/parents', label: 'Parents' },
      { path: '/admin/staff', label: 'Staff' },
      { path: '/admin/buses', label: 'Buses' },
      { path: '/admin/routes', label: 'Routes' },
      { path: '/admin/classes', label: 'Classes' },
      { path: '/admin/payments', label: 'Payments' },
      { path: '/admin/attendances', label: 'Attendances' },
      { path: '/admin/alerts', label: 'Alerts' },
      { path: '/admin/announcements', label: 'Announcements' }
    );
  }

  if (roleId === ROLES.TEACHER) {
    links.push(
      { path: '/teacher/students', label: 'My Students' },
      { path: '/teacher/attendances', label: 'Attendances' },
      { path: '/teacher/alerts', label: 'Alerts' }
    );
  }

  if (roleId === ROLES.PARENT) {
    links.push(
      { path: '/parent/children', label: 'My Children' },
      { path: '/parent/payments', label: 'Payments' }
    );
  }

  if (roleId === ROLES.STUDENT) {
    links.push(
      { path: '/student/profile', label: 'Profile' },
      { path: '/student/attendance', label: 'Attendance' }
    );
  }

  if (roleId === ROLES.DRIVER || roleId === ROLES.CLEANER) {
    links.push(
      { path: '/driver/route', label: 'My Route' },
      { path: '/driver/students', label: 'Students' },
      { path: '/driver/attendances', label: 'Attendances' }
    );
  }

  const widthClass = expanded ? 'w-64' : 'w-auto';

  const iconFor = (label) => {
    const key = label.toLowerCase();
    if (key.includes('dash')) return Icons.ChartBarIcon;
    if (key.includes('student')) return Icons.UserGroupIcon;
    if (key.includes('user') || key.includes('teacher')) return Icons.UserIcon;
    if (key.includes('parent')) return Icons.UserGroupIcon;
    if (key.includes('bus')) return Icons.HomeIcon;
    if (key.includes('route') || key.includes('stop') || key.includes('map')) return Icons.MapPinIcon || Icons.MapIcon;
    if (key.includes('payment')) return Icons.CreditCardIcon || Icons.CurrencyDollarIcon || Icons.WalletIcon;
    if (key.includes('attendance')) return Icons.CalendarDaysIcon || Icons.CalendarIcon;
    if (key.includes('alert') || key.includes('announcement')) return Icons.BellIcon;
    if (key.includes('home')) return Icons.HomeIcon;
    return Icons.Squares2X2Icon || Icons.HomeIcon;
  };

  const desktopSidebar = (
    <aside className={`hidden md:flex ${widthClass} mr-4 transition-all duration-200 flex-shrink-0`}>
      <div className="sticky top-16 flex flex-col h-[calc(100vh-4rem)]">
        <div
          role="button"
          tabIndex={0}
          onClick={() => setExpanded((s) => !s)}
          onKeyDown={(e) => { if (e.key === 'Enter' || e.key === ' ') setExpanded((s) => !s); }}
          className="flex items-center gap-3 px-4 py-3 cursor-pointer bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded shadow-sm hover:from-blue-700 hover:to-indigo-700"
        >
          <div className="flex-1">
              <div className="text-lg font-semibold" />
              <div className="text-sm opacity-90">{getRoleDisplayName(user.role.id)}</div>
            </div>
          
          <div className="text-white/80">{expanded ? '«' : '»'}</div>
        </div>

        <nav className="mt-4 bg-white/80 backdrop-blur rounded shadow p-2 flex-1 overflow-auto no-scrollbar">
          {links.map((l) => {
            const Icon = iconFor(l.label);
            const base = `group flex items-center gap-3 px-3 py-2 text-sm text-gray-800 hover:bg-gray-100 rounded ${expanded ? '' : 'justify-center'}`;
            return (
              <NavLink key={l.path} to={l.path} className={({ isActive }) => `${base} ${isActive ? 'bg-indigo-50 text-indigo-700' : ''}`}>
                {expanded ? (
                  <>
                    <div className="h-8 w-8 bg-white rounded flex items-center justify-center text-sm text-indigo-600 group-hover:bg-indigo-50 transition">
                      {Icon ? <Icon className="h-5 w-5" /> : l.label.charAt(0)}
                    </div>
                    <span className="truncate">{l.label}</span>
                  </>
                ) : (
                  <Tooltip text={l.label}>
                    <div className="h-8 w-8 bg-white rounded flex items-center justify-center text-sm text-indigo-600 transition">
                      {Icon ? <Icon className="h-5 w-5" /> : l.label.charAt(0)}
                    </div>
                  </Tooltip>
                )}
              </NavLink>
            );
          })}
        </nav>

        {/* user info moved to top navigation - sidebar should not contain logout/user controls */}
      </div>
    </aside>
  );

  const mobileDrawer = (
    <div className={`fixed inset-0 z-40 md:hidden ${mobileOpen ? '' : 'pointer-events-none'}`} aria-hidden={!mobileOpen}>
      <div className={`fixed inset-0 bg-black transition-opacity ${mobileOpen ? 'opacity-30' : 'opacity-0'}`} onClick={onMobileClose} />
      <div className={`fixed left-0 top-0 bottom-0 w-64 bg-white shadow-lg transform transition-transform ${mobileOpen ? 'translate-x-0' : '-translate-x-full'}`}>
        <div className="flex items-center justify-between px-4 py-3 border-b">
          <div className="flex items-center gap-3">
            {/* <div className="h-10 w-10 rounded-md bg-blue-600 text-white flex items-center justify-center font-bold">BM</div> */}
            <div>
              <div className="text-lg font-semibold">Bus Management</div>
              <div className="text-xs text-gray-500">{getRoleDisplayName(user.role.id)}</div>
            </div>
          </div>
          <button onClick={onMobileClose} className="p-2 text-gray-600">✕</button>
        </div>
        <nav className="p-2 overflow-auto">
          {links.map((l) => {
            const Icon = iconFor(l.label);
            return (
              <Link key={l.path} to={l.path} onClick={onMobileClose} className="flex items-center gap-3 px-3 py-2 text-sm text-gray-800 hover:bg-gray-50 rounded">
                <div className="h-8 w-8 bg-gray-100 rounded flex items-center justify-center text-sm text-gray-600">{Icon ? <Icon className="h-5 w-5 text-gray-600" /> : l.label.charAt(0)}</div>
                <span>{l.label}</span>
              </Link>
            );
          })}
        </nav>
        {/* mobile user info removed from drawer - handled by top nav */}
      </div>
    </div>
  );

  return (
    <>
      {desktopSidebar}
      {mobileDrawer}
    </>
  );
}
