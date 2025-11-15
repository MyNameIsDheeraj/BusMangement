import { useEffect, useState } from 'react';
import { apiService } from '../../services/api';
import { Link } from 'react-router-dom';

export default function PaymentList() {
	const [payments, setPayments] = useState([]);
	const [loading, setLoading] = useState(true);

	useEffect(() => {
		let mounted = true;
		apiService.getPayments({ per_page: 20 })
			.then((res) => {
				if (!mounted) return;
				const data = res.data;
				setPayments(Array.isArray(data) ? data : data?.data || []);
			})
			.catch((err) => console.error('Error loading payments', err))
			.finally(() => mounted && setLoading(false));

		return () => { mounted = false; };
	}, []);

	if (loading) return <div className="p-4">Loading payments...</div>;

	return (
		<div className="px-4 py-6 sm:px-0">
			<div className="mb-6">
				<h1 className="text-2xl font-bold">Payments</h1>
				<p className="text-sm text-gray-600">Manage payment records</p>
			</div>

			<div className="bg-white shadow overflow-hidden sm:rounded-md">
				<ul className="divide-y divide-gray-200">
					{payments.length === 0 && <li className="px-4 py-4">No payments found</li>}
					{payments.map((p) => (
						<li key={p.id} className="px-4 py-4 sm:px-6">
							<div className="flex items-center justify-between">
								<div>
									<div className="text-sm font-medium text-gray-900">{p.transaction_id || `Payment #${p.id}`}</div>
									<div className="text-sm text-gray-500">Amount: {p.amount_paid} | Status: {p.status}</div>
								</div>
								<div className="ml-2 flex-shrink-0">
									<Link to={`/admin/payments/${p.id}`} className="text-blue-600 hover:text-blue-800 text-sm font-medium">View</Link>
								</div>
							</div>
						</li>
					))}
				</ul>
			</div>
		</div>
	);
}

