import { useState, useEffect } from 'react';
import { busesService } from '../../services/buses';
import { apiService } from '../../services/api';

export default function BusForm({ bus = null, onSaved, onCancel }) {
  const [busNumber, setBusNumber] = useState(bus?.bus_number || bus?.registration_no || '');
  const [model, setModel] = useState(bus?.model || '');
  const [seatingCapacity, setSeatingCapacity] = useState(bus?.seating_capacity || '');
  const [driverId, setDriverId] = useState(bus?.driver_id || '');
  const [drivers, setDrivers] = useState([]);
  const [saving, setSaving] = useState(false);
  const [errors, setErrors] = useState({});

  useEffect(() => {
    let mounted = true;
    // try to load drivers list if endpoint exists
    apiService.getTeachers?.()
      .then((res) => {
        if (!mounted) return;
        const list = res.data?.data || res.data || [];
        setDrivers(list);
      })
      .catch(() => {
        if (!mounted) return;
        setDrivers([]);
      });
    return () => { mounted = false; };
  }, []);

  const validate = () => {
    const e = {};
    if (!busNumber) e.busNumber = 'Bus number is required';
    if (seatingCapacity && isNaN(Number(seatingCapacity))) e.seatingCapacity = 'Seating capacity must be a number';
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async (ev) => {
    ev.preventDefault();
    if (!validate()) return;
    setSaving(true);
    const payload = {
      bus_number: busNumber,
      registration_no: busNumber,
      model,
      seating_capacity: seatingCapacity ? Number(seatingCapacity) : null,
      driver_id: driverId || null,
    };

    try {
      if (bus?.id) {
        await busesService.updateBus(bus.id, payload);
      } else {
        await busesService.createBus(payload);
      }
      onSaved && onSaved();
    } catch (err) {
      console.error('Save bus error', err);
      setErrors({ form: err.response?.data?.message || err.message });
    } finally {
      setSaving(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-3">
      {errors.form && <div className="text-red-600">{errors.form}</div>}

      <div>
        <label className="block text-sm">Bus Number / Registration</label>
        <input value={busNumber} onChange={(e) => setBusNumber(e.target.value)} className="mt-1 block w-full border rounded p-2" />
        {errors.busNumber && <div className="text-red-600 text-sm">{errors.busNumber}</div>}
      </div>

      <div>
        <label className="block text-sm">Model</label>
        <input value={model} onChange={(e) => setModel(e.target.value)} className="mt-1 block w-full border rounded p-2" />
      </div>

      <div>
        <label className="block text-sm">Seating Capacity</label>
        <input value={seatingCapacity} onChange={(e) => setSeatingCapacity(e.target.value)} className="mt-1 block w-full border rounded p-2" />
        {errors.seatingCapacity && <div className="text-red-600 text-sm">{errors.seatingCapacity}</div>}
      </div>

      <div>
        <label className="block text-sm">Driver</label>
        <select value={driverId} onChange={(e) => setDriverId(e.target.value)} className="mt-1 block w-full border rounded p-2">
          <option value="">Unassigned</option>
          {drivers.map((d) => (
            <option key={d.id} value={d.id}>{d.name || d.email || `#${d.id}`}</option>
          ))}
        </select>
      </div>

      <div className="flex justify-end gap-2">
        <button type="button" onClick={onCancel} className="px-4 py-2 border rounded">Cancel</button>
        <button disabled={saving} className="px-4 py-2 bg-blue-600 text-white rounded">{saving ? 'Saving...' : 'Save'}</button>
      </div>
    </form>
  );
}
