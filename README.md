# Courier Management API

A RESTful API for managing courier master data, built with **Laravel 12**. This project was developed as part of a Backend Developer technical assessment.

## Features

- Complete CRUD operations for courier management.
- Paginated courier listing (10 records per page).
- Default alphabetical sorting by courier name.
- Optional sorting by registration date.
- Multi-keyword courier name search.
- Filtering by multiple courier levels.
- Input validation for creating and updating couriers.
- Automated feature testing.

## Tech Stack

| Technology | Description |
|---|---|
| PHP 8.2+ | Backend programming language |
| Laravel 12 | PHP framework |
| MySQL | Relational database |
| Eloquent ORM | Database interaction |
| PHPUnit | Automated testing |
| Postman | API testing |

## Requirements

Before installing this project, ensure you have:

- PHP 8.2 or higher
- Composer
- MySQL
- PHP extensions required by Laravel
- Git

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/YOUR_USERNAME/courier-management.git

cd courier-management
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

For Windows CMD:

```cmd
copy .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

### 4. Configure Database

Create a MySQL database named:

```text
courier_management
```

Update your `.env` configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=courier_management
DB_USERNAME=root
DB_PASSWORD=
```

Adjust the credentials according to your local environment.

### 5. Run Database Migrations

```bash
php artisan migrate
```

### 6. Start the Development Server

```bash
php artisan serve
```

The API will be available at:

```text
http://127.0.0.1:8000/api
```

---


## API Documentation

**Base URL**

```text
http://127.0.0.1:8000/api
```

### Available Endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/couriers` | Retrieve all couriers |
| GET | `/couriers/{id}` | Retrieve courier details |
| POST | `/couriers` | Create a new courier |
| PUT/PATCH | `/couriers/{id}` | Update courier information |
| DELETE | `/couriers/{id}` | Delete a courier |

