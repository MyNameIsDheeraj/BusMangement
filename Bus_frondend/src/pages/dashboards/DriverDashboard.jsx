import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { apiService } from '../../services/api';
// formatDate removed (unused)

const DriverDashboard = () => {
  const [route, setRoute] = useState(null);
  const [students, setStudents] = useState([]);
  const [attendances, setAttendances] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [busesRes, studentsRes, attendancesRes] = await Promise.all([
          apiService.getBuses({ per_page: 1 }),
          apiService.getStudents({ per_page: 100 }),
          apiService.getAttendances({ per_page: 10 }),
        ]);

        const buses = Array.isArray(busesRes.data) ? busesRes.data : busesRes.data?.data || [];
        if (buses.length > 0) {
          setRoute(buses[0].route);
        }

        setStudents(Array.isArray(studentsRes.data) ? studentsRes.data : studentsRes.data?.data || []);
        setAttendances(
          Array.isArray(attendancesRes.data) ? attendancesRes.data : attendancesRes.data?.data || []
        );
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

  const today = new Date().toISOString().split('T')[0];
  const todayAttendances = attendances.filter((a) => a.date === today);

  return (
    <div className="px-4 py-6 sm:px-0">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Driver Dashboard</h1>
        <p className="mt-2 text-sm text-gray-600">Manage your route and students</p>
      </div>

      <div className="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-blue-600">{students.length}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Students on Route</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-green-600">
                  {todayAttendances.filter((a) => a.status === 'present').length}
                </div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Present Today</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-red-600">
                  {todayAttendances.filter((a) => a.status === 'absent').length}
                </div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Absent Today</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      {route && (
        <div className="mt-8">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Route Information</h2>
          <div className="bg-white shadow overflow-hidden sm:rounded-md">
            <div className="px-4 py-5 sm:p-6">
              <dl className="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                  <dt className="text-sm font-medium text-gray-500">Route Name</dt>
                  <dd className="mt-1 text-sm text-gray-900">{route.name || 'N/A'}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">Start Time</dt>
                  <dd className="mt-1 text-sm text-gray-900">{route.start_time || 'N/A'}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">End Time</dt>
                  <dd className="mt-1 text-sm text-gray-900">{route.end_time || 'N/A'}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">Total Distance</dt>
                  <dd className="mt-1 text-sm text-gray-900">
                    {route.total_kilometer ? `${route.total_kilometer} km` : 'N/A'}
                  </dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      )}

      <div className="mt-8">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">Students on Route</h2>
        <div className="bg-white shadow overflow-hidden sm:rounded-md">
          <ul className="divide-y divide-gray-200">
            {students.length > 0 ? (
              students.map((student) => (
                <li key={student.id}>
                  <div className="px-4 py-4 sm:px-6">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center">
                        <div>
                          <div className="text-sm font-medium text-gray-900">
                            {student.user?.name || 'N/A'}
                          </div>
                          <div className="text-sm text-gray-500">
                            {student.admission_no} | {student.class?.name || 'N/A'}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              ))
            ) : (
              <li className="px-4 py-4 sm:px-6 text-sm text-gray-500">No students on route</li>
            )}
          </ul>
        </div>
      </div>

      <div className="mt-8">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <Link
            to="/driver/attendances"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">Mark Attendance</div>
            <div className="text-sm text-gray-500 mt-1">Mark student attendance</div>
          </Link>
          <Link
            to="/driver/alerts"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">Create Alert</div>
            <div className="text-sm text-gray-500 mt-1">Create alerts for students</div>
          </Link>
          <Link
            to="/driver/route"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">View Route</div>
            <div className="text-sm text-gray-500 mt-1">View route details and stops</div>
          </Link>
        </div>
      </div>
    </div>
  );
};

export default DriverDashboard;

