import BusForm from '../components/bus/BusForm';
import { useNavigate } from 'react-router-dom';

export default function BusCreate() {
  const navigate = useNavigate();

  return (
    <div className="p-4">
      <div className="mb-4 flex items-center justify-between">
        <h1 className="text-2xl font-semibold">Create Bus</h1>
      </div>
      <div className="bg-white p-4 rounded shadow">
        <BusForm onSaved={() => navigate('/admin/buses')} onCancel={() => navigate('/admin/buses')} />
      </div>
    </div>
  );
}
