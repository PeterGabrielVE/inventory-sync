📦 Inventory Management System

An inventory synchronization and management platform built with Laravel, MySQL, and Docker.
This project enables efficient stock updates, product tracking, and inventory log monitoring.

📚 Table of Contents

Setup Instructions

API Specification

Update Product Stock

Get Inventory Logs

Architectural Rationale

Frontend Integration

🧰 Setup Instructions

This project uses:

Backend: Laravel (PHP 8+)

Database: MySQL

Containerization: Docker & Docker Compose

✅ Prerequisites

Docker
 >= 20.x

Docker Compose
 >= 1.29.x

Git

🚀 Installation Steps

Clone the Repository

git clone https://github.com/your-username/inventory-sync.git
cd inventory-sync


Copy and Configure Environment Variables

cp backend/.env.example backend/.env


Update backend/.env as needed:

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=inventory
DB_USERNAME=inventory_user
DB_PASSWORD=inventory_pass

SESSION_DRIVER=file


Build and Start Containers

docker-compose up -d --build


Install PHP Dependencies

docker-compose exec backend composer install


Run Database Migrations

docker-compose exec backend php artisan migrate


(Optional) Seed Initial Data

docker-compose exec backend php artisan db:seed


Access the Application

Backend API: http://localhost:8000/api

Frontend Dev Server: http://localhost:5500

WordPress (if applicable): http://localhost:8080

Backend shell:

docker-compose exec backend bash

🧾 API Specification
🔸 Update Product Stock

Endpoint

PATCH /api/products/{id}/stock


Request Body Example

{
  "new_stock": 10,
  "operation": "add",
  "source": "API",
  "note": "Stock adjustment due to supplier"
}


Response Example (200 OK)

{
  "id": 1,
  "name": "Product A",
  "stock": 50
}


Error Responses

Code	Description
404	Product not found
422	Validation error (negative or insufficient stock)
500	Internal server error
🔸 Get Inventory Logs

Endpoint

GET /api/inventory-logs


Query Parameters

Parameter	Type	Optional	Description
from	date	Yes	Start date filter (YYYY-MM-DD)
to	date	Yes	End date filter (YYYY-MM-DD)
product_id	int	Yes	Filter by product ID

Response Example

[
  {
    "id": 1,
    "product_id": 2,
    "product_name": "Product A",
    "old_stock": 50,
    "new_stock": 60,
    "delta": 10,
    "type": "Entrada",
    "source": "API",
    "note": "Stock adjustment",
    "created_at": "15-10-2025 12:34:56"
  }
]

🏗️ Architectural Rationale
🧠 Framework Choice: Laravel

Robust MVC architecture with Eloquent ORM.

Built-in support for transactions, queues, and dependency injection.

Easy extensibility for API versioning and authentication.

🧰 Service & Repository Pattern

InventoryService handles business logic.

ProductRepository & InventoryLogRepository abstract database operations.

Promotes separation of concerns, testability, and clean architecture.

🔐 Transactions

All stock update operations are wrapped in database transactions.

Ensures atomicity: either all operations succeed, or none are applied.

Prevents race conditions and inconsistent inventory states.

⚡ Indexing & Performance

Indexed columns: product_id, created_at in inventory_logs.

Enables faster queries, particularly for filtering logs over time.

🐳 Dockerized Environment

Ensures consistent development and production environments.

Simplifies onboarding and reduces dependency conflicts.

One-command setup for the entire stack.

🧭 Frontend Integration

The backend exposes a RESTful API that can be easily consumed from a jQuery + HTML + CSS frontend.
This allows real-time interaction with the inventory system without needing a modern JavaScript framework.

🧰 Recommended Practices

Use jQuery’s AJAX ($.ajax or $.get/$.post) to make HTTP requests to the API.

Dynamically update the DOM to reflect stock changes without full page reloads.

Handle error states gracefully (e.g., insufficient stock, network errors).

Use the GET /api/inventory-logs endpoint to display stock movement history in tables or dashboards.

🧑‍💻 Contributing

Fork the repository

Create your feature branch (git checkout -b feature/my-feature)

Commit your changes (git commit -m 'Add my feature')

Push to the branch (git push origin feature/my-feature)

Open a Pull Request 🚀