Here's a polished, professional `API.md` file ready for your GitHub repository:

```md
# 🚌 Bus Management System API v1

**Total Endpoints**: 80  
**Architecture**: RESTful API with JWT Authentication  
**Access Control**: Role-based (Admin, Teacher, Parent, Student, Driver, Cleaner)

---

## 🔐 Authentication

Most endpoints require JWT authentication. Include your access token in the `Authorization` header:

```http
Authorization: Bearer {your-jwt-token}
```

### Auth Endpoints
| Method | Endpoint                 | Description                |
|--------|--------------------------|----------------------------|
| POST   | `/api/v1/login`          | User login                 |
| POST   | `/api/v1/register`       | User registration          |
| GET    | `/api/v1/me`             | Get authenticated user info|
| POST   | `/api/v1/logout`         | Logout user                |
| POST   | `/api/v1/refresh`        | Refresh JWT token          |

---

## 👥 Users (Admin Only)

| Method | Endpoint             | Description           |
|--------|----------------------|-----------------------|
| GET    | `/api/v1/users`      | Get all users         |
| POST   | `/api/v1/users`      | Create new user       |
| GET    | `/api/v1/users/{id}` | Get specific user     |
| PUT    | `/api/v1/users/{id}` | Update user           |
| PATCH  | `/api/v1/users/{id}` | Partial update        |
| DELETE | `/api/v1/users/{id}` | Delete user           |

---

## 🎓 Students

| Method | Endpoint                                      | Description                     |
|--------|-----------------------------------------------|---------------------------------|
| GET    | `/api/v1/students`                            | Get all students                |
| POST   | `/api/v1/students`                            | Create new student              |
| GET    | `/api/v1/students/{id}`                       | Get specific student            |
| PUT    | `/api/v1/students/{id}`                       | Update student                  |
| PATCH  | `/api/v1/students/{id}`                       | Partial update                  |
| DELETE | `/api/v1/students/{id}`                       | Delete student                  |
| POST   | `/api/v1/students/bulk-delete`                | Bulk delete students            |
| GET    | `/api/v1/students/class/{classId}`            | Get students by class           |
| POST   | `/api/v1/students/{studentId}/assign-parent`  | Assign parent to student        |
| DELETE | `/api/v1/students/{studentId}/remove-parent/{parentId}` | Remove parent from student |

---

## 👨‍👩‍👧 Student-Parent Relationships

| Method | Endpoint                                      | Description                     |
|--------|-----------------------------------------------|---------------------------------|
| GET    | `/api/v1/student-parents`                     | Get all relationships           |
| POST   | `/api/v1/student-parents`                     | Create relationship             |
| GET    | `/api/v1/student-parents/{id}`                | Get specific relationship       |
| DELETE | `/api/v1/student-parents/{id}`                | Delete relationship             |
| GET    | `/api/v1/student-parents/student/{studentId}` | Get parents for student         |
| GET    | `/api/v1/student-parents/parent/{parentId}`   | Get students for parent         |

---

## 👩‍🏫 Teachers

| Method | Endpoint                   | Description           |
|--------|----------------------------|-----------------------|
| GET    | `/api/v1/teachers`         | Get all teachers      |
| POST   | `/api/v1/teachers`         | Create new teacher    |
| GET    | `/api/v1/teachers/{id}`    | Get specific teacher  |
| PUT    | `/api/v1/teachers/{id}`    | Update teacher        |
| PATCH  | `/api/v1/teachers/{id}`    | Partial update        |
| DELETE | `/api/v1/teachers/{id}`    | Delete teacher        |

### Teacher-Specific Endpoints
| Method | Endpoint                         | Description       |
|--------|----------------------------------|-------------------|
| GET    | `/api/v1/teachers/me/classes`    | Get my classes    |
| GET    | `/api/v1/teachers/me/students`   | Get my students   |

---

## 👨‍👧 Parents

| Method | Endpoint                 | Description         |
|--------|--------------------------|---------------------|
| GET    | `/api/v1/parents`        | Get all parents     |
| POST   | `/api/v1/parents`        | Create new parent   |
| GET    | `/api/v1/parents/{id}`   | Get specific parent |
| PUT    | `/api/v1/parents/{id}`   | Update parent       |
| PATCH  | `/api/v1/parents/{id}`   | Partial update      |
| DELETE | `/api/v1/parents/{id}`   | Delete parent       |

### Parent-Specific Endpoints
| Method | Endpoint                     | Description       |
|--------|------------------------------|-------------------|
| GET    | `/api/v1/parents/me/students`| Get my students   |

---

## 👷 Staff Profiles

| Method | Endpoint                     | Description                                  |
|--------|------------------------------|----------------------------------------------|
| GET    | `/api/v1/staff-profiles`     | Get staff profiles (admin: all, others: own) |
| POST   | `/api/v1/staff-profiles`     | Create staff profile (admin only)            |
| GET    | `/api/v1/staff-profiles/{id}`| Get specific profile                         |
| PUT    | `/api/v1/staff-profiles/{id}`| Update profile                               |
| PATCH  | `/api/v1/staff-profiles/{id}`| Partial update                               |
| DELETE | `/api/v1/staff-profiles/{id}`| Delete profile (admin only)                  |

---

## 🚌 Buses

| Method | Endpoint             | Description        |
|--------|----------------------|--------------------|
| GET    | `/api/v1/buses`      | Get all buses      |
| POST   | `/api/v1/buses`      | Create new bus     |
| GET    | `/api/v1/buses/{id}` | Get specific bus   |
| PUT    | `/api/v1/buses/{id}` | Update bus         |
| PATCH  | `/api/v1/buses/{id}` | Partial update     |
| DELETE | `/api/v1/buses/{id}` | Delete bus         |

---

## 🗺️ Routes

| Method | Endpoint              | Description        |
|--------|-----------------------|--------------------|
| GET    | `/api/v1/routes`      | Get all routes     |
| POST   | `/api/v1/routes`      | Create new route   |
| GET    | `/api/v1/routes/{id}` | Get specific route |
| PUT    | `/api/v1/routes/{id}` | Update route       |
| PATCH  | `/api/v1/routes/{id}` | Partial update     |
| DELETE | `/api/v1/routes/{id}` | Delete route       |

---

## 🚌 Stops

| Method | Endpoint             | Description        |
|--------|----------------------|--------------------|
| GET    | `/api/v1/stops`      | Get all stops      |
| POST   | `/api/v1/stops`      | Create new stop    |
| GET    | `/api/v1/stops/{id}` | Get specific stop  |
| PUT    | `/api/v1/stops/{id}` | Update stop        |
| PATCH  | `/api/v1/stops/{id}` | Partial update     |
| DELETE | `/api/v1/stops/{id}` | Delete stop        |

---

## 💳 Payments

| Method | Endpoint                | Description          |
|--------|-------------------------|----------------------|
| GET    | `/api/v1/payments`      | Get all payments     |
| POST   | `/api/v1/payments`      | Create new payment   |
| GET    | `/api/v1/payments/{id}` | Get specific payment |
| PUT    | `/api/v1/payments/{id}` | Update payment       |
| PATCH  | `/api/v1/payments/{id}` | Partial update       |
| DELETE | `/api/v1/payments/{id}` | Delete payment       |

---

## 📝 Attendances

| Method | Endpoint                  | Description            |
|--------|---------------------------|------------------------|
| GET    | `/api/v1/attendances`     | Get all attendances    |
| POST   | `/api/v1/attendances`     | Create new attendance  |
| GET    | `/api/v1/attendances/{id}`| Get specific attendance|
| PUT    | `/api/v1/attendances/{id}`| Update attendance      |
| PATCH  | `/api/v1/attendances/{id}`| Partial update         |
| DELETE | `/api/v1/attendances/{id}`| Delete attendance      |

---

## ⚠️ Alerts

| Method | Endpoint               | Description          |
|--------|------------------------|----------------------|
| GET    | `/api/v1/alerts`       | Get all alerts       |
| POST   | `/api/v1/alerts`       | Create new alert     |
| GET    | `/api/v1/alerts/{id}`  | Get specific alert   |
| PUT    | `/api/v1/alerts/{id}`  | Update alert         |
| PATCH  | `/api/v1/alerts/{id}`  | Partial update       |
| DELETE | `/api/v1/alerts/{id}`  | Delete alert         |

---

## 📢 Announcements

| Method | Endpoint                    | Description             |
|--------|-----------------------------|-------------------------|
| GET    | `/api/v1/announcements`     | Get all announcements   |
| POST   | `/api/v1/announcements`     | Create new announcement |
| GET    | `/api/v1/announcements/{id}`| Get specific announcement|
| PUT    | `/api/v1/announcements/{id}`| Update announcement     |
| PATCH  | `/api/v1/announcements/{id}`| Partial update          |
| DELETE | `/api/v1/announcements/{id}`| Delete announcement     |

---

## 🧪 Testing

| Method | Endpoint        | Description     |
|--------|-----------------|-----------------|
| GET    | `/api/v1/test`  | Test endpoint   |

---

## 📚 Interactive Documentation

Explore and test all endpoints interactively with **Swagger UI**:

🔗 [Interactive API Documentation](#) *(Replace with your actual Swagger URL)*

> 💡 **Note**: The API follows RESTful principles, returns proper HTTP status codes, and uses consistent JSON responses.

---

## 🛡️ Role-Based Access Control

| Role      | Permissions                                                                 |
|-----------|-----------------------------------------------------------------------------|
| **Admin** | Full access to all resources                                                |
| **Teacher**| Manage classes/students, view their data                                   |
| **Parent** | View assigned students, manage their profile                               |
| **Student**| View own information                                                       |
| **Driver** | View assigned bus/route, manage own profile                               |
| **Cleaner**| View assigned bus, manage own profile                                     |

---

> ✨ **Happy Coding!** This API is designed for seamless integration with school bus management systems.
```

### To use this file:
1. Save it as `API.md` in your repository root
2. Replace `[#]` in the Swagger link with your actual documentation URL
3. Consider adding a badge for your Swagger docs if available:

```md
[![Swagger Docs](https://img.shields.io/badge/API-Swagger-blue)](your-swagger-url)
```

This format provides:
- Clean visual hierarchy with emojis for quick scanning
- Consistent table structure for all endpoints
- Clear role-based permissions section
- Professional GitHub-ready formatting
- Mobile-responsive tables
- Easy maintenance for future endpoint additions

