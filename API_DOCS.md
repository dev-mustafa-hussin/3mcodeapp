# API Documentation

Base URL: `/api/v1`

## Authentication

### Login

-   **Endpoint:** `POST /auth/login`
-   **Body:**
    ```json
    {
        "email": "user@example.com",
        "password": "password"
    }
    ```
-   **Response:**
    ```json
    {
      "access_token": "1|...",
      "token_type": "Bearer",
      "user": { ... }
    }
    ```

### Register

-   **Endpoint:** `POST /auth/register`
-   **Body:**
    ```json
    {
        "name": "User Name",
        "email": "user@example.com",
        "password": "password",
        "password_confirmation": "password"
    }
    ```

## Customers

### List Customers

-   **Endpoint:** `GET /customers`
-   **Query Params:** `?search=name`
-   **Response:** Paginated list of customers.

### Create Customer

-   **Endpoint:** `POST /customers`
-   **Body:**
    ```json
    {
        "name": "Customer Name",
        "email": "cust@example.com",
        "phone": "123456",
        "address": "Address",
        "tax_number": "TAX123"
    }
    ```

## Products

### List Products

-   **Endpoint:** `GET /products`
-   **Query Params:** `?search=name&category_id=1`
-   **Response:** Paginated list of products.

### Create Product

-   **Endpoint:** `POST /products`
-   **Body:**
    ```json
    {
        "name": "Product Name",
        "sku": "SKU-001",
        "price": 100,
        "cost": 50,
        "stock": 10,
        "category_id": 1
    }
    ```

## Invoices

### Create Invoice

-   **Endpoint:** `POST /invoices`
-   **Description:** Creates invoice, calculates totals, deducts stock.
-   **Body:**
    ```json
    {
        "customer_id": 1,
        "date": "2024-12-09",
        "due_date": "2024-12-16",
        "status": "draft",
        "discount": 0,
        "tax": 0,
        "items": [
            {
                "product_id": 1,
                "quantity": 2,
                "price": 100
            }
        ]
    }
    ```

## Payments

### Add Payment

-   **Endpoint:** `POST /payments`
-   **Body:**
    ```json
    {
        "invoice_id": 1,
        "amount": 200,
        "payment_method": "cash",
        "payment_date": "2024-12-09"
    }
    ```

## Dashboard

-   **Endpoint:** `GET /dashboard/stats`
-   **Response:** Stats overview.
