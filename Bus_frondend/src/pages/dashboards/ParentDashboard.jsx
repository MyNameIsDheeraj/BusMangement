import { useState, useEffect } from 'react';
import { apiService } from '../../services/api';
import { formatDate, formatCurrency } from '../../utils/helpers';

const ParentDashboard = () => {
  const [children, setChildren] = useState([]);
  const [loading, setLoading] = useState(true);
  const [stats, setStats] = useState({
    totalPayments: 0,
    pendingPayments: 0,
  });

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [childrenRes, paymentsRes] = await Promise.all([
          apiService.getParentMyStudents(),
          apiService.getPayments({ per_page: 100 }),
        ]);

        const childrenData = Array.isArray(childrenRes.data) ? childrenRes.data : childrenRes.data?.data || [];
        setChildren(childrenData);

        const payments = Array.isArray(paymentsRes.data) ? paymentsRes.data : paymentsRes.data?.data || [];
        const totalPayments = payments.reduce((sum, payment) => sum + (parseFloat(payment.amount_paid) || 0), 0);
        const pendingPayments = payments.filter(
          (payment) => payment.status === 'pending' || payment.status === 'due'
        ).length;

        setStats({
          totalPayments,
          pendingPayments,
        });
      } catch (error) {
        console.error('Error fetching data:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  if (loading) {
    return <div className="text-center py-8">Loading dashboard...</div>;
  }

  return (
    <div className="px-4 py-6 sm:px-0">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Parent Dashboard</h1>
        <p className="mt-2 text-sm text-gray-600">Monitor your children's transportation and academic progress</p>
      </div>

      <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-green-600">{formatCurrency(stats.totalPayments)}</div>
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
                <div className="text-2xl font-bold text-yellow-600">{stats.pendingPayments}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Pending Payments</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="mt-8">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">My Children</h2>
        <div className="bg-white shadow overflow-hidden sm:rounded-md">
          <ul className="divide-y divide-gray-200">
            {children.length > 0 ? (
              children.map((child) => (
                <li key={child.id}>
                  <div className="px-4 py-4 sm:px-6">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center">
                        <div>
                          <div className="text-sm font-medium text-gray-900">
                            {child.user?.name || 'N/A'}
                          </div>
                          <div className="text-sm text-gray-500">
                            Admission: {child.admission_no || 'N/A'} | Class: {child.class?.name || 'N/A'}
                          </div>
                          {child.bus && (
                            <div className="text-sm text-gray-500">
                              Bus: {child.bus.bus_number} | Route: {child.bus.route?.name || 'N/A'}
                            </div>
                          )}
                        </div>
                      </div>
                      <div className="ml-2 flex-shrink-0 space-x-2">
                        <a
                          href={`/parent/children/${child.id}`}
                          className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                        >
                          View Details
                        </a>
                      </div>
                    </div>
                  </div>
                </li>
              ))
            ) : (
              <li className="px-4 py-4 sm:px-6 text-sm text-gray-500">No children registered</li>
            )}
          </ul>
        </div>
      </div>

      <div className="mt-8">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <a
            href="/parent/children"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">View Children</div>
            <div className="text-sm text-gray-500 mt-1">View your children's information</div>
          </a>
          <a
            href="/parent/payments"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">Payments</div>
            <div className="text-sm text-gray-500 mt-1">View and make payments</div>
          </a>
          <a
            href="/parent/attendances"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">Attendance</div>
            <div className="text-sm text-gray-500 mt-1">View attendance records</div>
          </a>
        </div>
      </div>
    </div>
  );
};

export default ParentDashboard;

