import { apiService } from './api';

export const attendanceService = {
  getAttendances: (params) => apiService.getAttendances(params),
  getAttendance: (id) => apiService.getAttendance(id),
  createAttendance: (data) => apiService.createAttendance(data),
  updateAttendance: (id, data) => apiService.updateAttendance(id, data),
  // Convenience alias used by the UI
  markAttendance: (data) => apiService.createAttendance(data),
};

export default attendanceService;
