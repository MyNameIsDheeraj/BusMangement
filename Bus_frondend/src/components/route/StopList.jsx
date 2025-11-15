import { useEffect, useState } from 'react';
import { apiService } from '../../services/api';

export default function StopList({ routeId }) {
  const [stops, setStops] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    apiService.getStops({ route_id: routeId })
      .then((res) => setStops(res.data?.data || res.data || []))
      .catch((err) => console.error('Get stops error', err))
      .finally(() => setLoading(false));
  }, [routeId]);

  if (loading) return <div className="p-4">Loading stops...</div>;

  return (
    <div className="p-4">
      <h3 className="font-semibold mb-2">Stops</h3>
      <ul className="space-y-2">
        {stops.map((s) => (
          <li key={s.id} className="p-2 bg-white rounded shadow-sm">{s.name} — {s.time}</li>
        ))}
      </ul>
    </div>
  );
}
