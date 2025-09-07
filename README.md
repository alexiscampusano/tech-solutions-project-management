# Project Management System - Tech Solutions

A comprehensive project management system built with Laravel, JWT authentication, and a modern API-First architecture. This application provides secure user authentication, project CRUD operations with authorization controls, and real-time UF (Chilean Unidad de Fomento) integration.

## ✨ Features

### 🔐 **Authentication & Security**
- **JWT Authentication**: Secure token-based authentication system
- **User Registration & Login**: Complete authentication flow with encrypted passwords
- **Protected Routes**: API endpoints secured with JWT middleware
- **Authorization Controls**: Users can only edit/delete their own projects

### 📊 **Project Management**
- **Complete CRUD Operations**: Create, read, update, and delete projects
- **Owner-based Permissions**: Projects can only be modified by their creators
- **Project States Management**: Predefined states (Iniciado, En Progreso, Completado, Cancelado)
- **Responsive Web Interface**: Modern UI with Tailwind CSS

### 🏗️ **API-First Architecture**
- **RESTful API**: Clean, consistent API endpoints following REST principles
- **Standardized Responses**: Uniform JSON response format across all endpoints
- **Service Layer**: Business logic separated from controllers for maintainability
- **Error Handling**: Comprehensive error handling with appropriate HTTP status codes

### 💰 **UF Integration**
- **Real-time UF Values**: Integration with mindicador.cl API
- **Currency Conversion**: Convert Chilean pesos to UF
- **Historical Data**: Query UF values by specific dates
- **Smart Caching**: Optimized performance with intelligent cache management

### 🎨 **Modern Frontend**
- **Tailwind CSS**: Beautiful, responsive design
- **Alpine.js**: Reactive components for enhanced UX
- **Vite**: Fast development and optimized builds
- **Real-time Updates**: Dynamic content loading without page refreshes

## 🛠️ Technologies Used

- **Backend**: PHP 8.2, Laravel 11, JWT Auth
- **Frontend**: JavaScript, Alpine.js, Tailwind CSS
- **Database**: MySQL 8.0
- **Authentication**: JWT (JSON Web Tokens)
- **Containerization**: Docker & Docker Compose
- **Development**: Vite, Hot Module Replacement
- **External APIs**: mindicador.cl (UF values)

---

## 🚀 Getting Started

### Prerequisites

