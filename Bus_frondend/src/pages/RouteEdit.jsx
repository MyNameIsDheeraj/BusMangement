import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import RouteForm from '../components/route/RouteForm';
import { apiService } from '../services/api';

export default function RouteEdit() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [route, setRoute] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let mounted = true;
    if (!id) return;
    apiService.getRoute(id)
      .then((res) => {
        if (!mounted) return;
        setRoute(res.data?.data || res.data || null);
      })
      .catch((err) => console.error('Load route for edit error', err))
      .finally(() => mounted && setLoading(false));

    return () => { mounted = false; };
  }, [id]);

  if (loading) return <div className="p-4">Loading...</div>;
  if (!route) return <div className="p-4">Route not found</div>;

  return (
    <div className="p-4">
      <div className="mb-4 flex items-center justify-between">
        <h1 className="text-2xl font-semibold">Edit Route</h1>
      </div>
      <div className="bg-white p-4 rounded shadow">
        <RouteForm route={route} onSaved={() => navigate('/admin/routes')} onCancel={() => navigate('/admin/routes')} />
      </div>
    </div>
  );
}
