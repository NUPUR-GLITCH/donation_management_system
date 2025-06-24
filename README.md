# Donation Management System

A web-based platform for managing donations, donors, and fundraising campaigns. This system enables secure and efficient tracking of donations, donor management, and fund allocation for causes such as Education, Disaster Relief, and Health.

---

## Features

### Admin Panel
- Manage all donations (add, approve, reject, delete)
- View donation reports and donor information
- Secure admin authentication

### Donor Dashboard
- User-friendly interface for making donations
- Recurring (monthly) or one-time donations
- View donation history and track personal goals
- Donate to specific funds (Education, Disaster Relief, Health)
- Profile management

---

## Folder Structure

```
donation_management_system/
│
├── uploads/
│   └── WhatsApp Image 2025-06-19 at 13.36.57_bd3abc84.jpg
├── add_donation.php
├── db.php
├── db.sql
├── delete.php
├── donor_dashboard.php
├── (other PHP files)
└── README.md
```

---

## Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/NUPUR-GLITCH/donation_management_system.git
cd donation_management_system
```

### 2. Database Setup

- **Import the SQL file:**
  1. Open your MySQL client (e.g., phpMyAdmin or CLI).
  2. Run the following command to import the structure and default data:

      ```bash
      mysql -u root -p < db.sql
      ```

- **Database Name:**  
  The default is `donation_systems` (as set in `db.sql`).  
  Make sure your `db.php` matches this name.

### 3. Configure Database Connection

- Open `db.php` and update the following details if needed:

    ```php
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "donation_system";
    ```

### 4. Place the Image

- Move the provided image to the `uploads` folder:
    ```
    uploads/WhatsApp Image 2025-06-19 at 13.36.57_bd3abc84.jpg
    ```
- The system uses this image as a dashboard background.

### 5. Start the Server

- Place the entire folder in your web server directory (`htdocs` for XAMPP).
- Start **Apache** and **MySQL** using your local server stack (XAMPP, WAMP, etc.).
- Visit:
    ```
    http://localhost/donation_management_system/
    ```

---

## Default Accounts

**Admin Login**
- Username: `admin`
- Password: `admin123`

**Sample Donor Login**
- Username: `test_donor`
- Email: `test@donor.com`
- Password: `donor123`

---

## Security Notes

- Passwords use SHA1 hashing for demonstration; upgrade to `password_hash()` for production use.
- Basic session-based authentication is implemented for both admin and donors.
- Further security and input validation is recommended for production deployments.

---

## License

For educational purposes only.

---

**Developed by [NUPUR-GLITCH](https://github.com/NUPUR-GLITCH)**
