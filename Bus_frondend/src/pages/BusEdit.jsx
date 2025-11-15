import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import BusForm from '../components/bus/BusForm';
import { apiService } from '../services/api';

export default function BusEdit() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [bus, setBus] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let mounted = true;
    if (!id) return;
    apiService.getBus(id)
      .then((res) => {
        if (!mounted) return;
        setBus(res.data?.data || res.data || null);
      })
      .catch((err) => console.error('Load bus for edit error', err))
      .finally(() => mounted && setLoading(false));

    return () => { mounted = false; };
  }, [id]);

  if (loading) return <div className="p-4">Loading...</div>;
  if (!bus) return <div className="p-4">Bus not found</div>;

  return (
    <div className="p-4">
      <div className="mb-4 flex items-center justify-between">
        <h1 className="text-2xl font-semibold">Edit Bus</h1>
      </div>
      <div className="bg-white p-4 rounded shadow">
        <BusForm bus={bus} onSaved={() => navigate('/admin/buses')} onCancel={() => navigate('/admin/buses')} />
      </div>
    </div>
  );
}
