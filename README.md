# Blog API

A RESTful API for a blogging platform built with Laravel 11 and Sanctum authentication.

---

## Tech Stack

- **PHP** 8.3
- **Laravel** 11
- **MySQL**
- **Laravel Sanctum** (Authentication)

---

## Installation

```bash
# Clone the repository
git clone https://github.com/AhmadAlzaza/blog-api.git
cd blog-api

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Configure your database in .env
DB_DATABASE=blog_api
DB_USERNAME=root
DB_PASSWORD=

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Install Sanctum
php artisan install:api

# Start the server
php artisan serve
```

---

## API Endpoints

### Authentication

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/register` | No | Register and receive token |
| POST | `/api/login` | No | Login and receive token |
| POST | `/api/logout` | Yes | Logout current session |

### Posts

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/post` | No | Get all posts (paginated) |
| GET | `/api/post/{id}` | No | Get a single post |
| POST | `/api/post` | Yes | Create a new post |
| PUT | `/api/post/{id}` | Yes | Update a post |
| DELETE | `/api/post/{id}` | Yes | Delete a post |

### Comments

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/posts/{post}/comments` | No | Get all comments for a post |
| GET | `/api/posts/{post}/comments/{comment}` | No | Get a single comment |
| POST | `/api/posts/{post}/comments` | Yes | Add a comment to a post |
| PUT | `/api/posts/{post}/comments/{comment}` | Yes | Update a comment |
| DELETE | `/api/posts/{post}/comments/{comment}` | Yes | Delete a comment |

### Tags

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/tags` | No | Get all tags |
| GET | `/api/posts/{post}/tags/{tag}` | No | Get a tag for a post |
| POST | `/api/posts/{post}/tags` | Yes | Add a tag to a post |
| PUT | `/api/posts/{post}/tags/{tag}` | Yes | Update a tag |
| DELETE | `/api/posts/{post}/tags/{tag}` | Yes | Remove a tag from a post |

---

## Authentication

All protected routes require a Bearer token in the request header:

```
Authorization: Bearer {token}
```

A token is returned automatically upon **register** or **login**.

---

## Postman Collection

A full Postman collection is included in the repository: `blog-api.postman_collection.json`

It contains all 18 endpoints with example requests for:
- Auth (Register, Login, Logout)
- Posts (CRUD)
- Comments (CRUD)
- Tags (CRUD)

To use it: open Postman → Import → select the file.

---

## Notes

- Pagination is applied on all listing endpoints (15 items per page)
- Users can only **update** or **delete** their own posts and comments
- Tags use `firstOrCreate` — adding an existing tag will reuse it instead of creating a duplicate
- Removing a tag from a post does **not** delete the tag itself, only the association
- Posts and comments are linked via nested routes for clear REST structure