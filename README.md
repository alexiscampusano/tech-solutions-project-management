# Project Management System - Tech Solutions

This is a comprehensive project management system built with Laravel, Docker, and Vite. It allows users to perform CRUD operations on projects, features robust error handling, and includes real-time UF (Chilean Unidad de Fomento) value integration via an external API.

## Features

-   **Dockerized Environment**: The entire application stack (PHP, Apache, MySQL) runs in Docker containers.
-   **RESTful API**: Complete API endpoints for managing projects (`/api/proyectos`) with robust error handling.
-   **Web Interface**: Modern web views for creating, reading, updating, and deleting projects.
-   **External API Integration**: A reusable component that fetches and displays the daily value of the Chilean UF from an external service.
-   **Modern Frontend Workflow**: Uses Vite for fast and efficient asset compilation.
-   **Professional Error Handling**: Consistent error responses with appropriate HTTP status codes.
-   **Sample Data**: Pre-populated with realistic project data for testing and demonstration.

## Technologies Used

-   **Backend**: PHP 8.2, Laravel 11
-   **Frontend**: JavaScript, Alpine.js, Tailwind CSS
-   **Database**: MySQL 8.0
-   **Web Server**: Apache
-   **Containerization**: Docker & Docker Compose
-   **Development Tooling**: Vite
-   **External APIs**: mindicador.cl (UF values)

---

## 🚀 Getting Started

Follow these instructions to get the project up and running on your local machine.

### Prerequisites

