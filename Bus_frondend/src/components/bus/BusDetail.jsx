import { useEffect, useState } from 'react';
import { apiService } from '../../services/api';
import { useParams } from 'react-router-dom';

export default function BusDetail() {
  const { id } = useParams();
  const [bus, setBus] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!id) return;
    apiService.getBus(id)
      .then((res) => setBus(res.data))
      .catch((err) => console.error('Get bus error', err))
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) return <div className="p-4">Loading...</div>;
  if (!bus) return <div className="p-4">Bus not found</div>;

  return (
    <div className="p-4">
      <h2 className="text-xl font-semibold mb-2">{bus.bus_number || bus.registration_no}</h2>
      <pre className="mt-4 bg-gray-50 p-3 rounded">{JSON.stringify(bus, null, 2)}</pre>
    </div>
  );
}
