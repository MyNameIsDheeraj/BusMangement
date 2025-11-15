import { useState } from 'react';
import { usersService } from '../../services/users';

export default function UserForm({ user = null, onSaved }) {
  const [name, setName] = useState(user?.name || '');
  const [email, setEmail] = useState(user?.email || '');
  const [saving, setSaving] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);
    try {
      if (user?.id) {
        await usersService.updateUser(user.id, { name, email });
      } else {
        await usersService.createUser({ name, email, password: 'password123' });
      }
      onSaved && onSaved();
    } catch (err) {
      console.error('Save user error', err);
    } finally {
      setSaving(false);
    }
  };

  return (
    <form className="p-4 bg-white rounded shadow" onSubmit={handleSubmit}>
      <div className="mb-3">
        <label className="block text-sm font-medium">Name</label>
        <input value={name} onChange={(e) => setName(e.target.value)} className="mt-1 block w-full border rounded p-2" />
      </div>
      <div className="mb-3">
        <label className="block text-sm font-medium">Email</label>
        <input value={email} onChange={(e) => setEmail(e.target.value)} className="mt-1 block w-full border rounded p-2" />
      </div>
      <button disabled={saving} className="px-4 py-2 bg-blue-600 text-white rounded">{saving ? 'Saving...' : 'Save'}</button>
    </form>
  );
}