-   [Docker](https://www.docker.com/get-started) installed and running.
-   [Docker Compose](https://docs.docker.com/compose/install/) installed.
-   A command-line interface (Terminal, PowerShell, etc.).

### Installation Steps

**1. Clone the Repository**

First, clone this repository to your local machine.

```bash
git clone https://github.com/alexiscampusano/tech-solutions-project-management
cd tech-solutions-project-management
```

**2. Create the Environment File**

The project uses an `.env` file for all environment variables. An example file is provided, which you need to copy.

```bash
cp .env.example .env
```

**3. Generate the Application Key**

Laravel requires a unique application key for security. Run the following command to generate it. The command runs `php artisan key:generate` inside a temporary Docker container.

```bash
docker-compose run --rm app php artisan key:generate
```
_This command will automatically update the `APP_KEY` variable in your `.env` file._

**4. Build and Start the Docker Containers**

This command will build the Docker images and start all the necessary services (app, database, phpmyadmin) in the background.

```bash
docker-compose up -d --build
```
-   `app`: The Laravel application running on Apache.
-   `db`: The MySQL database.
-   `phpmyadmin`: A web interface to manage the database.

**5. Run Database Migrations and Seeders**

With the containers running, you need to set up the database schema and populate it with initial data.

```bash
docker-compose exec app php artisan migrate:fresh --seed
```
-   `migrate:fresh`: Drops all existing tables and re-runs all migrations.
-   `--seed`: Runs the database seeders to populate the tables with test data.

**6. Start the Vite Development Server**

This is a **critical step** for the frontend. This command compiles the JavaScript and CSS assets and keeps watching for changes.

**You must run this in a separate terminal window and keep it running while you are developing.**

```bash
docker-compose exec app npm run dev
```

---

## ✅ Accessing the Application

Once all the steps are completed, you can access the different parts of the application:

-   **🌐 Main Application**: [http://localhost:8000](http://localhost:8000)
-   **🗃️ Database (phpMyAdmin)**: [http://localhost:8080](http://localhost:8080)
    -   **Server**: `db`
    -   **Username**: `laraveluser`
    -   **Password**: `secret`

-   **⚡ Vite Dev Server**: Running in your terminal, typically on `http://localhost:5173`.

## Sample Data

The application comes pre-populated with 8 realistic project examples:
- Sistema de Gestión de Inventario (In Progress)
- Aplicación Móvil de Ventas (Completed)
- Portal Web Corporativo (Started)
- Sistema de Facturación Electrónica (Completed)
- Plataforma de E-learning (In Progress)
- API de Integración CRM (Started)
- Sistema de Monitoreo IoT (Cancelled)
- Dashboard Ejecutivo BI (In Progress)

Each project includes realistic data like amounts, responsible persons, dates, and different states.

## API Endpoints

The application exposes comprehensive API endpoints under the `/api` prefix:

### Projects
-   `GET /api/proyectos`: List all projects.
-   `POST /api/proyectos`: Create a new project.
-   `GET /api/proyectos/{id}`: Get a single project.
-   `PUT /api/proyectos/{id}`: Update a project.
-   `DELETE /api/proyectos/{id}`: Delete a project.
-   `GET /api/proyectos-estados`: Get available project states.

### UF (Unidad de Fomento) Service
-   `GET /api/uf`: Get the current UF value.
-   `GET /api/uf/fecha/{date}`: Get UF value for a specific date.
-   `POST /api/uf/convertir`: Convert pesos to UF (JSON body: `{"monto": 50000}`).
-   `DELETE /api/uf/cache`: Clear UF cache.
-   `GET /api/uf/cache`: Check cache status.

## Error Handling

The API includes comprehensive error handling with consistent response formats:

### Error Response Format
```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Human-readable error message",
    "details": {
      // Additional error information
    }
  }
}
```

### Error Types Handled
- **404**: Resource not found (`PROYECTO_NOT_FOUND`, `ROUTE_NOT_FOUND`)
- **405**: Method not allowed (`METHOD_NOT_ALLOWED`)
- **422**: Validation errors (`VALIDATION_ERROR`)
- **500**: Database and internal server errors (`DATABASE_ERROR`, `INTERNAL_ERROR`)

## Testing the API

You can test the API using curl commands:

```bash
# Get all projects
curl -H "Accept: application/json" http://localhost:8000/api/proyectos

# Get a specific project
curl -H "Accept: application/json" http://localhost:8000/api/proyectos/1

# Test error handling (non-existent project)
curl -H "Accept: application/json" http://localhost:8000/api/proyectos/999

# Create a new project
curl -X POST -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"nombre":"Test Project","fecha_inicio":"2025-08-01","estado":"iniciado","responsable":"Test User","monto":1000000}' \
  http://localhost:8000/api/proyectos

# Get current UF value
curl -H "Accept: application/json" http://localhost:8000/api/uf
```

---

## Troubleshooting

-   **`net::ERR_CONNECTION_REFUSED` in the browser console:** This usually means the Vite development server (`npm run dev`) is not running. Make sure to run it in a separate terminal.
-   **Changes in `.js` or `.css` files not appearing:** Ensure the Vite server is running. If it is, try restarting it.
-   **Database Errors:** If you encounter database errors, running `docker-compose exec app php artisan migrate:fresh --seed` can often resolve issues by resetting the database to a clean state.
-   **UF Widget not appearing:** Make sure both `docker-compose up -d` and `npm run dev` are running. The widget requires compiled JavaScript assets.
-   **API returns HTML instead of JSON:** Ensure you're accessing `/api/*` routes and not web routes. API routes always return JSON.

## Project Structure

```
├── app/
│   ├── Exceptions/          # Custom exception classes
│   ├── Http/Controllers/    # API and web controllers
│   ├── Models/             # Eloquent models
│   └── Services/           # Business logic services
├── database/
│   ├── migrations/         # Database schema definitions
│   └── seeders/           # Sample data seeders
├── resources/
│   ├── js/                # Frontend JavaScript components
│   ├── views/             # Blade templates
│   └── css/               # Stylesheets
├── routes/
│   ├── api.php            # API routes
│   └── web.php            # Web routes
└── docker-compose.yml     # Docker services configuration
```

---

## Development Notes

- The application follows Laravel's MVC pattern with additional service layer for business logic.
- API responses are consistent and follow REST principles.
- Error handling is comprehensive and developer-friendly.
- The UF component is reusable and can be easily integrated into other parts of the application.
- All database operations are wrapped in proper transaction handling.
