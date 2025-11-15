import { apiService } from './api';

export const stopsService = {
  getStops: async (params = {}) => {
    const response = await apiService.getStops(params);
    return response.data;
  },

  getStop: async (id) => {
    const response = await apiService.getStop(id);
    return response.data;
  },

  createStop: async (data) => {
    const response = await apiService.createStop(data);
    return response.data;
  },

  updateStop: async (id, data) => {
    const response = await apiService.updateStop(id, data);
    return response.data;
  },

  deleteStop: async (id) => {
    const response = await apiService.deleteStop(id);
    return response.data;
  },

  getStopsByRoute: async (routeId) => {
    const response = await apiService.getStops({ route_id: routeId });
    return response.data;
  },
};
