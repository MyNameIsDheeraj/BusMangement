import { useState, useEffect } from 'react';
import { apiService } from '../services/api';
import { formatDate } from '../utils/helpers';
import { useAuth } from '../contexts/AuthContext';

const StudentsList = () => {
  const { user } = useAuth();
  const [students, setStudents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [searchTerm, setSearchTerm] = useState('');
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  useEffect(() => {
    fetchStudents();
  }, [page, searchTerm]);

  const fetchStudents = async () => {
    try {
      setLoading(true);
      const params = {
        page,
        per_page: 20,
      };

      if (searchTerm) {
        params.search = searchTerm;
      }

      const response = await apiService.getStudents(params);
      const data = response.data;

      setStudents(Array.isArray(data) ? data : data?.data || []);
      setTotalPages(data?.last_page || 1);
      setError('');
    } catch (err) {
      setError('Failed to fetch students');
      console.error('Error fetching students:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Are you sure you want to delete this student?')) {
      return;
    }

    try {
      await apiService.deleteStudent(id);
      fetchStudents();
    } catch (err) {
      setError('Failed to delete student');
      console.error('Error deleting student:', err);
    }
  };

  if (loading && students.length === 0) {
    return <div className="text-center py-8">Loading students...</div>;
  }

  return (
    <div className="px-4 py-6 sm:px-0">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Students</h1>
        <p className="mt-2 text-sm text-gray-600">Manage student information</p>
      </div>

      {error && (
        <div className="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
          {error}
        </div>
      )}

      <div className="mb-4 flex gap-4">
        <input
          type="text"
          placeholder="Search students..."
          value={searchTerm}
          onChange={(e) => {
            setSearchTerm(e.target.value);
            setPage(1);
          }}
          className="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        {user?.role?.id === 1 && (
          <a
            href="/admin/students/new"
            className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            Add Student
          </a>
        )}
      </div>

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
                          Admission: {student.admission_no || 'N/A'} | Class: {student.class?.name || 'N/A'}
                        </div>
                        <div className="text-sm text-gray-500">
                          Bus: {student.bus?.bus_number || 'N/A'} | Route: {student.bus?.route?.name || 'N/A'}
                        </div>
                      </div>
                    </div>
                    <div className="ml-2 flex-shrink-0 space-x-2">
                      <a
                        href={`/admin/students/${student.id}`}
                        className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                      >
                        View
                      </a>
                      {user?.role?.id === 1 && (
                        <>
                          <a
                            href={`/admin/students/${student.id}/edit`}
                            className="text-green-600 hover:text-green-800 text-sm font-medium"
                          >
                            Edit
                          </a>
                          <button
                            onClick={() => handleDelete(student.id)}
                            className="text-red-600 hover:text-red-800 text-sm font-medium"
                          >
                            Delete
                          </button>
                        </>
                      )}
                    </div>
                  </div>
                </div>
              </li>
            ))
          ) : (
            <li className="px-4 py-4 sm:px-6 text-sm text-gray-500">No students found</li>
          )}
        </ul>
      </div>

      {totalPages > 1 && (
        <div className="mt-4 flex justify-center space-x-2">
          <button
            onClick={() => setPage((p) => Math.max(1, p - 1))}
            disabled={page === 1}
            className="px-4 py-2 border border-gray-300 rounded-md disabled:opacity-50"
          >
            Previous
          </button>
          <span className="px-4 py-2">
            Page {page} of {totalPages}
          </span>
          <button
            onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
            disabled={page === totalPages}
            className="px-4 py-2 border border-gray-300 rounded-md disabled:opacity-50"
          >
            Next
          </button>
        </div>
      )}
    </div>
  );
};

export default StudentsList;

