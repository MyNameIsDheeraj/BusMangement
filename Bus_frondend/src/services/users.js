import { apiService } from './api';

export const usersService = {
  getUsers: (params) => apiService.getUsers(params),
  getUser: (id) => apiService.getUser(id),
  getAvailableStudentUsers: (params) => apiService.getAvailableStudentUsers(params),
  createUser: (data) => apiService.createUser(data),
  updateUser: (id, data) => apiService.updateUser(id, data),
  deleteUser: (id) => apiService.deleteUser(id),
};

export default usersService;
