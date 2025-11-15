import { useEffect, useState } from 'react';
import { teachersService } from '../../services/teachers';
import { useAuth } from '../../contexts/AuthContext';
import { hasPermission } from '../../utils/helpers';
import { PERMISSIONS } from '../../utils/constants';
import ConfirmModal from '../shared/ConfirmModal';
import UserRegisterModal from '../users/UserRegisterModal';

export default function TeachersList() {
  const { user } = useAuth();
  const [teachers, setTeachers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const [showCreateModal, setShowCreateModal] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);
  const [deletingTeacherId, setDeletingTeacherId] = useState(null);

  const fetchTeachers = async () => {
    try {
      setLoading(true);
      const data = await teachersService.getTeachers({ per_page: 100 });
      setTeachers(Array.isArray(data) ? data : data?.data || []);
      setError('');
    } catch (err) {
      console.error('Load teachers error', err);
      setError('Failed to load teachers');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchTeachers();
  }, []);

  const canCreate = hasPermission(user, PERMISSIONS.CREATE_USERS);
  const canDelete = hasPermission(user, PERMISSIONS.DELETE_USERS);

  const handleCreated = () => {
    setShowCreateModal(false);
    fetchTeachers();
  };

  const handleDeleteClick = (teacherId) => {
    setDeletingTeacherId(teacherId);
    setShowConfirm(true);
  };

  const handleConfirmDelete = async () => {
    if (!deletingTeacherId) return;
    try {
      await teachersService.deleteTeacher(deletingTeacherId);
      setShowConfirm(false);
      setDeletingTeacherId(null);
      fetchTeachers();
    } catch (err) {
      console.error('Delete error', err);
      alert('Failed to delete teacher');
    }
  };

  if (loading) return <div className="p-4">Loading teachers...</div>;
  if (error) return <div className="p-4 text-red-600">Error: {error}</div>;

  return (
    <div className="px-4 py-6 sm:px-0">
      <div className="mb-6 flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold">Teachers</h1>
          <p className="text-sm text-gray-600">Manage teacher accounts</p>
        </div>
        {canCreate && (
          <button
            onClick={() => setShowCreateModal(true)}
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            + New Teacher
          </button>
        )}
      </div>

      <div className="bg-white shadow overflow-hidden sm:rounded-md">
        {teachers.length === 0 ? (
          <div className="px-4 py-4 text-center text-gray-500">No teachers found</div>
        ) : (
          <ul className="divide-y divide-gray-200">
            {teachers.map((teacher) => (
              <li key={teacher.id} className="px-4 py-4 sm:px-6 hover:bg-gray-50">
                <div className="flex justify-between items-center">
                  <div className="flex-1">
                    <p className="text-sm font-medium text-gray-900">{teacher.name || teacher.user?.name}</p>
                    <p className="text-sm text-gray-600">{teacher.email || teacher.user?.email}</p>
                    {teacher.contact_number && (
                      <p className="text-xs text-gray-500 mt-1">Contact: {teacher.contact_number}</p>
                    )}
                  </div>
                  <div className="flex gap-2">
                    {canDelete && (
                      <button
                        onClick={() => handleDeleteClick(teacher.id)}
                        className="text-red-600 hover:text-red-900 text-sm font-medium"
                      >
                        Delete
                      </button>
                    )}
                  </div>
                </div>
              </li>
            ))}
          </ul>
        )}
      </div>

      {showCreateModal && (
        <UserRegisterModal
          roleId={2}
          roleName="Teacher"
          onCreated={handleCreated}
          onClose={() => setShowCreateModal(false)}
        />
      )}

      {showConfirm && (
        <ConfirmModal
          title="Delete Teacher"
          message="Are you sure you want to delete this teacher?"
          confirmText="Delete"
          onConfirm={handleConfirmDelete}
          onCancel={() => {
            setShowConfirm(false);
            setDeletingTeacherId(null);
          }}
        />
      )}
    </div>
  );
}
