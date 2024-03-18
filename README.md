
## Requirements
- PHP 8.3 or greater.

# Task Management

Task Management App is a web application built with Laravel that allows users to manage their tasks efficiently.




## Features

- Task CRUD Operations: Create, Read, Update, and Delete tasks.
- User Authentication: Secure user authentication system.
- Task Assignment: Assign tasks to specific users.
- Task Filtering and Sorting: Filter tasks based on status or due date, and sort tasks by various criteria.
- RESTful API: Provides a RESTful API for accessing tasks programmatically.


## Installation

Clone the repository:

```bash
  git clone https://github.com/mahmoudaboelela/task-management.git
```

Navigate to the project directory:

```bash
 cd task-management
```

Install dependencies:

```bash
 composer install
```

Create a .env file by copying the .env.example file:

```bash
 cp .env.example .env
```

Generate an application key:

```bash
 php artisan key:generate
```

Configure your database settings in the .env file.



Run database migrations to create tables:

```bash
php artisan migrate
```

Seed the database with sample data:

```bash
 php artisan db:seed
```

Create a personal access token for Passport:

```bash
 php artisan passport:client --personal
```

Serve the application:


```bash
 php artisan serve
```

## Demo Users

| Email | Password     | Type                |
| :-------- | :------- | :------------------------- |
| `manager@app.com` | `12345678` | **Manager** |
 `ahmed@app.com` | `12345678` | **User** |
  `mohamed@app.com` | `12345678` | **User** |
   `nada@app.com` | `12345678` | **User** |

