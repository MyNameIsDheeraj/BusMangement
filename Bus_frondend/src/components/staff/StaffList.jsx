import { useEffect, useState } from 'react';
import { staffService } from '../../services/staff';
import { usersService } from '../../services/users';
import { useAuth } from '../../contexts/AuthContext';
import { hasPermission } from '../../utils/helpers';
import { PERMISSIONS } from '../../utils/constants';
import ConfirmModal from '../shared/ConfirmModal';
import StaffForm from './StaffForm';

export default function StaffList() {
  const { user } = useAuth();
  const [staffProfiles, setStaffProfiles] = useState([]);
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const [showForm, setShowForm] = useState(false);
  const [editingStaff, setEditingStaff] = useState(null);

  const [showConfirm, setShowConfirm] = useState(false);
  const [deletingStaffId, setDeletingStaffId] = useState(null);

  const fetchData = async () => {
    try {
      setLoading(true);
      const [staffData, usersData] = await Promise.all([
        staffService.getStaffProfiles({ per_page: 100 }),
        usersService.getUsers({ per_page: 100 }),
      ]);
      setStaffProfiles(Array.isArray(staffData) ? staffData : staffData?.data || []);
      setUsers(Array.isArray(usersData) ? usersData : usersData?.data || []);
      setError('');
    } catch (err) {
      console.error('Load staff error', err);
      setError('Failed to load staff profiles');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const canCreate = hasPermission(user, PERMISSIONS.CREATE_STAFF);
  const canEdit = hasPermission(user, PERMISSIONS.EDIT_STAFF);
  const canDelete = hasPermission(user, PERMISSIONS.DELETE_STAFF);

  const getUserName = (userId) => {
    const u = users.find(u => u.id === userId);
    return u ? u.name : `User #${userId}`;
  };

  const openCreate = () => {
    setEditingStaff(null);
    setShowForm(true);
  };

  const openEdit = (staff) => {
    setEditingStaff(staff);
    setShowForm(true);
  };

  const handleSaved = () => {
    setShowForm(false);
    setEditingStaff(null);
    fetchData();
  };

  const handleDeleteClick = (staffId) => {
    setDeletingStaffId(staffId);
    setShowConfirm(true);
  };

  const handleConfirmDelete = async () => {
    if (!deletingStaffId) return;
    try {
      await staffService.deleteStaffProfile(deletingStaffId);
      setShowConfirm(false);
      setDeletingStaffId(null);
      fetchData();
    } catch (err) {
      console.error('Delete error', err);
      alert('Failed to delete staff profile');
    }
  };

  if (loading) return <div className="p-4">Loading staff profiles...</div>;
  if (error) return <div className="p-4 text-red-600">Error: {error}</div>;

  return (
    <div className="px-4 py-6 sm:px-0">
      <div className="mb-6 flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold">Staff Profiles</h1>
          <p className="text-sm text-gray-600">Manage drivers, cleaners, and other staff</p>
        </div>
        {canCreate && (
          <button
            onClick={openCreate}
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            + New Staff
          </button>
        )}
      </div>

      <div className="bg-white shadow overflow-hidden sm:rounded-md">
        {staffProfiles.length === 0 ? (
          <div className="px-4 py-4 text-center text-gray-500">No staff profiles found</div>
        ) : (
          <ul className="divide-y divide-gray-200">
            {staffProfiles.map((staff) => (
              <li key={staff.id} className="px-4 py-4 sm:px-6 hover:bg-gray-50">
                <div className="flex justify-between items-center">
                  <div className="flex-1">
                    <p className="text-sm font-medium text-gray-900">{getUserName(staff.user_id)}</p>
                    <p className="text-sm text-gray-600">Position: {staff.position}</p>
                    <p className="text-xs text-gray-500 mt-1">
                      Hire Date: {staff.hire_date} | Salary: Rs. {staff.salary}
                    </p>
                    <p className="text-xs text-gray-500">Contact: {staff.contact_number}</p>
                  </div>
                  <div className="flex gap-2">
                    {canEdit && (
                      <button
                        onClick={() => openEdit(staff)}
                        className="text-blue-600 hover:text-blue-900 text-sm font-medium"
                      >
                        Edit
                      </button>
                    )}
                    {canDelete && (
                      <button
                        onClick={() => handleDeleteClick(staff.id)}
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

      {showForm && (
        <StaffForm
          staff={editingStaff}
          users={users}
          onSaved={handleSaved}
          onClose={() => setShowForm(false)}
        />
      )}

      {showConfirm && (
        <ConfirmModal
          title="Delete Staff Profile"
          message="Are you sure you want to delete this staff profile?"
          confirmText="Delete"
          onConfirm={handleConfirmDelete}
          onCancel={() => {
            setShowConfirm(false);
            setDeletingStaffId(null);
          }}
        />
      )}
    </div>
  );
}
