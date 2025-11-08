import { useState, useEffect } from 'react';
import { apiService } from '../../services/api';
import { formatDate } from '../../utils/helpers';

const TeacherDashboard = () => {
  const [stats, setStats] = useState({
    students: 0,
    classes: 0,
    attendances: 0,
    alerts: 0,
  });
  const [loading, setLoading] = useState(true);
  const [myClasses, setMyClasses] = useState([]);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [studentsRes, classesRes, attendancesRes, alertsRes] = await Promise.all([
          apiService.getMyStudents(),
          apiService.getMyClasses(),
          apiService.getAttendances({ per_page: 1 }),
          apiService.getAlerts({ per_page: 1 }),
        ]);

        setStats({
          students: Array.isArray(studentsRes.data) ? studentsRes.data.length : studentsRes.data?.total || 0,
          classes: Array.isArray(classesRes.data) ? classesRes.data.length : classesRes.data?.total || 0,
          attendances: attendancesRes.data?.total || 0,
          alerts: alertsRes.data?.total || 0,
        });

        setMyClasses(Array.isArray(classesRes.data) ? classesRes.data : classesRes.data?.data || []);
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
        <h1 className="text-3xl font-bold text-gray-900">Teacher Dashboard</h1>
        <p className="mt-2 text-sm text-gray-600">Manage your classes and students</p>
      </div>

      <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-blue-600">{stats.students}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">My Students</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-green-600">{stats.classes}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">My Classes</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-purple-600">{stats.attendances}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Attendances</dt>
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
                  <dt className="text-sm font-medium text-gray-500 truncate">Alerts</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="mt-8">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">My Classes</h2>
        <div className="bg-white shadow overflow-hidden sm:rounded-md">
          <ul className="divide-y divide-gray-200">
            {myClasses.length > 0 ? (
              myClasses.map((classItem) => (
                <li key={classItem.id}>
                  <div className="px-4 py-4 sm:px-6">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center">
                        <div>
                          <div className="text-sm font-medium text-gray-900">{classItem.name}</div>
                          {classItem.academic_year && (
                            <div className="text-sm text-gray-500">{classItem.academic_year}</div>
                          )}
                        </div>
                      </div>
                      <div className="ml-2 flex-shrink-0">
                        <a
                          href={`/teacher/students?class=${classItem.id}`}
                          className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                        >
                          View Students
                        </a>
                      </div>
                    </div>
                  </div>
                </li>
              ))
            ) : (
              <li className="px-4 py-4 sm:px-6 text-sm text-gray-500">No classes assigned</li>
            )}
          </ul>
        </div>
      </div>

      <div className="mt-8">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <a
            href="/teacher/students"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">My Students</div>
            <div className="text-sm text-gray-500 mt-1">View students in your classes</div>
          </a>
          <a
            href="/teacher/attendances"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">Mark Attendance</div>
            <div className="text-sm text-gray-500 mt-1">Mark student attendance</div>
          </a>
          <a
            href="/teacher/alerts"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">Create Alert</div>
            <div className="text-sm text-gray-500 mt-1">Create alerts for students</div>
          </a>
          <a
            href="/teacher/payments"
            className="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
          >
            <div className="font-medium text-gray-900">Payments</div>
            <div className="text-sm text-gray-500 mt-1">View and create payments</div>
          </a>
        </div>
      </div>
    </div>
  );
};

export default TeacherDashboard;

