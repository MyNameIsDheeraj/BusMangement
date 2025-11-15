import { apiService } from './api';

export const classesService = {
  getClasses: (params) => apiService.getClasses ? apiService.getClasses(params) : Promise.resolve({ data: [] }),
  getClass: (id) => apiService.getClass ? apiService.getClass(id) : Promise.resolve({ data: null }),
  createClass: (data) => apiService.createClass ? apiService.createClass(data) : Promise.resolve({ data }),
  updateClass: (id, data) => apiService.updateClass ? apiService.updateClass(id, data) : Promise.resolve({ data }),
};

export default classesService;
