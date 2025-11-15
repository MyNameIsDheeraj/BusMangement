import { useEffect, useState } from 'react';
import { attendanceService } from '../../services/attendance';

export default function AttendanceHistory() {
  const [records, setRecords] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    attendanceService.getAttendances()
      .then((res) => setRecords(res.data?.data || res.data || []))
      .catch((err) => console.error(err))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <div className="p-4">Loading attendance...</div>;

  return (
    <div className="p-4">
      <h2 className="text-xl font-semibold mb-4">Attendance History</h2>
      <ul className="space-y-2">
        {records.map((r) => (
          <li key={r.id} className="p-3 bg-white rounded shadow-sm">
            <div>{r.date} — {r.student_name || r.student?.name} — {r.status}</div>
          </li>
        ))}
      </ul>
    </div>
  );
}
