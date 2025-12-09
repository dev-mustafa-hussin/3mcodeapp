# Backend Handover & Frontend Integration Plan

## 1. Project Status: Backend Deployed ✅

We have successfully built, deployed, and verified the Laravel API Backend for the **EDOXO-PRO (Alarabia Cosmetics)** project.

### 🌐 Deployment Details

-   **Server Host:** Hostinger (Shared Hosting)
-   **Domain:** `https://alarabia-cosmetics.com`
-   **API Base URL:** `https://alarabia-cosmetics.com/api/v1`
-   **Server Path:** `~/domains/alarabia-cosmetics.com`
    -   **Project Root:** Contains full Laravel files (`app`, `bootstrap`, `vendor`, etc.)
    -   **Public Folder:** Mapped to `public_html` via symlink or direct content placement.
    -   **.htaccess:** Custom configured to handle Authorization headers and routing on Hostinger.

### 🛠 Technical Configuration Performed

1.  **Database:**
    -   MySQL Database connected.
    -   Migrations run successfully (`users`, `products`, `invoices`, etc.).
    -   Seeder executed (20 dummy products).
2.  **Authentication:**
    -   **Laravel Sanctum** installed and configured.
    -   `stateful` domains configured in `.env`.
    -   `cors` paths configured to allow frontend access.
3.  **Server Fixes (SSH):**
    -   Fixed file permissions (`chmod 755` for dirs, `644` for files).
    -   Fixed `.htaccess` to handle `Bearer Token` & `ModuleRewrite` issues (403 Forbidden).
    -   Removed temporary migration routes for security.

### 🧪 Verified Endpoints (Postman)

-   **POST** `/api/v1/auth/register` - Creates user & returns Token.
-   **GET** `/api/v1/products` - Returns paginated product list (Requires Bearer Token).

---

## 2. Frontend Integration Plan (Next.js) 🚀

This section outlines how the Frontend Developer (or future you) should proceed to connect the Next.js application to this backend.

### 📋 Prerequisites

-   **Framework:** Next.js 14+ (App Router recommended).
-   **Styling:** Tailwind CSS.
-   **HTTP Client:** Axios (recommended for automatic header handling).

### ⚙️ Step 1: Environment Setup

In your Next.js project, create a `.env.local` file:

```env
NEXT_PUBLIC_API_URL=https://alarabia-cosmetics.com/api/v1
NEXT_PUBLIC_BACKEND_URL=https://alarabia-cosmetics.com
```

### 🔌 Step 2: Axios Configuration

Create a dedicated Axios instance (`lib/axios.js`) to handle the Base URL and Tokens automatically.

```javascript
import axios from "axios";

const api = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_URL,
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
    withCredentials: true, // Important for Sanctum cookies if using SPA mode
});

// Add interceptor to inject Token if stored in localStorage/Cookies
api.interceptors.request.use((config) => {
    const token = localStorage.getItem("token");
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export default api;
```

### 🔐 Step 3: Authentication Flow

1.  **Login Page:**
    -   Form accepts `email` & `password`.
    -   On Submit -> POST `/auth/login`.
    -   **Success:** Save `access_token` to LocalStorage (or HttpOnly Cookie via Server Action) & Redirect to Dashboard.
    -   **Error:** Show validation message.
2.  **Register Page:**
    -   Form accepts `name`, `email`, `password`, `password_confirmation`.
    -   On Submit -> POST `/auth/register`.

### 📦 Step 4: Data Fetching (Products Example)

Use the configured Axios instance to fetch data.

```javascript
// Example in a Component
import { useEffect, useState } from 'react';
import api from '@/lib/axios';

export default function ProductsList() {
    const [products, setProducts] = useState([]);

    useEffect(() => {
        api.get('/products')
            .then(response => {
                setProducts(response.data.data);
            })
            .catch(error => console.error("Error fetching products", error));
    }, []);

    return (
        // Render products...
    );
}
```

### 🛡 Step 5: Route Protection

-   Create a Higher-Order Component (HOC) or use Middleware to check if the user is authenticated (has token).
-   If no token -> Redirect to `/login`.

---

## 3. Where We Stopped (Handover Note) 🛑

-   **Current State:** Backend is **LIVE** and awaiting frontend connection.
-   **Immediate Next Task:** Initialize the Next.js project and implement the **Login/Register** pages to test the connection "in the real world".
-   **Credentials:** You have a valid token for `Admin User` (id: 2). You can use it to test immediately or register a new user.

**Important Note for Developer:**
If you encounter `403 Forbidden` or `CORS` errors, first check that you are sending the `Authorization: Bearer <token>` header correctly. The server is configured correctly.
