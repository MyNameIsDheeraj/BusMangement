import { useState, useEffect } from 'react';
import { staffService } from '../../services/staff';

const POSITIONS = ['Driver', 'Cleaner', 'Conductor', 'Mechanic', 'Manager'];

export default function StaffForm({ staff, users = [], onSaved, onClose }) {
  const [formData, setFormData] = useState({
    user_id: '',
    position: '',
    hire_date: '',
    salary: '',
    contact_number: '',
  });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (staff) {
      setFormData({
        user_id: staff.user_id || '',
        position: staff.position || '',
        hire_date: staff.hire_date || '',
        salary: staff.salary || '',
        contact_number: staff.contact_number || '',
      });
    }
  }, [staff]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value,
    }));
  };

  const validate = () => {
    if (!formData.user_id) {
      setError('User is required');
      return false;
    }
    if (!formData.position) {
      setError('Position is required');
      return false;
    }
    if (!formData.hire_date) {
      setError('Hire date is required');
      return false;
    }
    if (!formData.salary) {
      setError('Salary is required');
      return false;
    }
    if (!formData.contact_number) {
      setError('Contact number is required');
      return false;
    }
    return true;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    if (!validate()) return;

    try {
      setLoading(true);
      const submitData = {
        ...formData,
        user_id: parseInt(formData.user_id),
        salary: parseFloat(formData.salary),
      };
      
      if (staff) {
        await staffService.updateStaffProfile(staff.id, submitData);
      } else {
        await staffService.createStaffProfile(submitData);
      }
      onSaved();
    } catch (err) {
      console.error('Save error', err);
      setError(err.response?.data?.message || 'Failed to save staff profile');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-lg p-6 w-96 max-h-screen overflow-y-auto">
        <h2 className="text-xl font-bold mb-4">
          {staff ? 'Edit Staff Profile' : 'New Staff Profile'}
        </h2>

        {error && (
          <div className="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700">
              User *
            </label>
            <select
              name="user_id"
              value={formData.user_id}
              onChange={handleChange}
              disabled={!!staff}
              className="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 disabled:bg-gray-100"
            >
              <option value="">Select user</option>
              {users.map(u => (
                <option key={u.id} value={u.id}>
                  {u.name} ({u.email})
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700">
              Position *
            </label>
            <select
              name="position"
              value={formData.position}
              onChange={handleChange}
              className="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
            >
              <option value="">Select position</option>
              {POSITIONS.map(pos => (
                <option key={pos} value={pos}>{pos}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700">
              Hire Date *
            </label>
            <input
              type="date"
              name="hire_date"
              value={formData.hire_date}
              onChange={handleChange}
              className="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700">
              Salary (Rs.) *
            </label>
            <input
              type="number"
              step="0.01"
              name="salary"
              value={formData.salary}
              onChange={handleChange}
              className="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
              placeholder="0.00"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700">
              Contact Number *
            </label>
            <input
              type="tel"
              name="contact_number"
              value={formData.contact_number}
              onChange={handleChange}
              className="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
              placeholder="e.g., +92 300 1234567"
            />
          </div>

          <div className="flex gap-2 pt-4">
            <button
              type="submit"
              disabled={loading}
              className="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:bg-gray-400"
            >
              {loading ? 'Saving...' : 'Save'}
            </button>
            <button
              type="button"
              onClick={onClose}
              className="flex-1 bg-gray-300 text-gray-700 py-2 rounded hover:bg-gray-400"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
