import { useState, useEffect } from 'react';
import { studentsService } from '../../services/students';
import { apiService } from '../../services/api';
import { usersService } from '../../services/users';
import { classesService } from '../../services/classes';
import { PERMISSIONS } from '../../utils/constants';

export default function StudentForm({ student = null, onSaved, onCancel }) {
  // removed unused `user` from AuthContext
  const [userId, setUserId] = useState(student?.user_id || '');
  const [classId, setClassId] = useState(student?.class_id || '');
  const [admissionNo, setAdmissionNo] = useState(student?.admission_no || '');
  const [dob, setDob] = useState(student?.dob ? student.dob.slice(0, 10) : '');
  const [address, setAddress] = useState(student?.address || '');
  const [pickupStop, setPickupStop] = useState(student?.pickup_stop_id || '');
  const [dropStop, setDropStop] = useState(student?.drop_stop_id || '');
  const [academicYear, setAcademicYear] = useState(student?.academic_year || '');
  const [busServiceActive, setBusServiceActive] = useState(!!student?.bus_service_active);
  const [saving, setSaving] = useState(false);
  const [classes, setClasses] = useState([]);
  // parents state removed (not used in this form)
  const [stops, setStops] = useState([]);
  const [users, setUsers] = useState([]);
  const [errors, setErrors] = useState({});

  useEffect(() => {
    let mounted = true;
    // Use classesService to fetch classes (works for admin and has a graceful fallback)
    classesService.getClasses()
      .then((res) => mounted && setClasses(res.data?.data || res.data || []))
      .catch(() => mounted && setClasses([]));

    // parents API call removed (not used in this form)

    apiService.getStops?.()
      .then((res) => mounted && setStops(res.data?.data || res.data || []))
      .catch(() => mounted && setStops([]));

    // Fetch users with role student and filter out those already assigned to a student record
    usersService.getAvailableStudentUsers({ per_page: 1000 })
      .then((res) => mounted && setUsers(res.data?.data || res.data || []))
      .catch(() => mounted && setUsers([]));

    return () => { mounted = false; };
  }, []);

  const validate = () => {
    const e = {};
    if (!userId) e.userId = 'Student user is required';
    if (!classId) e.classId = 'Class is required';
    if (!admissionNo) e.admissionNo = 'Admission number is required';
    if (!dob) e.dob = 'Date of birth is required';
    else if (new Date(dob) > new Date()) e.dob = 'DOB cannot be in the future';
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!validate()) return;
    setSaving(true);
    const payload = {
      user_id: userId,
      class_id: classId,
      admission_no: admissionNo,
      dob,
      address,
      pickup_stop_id: pickupStop,
      drop_stop_id: dropStop,
      academic_year: academicYear,
      bus_service_active: busServiceActive,
    };

    try {
      if (student?.id) {
        await studentsService.updateStudent(student.id, payload);
      } else {
        await studentsService.createStudent(payload);
      }
      onSaved && onSaved();
    } catch (err) {
      console.error('Save student error', err);
      const message = err.response?.data?.message || err.message;
      setErrors({ form: message });
    } finally {
      setSaving(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-3">
      {errors.form && <div className="text-red-600">{errors.form}</div>}

      <div>
        <label className="block text-sm">Student (user)</label>
        <select value={userId} onChange={(e) => setUserId(e.target.value)} className="mt-1 block w-full border rounded p-2">
          <option value="">Select user account</option>
          {users.map((u) => (
            <option key={u.id} value={u.id}>{u.name || u.email || `#${u.id}`}</option>
          ))}
        </select>
        {errors.userId && <div className="text-red-600 text-sm">{errors.userId}</div>}
      </div>

      <div>
        <label className="block text-sm">Class</label>
        <select value={classId} onChange={(e) => setClassId(e.target.value)} className="mt-1 block w-full border rounded p-2">
          <option value="">Select class</option>
          {classes.map((c) => (
            <option key={c.id} value={c.id}>{c.name}</option>
          ))}
        </select>
        {errors.classId && <div className="text-red-600 text-sm">{errors.classId}</div>}
      </div>

      <div>
        <label className="block text-sm">Admission No</label>
        <input value={admissionNo} onChange={(e) => setAdmissionNo(e.target.value)} className="mt-1 block w-full border rounded p-2" />
        {errors.admissionNo && <div className="text-red-600 text-sm">{errors.admissionNo}</div>}
      </div>

      <div>
        <label className="block text-sm">DOB</label>
        <input type="date" value={dob} onChange={(e) => setDob(e.target.value)} className="mt-1 block w-full border rounded p-2" />
        {errors.dob && <div className="text-red-600 text-sm">{errors.dob}</div>}
      </div>

      <div>
        <label className="block text-sm">Address</label>
        <input value={address} onChange={(e) => setAddress(e.target.value)} className="mt-1 block w-full border rounded p-2" />
      </div>

      <div className="flex gap-2">
        <div className="flex-1">
          <label className="block text-sm">Pickup Stop</label>
          <select value={pickupStop} onChange={(e) => setPickupStop(e.target.value)} className="mt-1 block w-full border rounded p-2">
            <option value="">Select pickup stop</option>
            {stops.map((s) => (<option key={s.id} value={s.id}>{s.name || s.title || `#${s.id}`}</option>))}
          </select>
        </div>
        <div className="flex-1">
          <label className="block text-sm">Drop Stop</label>
          <select value={dropStop} onChange={(e) => setDropStop(e.target.value)} className="mt-1 block w-full border rounded p-2">
            <option value="">Select drop stop</option>
            {stops.map((s) => (<option key={s.id} value={s.id}>{s.name || s.title || `#${s.id}`}</option>))}
          </select>
        </div>
      </div>

      <div>
        <label className="block text-sm">Academic Year</label>
        <input value={academicYear} onChange={(e) => setAcademicYear(e.target.value)} className="mt-1 block w-full border rounded p-2" />
      </div>

      <div className="flex items-center gap-2">
        <input type="checkbox" checked={busServiceActive} onChange={(e) => setBusServiceActive(e.target.checked)} />
        <label className="text-sm">Bus service active</label>
      </div>

      <div className="flex justify-end gap-2">
        <button type="button" onClick={onCancel} className="px-4 py-2 border rounded">Cancel</button>
        <button disabled={saving} className="px-4 py-2 bg-blue-600 text-white rounded">{saving ? 'Saving...' : 'Save'}</button>
      </div>
    </form>
  );
}
