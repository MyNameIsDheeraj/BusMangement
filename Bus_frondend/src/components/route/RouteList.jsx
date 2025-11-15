import { useEffect, useState } from 'react';
import { apiService } from '../../services/api';
import { Link } from 'react-router-dom';

export default function RouteList() {
	const [routes, setRoutes] = useState([]);
	const [loading, setLoading] = useState(true);
	const [error, setError] = useState('');

	useEffect(() => {
		let mounted = true;
		apiService.getRoutes({ per_page: 20 })
			.then((res) => {
				if (!mounted) return;
				const data = res.data;
				// Handle various response shapes
				let routesArray = [];
				if (Array.isArray(data)) {
					routesArray = data;
				} else if (data?.data && Array.isArray(data.data)) {
					routesArray = data.data;
				} else if (data?.routes && Array.isArray(data.routes)) {
					routesArray = data.routes;
				}
				setRoutes(routesArray);
				setError('');
			})
			.catch((err) => {
				console.error('Error loading routes', err);
				setError(err.response?.data?.message || err.message || 'Failed to load routes');
			})
			.finally(() => mounted && setLoading(false));

		return () => { mounted = false; };
	}, []);

	if (loading) return <div className="p-4">Loading routes...</div>;
	if (error) return <div className="p-4 text-red-600">Error: {error}</div>;

	return (
		<div className="px-4 py-6 sm:px-0">
			<div className="mb-6">
				<h1 className="text-2xl font-bold">Routes</h1>
				<p className="text-sm text-gray-600">Manage bus routes and stops</p>
			</div>

			<div className="bg-white shadow overflow-hidden sm:rounded-md">
				<ul className="divide-y divide-gray-200">
					{routes.length === 0 && <li className="px-4 py-4">No routes found</li>}
					{routes.map((r) => (
						<li key={r.id} className="px-4 py-4 sm:px-6">
							<div className="flex items-center justify-between">
								<div>
									<div className="text-sm font-medium text-gray-900">{r.name}</div>
									<div className="text-sm text-gray-500">Bus: {r.bus?.bus_number || 'N/A'} | {r.start_time} - {r.end_time}</div>
								</div>
								<div className="ml-2 flex-shrink-0">
									<Link to={`/admin/routes/${r.id}`} className="text-blue-600 hover:text-blue-800 text-sm font-medium">View</Link>
								</div>
							</div>
						</li>
					))}
				</ul>
			</div>
		</div>
	);
}
