# User Management Module (CodeIgniter 3)

[![Framework](https://img.shields.io/badge/Framework-CodeIgniter%203.x-EE4326?style=for-the-badge&logo=codeigniter&logoColor=white)](https://codeigniter.com/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%20--%208.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Frontend-Bootstrap%205-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![jQuery](https://img.shields.io/badge/Library-jQuery%203.6-0769AD?style=for-the-badge&logo=jquery&logoColor=white)](https://jquery.com/)

A secure, role-based User Management System developed with **CodeIgniter 3 (MVC)**, **MySQL**, **Bootstrap 5**, and **jQuery / AJAX**.

---

## 📌 Project Overview

This application fulfills all technical requirements of the Developer Machine Test:
- **Clean MVC Architecture** following CodeIgniter 3 standards.
- **Role-Based Access Control (RBAC)** for `ADMIN` and `OPERATOR` with strict server-side validation.
- **Real-Time Asynchronous Operations** using jQuery AJAX for instant status toggling (`ACTIVE` / `INACTIVE`).
- **Cryptographic Security** with PHP native `password_hash()` (Bcrypt) and `password_verify()`.
- **Search, Multi-Filtering & Dynamic Pagination**.

---

## 🔐 Role-Based Access Control (RBAC) Matrix

| Feature / Action | ADMIN | OPERATOR | Server-Side Enforcement |
| :--- | :---: | :---: | :--- |
| **Login & View Dashboard** | ✅ | ✅ | Session authentication guard (`Auth_Controller`). |
| **Search & Multi-Filter Users** | ✅ | ✅ | Sanitized GET query binding in `User_model`. |
| **Create New User** | ✅ | ❌ | `require_admin()` stops direct URL/POST attempts. |
| **Edit Name & Email** | ✅ | ✅ | Role-aware form validation with unique email callback. |
| **Change Role & Status** | ✅ | ❌ | Controller ignores injected role/status payload from Operators. |
| **Delete / Deactivate User** | ✅ | ❌ | Restricted to Admin; prevents self-deletion. |
| **AJAX Status Switch** | ✅ | ❌ | Restricts endpoint to authenticated Admins with JSON status response. |

---

## 🚀 Installation & Setup Guide

### 1. Prerequisites
- **Web Server:** Apache (XAMPP / WAMP / Laragon)
- **PHP Version:** PHP 7.4.x, 8.0.x, 8.1.x, or 8.2.x+
- **Database:** MySQL 5.7+ / MariaDB 10.4+

---

## Step 1: Start XAMPP Servers
1. Open the **XAMPP Control Panel** on your computer.
2. Click the **Start** button next to **Apache**.
3. Click the **Start** button next to **MySQL**.
4. Both **Apache** and **MySQL** should now show green background/status.
---
## Step 2: Place Project Files
1. Open your computer's file explorer.
2. Navigate to your XAMPP web root folder:
   - **Windows:** `C:\xampp\htdocs\`
3. Create a folder named `ci3_project` or paste this project repository inside it so the path looks like:
4. Verify that inside `C:\xampp\htdocs\ci3_project\`, you can see folders like `application`, `system`, and files like `index.php` and `database.sql`.
---
## Step 3: Setup the Database (phpMyAdmin)
### A. Open phpMyAdmin:
1. Open your web browser.
2. In the address bar, type:
   http://localhost/phpmyadmin/
and press **Enter**.
### B. Create Database:
1. Click on the **Databases** tab at the top.
2. Under **Create database**, in the **Database name** box, type:
   ci3_user_management
3. Click the **Create** button.
### C. Import the Database File:
1. On the left sidebar, click on your newly created database **`ci3_user_management`**.
2. Click on the **Import** tab in the top navigation bar.
3. Click **Choose File** (or *Browse*).
4. Navigate to your project folder: `C:\xampp\htdocs\ci3_project\` and select the **`database.sql`** file.
5. Scroll down to the bottom and click **Import** (or **Go**).
6. You should see a green success message: *"Import has been successfully finished."*
---
## Step 4: Configure the Project Settings
### 1. Database Connection (`application/config/database.php`)
Open the file `application/config/database.php` in any code editor (Notepad, VS Code, Sublime) and verify these lines (around line 75):
```php
'hostname' => 'localhost',
'username' => 'root',          // Default XAMPP username is 'root'
'password' => '',              // Default XAMPP password is empty (leave blank)
'database' => 'ci3_user_management', // Database name created in Step 3

### Step 5: Run the Application in Browser

1. Open your web browser.
2. Enter this URL in the address bar:
   ```text
   http://localhost/ci3_project/
   ```
3. You will see the Sign In screen.

---

## 🔑 Default Test Logins

Use these accounts to test the application:

### 1. Admin Account (Full Permissions)
* **Email:** `admin@example.com`
* **Password:** `Admin@123`
* **What you can test:**
  * Create new users.
  * Edit all user details (including changing roles and statuses).
  * Toggle user ACTIVE / INACTIVE status instantly using the AJAX switch.
  * Delete / deactivate users.
  * Search and filter by Role or Status.

---

### 2. Operator Account (Restricted Permissions)
* **Email:** `operator@example.com`
* **Password:** `Admin@123`
* **What you can test:**
  * View the list of all users.
  * Search and filter users.
  * Edit Name and Email only.
  * Cannot see or use the "Add User" button.
  * Cannot change user roles or status.
  * Cannot delete users.