- [Docker](https://www.docker.com/get-started) installed and running
- [Docker Compose](https://docs.docker.com/compose/install/) installed
- Command-line interface (Terminal, PowerShell, etc.)

### Installation Steps

**1. Clone the Repository**

```bash
git clone https://github.com/alexiscampusano/tech-solutions-project-management
cd tech-solutions-project-management
```

**2. Create Environment File**

```bash
cp .env.example .env
```

**3. Configure Environment Variables**

Update the `.env` file with the following database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=desarrollo_software_1
DB_USERNAME=root
DB_PASSWORD=desarrollo_software_1
```

**4. Generate Application Key**

```bash
docker-compose run --rm app php artisan key:generate
```

**5. Generate JWT Secret**

```bash
docker-compose run --rm app php artisan jwt:secret
```

**6. Build and Start Docker Containers**

```bash
docker-compose up -d --build
```

**7. Run Database Migrations and Seeders**

```bash
docker-compose exec app php artisan migrate:fresh --seed
```

**8. Start Vite Development Server**

**Critical step for frontend development:**

```bash
docker-compose exec app npm run dev
```

---

## 🌐 Accessing the Application

- **🏠 Main Application**: [http://localhost:8000](http://localhost:8000)
- **🗃️ phpMyAdmin**: [http://localhost:8081](http://localhost:8081)
  - **Server**: `db`
  - **Username**: `root`
  - **Password**: `desarrollo_software_1`
- **⚡ Vite Dev Server**: Typically running on `http://localhost:5173`

## 👥 Sample Users

The application comes with pre-configured test users:

```
📧 Email: juan.perez@empresa.com
🔑 Password: password123

📧 Email: maria.gonzalez@empresa.com  
🔑 Password: password123
```

## 📋 Sample Projects

Pre-populated with realistic project data:
- Sistema de Gestión de Inventario (En Progreso)
- Plataforma E-commerce (Iniciado)
- Portal Web Corporativo (Completado)
- Sistema de Facturación (Cancelado)

---

## 🔌 API Documentation

### 🔐 Authentication Endpoints

#### Public Routes (No Authentication Required)
```bash
POST /api/auth/register    # Register new user
POST /api/auth/login       # User login
```

#### Protected Routes (JWT Required)
```bash
POST /api/auth/logout      # Logout user
GET  /api/auth/me          # Get authenticated user data
POST /api/auth/refresh     # Refresh JWT token
```

### 📊 Project Management Endpoints

#### Public Routes
```bash
GET /api/proyectos         # List all projects
GET /api/proyectos/{id}    # Get specific project
GET /api/proyectos/estados # Get available project states
```

#### Protected Routes (JWT Required)
```bash
POST   /api/proyectos      # Create new project
PUT    /api/proyectos/{id} # Update project (owners only)
DELETE /api/proyectos/{id} # Delete project (owners only)
```

### 💰 UF (Unidad de Fomento) Endpoints

```bash
GET    /api/uf             # Get current UF value
GET    /api/uf/date        # Get UF value by date (?fecha=2025-01-01)
POST   /api/uf/convert     # Convert pesos to UF
DELETE /api/uf/cache       # Clear UF cache
GET    /api/uf/cache       # Get cache status
```

---

## 🧪 Testing the API

### Authentication Flow

**1. Register a New User**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**2. Login**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**3. Use JWT Token for Protected Routes**
```bash
# Replace YOUR_JWT_TOKEN with the token from login response
curl -H "Authorization: Bearer YOUR_JWT_TOKEN" \
     -H "Accept: application/json" \
     http://localhost:8000/api/auth/me
```

### Project Management

**Get All Projects**
```bash
curl -H "Accept: application/json" http://localhost:8000/api/proyectos
```

**Create New Project (Requires Authentication)**
```bash
curl -X POST http://localhost:8000/api/proyectos \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nombre": "Mi Nuevo Proyecto",
    "fecha_inicio": "2025-01-15",
    "estado": "iniciado",
    "responsable": "Juan Pérez",
    "monto": 1500000
  }'
```

### UF Integration

**Get Current UF Value**
```bash
curl -H "Accept: application/json" http://localhost:8000/api/uf
```

**Convert Pesos to UF**
```bash
curl -X POST http://localhost:8000/api/uf/convert \
  -H "Content-Type: application/json" \
  -d '{"monto": 1000000}'
```

---

## 🎨 Frontend Features

### User Authentication
- **Login Page**: `/login` - User authentication with JWT
- **Register Page**: `/register` - New user registration
- **Auto-redirect**: Automatic redirection based on authentication status

### Project Management
- **Dashboard**: `/proyectos` - List all projects with search and filters
- **Create Project**: `/proyectos/create` - Form to create new projects
- **View Details**: `/proyectos/{id}` - Detailed project information
- **Edit Project**: `/proyectos/{id}/edit` - Edit form (owners only)

### UF Widget
- **Real-time Display**: Current UF value in navigation
- **Currency Converter**: Convert pesos to UF interactively
- **Auto-refresh**: Periodic updates of UF values

---

## 🔒 Security Features

### JWT Authentication
- **Token-based Security**: Stateless authentication using JWT
- **Token Expiration**: Configurable token lifetime (default: 60 minutes)
- **Refresh Mechanism**: Automatic token refresh for extended sessions
- **Secure Logout**: Token invalidation on logout

### Authorization
- **Role-based Access**: Users can only modify their own projects
- **Protected Endpoints**: API routes secured with JWT middleware
- **Input Validation**: Comprehensive request validation
- **Password Hashing**: Secure password storage using bcrypt

### Error Handling
```json
{
  "success": false,
  "message": "Error description",
  "error": "Technical details"
}
```

---

## 📁 Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/     # API controllers (JWT protected)
│   │   ├── Requests/           # Form request validation
│   │   └── Middleware/         # Custom middleware
│   ├── Models/                 # Eloquent models
│   └── Services/              # Business logic layer
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/              # Sample data
├── resources/
│   ├── js/                   # Frontend JavaScript
│   ├── views/               # Blade templates
│   │   ├── auth/           # Authentication views
│   │   └── proyectos/      # Project management views
│   └── css/                # Stylesheets
├── routes/
│   ├── api.php             # API routes (JWT protected)
│   └── web.php             # Web routes
└── config/
    └── jwt.php             # JWT configuration
```

---

## 🐛 Troubleshooting

### Common Issues

**Authentication Issues**
- Ensure JWT secret is generated: `php artisan jwt:secret`
- Check token expiration in browser's localStorage
- Verify `Authorization: Bearer <token>` header format

**Database Connection**
- Confirm database credentials in `.env` file
- Ensure Docker containers are running: `docker-compose ps`
- Reset database if needed: `php artisan migrate:fresh --seed`

**Frontend Issues**
- Vite server must be running: `npm run dev`
- Check browser console for JavaScript errors
- Clear browser cache if assets aren't updating

**UF Widget Not Working**
- Verify internet connection for external API calls
- Check API cache status: `GET /api/uf/cache`
- Clear UF cache if stale: `DELETE /api/uf/cache`

### Development Tips

**JWT Debugging**
```bash
# Check if JWT is working
curl -H "Authorization: Bearer <token>" http://localhost:8000/api/auth/me

# Refresh expired token
curl -X POST -H "Authorization: Bearer <token>" http://localhost:8000/api/auth/refresh
```

**Database Reset**
```bash
# Complete database reset with fresh data
docker-compose exec app php artisan migrate:fresh --seed
```

---

## 🎯 Architecture Highlights

### API-First Design
- **Decoupled Frontend/Backend**: Complete separation for scalability
- **Consistent JSON Responses**: Standardized format across all endpoints
- **RESTful Conventions**: Proper HTTP methods and status codes
- **Service Layer**: Business logic abstracted from controllers

### Modern Development Practices
- **Clean Code**: Well-organized, documented, and maintainable
- **Error Handling**: Comprehensive exception management
- **Security First**: JWT authentication and authorization controls
- **Performance Optimized**: Caching, eager loading, and efficient queries

### Scalability Ready
- **Microservice Friendly**: API can serve multiple frontend applications
- **Docker Containerized**: Easy deployment and scaling
- **Environment Configured**: Separate configurations for different environments
- **Database Optimized**: Proper indexes and relationships

---

## 📊 Response Format Standards

### Success Response
```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Operation completed successfully"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "error": "Technical error details"
}
```

### Authentication Response
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
  },
  "message": "Login successful"
}
```

---

## 🔄 Development Workflow

1. **Start Development Environment**
   ```bash
   docker-compose up -d
   docker-compose exec app npm run dev
   ```

2. **Make Changes**
   - Backend: Controllers, Models, Services in `app/`
   - Frontend: JavaScript/CSS in `resources/`
   - Database: Migrations in `database/migrations/`

3. **Test Changes**
   - API: Use curl or Postman for endpoint testing
   - Frontend: Browser testing with hot reload
   - Database: phpMyAdmin for data inspection

4. **Commit Changes**
   ```bash
   git add .
   git commit -m "feat: description of changes"
   ```

