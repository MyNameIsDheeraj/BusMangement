import { useEffect, useState } from 'react';
import { apiService } from '../../services/api';
import { Link } from 'react-router-dom';

export default function BusList() {
	const [buses, setBuses] = useState([]);
	const [loading, setLoading] = useState(true);
	const [error, setError] = useState('');

	useEffect(() => {
		let mounted = true;
		apiService.getBuses({ per_page: 20 })
			.then((res) => {
				if (!mounted) return;
				const data = res.data;
				// Handle various response shapes
				let busesArray = [];
				if (Array.isArray(data)) {
					busesArray = data;
				} else if (data?.data && Array.isArray(data.data)) {
					busesArray = data.data;
				} else if (data?.buses && Array.isArray(data.buses)) {
					busesArray = data.buses;
				}
				setBuses(busesArray);
				setError('');
			})
			.catch((err) => {
				console.error('Error loading buses', err);
				setError(err.response?.data?.message || err.message || 'Failed to load buses');
			})
			.finally(() => mounted && setLoading(false));

		return () => { mounted = false; };
	}, []);

	if (loading) return <div className="p-4">Loading buses...</div>;
	if (error) return <div className="p-4 text-red-600">Error: {error}</div>;

	return (
		<div className="px-4 py-6 sm:px-0">
			<div className="mb-6">
				<h1 className="text-2xl font-bold">Buses</h1>
				<p className="text-sm text-gray-600">Manage buses and assignments</p>
			</div>

			<div className="bg-white shadow overflow-hidden sm:rounded-md">
				<ul className="divide-y divide-gray-200">
					{buses.length === 0 && <li className="px-4 py-4">No buses found</li>}
					{buses.map((bus) => (
						<li key={bus.id} className="px-4 py-4 sm:px-6">
							<div className="flex items-center justify-between">
								<div>
									<div className="text-sm font-medium text-gray-900">{bus.bus_number || bus.registration_no}</div>
									<div className="text-sm text-gray-500">Model: {bus.model || 'N/A'} | Capacity: {bus.seating_capacity || 'N/A'}</div>
								</div>
								<div className="ml-2 flex-shrink-0">
									<Link to={`/admin/buses/${bus.id}`} className="text-blue-600 hover:text-blue-800 text-sm font-medium">View</Link>
								</div>
							</div>
						</li>
					))}
				</ul>
			</div>
		</div>
	);
}

