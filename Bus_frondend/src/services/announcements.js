import { apiService } from './api';

export const announcementsService = {
  getAnnouncements: async (params = {}) => {
    const response = await apiService.getAnnouncements(params);
    return response.data;
  },

  getAnnouncement: async (id) => {
    const response = await apiService.getAnnouncement(id);
    return response.data;
  },

  createAnnouncement: async (data) => {
    const response = await apiService.createAnnouncement(data);
    return response.data;
  },

  updateAnnouncement: async (id, data) => {
    const response = await apiService.updateAnnouncement(id, data);
    return response.data;
  },

  deleteAnnouncement: async (id) => {
    const response = await apiService.deleteAnnouncement(id);
    return response.data;
  },
};
