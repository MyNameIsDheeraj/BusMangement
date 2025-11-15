import StudentForm from '../components/student/StudentForm';
import { useNavigate } from 'react-router-dom';

export default function StudentCreate() {
  const navigate = useNavigate();

  return (
    <div className="p-4">
      <div className="mb-4 flex items-center justify-between">
        <h1 className="text-2xl font-semibold">Create Student</h1>
      </div>
      <div className="bg-white p-4 rounded shadow">
        <StudentForm onSaved={() => navigate('/admin/students')} onCancel={() => navigate('/admin/students')} />
      </div>
    </div>
  );
}
