import { apiService } from './api';

export const parentsService = {
  getParents: async (params = {}) => {
    const response = await apiService.getParents(params);
    return response.data;
  },

  getParent: async (id) => {
    const response = await apiService.getParent(id);
    return response.data;
  },

  createParent: async (data) => {
    const response = await apiService.createParent(data);
    return response.data;
  },

  updateParent: async (id, data) => {
    const response = await apiService.updateParent(id, data);
    return response.data;
  },

  deleteParent: async (id) => {
    const response = await apiService.deleteParent(id);
    return response.data;
  },

  getMyStudents: async () => {
    const response = await apiService.getParentMyStudents();
    return response.data;
  },
};
