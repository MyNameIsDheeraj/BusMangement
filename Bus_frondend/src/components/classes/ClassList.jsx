import { useEffect, useState } from 'react';
import { classesService } from '../../services/classes';

export default function ClassList() {
  const [classes, setClasses] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    classesService.getClasses()
      .then((res) => setClasses(res.data?.data || res.data || []))
      .catch((err) => console.error(err))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <div className="p-4">Loading classes...</div>;

  return (
    <div className="p-4">
      <h2 className="text-xl font-semibold mb-4">Classes</h2>
      <ul className="space-y-2">
        {classes.map((c) => (
          <li key={c.id} className="p-3 bg-white rounded shadow-sm">{c.name}</li>
        ))}
      </ul>
    </div>
  );
}
