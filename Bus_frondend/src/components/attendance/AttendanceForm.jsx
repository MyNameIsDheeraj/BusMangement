import { useState, useEffect } from 'react';
import { attendanceService } from '../../services/attendance';
import { studentsService } from '../../services/students';

export default function AttendanceForm({ onSaved, defaultDate = null }) {
  const [date, setDate] = useState(defaultDate || new Date().toISOString().slice(0, 10));
  const [students, setStudents] = useState([]);
  const [marks, setMarks] = useState({});
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    let mounted = true;
    studentsService.getStudents?.()
      .then((res) => {
        if (!mounted) return;
        const list = res.data?.data || res.data || [];
        setStudents(list);
        const initial = {};
        list.forEach((s) => { initial[s.id] = 'present'; });
        setMarks(initial);
      })
      .catch(() => setStudents([]));
    return () => { mounted = false; };
  }, []);

  const toggle = (id, value) => {
    setMarks((m) => ({ ...m, [id]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);
    try {
      const payload = {
        date,
        attendance: Object.keys(marks).map((student_id) => ({ student_id, status: marks[student_id] })),
      };
      await attendanceService.markAttendance(payload);
      onSaved && onSaved();
    } catch (err) {
      console.error('Save attendance error', err);
    } finally {
      setSaving(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <div>
        <label className="block text-sm">Date</label>
        <input type="date" value={date} onChange={(e) => setDate(e.target.value)} className="mt-1 block w-full border rounded p-2" />
      </div>

      <div className="space-y-2 max-h-64 overflow-auto border rounded p-2">
        {students.map((s) => (
          <div key={s.id} className="flex items-center justify-between p-2 border-b last:border-b-0">
            <div>{s.name || s.full_name || `#${s.id}`}</div>
            <div className="flex gap-2">
              <label className={`px-2 py-1 rounded ${marks[s.id] === 'present' ? 'bg-green-100' : ''}`}>
                <input type="radio" name={`att-${s.id}`} checked={marks[s.id] === 'present'} onChange={() => toggle(s.id, 'present')} /> Present
              </label>
              <label className={`px-2 py-1 rounded ${marks[s.id] === 'absent' ? 'bg-red-100' : ''}`}>
                <input type="radio" name={`att-${s.id}`} checked={marks[s.id] === 'absent'} onChange={() => toggle(s.id, 'absent')} /> Absent
              </label>
              <label className={`px-2 py-1 rounded ${marks[s.id] === 'late' ? 'bg-yellow-100' : ''}`}>
                <input type="radio" name={`att-${s.id}`} checked={marks[s.id] === 'late'} onChange={() => toggle(s.id, 'late')} /> Late
              </label>
            </div>
          </div>
        ))}
      </div>

      <div>
        <button disabled={saving} className="px-4 py-2 bg-blue-600 text-white rounded">{saving ? 'Saving...' : 'Save Attendance'}</button>
      </div>
    </form>
  );
}
