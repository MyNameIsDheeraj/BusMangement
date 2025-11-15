import { useParams, useNavigate } from 'react-router-dom';
import { useEffect, useState } from 'react';
import StudentForm from '../components/student/StudentForm';
import { studentsService } from '../services/students';

export default function StudentEdit() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [student, setStudent] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let mounted = true;
    const load = async () => {
      try {
        const res = await studentsService.getStudent(id);
        if (!mounted) return;
        setStudent(res.data?.data || res.data || null);
      } catch (err) {
        console.error('Load student for edit error', err);
      } finally {
        setLoading(false);
      }
    };
    load();
    return () => { mounted = false; };
  }, [id]);

  if (loading) return <div className="p-4">Loading...</div>;
  if (!student) return <div className="p-4">Student not found</div>;

  return (
    <div className="p-4">
      <div className="mb-4 flex items-center justify-between">
        <h1 className="text-2xl font-semibold">Edit Student</h1>
      </div>
      <div className="bg-white p-4 rounded shadow">
        <StudentForm student={student} onSaved={() => navigate('/admin/students')} onCancel={() => navigate('/admin/students')} />
      </div>
    </div>
  );
}
