import { apiService } from './api';

export const alertsService = {
  getAlerts: async (params = {}) => {
    const response = await apiService.getAlerts(params);
    return response.data;
  },

  getAlert: async (id) => {
    const response = await apiService.getAlert(id);
    return response.data;
  },

  createAlert: async (data) => {
    const response = await apiService.createAlert(data);
    return response.data;
  },

  updateAlert: async (id, data) => {
    const response = await apiService.updateAlert(id, data);
    return response.data;
  },

  deleteAlert: async (id) => {
    const response = await apiService.deleteAlert(id);
    return response.data;
  },
};
