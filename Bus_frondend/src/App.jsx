import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';
import ProtectedRoute from './components/ProtectedRoute';
import Layout from './components/Layout';
import RoleRedirect from './components/RoleRedirect';
import Login from './pages/Login';
import { ROLES } from './utils/constants';

// Dashboard imports
import AdminDashboard from './pages/dashboards/AdminDashboard';
import TeacherDashboard from './pages/dashboards/TeacherDashboard';
import ParentDashboard from './pages/dashboards/ParentDashboard';
import StudentDashboard from './pages/dashboards/StudentDashboard';
import DriverDashboard from './pages/dashboards/DriverDashboard';
import CleanerDashboard from './pages/dashboards/CleanerDashboard';

function App() {
  return (
    <AuthProvider>
      <Router>
        <Routes>
          {/* Public routes */}
          <Route path="/login" element={<Login />} />

          {/* Protected routes with role-based access */}
          <Route
            path="/admin/dashboard"
            element={
              <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                <Layout>
                  <AdminDashboard />
                </Layout>
              </ProtectedRoute>
            }
          />

          <Route
            path="/teacher/dashboard"
            element={
              <ProtectedRoute allowedRoles={[ROLES.TEACHER]}>
                <Layout>
                  <TeacherDashboard />
                </Layout>
              </ProtectedRoute>
            }
          />

          <Route
            path="/parent/dashboard"
            element={
              <ProtectedRoute allowedRoles={[ROLES.PARENT]}>
                <Layout>
                  <ParentDashboard />
                </Layout>
              </ProtectedRoute>
            }
          />

          <Route
            path="/student/dashboard"
            element={
              <ProtectedRoute allowedRoles={[ROLES.STUDENT]}>
                <Layout>
                  <StudentDashboard />
                </Layout>
              </ProtectedRoute>
            }
          />

          <Route
            path="/driver/dashboard"
            element={
              <ProtectedRoute allowedRoles={[ROLES.DRIVER]}>
                <Layout>
                  <DriverDashboard />
                </Layout>
              </ProtectedRoute>
            }
          />

          <Route
            path="/cleaner/dashboard"
            element={
              <ProtectedRoute allowedRoles={[ROLES.CLEANER]}>
                <Layout>
                  <CleanerDashboard />
                </Layout>
              </ProtectedRoute>
            }
          />

          {/* Role-based redirect */}
          <Route
            path="/dashboard"
            element={
              <ProtectedRoute>
                <RoleRedirect />
              </ProtectedRoute>
            }
          />

          {/* Default redirect */}
          <Route path="/" element={<Navigate to="/login" replace />} />
          <Route path="*" element={<Navigate to="/login" replace />} />
        </Routes>
      </Router>
    </AuthProvider>
  );
}

export default App;
