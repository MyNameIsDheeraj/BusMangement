import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import { getRoleDisplayName } from '../../utils/helpers';

export default function Header() {
	const { user } = useAuth();

	return (
		<div className="bg-white p-4 rounded shadow mb-4">
			<div className="flex items-center justify-between">
				<div>
					<h1 className="text-xl font-semibold">Bus Management System</h1>
					<div className="text-sm text-gray-500">{user?.name} — {getRoleDisplayName(user?.role?.id)}</div>
				</div>
				<div>
					<Link to="/dashboard" className="text-sm text-blue-600 hover:underline">Go to Dashboard</Link>
				</div>
			</div>
		</div>
	);
}
