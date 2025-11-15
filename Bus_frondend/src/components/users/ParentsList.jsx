import { useEffect, useState } from 'react';
import { parentsService } from '../../services/parents';
import { useAuth } from '../../contexts/AuthContext';
import { hasPermission } from '../../utils/helpers';
import { PERMISSIONS } from '../../utils/constants';
import ConfirmModal from '../shared/ConfirmModal';
import UserRegisterModal from '../users/UserRegisterModal';

export default function ParentsList() {
  const { user } = useAuth();
  const [parents, setParents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const [showCreateModal, setShowCreateModal] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);
  const [deletingParentId, setDeletingParentId] = useState(null);

  const fetchParents = async () => {
    try {
      setLoading(true);
      const data = await parentsService.getParents({ per_page: 100 });
      setParents(Array.isArray(data) ? data : data?.data || []);
      setError('');
    } catch (err) {
      console.error('Load parents error', err);
      setError('Failed to load parents');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchParents();
  }, []);

  const canCreate = hasPermission(user, PERMISSIONS.CREATE_USERS);
  const canDelete = hasPermission(user, PERMISSIONS.DELETE_USERS);

  const handleCreated = () => {
    setShowCreateModal(false);
    fetchParents();
  };

  const handleDeleteClick = (parentId) => {
    setDeletingParentId(parentId);
    setShowConfirm(true);
  };

  const handleConfirmDelete = async () => {
    if (!deletingParentId) return;
    try {
      await parentsService.deleteParent(deletingParentId);
      setShowConfirm(false);
      setDeletingParentId(null);
      fetchParents();
    } catch (err) {
      console.error('Delete error', err);
      alert('Failed to delete parent');
    }
  };

  if (loading) return <div className="p-4">Loading parents...</div>;
  if (error) return <div className="p-4 text-red-600">Error: {error}</div>;

  return (
    <div className="px-4 py-6 sm:px-0">
      <div className="mb-6 flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold">Parents</h1>
          <p className="text-sm text-gray-600">Manage parent accounts</p>
        </div>
        {canCreate && (
          <button
            onClick={() => setShowCreateModal(true)}
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            + New Parent
          </button>
        )}
      </div>

      <div className="bg-white shadow overflow-hidden sm:rounded-md">
        {parents.length === 0 ? (
          <div className="px-4 py-4 text-center text-gray-500">No parents found</div>
        ) : (
          <ul className="divide-y divide-gray-200">
            {parents.map((parent) => (
              <li key={parent.id} className="px-4 py-4 sm:px-6 hover:bg-gray-50">
                <div className="flex justify-between items-center">
                  <div className="flex-1">
                    <p className="text-sm font-medium text-gray-900">{parent.name || parent.user?.name}</p>
                    <p className="text-sm text-gray-600">{parent.email || parent.user?.email}</p>
                    {parent.phone && (
                      <p className="text-xs text-gray-500 mt-1">Phone: {parent.phone}</p>
                    )}
                  </div>
                  <div className="flex gap-2">
                    {canDelete && (
                      <button
                        onClick={() => handleDeleteClick(parent.id)}
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
          roleId={3}
          roleName="Parent"
          onCreated={handleCreated}
          onClose={() => setShowCreateModal(false)}
        />
      )}

      {showConfirm && (
        <ConfirmModal
          title="Delete Parent"
          message="Are you sure you want to delete this parent?"
          confirmText="Delete"
          onConfirm={handleConfirmDelete}
          onCancel={() => {
            setShowConfirm(false);
            setDeletingParentId(null);
          }}
        />
      )}
    </div>
  );
}
