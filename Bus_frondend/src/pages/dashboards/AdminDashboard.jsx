import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { apiService } from '../../services/api';
// formatDate removed (unused)

const AdminDashboard = () => {
  const [stats, setStats] = useState({
    students: 0,
    buses: 0,
    routes: 0,
    payments: 0,
    attendances: 0,
    alerts: 0,
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const [studentsRes, busesRes, routesRes, paymentsRes, attendancesRes, alertsRes] = await Promise.allSettled([
          apiService.getStudents({ per_page: 1 }),
          apiService.getBuses({ per_page: 1 }),
          apiService.getRoutes({ per_page: 1 }),
          apiService.getPayments({ per_page: 1 }),
          apiService.getAttendances({ per_page: 1 }),
          apiService.getAlerts({ per_page: 1 }),
        ]);

        // Handle responses - use Promise.allSettled to prevent one failure from breaking all
        const getTotal = (result, index) => {
          if (result.status === 'fulfilled') {
            const data = result.value.data;
            return data?.total || data?.data?.total || (Array.isArray(data) ? data.length : 0) || (Array.isArray(data?.data) ? data.data.length : 0) || 0;
          } else {
            console.error(`Error fetching stat ${index}:`, result.reason);
            return 0;
          }
        };

        setStats({
          students: getTotal(studentsRes, 0),
          buses: getTotal(busesRes, 1),
          routes: getTotal(routesRes, 2),
          payments: getTotal(paymentsRes, 3),
          attendances: getTotal(attendancesRes, 4),
          alerts: getTotal(alertsRes, 5),
        });
      } catch (error) {
        console.error('Error fetching stats:', error);
        // Don't throw - just log the error and show empty stats
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, []);

  if (loading) {
    return <div className="text-center py-8">Loading dashboard...</div>;
  }

  return (
    <div className="px-4 py-6 sm:px-0">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p className="mt-2 text-sm text-gray-600">System-wide overview and management</p>
      </div>

      <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-blue-600">{stats.students}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-green-600">{stats.buses}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Total Buses</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-purple-600">{stats.routes}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Total Routes</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-yellow-600">{stats.payments}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Total Payments</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-indigo-600">{stats.attendances}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Total Attendances</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-red-600">{stats.alerts}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Total Alerts</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="mt-8">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <Link
            to="/admin/students"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">Manage Students</div>
            <div className="text-sm text-gray-500 mt-1">View and manage all students</div>
          </Link>
          <Link
            to="/admin/buses"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">Manage Buses</div>
            <div className="text-sm text-gray-500 mt-1">View and manage buses</div>
          </Link>
          <Link
            to="/admin/routes"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">Manage Routes</div>
            <div className="text-sm text-gray-500 mt-1">View and manage routes</div>
          </Link>
          <Link
            to="/admin/users"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">Manage Users</div>
            <div className="text-sm text-gray-500 mt-1">View and manage users</div>
          </Link>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboard;

