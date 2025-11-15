import { apiService } from './api';

export const busesService = {
  getBuses: (params) => apiService.getBuses(params),
  getBus: (id) => apiService.getBus(id),
  createBus: (data) => apiService.createBus(data),
  updateBus: (id, data) => apiService.updateBus(id, data),
  deleteBus: (id) => apiService.deleteBus(id),
};

export default busesService;
