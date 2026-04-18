# 🛡️ GuardVAX — Complete Setup & Deployment Guide

**Hospital Vaccination & Health Data Management System**

---

## 📁 Project Structure

```
guardvax/
├── index.php                    ← Root redirect
├── install_dompdf.sh            ← dompdf installer
├── composer.json                ← (created by composer)
│
├── config/
│   └── db.php                   ← DB connection + constants
│
├── includes/
│   ├── functions.php            ← Core helpers (CSRF, sessions, audit…)
│   ├── auth.php                 ← Auth guards & user functions
│   ├── layout.php               ← Shared HTML layout (nav, header, footer)
│   └── 403.php                  ← Forbidden error page
│
├── auth/
│   ├── login.php                ← Login (Step 1: creds + Step 2: OTP/2FA)
│   ├── signup.php               ← Register (nurse/patient) + email verify
│   ├── logout.php               ← Destroy session
│   └── forgot_password.php      ← Password reset via email code
│
├── admin/
│   ├── index.php                ← Admin dashboard (charts + stats)
│   ├── users.php                ← User management (CRUD)
│   ├── patients.php             ← Patient registry view
│   ├── reports.php              ← Report generation hub
│   └── audit_logs.php           ← Full audit trail
│
├── nurse/
│   ├── index.php                ← Nurse dashboard
│   ├── patients.php             ← Register & manage patients
│   ├── vaccinations.php         ← Record vaccinations
│   ├── medical_records.php      ← Add & view medical records
│   └── search.php               ← Advanced patient search
│
├── patient/
│   ├── index.php                ← Patient dashboard
│   ├── profile.php              ← View/update own profile
│   ├── vaccinations.php         ← View vaccination history
│   └── records.php              ← View medical records
│
├── reports/
│   ├── pdf_helper.php           ← dompdf wrapper + HTML helpers
│   ├── patient_report.php       ← Individual patient PDF
│   ├── vaccination_summary.php  ← Admin summary PDF
│   └── patient_list.php         ← Full patient list PDF
│
├── assets/
│   ├── css/style.css            ← Main stylesheet
│   └── js/main.js               ← Core JavaScript
│
├── sql/
│   └── guardvax.sql             ← Full database schema + seed data
│
└── vendor/                      ← Created by Composer (dompdf)
```

---

## ⚡ Quick Local Setup (XAMPP / Laragon)

### Step 1 — Install XAMPP
Download from https://www.apachefriends.org/ and start **Apache** + **MySQL**.

### Step 2 — Place project files
```bash
# Copy the guardvax/ folder to:
C:\xampp\htdocs\guardvax\         # Windows
/Applications/XAMPP/htdocs/guardvax/  # macOS
/var/www/html/guardvax/            # Linux
```

### Step 3 — Import database
1. Open **phpMyAdmin** → http://localhost/phpmyadmin
2. Click **"New"** → create database named `guardvax`
3. Click the database → **Import** tab
4. Select `sql/guardvax.sql` → click **Go**

### Step 4 — Configure DB connection
Edit `config/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'guardvax');
define('DB_USER', 'root');    // your MySQL username
define('DB_PASS', '');        // your MySQL password

define('SITE_URL', 'http://localhost/guardvax');
define('SITE_EMAIL', 'your@email.com');
```

### Step 5 — Install dompdf (for PDF reports)
```bash
cd C:\xampp\htdocs\guardvax
composer require dompdf/dompdf
```
> **No composer?** Download from https://getcomposer.org/

### Step 6 — Test it!
Visit: http://localhost/guardvax

**Default Admin Login:**
```
Email:    admin@guardvax.com
Password: Admin@GuardVAX1
```
> ⚠️ Change this password immediately after first login!

---

## 🌐 Deploy to InfinityFree (Free PHP + MySQL Hosting)

### Step 1 — Create account
Go to https://www.infinityfree.com → Sign up free

### Step 2 — Create hosting account
- Dashboard → "Add Account"
- Choose a subdomain (e.g., `guardvax.infinityfreeapp.com`)
- Note your **FTP credentials** and **MySQL details**

### Step 3 — Create database
- InfinityFree Control Panel → **MySQL Databases**
- Create a new database and note:
  - Database name (e.g., `epiz_12345678_guardvax`)
  - MySQL host (e.g., `sql208.infinityfree.com`)
  - Username + password

### Step 4 — Import SQL
- Open **phpMyAdmin** from your InfinityFree control panel
- Select your database → Import → upload `guardvax.sql`

### Step 5 — Update config/db.php
```php
define('DB_HOST', 'sql208.infinityfree.com');  // your host
define('DB_NAME', 'epiz_12345678_guardvax');    // your db name
define('DB_USER', 'epiz_12345678');             // your username
define('DB_PASS', 'yourpassword');              // your password

define('SITE_URL', 'https://yoursubdomain.infinityfreeapp.com/guardvax');
```

