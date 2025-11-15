import { useEffect, useState } from 'react';
import { apiService } from '../../services/api';
import { useParams } from 'react-router-dom';

export default function PaymentDetail() {
  const { id } = useParams();
  const [payment, setPayment] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!id) return;
    apiService.getPayment(id)
      .then((res) => setPayment(res.data))
      .catch((err) => console.error(err))
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) return <div className="p-4">Loading payment...</div>;
  if (!payment) return <div className="p-4">Payment not found</div>;

  return (
    <div className="p-4">
      <h2 className="text-xl font-semibold">Payment #{payment.id}</h2>
      <pre className="mt-4 bg-gray-50 p-3 rounded">{JSON.stringify(payment, null, 2)}</pre>
    </div>
  );
}
