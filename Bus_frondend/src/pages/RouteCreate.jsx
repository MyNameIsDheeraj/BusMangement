import RouteForm from '../components/route/RouteForm';
import { useNavigate } from 'react-router-dom';

export default function RouteCreate() {
  const navigate = useNavigate();

  return (
    <div className="p-4">
      <div className="mb-4 flex items-center justify-between">
        <h1 className="text-2xl font-semibold">Create Route</h1>
      </div>
      <div className="bg-white p-4 rounded shadow">
        <RouteForm onSaved={() => navigate('/admin/routes')} onCancel={() => navigate('/admin/routes')} />
      </div>
    </div>
  );
}
