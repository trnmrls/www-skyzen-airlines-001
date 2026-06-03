# ✈️ SkyZen Airlines

Welcome to the **SkyZen Airlines Flight Management System!** This project is a fully functional, MVC-structured web application designed to handle professional airline operations. It features secure user registration, flight scheduling, dynamic data visualization, and automated email notifications.

## 🛠️ Tech Stack
* **Frontend:** HTML5, CSS3, JavaScript (AJAX, jQuery)
* **Backend:** PHP (PDO, OOP, MVC Architecture)
* **Database:** MySQL (Relational Schema with Strict Foreign Keys)
* **Libraries:** Chart.js (Dashboards), SweetAlert2 (Popups), PHPMailer (Email Routing)
* **Security:** Argon2id Password Hashing, Server-Side & Client-Side Regex Validation

---

## 🚀 Getting Started

### 1. Database Setup
1. Open **phpMyAdmin** and create a new database named `airlines_db`.
2. Import the provided `airlines_db.sql` file to instantly generate the tables, constraints, and master data (flights, users, etc.).

### 2. Composer Dependencies
This project uses Composer to route emails via SMTP.
1. Open your terminal in the project root folder.
2. Run `composer install` to download the `vendor` folder (which includes the PHPMailer library).
3. Ensure your `config/mail.php` contains your valid SMTP credentials.

---

## 🔐 Test Accounts

Due to our strict **Argon2id password hashing protocol**, raw passwords cannot be read directly from the database. You can either register a new account via the UI, or use your pre-configured accounts:

### 👑 Admin Account
* **Email:** `trine@abc.com`
* **Username:** `trnmrls`
* **Password:** *trine*

### 👤 Customer Accounts (Pre-loaded)
* **Email:** `kwp@abc.com` | **Username:** `kwpday6`
* **Password:** *wonpilkim*
* *(Note: To test the full application flow, we highly recommend using the Registration page to create a brand new customer so you can see the PHPMailer Welcome Email arrive in your real inbox!)*

---

## 🛫 Flight Schedules (Master Data)

The database comes pre-loaded with historical, active, and future flights to automatically populate your Dashboard charts and booking engines. 

| Flight # | Origin | Destination | Departure Time | Status | Base Price |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **SZ-101** | Manila (MNL) | Osaka (KIX) | Nov 15, 2026 - 08:00 AM | Scheduled | ₱15,000.00 |
| **SZ-102** | Manila (MNL) | Seoul (ICN) | Nov 16, 2026 - 10:30 AM | Scheduled | ₱12,500.00 |
| **SZ-103** | Manila (MNL) | Singapore (SIN) | Nov 17, 2026 - 06:00 AM | Scheduled | ₱9,800.00 |
| **SZ-104** | New York (JFK) | Dubai (DXB) | Dec 01, 2026 - 10:00 PM | Scheduled | ₱45,000.00 |
| **SZ-105** | Osaka (KIX) | Manila (MNL) | Nov 20, 2026 - 03:00 PM | Scheduled | ₱14,500.00 |
| **SZ-201** | Sydney (SYD) | Manila (MNL) | *In Air* | Active | ₱25,000.00 |
| **SZ-301** | Vancouver (YVR)| Taipei (TPE) | Nov 10, 2026 - 02:00 PM | Delayed | ₱32,000.00 |
| **SZ-401** | Manila (MNL) | Bangkok (BKK) | Oct 05, 2026 - 09:00 AM | Cancelled | ₱8,500.00 |

*(Note: Historical flights SZ-501 and SZ-502 are also included in the database behind the scenes to generate past revenue analytics on your Admin Dashboard).*

---

## 📂 Project Structure
* `/bl/` - **Business Logic** (Data processing & analytical queries)
* `/config/` - **System Configurations** (Database & SMTP Mail settings)
* `/controllers/` - **Controllers** (Traffic directors between Views and Models)
* `/helper/` - **Reusable Helpers** (e.g., `sendEmail.php` and HTML Templates)
* `/model/` - **Models** (Direct Database interaction and CRUD operations)
* `/scripts/` - **Frontend Scripts** (AJAX submission, Logical Validation, Chart.js)
* `/views/` - **User Interfaces** (HTML/PHP visual rendering)
