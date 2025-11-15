import { useEffect, useState } from 'react';
import { apiService } from '../../services/api';

export default function AttendanceList() {
	const [records, setRecords] = useState([]);
	const [loading, setLoading] = useState(true);

	useEffect(() => {
		let mounted = true;
		apiService.getAttendances({ per_page: 20 })
			.then((res) => {
				if (!mounted) return;
				const data = res.data;
				setRecords(Array.isArray(data) ? data : data?.data || []);
			})
			.catch((err) => console.error('Error loading attendances', err))
			.finally(() => mounted && setLoading(false));

		return () => { mounted = false; };
	}, []);

	if (loading) return <div className="p-4">Loading attendances...</div>;

	return (
		<div className="px-4 py-6 sm:px-0">
			<div className="mb-6">
				<h1 className="text-2xl font-bold">Attendances</h1>
				<p className="text-sm text-gray-600">View attendance records</p>
			</div>

			<div className="bg-white shadow overflow-hidden sm:rounded-md">
				<ul className="divide-y divide-gray-200">
					{records.length === 0 && <li className="px-4 py-4">No records found</li>}
					{records.map((r) => (
						<li key={r.id} className="px-4 py-4 sm:px-6">
							<div className="flex items-center justify-between">
								<div>
									<div className="text-sm font-medium text-gray-900">{r.date} — {r.student?.name || r.student_name}</div>
									<div className="text-sm text-gray-500">Status: {r.status} | Bus: {r.bus?.bus_number || 'N/A'}</div>
								</div>
							</div>
						</li>
					))}
				</ul>
			</div>
		</div>
	);
}
