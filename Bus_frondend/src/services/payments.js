import { apiService } from './api';

export const paymentsService = {
  getPayments: (params) => apiService.getPayments(params),
  getPayment: (id) => apiService.getPayment(id),
  createPayment: (data) => apiService.createPayment(data),
  updatePayment: (id, data) => apiService.updatePayment(id, data),
  deletePayment: (id) => apiService.deletePayment(id),
};

export default paymentsService;
