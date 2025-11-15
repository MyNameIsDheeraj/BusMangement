import { useEffect, useState } from 'react';
import { apiService } from '../../services/api';
import { useParams } from 'react-router-dom';

export default function RouteDetail() {
  const { id } = useParams();
  const [route, setRoute] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!id) return;
    apiService.getRoute(id)
      .then((res) => setRoute(res.data))
      .catch((err) => console.error('Get route error', err))
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) return <div className="p-4">Loading...</div>;
  if (!route) return <div className="p-4">Route not found</div>;

  return (
    <div className="p-4">
      <h2 className="text-xl font-semibold">{route.name}</h2>
      <pre className="mt-4 bg-gray-50 p-3 rounded">{JSON.stringify(route, null, 2)}</pre>
    </div>
  );
}
