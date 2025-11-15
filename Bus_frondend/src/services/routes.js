import { apiService } from './api';

export const routesService = {
  getRoutes: (params) => apiService.getRoutes(params),
  getRoute: (id) => apiService.getRoute(id),
  createRoute: (data) => apiService.createRoute(data),
  updateRoute: (id, data) => apiService.updateRoute(id, data),
  deleteRoute: (id) => apiService.deleteRoute(id),
  getStops: (params) => apiService.getStops(params),
};

export default routesService;
