import { apiService } from './api';

export const teachersService = {
  getTeachers: async (params = {}) => {
    const response = await apiService.getTeachers(params);
    return response.data;
  },

  getTeacher: async (id) => {
    const response = await apiService.getTeacher(id);
    return response.data;
  },

  createTeacher: async (data) => {
    const response = await apiService.createTeacher(data);
    return response.data;
  },

  updateTeacher: async (id, data) => {
    const response = await apiService.updateTeacher(id, data);
    return response.data;
  },

  deleteTeacher: async (id) => {
    const response = await apiService.deleteTeacher(id);
    return response.data;
  },

  getMyClasses: async () => {
    const response = await apiService.getMyClasses();
    return response.data;
  },

  getMyStudents: async () => {
    const response = await apiService.getMyStudents();
    return response.data;
  },
};
