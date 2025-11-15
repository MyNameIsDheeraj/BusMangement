import { apiService } from './api';

export const studentsService = {
  getStudents: (params) => apiService.getStudents(params),
  getStudent: (id) => apiService.getStudent(id),
  createStudent: (payload) => apiService.createStudent(payload),
  updateStudent: (id, payload) => apiService.updateStudent(id, payload),
  deleteStudent: (id) => apiService.deleteStudent(id),
};

export default studentsService;
