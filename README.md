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

## Getting Started

To get started with this project:

```bash
git clone git@github.com:subtain-haider/task-management.git
cd task-management-api
composer install
php artisan migrate --seed
php artisan serve
```

Make sure to set up your `.env` file with appropriate database and Sanctum configurations.
