import { useEffect, useState } from 'react';
import { studentsService } from '../../services/students';
import StudentForm from './StudentForm';
import { useAuth } from '../../contexts/AuthContext';
import { hasPermission } from '../../utils/helpers';
import { PERMISSIONS } from '../../utils/constants';
import { Link } from 'react-router-dom';

export default function StudentList() {
  const { user } = useAuth();
  const [students, setStudents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [editing, setEditing] = useState(null);

  const load = async () => {
    setLoading(true);
    try {
      const res = await studentsService.getStudents();
      setStudents(res.data?.data || res.data || []);
    } catch (err) {
      console.error('Load students error', err);
      setStudents([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { load(); }, []);

  const handleCreate = () => {
    setEditing(null);
    setShowModal(true);
  };

  const handleEdit = (s) => {
    setEditing(s);
    setShowModal(true);
  };

  const handleSaved = () => {
    setShowModal(false);
    setEditing(null);
    load();
  };

  return (
    <div className="p-4">
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-2xl font-semibold">Students</h2>
        {hasPermission(user, PERMISSIONS.CREATE_STUDENT) && (
          <button onClick={handleCreate} className="px-4 py-2 bg-green-600 text-white rounded">Add student</button>
        )}
      </div>

      {loading ? (
        <div>Loading...</div>
      ) : (
        <div className="space-y-2">
          {students.length === 0 && <div>No students found.</div>}
          <div className="border rounded overflow-hidden">
            <table className="w-full table-auto">
              <thead className="bg-gray-100">
                <tr>
                  <th className="p-2 text-left">#</th>
                  <th className="p-2 text-left">Name</th>
                  <th className="p-2 text-left">Class</th>
                  <th className="p-2 text-left">Admission No</th>
                  <th className="p-2">Actions</th>
                </tr>
              </thead>
              <tbody>
                {students.map((s) => (
                  <tr key={s.id} className="border-t">
                    <td className="p-2">{s.id}</td>
                    <td className="p-2">{s.name || s.full_name || s.user?.name || `#${s.id}`}</td>
                    <td className="p-2">{s.class?.name || s.class_name || '-'}</td>
                    <td className="p-2">{s.admission_no || '-'}</td>
                    <td className="p-2">
                      <div className="flex gap-2">
                        <Link to={`/admin/students/${s.id}`} className="px-2 py-1 border rounded">View</Link>
                        {hasPermission(user, PERMISSIONS.EDIT_STUDENT) && (
                          <button onClick={() => handleEdit(s)} className="px-2 py-1 border rounded">Edit</button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Modal area - simple inline modal markup */}
      {showModal && (
        <div className="fixed inset-0 bg-black bg-opacity-40 flex items-start justify-center p-4 z-50">
          <div className="bg-white max-w-2xl w-full rounded shadow p-4">
            <div className="flex items-center justify-between mb-2">
              <h3 className="text-lg font-medium">{editing ? 'Edit student' : 'Create student'}</h3>
              <button onClick={() => { setShowModal(false); setEditing(null); }} className="text-gray-600">✕</button>
            </div>
            <StudentForm student={editing} onSaved={handleSaved} onCancel={() => { setShowModal(false); setEditing(null); }} />
          </div>
        </div>
      )}
    </div>
  );
}
