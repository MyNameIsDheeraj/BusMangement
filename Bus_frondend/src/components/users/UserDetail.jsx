import { useEffect, useState } from 'react';
import { usersService } from '../../services/users';
import { useParams } from 'react-router-dom';

export default function UserDetail() {
  const { id } = useParams();
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!id) return;
    usersService.getUser(id)
      .then((res) => setUser(res.data))
      .catch((err) => console.error('Get user error', err))
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) return <div className="p-4">Loading...</div>;
  if (!user) return <div className="p-4">User not found</div>;

  return (
    <div className="p-4">
      <h2 className="text-xl font-semibold mb-2">{user.name}</h2>
      <div className="text-sm text-gray-600">{user.email}</div>
      <pre className="mt-4 bg-gray-50 p-3 rounded text-sm">{JSON.stringify(user, null, 2)}</pre>
    </div>
  );
}
