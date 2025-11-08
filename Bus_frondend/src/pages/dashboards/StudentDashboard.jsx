import { useState, useEffect } from 'react';
import { apiService } from '../../services/api';
import { useAuth } from '../../contexts/AuthContext';
import { formatDate, formatCurrency } from '../../utils/helpers';

const StudentDashboard = () => {
  const { user } = useAuth();
  const [student, setStudent] = useState(null);
  const [attendances, setAttendances] = useState([]);
  const [payments, setPayments] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        // Try to get student ID from user object (could be user.student.id or user.id)
        const studentId = user?.student?.id || user?.id;
        
        if (studentId) {
          // For students, they can only view their own data
          // The API should filter based on the authenticated user's role
          const [studentsRes, attendancesRes, paymentsRes] = await Promise.all([
            apiService.getStudents({ per_page: 1 }),
            apiService.getAttendances({ per_page: 10 }),
            apiService.getPayments({ per_page: 10 }),
          ]);

          // Get the first student (should be the logged-in student)
          const students = Array.isArray(studentsRes.data) 
            ? studentsRes.data 
            : studentsRes.data?.data || [];
          
          if (students.length > 0) {
            setStudent(students[0]);
          }

          setAttendances(
            Array.isArray(attendancesRes.data) ? attendancesRes.data : attendancesRes.data?.data || []
          );
          setPayments(Array.isArray(paymentsRes.data) ? paymentsRes.data : paymentsRes.data?.data || []);
        }
      } catch (error) {
        console.error('Error fetching data:', error);
      } finally {
        setLoading(false);
      }
    };

    if (user) {
      fetchData();
    }
  }, [user]);

  if (loading) {
    return <div className="text-center py-8">Loading dashboard...</div>;
  }

  if (!student) {
    return <div className="text-center py-8">No student information available</div>;
  }

  const presentCount = attendances.filter((a) => a.status === 'present').length;
  const absentCount = attendances.filter((a) => a.status === 'absent').length;

  return (
    <div className="px-4 py-6 sm:px-0">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Student Dashboard</h1>
        <p className="mt-2 text-sm text-gray-600">Your academic and transportation information</p>
      </div>

      <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-green-600">{presentCount}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Present Days</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-red-600">{absentCount}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Absent Days</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-2xl font-bold text-blue-600">{payments.length}</div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Payments</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white overflow-hidden shadow rounded-lg">
          <div className="p-5">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="text-sm font-bold text-purple-600">
                  {student.bus_service_active ? 'Active' : 'Inactive'}
                </div>
              </div>
              <div className="ml-5 w-0 flex-1">
                <dl>
                  <dt className="text-sm font-medium text-gray-500 truncate">Bus Service</dt>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
        <div>
          <h2 className="text-xl font-semibold text-gray-900 mb-4">My Information</h2>
          <div className="bg-white shadow overflow-hidden sm:rounded-md">
            <div className="px-4 py-5 sm:p-6">
              <dl className="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                  <dt className="text-sm font-medium text-gray-500">Admission Number</dt>
                  <dd className="mt-1 text-sm text-gray-900">{student.admission_no || 'N/A'}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">Class</dt>
                  <dd className="mt-1 text-sm text-gray-900">{student.class?.name || 'N/A'}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">Academic Year</dt>
                  <dd className="mt-1 text-sm text-gray-900">{student.academic_year || 'N/A'}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">Bus Number</dt>
                  <dd className="mt-1 text-sm text-gray-900">{student.bus?.bus_number || 'N/A'}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">Route</dt>
                  <dd className="mt-1 text-sm text-gray-900">{student.bus?.route?.name || 'N/A'}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">Pickup Stop</dt>
                  <dd className="mt-1 text-sm text-gray-900">{student.pickup_stop?.name || 'N/A'}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>

        <div>
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Recent Payments</h2>
          <div className="bg-white shadow overflow-hidden sm:rounded-md">
            <ul className="divide-y divide-gray-200">
              {payments.length > 0 ? (
                payments.slice(0, 5).map((payment) => (
                  <li key={payment.id} className="px-4 py-4 sm:px-6">
                    <div className="flex items-center justify-between">
                      <div>
                        <div className="text-sm font-medium text-gray-900">
                          {formatCurrency(payment.amount_paid)}
                        </div>
                        <div className="text-sm text-gray-500">
                          {formatDate(payment.payment_date)} | {payment.status}
                        </div>
                      </div>
                    </div>
                  </li>
                ))
              ) : (
                <li className="px-4 py-4 sm:px-6 text-sm text-gray-500">No payments found</li>
              )}
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
};

export default StudentDashboard;

