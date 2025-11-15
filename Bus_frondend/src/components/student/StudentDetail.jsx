import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { studentsService } from '../../services/students';
import { formatDate } from '../../utils/helpers';

export default function StudentDetail() {
  const { id } = useParams();
  const [student, setStudent] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let mounted = true;
    const load = async () => {
      setLoading(true);
      try {
        const res = await studentsService.getStudent(id);
        if (!mounted) return;
        setStudent(res.data?.data || res.data || null);
      } catch (err) {
        console.error('Load student detail error', err);
      } finally {
        setLoading(false);
      }
    };
    load();
    return () => { mounted = false; };
  }, [id]);

  if (loading) return <div className="p-4">Loading...</div>;
  if (!student) return <div className="p-4">Student not found.</div>;

  return (
    <div className="p-4">
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-2xl font-semibold">Student detail</h2>
        <Link to="/admin/students" className="px-3 py-1 border rounded">Back</Link>
      </div>

      <div className="grid grid-cols-2 gap-4">
        <div className="border p-4 rounded">
          <h3 className="font-medium">Basic</h3>
          <p><strong>Name:</strong> {student.name || student.user?.name || '-'}</p>
          <p><strong>Admission No:</strong> {student.admission_no || '-'}</p>
          <p><strong>DOB:</strong> {formatDate(student.dob)}</p>
          <p><strong>Class:</strong> {student.class?.name || '-'}</p>
        </div>

        <div className="border p-4 rounded">
          <h3 className="font-medium">Transport</h3>
          <p><strong>Pickup stop:</strong> {student.pickup_stop?.name || student.pickup_stop_id || '-'}</p>
          <p><strong>Drop stop:</strong> {student.drop_stop?.name || student.drop_stop_id || '-'}</p>
          <p><strong>Bus service:</strong> {student.bus_service_active ? 'Yes' : 'No'}</p>
        </div>
      </div>
    </div>
  );
}
