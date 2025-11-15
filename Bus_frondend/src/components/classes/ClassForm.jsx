import { useState } from 'react';
import { classesService } from '../../services/classes';

export default function ClassForm({ classData = null, onSaved }) {
  const [name, setName] = useState(classData?.name || '');
  const [saving, setSaving] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);
    try {
      if (classData?.id) await classesService.updateClass(classData.id, { name });
      else await classesService.createClass({ name });
      onSaved && onSaved();
    } catch (err) {
      console.error('Save class error', err);
    } finally { setSaving(false); }
  };

  return (
    <form className="p-4 bg-white rounded shadow" onSubmit={handleSubmit}>
      <div className="mb-3">
        <label className="block text-sm font-medium">Class name</label>
        <input value={name} onChange={(e) => setName(e.target.value)} className="mt-1 block w-full border rounded p-2" />
      </div>
      <button disabled={saving} className="px-4 py-2 bg-green-600 text-white rounded">{saving ? 'Saving...' : 'Save'}</button>
    </form>
  );
}
