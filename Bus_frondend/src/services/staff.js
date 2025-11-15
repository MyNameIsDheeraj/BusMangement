import { apiService } from './api';

export const staffService = {
  getStaffProfiles: async (params = {}) => {
    const response = await apiService.getStaffProfiles(params);
    return response.data;
  },

  getStaffProfile: async (id) => {
    const response = await apiService.getStaffProfile(id);
    return response.data;
  },

  createStaffProfile: async (data) => {
    const response = await apiService.createStaffProfile(data);
    return response.data;
  },

  updateStaffProfile: async (id, data) => {
    const response = await apiService.updateStaffProfile(id, data);
    return response.data;
  },

  deleteStaffProfile: async (id) => {
    const response = await apiService.deleteStaffProfile(id);
    return response.data;
  },
};
