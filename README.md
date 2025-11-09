
# 🛡️ Compliance Tool (Multi-Framework Evidence Tracker)

A lightweight web-based compliance evidence tracking tool built with PHP and MySQL, supporting multiple frameworks like ISO 27001, PCI DSS, GDPR, and more.

---

## 🛠️ Prerequisites

- [XAMPP](https://www.apachefriends.org/index.html) (includes Apache, MySQL, PHP)
- PHP >= 7.4

---

## 🚀 Setup Instructions

### 1. Create New Database

Create a new MySQL database using **phpMyAdmin** (e.g., `PCI DSS/ISO/SOC2`).

### 2. Import SQL Dump

Import the provided `.sql` file (from `db/` or `sql/` folder) using phpMyAdmin or MySQL CLI.

### 3. Update Password

Update the default password in the users table (if needed). Passwords are hashed using `password_hash()`.

### 4. Configure Database Connection

Open the file `config.php` and update:

```php
// config.php (example)
$host = 'localhost';
$db   = 'your_database_name'; // <-- Line 2
$user = 'your_db_username';   // <-- Line 6 (usually 'root')
$pass = 'your_db_password';   // <-- Line 7 (blank by default in XAMPP)
```

---

## 🔐 Security Concerns

Avoid committing hardcoded credentials in production.

### 🔒 Recommended:
- Use `.env` files and [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv)
- Or use secure secrets managers:
  - [HashiCorp Vault](https://www.vaultproject.io/)
  - [AWS Secrets Manager](https://aws.amazon.com/secrets-manager/)
  - [GCP Secret Manager](https://cloud.google.com/secret-manager)

---

## 📂 Folder Structure

```
├── index.php
├── add_user.php
├── auth.php
├── composer.php
├── config.php
├── controls.php
├── delete_user.php
├── download.php
├── edit_user.php
├── evidence.php
├── footer.php
├── forgot_password.php
├── header.php
├── history.php
├── login.php
├── logout.php
├── report.php
├── reset_password.php
├── upload_excel.php
├── user_management.php
├── /evidence
├── /assets
└── /vendor
```

---

## 👨‍💻 Admin Instructions (IN PROGRESS)

- Only users with `role = 'Admin'` can:
  - Manage Users
  - Add/Delete Projects (Feature toggle)
  - Access Audit Logs (if enabled)

---

## 📌 Features

- 🌐 Tab-based dashboard: Controls, Upload, History, Report
- 🔐 Login with role-based redirection
- 🧠 Role-based access: Admin, Auditor, Viewer
- 🔄 Forgot/reset password workflow (non-admins only)(IN PROGRESS)
- 🧹 Admin can "Clear Project" (reset data)(IN PROGRESS)
- 🎨 Gradient UI with Poppins font and animated navigation

---

## 📣 Contribution

Pull requests welcome! Please include clean commits and screenshots for frontend changes.

---

## 📄 License

MIT License © 2025 Tej Kumar

