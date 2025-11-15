import { useEffect, useState, useCallback } from 'react';
import { stopsService } from '../../services/stops';
import { useAuth } from '../../contexts/AuthContext';
import { hasPermission } from '../../utils/helpers';
import { PERMISSIONS } from '../../utils/constants';
import ConfirmModal from '../shared/ConfirmModal';
import StopForm from './StopForm';

export default function StopList({ routeId = null }) {
  const { user } = useAuth();
  const [stops, setStops] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const [showForm, setShowForm] = useState(false);
  const [editingStop, setEditingStop] = useState(null);

  const [showConfirm, setShowConfirm] = useState(false);
  const [deletingStopId, setDeletingStopId] = useState(null);

  const fetchStops = useCallback(async () => {
    try {
      setLoading(true);
      const params = routeId ? { route_id: routeId } : {};
      const data = await stopsService.getStops(params);
      setStops(Array.isArray(data) ? data : data?.data || data?.stops || []);
      setError('');
    } catch (err) {
      console.error('Load stops error', err);
      setError('Failed to load stops');
    } finally {
      setLoading(false);
    }
  }, [routeId]);

  useEffect(() => {
    fetchStops();
  }, [fetchStops]);

  const canCreate = !routeId && hasPermission(user, PERMISSIONS.CREATE_ROUTE);
  const canEdit = !routeId && hasPermission(user, PERMISSIONS.EDIT_ROUTE);
  const canDelete = !routeId && hasPermission(user, PERMISSIONS.DELETE_ROUTE);

  const openCreate = () => {
    setEditingStop(null);
    setShowForm(true);
  };

  const openEdit = (stop) => {
    setEditingStop(stop);
    setShowForm(true);
  };

  const handleSaved = () => {
    setShowForm(false);
    setEditingStop(null);
    fetchStops();
  };

  const handleDeleteClick = (stopId) => {
    setDeletingStopId(stopId);
    setShowConfirm(true);
  };

  const handleConfirmDelete = async () => {
    if (!deletingStopId) return;
    try {
      await stopsService.deleteStop(deletingStopId);
      setShowConfirm(false);
      setDeletingStopId(null);
      fetchStops();
    } catch (err) {
      console.error('Delete error', err);
      alert('Failed to delete stop');
    }
  };

  if (loading) return <div className="p-4">Loading stops...</div>;
  if (error) return <div className="p-4 text-red-600">Error: {error}</div>;

  return (
    <div className="px-4 py-6 sm:px-0">
      <div className="mb-6 flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold">Stops</h1>
          <p className="text-sm text-gray-600">Manage bus route stops</p>
        </div>
        {canCreate && (
          <button
            onClick={openCreate}
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            + New Stop
          </button>
        )}
      </div>

      <div className="bg-white shadow overflow-hidden sm:rounded-md">
        {stops.length === 0 ? (
          <div className="px-4 py-4 text-center text-gray-500">No stops found</div>
        ) : (
          <ul className="divide-y divide-gray-200">
            {stops.map((stop) => (
              <li key={stop.id} className="px-4 py-4 sm:px-6 hover:bg-gray-50">
                <div className="flex justify-between items-center">
                  <div className="flex-1">
                    <p className="text-sm font-medium text-gray-900">{stop.name}</p>
                    <p className="text-sm text-gray-600">{stop.location}</p>
                    <p className="text-xs text-gray-500 mt-1">
                      Order: {stop.order} | Time: {stop.time}
                    </p>
                  </div>
                  <div className="flex gap-2">
                    {canEdit && (
                      <button
                        onClick={() => openEdit(stop)}
                        className="text-blue-600 hover:text-blue-900 text-sm font-medium"
                      >
                        Edit
                      </button>
                    )}
                    {canDelete && (
                      <button
                        onClick={() => handleDeleteClick(stop.id)}
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
        <StopForm
          stop={editingStop}
          routeId={routeId}
          onSaved={handleSaved}
          onClose={() => setShowForm(false)}
        />
      )}

      {showConfirm && (
        <ConfirmModal
          title="Delete Stop"
          message="Are you sure you want to delete this stop?"
          confirmText="Delete"
          onConfirm={handleConfirmDelete}
          onCancel={() => {
            setShowConfirm(false);
            setDeletingStopId(null);
          }}
        />
      )}
    </div>
  );
}
