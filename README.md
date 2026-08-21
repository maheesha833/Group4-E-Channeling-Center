# E-Channeling Center - Setup Guide

A plain **HTML / CSS / JavaScript / PHP / MySQL** web app (no frameworks) for the
SENG 21253 practical exam project.

## Folder Structure
```
echanneling/
├── database.sql              -> run this first (creates DB + tables + sample doctors)
├── setup_admin.php           -> run once in browser to create admin login
├── includes/
│   ├── db_connect.php        -> DB connection settings (edit host/user/pass here)
│   ├── header.php
│   └── footer.php
├── css/style.css
├── js/script.js              -> AJAX search/filter for the homepage
├── uploads/default-doctor.png
├── index.php                 -> Patient homepage: doctor catalog + search/filter
├── search.php                -> AJAX endpoint returning doctor JSON
├── booking.php                -> Patient booking form
├── process_booking.php       -> Validates + saves booking (status = Pending)
├── payment.php                -> Mock secure payment gateway
├── process_payment.php       -> Confirms payment (status = Paid)
└── admin/
    ├── login.php / login_process.php / logout.php
    ├── dashboard.php          -> Add / Edit / Delete doctors
    ├── add_doctor.php / edit_doctor.php / delete_doctor.php
    └── appointments.php       -> View & filter all bookings
```

## Step-by-Step Setup (XAMPP / WAMP / MAMP)

1. **Install XAMPP** (or WAMP/MAMP) and start **Apache** and **MySQL**.
2. Copy the whole `echanneling` folder into your server's web root:
   - XAMPP (Windows): `C:\xampp\htdocs\echanneling`
   - XAMPP (Mac/Linux): `/opt/lampp/htdocs/echanneling`
3. **Create the database**:
   - Open `http://localhost/phpmyadmin`
   - Click **Import** → choose `database.sql` → Go
   - This creates `echanneling_db` with the `doctors`, `appointments`, and
     `admins` tables, plus 6 sample doctors.
4. **Check DB credentials** in `includes/db_connect.php` (default XAMPP is
   user `root`, password empty — usually no changes needed).
5. **Create the admin account** by visiting:
   `http://localhost/echanneling/setup_admin.php`
   This safely creates username `admin` / password `admin123` with a proper
   password hash (never store plain-text passwords).
   **Delete `setup_admin.php` after this step.**
6. **Open the app**: `http://localhost/echanneling/index.php`

## How Each Feature Works

- **Doctor Catalog (index.php)**: PHP fetches distinct specializations for
  the filter dropdown, then JavaScript (`js/script.js`) calls `search.php`
  via `fetch()` (AJAX) to load and re-render doctor cards live as you type
  or change the specialization filter — no page reload needed.
- **Search/Filter (search.php)**: A PHP endpoint using prepared PDO
  statements (`LIKE` for name, exact match for specialization) that returns
  JSON.
- **Booking (booking.php → process_booking.php)**: Patient fills a form
  (validated both client-side with JS and, more importantly, server-side
  with PHP). A row is inserted into `appointments` with
  `payment_status = 'Pending'`.
- **Payment (payment.php → process_payment.php)**: A mock card form (auto
  formats card number/expiry with JS). On submit, PHP validates the mock
  card fields, marks the appointment `Paid`, and stores only the **last 4
  digits** of the card (never the full number) as a security best practice.
- **Admin (admin/ folder)**: Session-based login (`password_verify`),
  route-guarded pages. `dashboard.php` lets the manager add/edit/delete
  doctors. `appointments.php` lists every booking with a status filter
  (All / Paid / Pending / Cancelled).

## Default Login
- Admin URL: `http://localhost/echanneling/admin/login.php`
- Username: `admin`
- Password: `admin123`

## Security Notes Built In (good to mention in your report/viva)
- All SQL uses **PDO prepared statements** → prevents SQL injection.
- All output uses `htmlspecialchars()` → prevents XSS.
- Admin passwords are hashed with `password_hash()` / verified with
  `password_verify()` → never stored in plain text.
- Payment gateway never stores full card numbers — only last 4 digits.
- Server-side validation always re-checks everything the client-side JS
  already checked, since client-side checks can be bypassed.

## Possible Extensions (if you have extra time)
- Add a doctor profile photo **upload** field (currently uses a shared
  default image) using PHP's `move_uploaded_file()`.
- Add appointment cancellation for patients using a lookup by contact
  number + appointment ID.
- Add pagination to the appointments table for large datasets.