### Step 6 — Upload files via FTP
Using **FileZilla** (free, https://filezilla-project.org/):
1. Host: your FTP host from InfinityFree
2. Username/Password: your FTP credentials
3. Port: 21
4. Upload all files to `htdocs/guardvax/`

### Step 7 — dompdf on shared hosting
Since you can't run Composer on free hosts:
1. Run `composer require dompdf/dompdf` on your LOCAL machine first
2. Upload the entire `vendor/` folder via FTP (it's large ~10MB, be patient)

---

## ☁️ Deploy to 000WebHost

### Step 1 — Sign up at https://www.000webhost.com

### Step 2 — Upload files
- Dashboard → File Manager → Upload files to `public_html/guardvax/`
- Or use FTP credentials from the dashboard

### Step 3 — Create MySQL database
- Dashboard → Manage Database → Create database
- Note host, name, user, password

### Step 4 — Import SQL
- phpMyAdmin → Import your SQL file

### Step 5 — Update config/db.php
Same as InfinityFree — use your 000webhost MySQL details.

---

## 🔷 Deploy to Azure App Service (Optional — Advanced)

### Prerequisites
- Azure account (free trial at https://azure.com/free)
- Azure CLI installed

### Step 1 — Create App Service
```bash
az group create --name GuardVAX-RG --location eastus
az appservice plan create --name GuardVAXPlan --resource-group GuardVAX-RG --sku B1 --is-linux
az webapp create --name guardvax-app --resource-group GuardVAX-RG --plan GuardVAXPlan --runtime "PHP:8.2"
```

### Step 2 — Create Azure MySQL
```bash
az mysql flexible-server create \
  --resource-group GuardVAX-RG \
  --name guardvax-db \
  --admin-user adminuser \
  --admin-password YourSecurePass! \
  --sku-name Standard_B1ms \
  --public-access 0.0.0.0
```

### Step 3 — Deploy code
```bash
cd /path/to/guardvax
zip -r guardvax.zip . -x "*.git*"
az webapp deployment source config-zip \
  --resource-group GuardVAX-RG \
  --name guardvax-app \
  --src guardvax.zip
```

### Step 4 — Set environment variables (App Settings)
In Azure Portal → App Service → Configuration → App Settings:
```
DB_HOST = guardvax-db.mysql.database.azure.com
DB_NAME = guardvax
DB_USER = adminuser@guardvax-db
DB_PASS = YourSecurePass!
SITE_URL = https://guardvax-app.azurewebsites.net
```

---

## 📧 Email Setup (Required for 2FA & Verification)

### Option A — PHP mail() (Local/Simple)
Works on most servers automatically. May go to spam.
```php
// config/db.php — already configured
define('SITE_EMAIL', 'noreply@yourdomain.com');
```

### Option B — SMTP via PHPMailer (Recommended for Production)
```bash
composer require phpmailer/phpmailer
```

Replace `sendEmail()` in `includes/functions.php`:
```php
use PHPMailer\PHPMailer\PHPMailer;

function sendEmail(string $to, string $subject, string $body): bool {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';   // or your SMTP host
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your@gmail.com';
        $mail->Password   = 'your-app-password'; // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom(SITE_EMAIL, SITE_NAME);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail error: " . $e->getMessage());
        return false;
    }
}
```

---

## 🔒 Security Checklist for Production

```
✅ Change default admin password immediately
✅ Use strong DB_PASS (random 32+ char string)
✅ Enable HTTPS (SSL certificate — free via Let's Encrypt)
✅ Set secure=true in session cookie (only works over HTTPS)
✅ Move config/db.php above public web root (optional hardening)
✅ Disable PHP error display: display_errors = Off in php.ini
✅ Enable error logging: log_errors = On, error_log = /path/to/error.log
✅ Set proper file permissions: directories 755, files 644
✅ Add .htaccess to restrict direct access to /includes, /config
✅ Regularly export and back up the database
✅ Keep PHP and MySQL updated
```

**Recommended .htaccess for /includes/ and /config/:**
```apache
Options -Indexes
Deny from all
```

---

## 👥 Default Test Accounts

After importing the SQL, only the admin exists. Create test accounts by:
1. Logging in as admin → Users → Add User
2. Or using the signup page for nurse/patient accounts

| Role    | Email                   | Password        |
|---------|-------------------------|-----------------|
| Admin   | admin@guardvax.com      | Admin@GuardVAX1 |

---

## 🧪 Testing Checklist

```
□ Login with admin credentials
□ Change admin password
□ Create a nurse account (via Admin → Users)
□ Create a patient account (via Nurse → Register Patient)
□ Log in as nurse → record a vaccination
□ Log in as patient → view vaccination history
□ Generate a patient PDF report
□ Generate vaccination summary PDF
□ Test 2FA (check email for OTP code)
□ Test forgot password flow
□ View audit logs (Admin → Audit Logs)
□ Test search/filter on patient list
□ Test mobile responsiveness
```

---

## 🛠️ Troubleshooting

| Problem | Solution |
|---------|----------|
| Blank page | Check PHP error logs; enable `display_errors = On` temporarily |
| DB connection failed | Verify DB_HOST, DB_NAME, DB_USER, DB_PASS in config/db.php |
| PDF not generating | Run `composer require dompdf/dompdf` in project root |
| Email not sending | Check SMTP settings or use Option B (PHPMailer) |
| Session issues | Ensure `session.save_path` is writable on your server |
| 403 on files | Check `.htaccess` and folder permissions (755/644) |
| OTP not received | Check spam folder; configure SMTP for production |

---

## 📋 Tech Stack Summary

| Component | Technology |
|-----------|-----------|
| Backend   | PHP 8.x (PDO, prepared statements) |
| Database  | MySQL 8.x (normalized, foreign keys) |
| Frontend  | HTML5, CSS3, JavaScript (ES6+) |
| UI        | Bootstrap 5.3 |
| Charts    | Chart.js 4.x |
| Icons     | Bootstrap Icons 1.11 |
| PDF       | dompdf |
| Security  | CSRF tokens, bcrypt, session hardening, input sanitization |

---

*GuardVAX v1.0 — Built for learning and small hospital deployments.*
*Extend freely; add features like appointment scheduling, inventory, etc.*
