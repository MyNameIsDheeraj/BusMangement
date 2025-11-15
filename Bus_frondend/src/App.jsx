import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';
import ProtectedRoute from './components/ProtectedRoute';
import Layout from './components/Layout';
import RoleRedirect from './components/RoleRedirect';
import Login from './pages/Login';
import ApiRedirectHandler from './components/ApiRedirectHandler';
import { ROLES } from './utils/constants';

// Dashboard imports
import AdminDashboard from './pages/dashboards/AdminDashboard';
import TeacherDashboard from './pages/dashboards/TeacherDashboard';
import ParentDashboard from './pages/dashboards/ParentDashboard';
import StudentDashboard from './pages/dashboards/StudentDashboard';
import DriverDashboard from './pages/dashboards/DriverDashboard';
import CleanerDashboard from './pages/dashboards/CleanerDashboard';
// Admin pages/components
import StudentsList from './pages/StudentsList';
import StudentCreate from './pages/StudentCreate';
import StudentEdit from './pages/StudentEdit';
import StudentDetail from './components/student/StudentDetail';
import UserList from './components/users/UserList';
import UserDetail from './components/users/UserDetail';
import BusList from './components/bus/BusList';
import BusDetail from './components/bus/BusDetail';
import BusCreate from './pages/BusCreate';
import BusEdit from './pages/BusEdit';
import RouteList from './components/route/RouteList';
import RouteDetail from './components/route/RouteDetail';
import RouteCreate from './pages/RouteCreate';
import RouteEdit from './pages/RouteEdit';
import PaymentList from './components/payment/PaymentList';
import PaymentDetail from './components/payment/PaymentDetail';
import AttendanceList from './components/attendance/AttendanceList';
import AttendancePage from './components/attendance/AttendancePage';
import AlertList from './components/alert/AlertList';
import AnnouncementList from './components/announcement/AnnouncementList';
import StaffList from './pages/StaffList';
import TeachersList from './pages/TeachersList';
import ParentsList from './pages/ParentsList';
import ClassList from './components/classes/ClassList';

function App() {
  return (
    <AuthProvider>
      <Router>
        <ApiRedirectHandler>
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

            {/* Admin management routes */}
            <Route
              path="/admin/students"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <StudentsList />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/students/new"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <StudentCreate />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/students/:id/edit"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <StudentEdit />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/students/:id"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <StudentDetail />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/users"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <UserList />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/users/:id"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <UserDetail />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/buses"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <BusList />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/buses/new"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <BusCreate />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/buses/:id/edit"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <BusEdit />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/buses/:id"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <BusDetail />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/routes"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <RouteList />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/routes/new"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <RouteCreate />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/routes/:id/edit"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <RouteEdit />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/routes/:id"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <RouteDetail />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/payments"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <PaymentList />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/payments/:id"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <PaymentDetail />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/attendances"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <AttendanceList />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/alerts"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <AlertList />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/announcements"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <AnnouncementList />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/staff"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <StaffList />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/teachers"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <TeachersList />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/parents"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <ParentsList />
                  </Layout>
                </ProtectedRoute>
              }
            />

            <Route
              path="/admin/classes"
              element={
                <ProtectedRoute allowedRoles={[ROLES.ADMIN]}>
                  <Layout>
                    <ClassList />
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
            path="/teacher/attendances"
            element={
              <ProtectedRoute allowedRoles={[ROLES.TEACHER, ROLES.ADMIN]}>
                <Layout>
                  <AttendancePage />
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
        </ApiRedirectHandler>
      </Router>
    </AuthProvider>
  );
}

export default App;
