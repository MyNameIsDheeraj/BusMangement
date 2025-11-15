import { useEffect, useState } from 'react';
import { classesService } from '../../services/classes';
import { useParams } from 'react-router-dom';

export default function ClassDetail() {
  const { id } = useParams();
  const [cls, setCls] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!id) return;
    classesService.getClass(id)
      .then((res) => setCls(res.data))
      .catch((err) => console.error(err))
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) return <div className="p-4">Loading...</div>;
  if (!cls) return <div className="p-4">Class not found</div>;

  return (
    <div className="p-4">
      <h2 className="text-xl font-semibold">{cls.name}</h2>
      <pre className="mt-4 bg-gray-50 p-3 rounded">{JSON.stringify(cls, null, 2)}</pre>
    </div>
  );
}
