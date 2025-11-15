import { useEffect, useState } from 'react';
import { usersService } from '../../services/users';
import UserForm from './UserForm';
import ConfirmModal from '../shared/ConfirmModal';
import { hasPermission } from '../../utils/helpers';
import { useAuth } from '../../contexts/AuthContext';
import { PERMISSIONS } from '../../utils/constants';

export default function UserList() {
  const { user } = useAuth();
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const [showForm, setShowForm] = useState(false);
  const [editingUser, setEditingUser] = useState(null);

  const [showConfirm, setShowConfirm] = useState(false);
  const [deletingUserId, setDeletingUserId] = useState(null);

  const fetchUsers = async () => {
    try {
      setLoading(true);
      const res = await usersService.getUsers();
      const data = res.data;
      setUsers(Array.isArray(data) ? data : data?.data || []);
      setError('');
    } catch (err) {
      console.error('Load users error', err);
      setError('Failed to load users');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchUsers();
  }, []);

  const openCreate = () => {
    setEditingUser(null);
    setShowForm(true);
  };

  const openEdit = (user) => {
    setEditingUser(user);
    setShowForm(true);
  };

  const handleSaved = () => {
    setShowForm(false);
    setEditingUser(null);
    fetchUsers();
  };

  const confirmDelete = (id) => {
    setDeletingUserId(id);
    setShowConfirm(true);
  };

  const doDelete = async () => {
    if (!deletingUserId) return;
    try {
      await usersService.deleteUser(deletingUserId);
      setShowConfirm(false);
      setDeletingUserId(null);
      fetchUsers();
    } catch (err) {
      console.error('Delete user error', err);
      setError('Failed to delete user');
    }
  };

  if (loading) return <div className="p-4">Loading users...</div>;

  return (
    <div className="p-4">
      <div className="mb-4 flex items-center justify-between">
        <h2 className="text-xl font-semibold">Users</h2>
        {hasPermission(user, PERMISSIONS.CREATE_USERS) && (
          <button onClick={openCreate} className="px-3 py-2 bg-blue-600 text-white rounded">Create User</button>
        )}
      </div>

      {error && <div className="mb-4 text-sm text-red-600">{error}</div>}

      <ul className="space-y-2">
        {users.length === 0 && <li className="p-3 bg-white rounded shadow-sm">No users found</li>}
        {users.map((u) => (
          <li key={u.id} className="p-3 bg-white rounded shadow-sm flex items-center justify-between">
            <div>
              <div className="font-medium">{u.name}</div>
              <div className="text-sm text-gray-500">{u.email}</div>
              <div className="text-xs text-gray-400">Role: {u.role?.name || u.role}</div>
            </div>
            <div className="space-x-2">
              {hasPermission(user, PERMISSIONS.EDIT_USERS) && (
                <button onClick={() => openEdit(u)} className="text-sm text-green-600">Edit</button>
              )}
              {hasPermission(user, PERMISSIONS.DELETE_USERS) && (
                <button onClick={() => confirmDelete(u.id)} className="text-sm text-red-600">Delete</button>
              )}
            </div>
          </li>
        ))}
      </ul>

      {/* Modal: create/edit */}
      {showForm && (
        <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
          <div className="w-full max-w-xl p-4">
            <div className="bg-white rounded shadow p-4">
              <div className="flex justify-between items-center mb-3">
                <h3 className="text-lg font-semibold">{editingUser ? 'Edit User' : 'Create User'}</h3>
                <button onClick={() => setShowForm(false)} className="text-gray-500">Close</button>
              </div>
              <UserForm user={editingUser} onSaved={handleSaved} />
            </div>
          </div>
        </div>
      )}

      <ConfirmModal open={showConfirm} title="Delete user" onConfirm={doDelete} onCancel={() => setShowConfirm(false)}>
        Are you sure you want to delete this user?
      </ConfirmModal>
    </div>
  );
}
