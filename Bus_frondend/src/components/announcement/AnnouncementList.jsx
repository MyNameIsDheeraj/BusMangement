import { useEffect, useState } from 'react';
import { announcementsService } from '../../services/announcements';
import { useAuth } from '../../contexts/AuthContext';
import { hasPermission } from '../../utils/helpers';
import { PERMISSIONS } from '../../utils/constants';
import ConfirmModal from '../shared/ConfirmModal';
import AnnouncementForm from './AnnouncementForm';

export default function AnnouncementList() {
  const { user } = useAuth();
  const [announcements, setAnnouncements] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const [showForm, setShowForm] = useState(false);
  const [editingAnnouncement, setEditingAnnouncement] = useState(null);

  const [showConfirm, setShowConfirm] = useState(false);
  const [deletingAnnouncementId, setDeletingAnnouncementId] = useState(null);

  const fetchAnnouncements = async () => {
    try {
      setLoading(true);
      const data = await announcementsService.getAnnouncements({ per_page: 100 });
      setAnnouncements(Array.isArray(data) ? data : data?.data || []);
      setError('');
    } catch (err) {
      console.error('Load announcements error', err);
      setError('Failed to load announcements');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchAnnouncements();
  }, []);

  const canCreate = hasPermission(user, PERMISSIONS.CREATE_ANNOUNCEMENT);
  const canEdit = hasPermission(user, PERMISSIONS.EDIT_ANNOUNCEMENT);
  const canDelete = hasPermission(user, PERMISSIONS.DELETE_ANNOUNCEMENT);

  const openCreate = () => {
    setEditingAnnouncement(null);
    setShowForm(true);
  };

  const openEdit = (announcement) => {
    setEditingAnnouncement(announcement);
    setShowForm(true);
  };

  const handleSaved = () => {
    setShowForm(false);
    setEditingAnnouncement(null);
    fetchAnnouncements();
  };

  const handleDeleteClick = (announcementId) => {
    setDeletingAnnouncementId(announcementId);
    setShowConfirm(true);
  };

  const handleConfirmDelete = async () => {
    if (!deletingAnnouncementId) return;
    try {
      await announcementsService.deleteAnnouncement(deletingAnnouncementId);
      setShowConfirm(false);
      setDeletingAnnouncementId(null);
      fetchAnnouncements();
    } catch (err) {
      console.error('Delete error', err);
      alert('Failed to delete announcement');
    }
  };

  if (loading) return <div className="p-4">Loading announcements...</div>;
  if (error) return <div className="p-4 text-red-600">Error: {error}</div>;

  return (
    <div className="px-4 py-6 sm:px-0">
      <div className="mb-6 flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold">Announcements</h1>
          <p className="text-sm text-gray-600">Manage school announcements</p>
        </div>
        {canCreate && (
          <button
            onClick={openCreate}
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            + New Announcement
          </button>
        )}
      </div>

      <div className="bg-white shadow overflow-hidden sm:rounded-md">
        {announcements.length === 0 ? (
          <div className="px-4 py-4 text-center text-gray-500">No announcements found</div>
        ) : (
          <ul className="divide-y divide-gray-200">
            {announcements.map((announcement) => (
              <li key={announcement.id} className="px-4 py-4 sm:px-6 hover:bg-gray-50">
                <div className="flex justify-between items-start">
                  <div className="flex-1">
                    <div className="flex items-center gap-2">
                      <p className="text-sm font-medium text-gray-900">{announcement.title}</p>
                      <span className={`px-2 py-1 text-xs rounded font-medium ${
                        announcement.is_active 
                          ? 'bg-green-100 text-green-800' 
                          : 'bg-gray-100 text-gray-800'
                      }`}>
                        {announcement.is_active ? 'Active' : 'Inactive'}
                      </span>
                    </div>
                    <p className="text-sm text-gray-600 mt-1">{announcement.description || announcement.body}</p>
                    {announcement.audience && (
                      <p className="text-xs text-gray-500 mt-2">Audience: {announcement.audience}</p>
                    )}
                  </div>
                  <div className="flex gap-2 ml-4">
                    {canEdit && (
                      <button
                        onClick={() => openEdit(announcement)}
                        className="text-blue-600 hover:text-blue-900 text-sm font-medium whitespace-nowrap"
                      >
                        Edit
                      </button>
                    )}
                    {canDelete && (
                      <button
                        onClick={() => handleDeleteClick(announcement.id)}
                        className="text-red-600 hover:text-red-900 text-sm font-medium whitespace-nowrap"
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
        <AnnouncementForm
          announcement={editingAnnouncement}
          onSaved={handleSaved}
          onClose={() => setShowForm(false)}
        />
      )}

      {showConfirm && (
        <ConfirmModal
          title="Delete Announcement"
          message="Are you sure you want to delete this announcement?"
          confirmText="Delete"
          onConfirm={handleConfirmDelete}
          onCancel={() => {
            setShowConfirm(false);
            setDeletingAnnouncementId(null);
          }}
        />
      )}
    </div>
  );
}
