# Task Management API

A RESTful API backend for a task management application built with **Laravel 12**. This API provides secure authentication and task management functionality.

## Features

- 🔐 **Token-based Authentication** with Laravel Sanctum  
- ✅ **Task CRUD Operations** with user relationships  
- 🔍 **Advanced Filtering** by status, due date, and search terms  
- 📄 **Pagination** for efficient data handling  
- 🧪 **Comprehensive Testing** using Pest  

## Architecture

This application is built using a **Clean Architecture** approach with the **Service Repository Pattern** to ensure separation of concerns and maintainability.

### Request Flow

```
HTTP Request → Controller → Service → Repository → Database
```

### Layers

1. **Controllers** – Handle HTTP requests and return responses  
2. **Services** – Contain business logic  
3. **Repositories** – Manage database access  
4. **Models** – Represent database entities  

## Key Components

- 📥 **Form Requests** – Centralize request validation logic  
- 📦 **API Resources** – Consistent and clean response formatting  
- 🔐 **Policies** – Handle user authorization  
- 🧪 **Factories & Seeders** – Generate sample and test data  
- ⚙️ **Service Providers** – Bind interfaces to concrete implementations  

## Authentication

Authentication is implemented using **Laravel Sanctum** for secure, token-based access.

- Users receive tokens upon login
- Requests include tokens in the `Authorization` header
- Authorization policies ensure users can only access their own tasks

## Testing

The test suite uses the **Pest** testing framework and covers:

- 🔐 Authentication flows  
- ✏️ CRUD operations  
- ✅ Authorization rules  
- 🔍 Filtering functionality  
- 📥 Data validation  

## Getting Started Without Docker

To get started with this project:

```bash
git clone git@github.com:subtain-haider/task-management.git
cd task-management
composer install
cp .env.example .env
# Make sure to update your .env file with correct DB and other settings
php artisan key:generate 
php artisan migrate --force
# Optional: Run seeder
# php artisan db:seed --force
# Optional: Run tests
php artisan test
php artisan serve
```

Make sure to set up your `.env` file with appropriate database and Sanctum configurations.


## Docker Setup

This application can be run using Docker for both development and production environments.

### Prerequisites

- Docker and Docker Compose installed on your system
- Git

### Quick Start With Docker

1. Clone the repository: 
```bash
git clone git@github.com:subtain-haider/task-management.git
cd task-management
```

2. Start the application:

For development:
```bash
docker compose up -d
```
The default app will be accessible at [http://localhost:8000](http://localhost:8000)  
The MySQL database runs on port `3307` by default.

> ℹ️ **Note**: If port `8000` (app) or `3307` (MySQL) are already in use on your system, you can update them in docker files. Make sure to also adjust these in your frontend or any client that connects to this backend.

### Environment

If you're using Docker, the `.env` file will be automatically created from `.env.example` on the first run.  
If you need to customize it (e.g., for SMTP, Redis, etc.), just update the `.env` file before running the containers.