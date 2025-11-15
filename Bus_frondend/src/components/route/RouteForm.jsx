import { useState, useEffect } from 'react';
import { routesService } from '../../services/routes';
import { apiService } from '../../services/api';

export default function RouteForm({ route = null, onSaved, onCancel }) {
  const [name, setName] = useState(route?.name || '');
  const [startTime, setStartTime] = useState(route?.start_time || '');
  const [endTime, setEndTime] = useState(route?.end_time || '');
  const [busId, setBusId] = useState(route?.bus_id || '');
  const [buses, setBuses] = useState([]);
  const [stops, setStops] = useState(route?.stops || []);
  const [saving, setSaving] = useState(false);
  const [errors, setErrors] = useState({});

  useEffect(() => {
    let mounted = true;
    // load available buses and stops
    apiService.getBuses?.({ per_page: 50 })
      .then((res) => mounted && setBuses(res.data?.data || res.data || []))
      .catch(() => mounted && setBuses([]));

    apiService.getStops?.()
      .then((res) => mounted && setStops(res.data?.data || res.data || []))
      .catch(() => mounted && setStops([]));

    return () => { mounted = false; };
  }, []);

  const validate = () => {
    const e = {};
    if (!name) e.name = 'Route name is required';
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async (ev) => {
    ev.preventDefault();
    if (!validate()) return;
    setSaving(true);
    const payload = {
      name,
      start_time: startTime || null,
      end_time: endTime || null,
      bus_id: busId || null,
      stops: stops,
    };

    try {
      if (route?.id) {
        await routesService.updateRoute(route.id, payload);
      } else {
        await routesService.createRoute(payload);
      }
      onSaved && onSaved();
    } catch (err) {
      console.error('Save route error', err);
      setErrors({ form: err.response?.data?.message || err.message });
    } finally {
      setSaving(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-3">
      {errors.form && <div className="text-red-600">{errors.form}</div>}

      <div>
        <label className="block text-sm">Route Name</label>
        <input value={name} onChange={(e) => setName(e.target.value)} className="mt-1 block w-full border rounded p-2" />
        {errors.name && <div className="text-red-600 text-sm">{errors.name}</div>}
      </div>

      <div className="flex gap-2">
        <div className="flex-1">
          <label className="block text-sm">Start Time</label>
          <input type="time" value={startTime} onChange={(e) => setStartTime(e.target.value)} className="mt-1 block w-full border rounded p-2" />
        </div>
        <div className="flex-1">
          <label className="block text-sm">End Time</label>
          <input type="time" value={endTime} onChange={(e) => setEndTime(e.target.value)} className="mt-1 block w-full border rounded p-2" />
        </div>
      </div>

      <div>
        <label className="block text-sm">Assign Bus</label>
        <select value={busId} onChange={(e) => setBusId(e.target.value)} className="mt-1 block w-full border rounded p-2">
          <option value="">Unassigned</option>
          {buses.map((b) => (<option key={b.id} value={b.id}>{b.bus_number || b.registration_no || `#${b.id}`}</option>))}
        </select>
      </div>

      <div className="flex justify-end gap-2">
        <button type="button" onClick={onCancel} className="px-4 py-2 border rounded">Cancel</button>
        <button disabled={saving} className="px-4 py-2 bg-blue-600 text-white rounded">{saving ? 'Saving...' : 'Save'}</button>
      </div>
    </form>
  );
}
