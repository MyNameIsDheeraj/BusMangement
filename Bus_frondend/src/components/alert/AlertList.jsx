import { useEffect, useState } from 'react';
import { apiService } from '../../services/api';

export default function AlertList() {
	const [alerts, setAlerts] = useState([]);
	const [loading, setLoading] = useState(true);

	useEffect(() => {
		let mounted = true;
		apiService.getAlerts({ per_page: 20 })
			.then((res) => {
				if (!mounted) return;
				const data = res.data;
				setAlerts(Array.isArray(data) ? data : data?.data || []);
			})
			.catch((err) => console.error('Error loading alerts', err))
			.finally(() => mounted && setLoading(false));

		return () => { mounted = false; };
	}, []);

	if (loading) return <div className="p-4">Loading alerts...</div>;

	return (
		<div className="px-4 py-6 sm:px-0">
			<div className="mb-6">
				<h1 className="text-2xl font-bold">Alerts</h1>
				<p className="text-sm text-gray-600">View and create alerts for students</p>
			</div>

			<div className="bg-white shadow overflow-hidden sm:rounded-md">
				<ul className="divide-y divide-gray-200">
					{alerts.length === 0 && <li className="px-4 py-4">No alerts found</li>}
					{alerts.map((a) => (
						<li key={a.id} className="px-4 py-4 sm:px-6">
							<div>
								<div className="text-sm font-medium text-gray-900">{a.description || `Alert #${a.id}`}</div>
								<div className="text-sm text-gray-500">Severity: {a.severity} | Status: {a.status}</div>
							</div>
						</li>
					))}
				</ul>
			</div>
		</div>
	);
}
