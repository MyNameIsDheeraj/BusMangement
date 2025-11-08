# Bus Management System - Frontend

A comprehensive React-based frontend application for managing school bus transportation operations with role-based access control (RBAC).

## Features

- **Role-Based Access Control (RBAC)**: 6 distinct user roles (Admin, Teacher, Parent, Student, Driver, Cleaner)
- **JWT Authentication**: Secure token-based authentication with automatic refresh
- **Role-Specific Dashboards**: Customized dashboards for each user role
- **Student Management**: Complete student information management
- **Bus & Route Management**: Manage buses, routes, and stops
- **Attendance Tracking**: Mark and track student attendance
- **Payment Management**: Handle payments and payment history
- **Alert System**: Create and manage alerts for students
- **Announcement System**: System-wide announcements

## Technology Stack

- **React 19**: Modern React with hooks
- **React Router**: Client-side routing
- **Axios**: HTTP client for API requests
- **Tailwind CSS**: Utility-first CSS framework
- **Vite**: Fast build tool and dev server

## Project Structure

```
src/
├── components/          # Reusable components
│   ├── Layout.jsx      # Main layout with navigation
│   ├── ProtectedRoute.jsx  # Route protection component
│   └── RoleRedirect.jsx    # Role-based redirect component
├── contexts/           # React contexts
│   └── AuthContext.jsx # Authentication context
├── pages/              # Page components
│   ├── Login.jsx       # Login page
│   ├── dashboards/     # Role-specific dashboards
│   │   ├── AdminDashboard.jsx
│   │   ├── TeacherDashboard.jsx
│   │   ├── ParentDashboard.jsx
│   │   ├── StudentDashboard.jsx
│   │   ├── DriverDashboard.jsx
│   │   └── CleanerDashboard.jsx
│   └── StudentsList.jsx
├── services/           # API services
│   └── api.js          # API service layer
├── utils/              # Utility functions
│   ├── constants.js    # Constants and configuration
│   └── helpers.js      # Helper functions
├── App.jsx             # Main app component
└── main.jsx            # Entry point
```

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Bus_frontend
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Configure environment variables**
   Create a `.env` file in the root directory:
   ```env
   VITE_API_BASE_URL=http://localhost:8000/api/v1
   ```

4. **Start the development server**
   ```bash
   npm run dev
   ```

5. **Build for production**
   ```bash
   npm run build
   ```

## User Roles

### 1. Admin (Role ID: 1)
- Full system access
- Manage all users, students, buses, routes, payments
- System configuration and user management

### 2. Teacher (Role ID: 2)
- Access to students in assigned classes
- Mark attendance for assigned students
- Create payments and alerts for assigned students
- View class-specific information

### 3. Parent (Role ID: 3)
- Access to their own children's information
- View and make payments for their children
- Track transportation and attendance
- Receive alerts about their children

### 4. Student (Role ID: 4)
- Access to their own information
- View attendance, payments, and bus information
- Self-service access to personal records

### 5. Driver (Role ID: 5)
- Access to students on assigned route
- Mark attendance for students on route
- Create alerts for route students
- View route information and stops

### 6. Cleaner (Role ID: 6)
- Same permissions as Driver
- Access to students on assigned route
- Mark attendance and create alerts

## API Integration

The frontend communicates with the Laravel backend API. All API requests are handled through the `apiService` in `src/services/api.js`.

### Authentication Flow

1. User logs in with email and password
2. Backend returns JWT access token and refresh token
3. Tokens are stored in localStorage
4. Access token is included in Authorization header for all API requests
5. Token is automatically refreshed when expired

### API Service Methods

The `apiService` provides methods for:
- Authentication (login, logout, refresh)
- User management
- Student management
- Bus and route management
- Payment management
- Attendance management
- Alert management
- Announcement management

## Routing

The application uses React Router for client-side routing with protected routes:

- `/login` - Login page (public)
- `/dashboard` - Role-based redirect
- `/admin/dashboard` - Admin dashboard
- `/teacher/dashboard` - Teacher dashboard
- `/parent/dashboard` - Parent dashboard
- `/student/dashboard` - Student dashboard
- `/driver/dashboard` - Driver dashboard
- `/cleaner/dashboard` - Cleaner dashboard

## Security Features

- JWT token-based authentication
- Automatic token refresh
- Role-based route protection
- Context-based data access control
- Secure token storage
- Protected API endpoints

## Development

### Available Scripts

- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run preview` - Preview production build
- `npm run lint` - Run ESLint

### Code Style

The project uses ESLint for code quality. Follow the existing code style and run `npm run lint` before committing.

## Environment Variables

- `VITE_API_BASE_URL` - Base URL for the API backend (default: `http://localhost:8000/api/v1`)

## Contributing

1. Create a feature branch
2. Make your changes
3. Run tests and linting
4. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support, please contact the development team or create an issue in the repository.
