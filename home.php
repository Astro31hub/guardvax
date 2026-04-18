<?php
// home.php — GuardVAX Hospital Homepage
require_once __DIR__ . '/config/db.php';
$siteUrl = SITE_URL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HCare Hospital — GuardVAX Health Management System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; color: #1a1d24; background: #fff; }

    /* ── Topbar ── */
    .topbar {
      background: #0b1f45;
      padding: 8px 0;
      font-size: .78rem;
      color: rgba(255,255,255,.6);
    }
    .topbar a { color: rgba(255,255,255,.6); text-decoration: none; }
    .topbar a:hover { color: #fff; }
    .topbar i { margin-right: 4px; }

    /* ── Navbar ── */
    .main-nav {
      background: #fff;
      border-bottom: 1px solid #e5e9f0;
      padding: 14px 0;
      position: sticky;
      top: 0;
      z-index: 999;
      box-shadow: 0 2px 10px rgba(0,0,0,.06);
    }
    .nav-brand {
      font-family: 'Syne', sans-serif;
      font-size: 1.5rem;
      color: #0b1f45;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .nav-brand span { color: #0d6efd; }
    .nav-links-main { display: flex; align-items: center; gap: 28px; }
    .nav-links-main a {
      color: #444;
      text-decoration: none;
      font-size: .88rem;
      font-weight: 500;
      transition: color .2s;
    }
    .nav-links-main a:hover { color: #0d6efd; }
    .btn-login {
      background: #0d6efd;
      color: #fff !important;
      padding: 9px 22px;
      border-radius: 8px;
      font-weight: 600;
      font-size: .88rem;
      text-decoration: none;
      transition: background .2s;
    }
    .btn-login:hover { background: #0a58ca; }

    /* ── Hero ── */
    .hero {
      background: linear-gradient(120deg, #0b1f45 0%, #1a4a9e 100%);
      padding: 80px 0;
      color: #fff;
    }
    .hero-tag {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(255,255,255,.12);
      border: 1px solid rgba(255,255,255,.2);
      color: #90c2ff;
      padding: 5px 14px;
      border-radius: 20px;
      font-size: .78rem;
      font-weight: 600;
      margin-bottom: 18px;
    }
    .hero h1 {
      font-family: 'Syne', sans-serif;
      font-size: clamp(2rem, 4vw, 3rem);
      font-weight: 800;
      line-height: 1.2;
      margin-bottom: 16px;
    }
    .hero p {
      color: rgba(255,255,255,.75);
      font-size: 1rem;
      line-height: 1.7;
      max-width: 500px;
      margin-bottom: 30px;
    }
    .hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }
    .btn-white {
      background: #fff;
      color: #0b1f45;
      padding: 12px 28px;
      border-radius: 8px;
      font-weight: 700;
      font-size: .9rem;
      text-decoration: none;
      transition: transform .2s, box-shadow .2s;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.2); color: #0b1f45; }
    .btn-outline-white {
      background: transparent;
      color: #fff;
      padding: 12px 28px;
      border-radius: 8px;
      font-weight: 600;
      font-size: .9rem;
      text-decoration: none;
      border: 2px solid rgba(255,255,255,.4);
      transition: border-color .2s;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-outline-white:hover { border-color: #fff; color: #fff; }

    /* Hero info box */
    .hero-info-box {
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.15);
      border-radius: 12px;
      padding: 24px;
      margin-top: 32px;
    }
    .hero-info-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 8px 0;
    }
    .hero-info-item + .hero-info-item {
      border-top: 1px solid rgba(255,255,255,.08);
    }
    .hero-info-icon {
      width: 36px; height: 36px;
      background: rgba(255,255,255,.12);
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      font-size: .9rem;
    }
    .hero-info-label { font-size: .7rem; color: rgba(255,255,255,.5); }
    .hero-info-val   { font-size: .88rem; font-weight: 600; color: #fff; }

    /* ── Services ── */
    .services { padding: 70px 0; background: #f8f9fb; }
    .sec-label {
      font-size: .72rem;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: #0d6efd;
      margin-bottom: 8px;
    }
    .sec-title {
      font-family: 'Syne', sans-serif;
      font-size: clamp(1.6rem, 3vw, 2.2rem);
      font-weight: 800;
      color: #0b1f45;
      margin-bottom: 10px;
    }
    .sec-sub {
      color: #6c757d;
      font-size: .9rem;
      max-width: 500px;
      line-height: 1.7;
    }
    .service-card {
      background: #fff;
      border: 1px solid #e5e9f0;
      border-radius: 12px;
      padding: 24px;
      height: 100%;
      transition: transform .2s, box-shadow .2s, border-color .2s;
    }
    .service-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 24px rgba(13,110,253,.08);
      border-color: #bfdbfe;
    }
    .service-icon {
      width: 48px; height: 48px;
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.3rem;
      margin-bottom: 14px;
    }
    .service-title { font-weight: 700; font-size: .95rem; color: #0b1f45; margin-bottom: 6px; }
    .service-desc  { font-size: .82rem; color: #6c757d; line-height: 1.6; }

    /* ── Departments ── */
    .depts { padding: 60px 0; }
    .dept-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px 16px;
      border: 1px solid #e5e9f0;
      border-radius: 10px;
      background: #fff;
      transition: border-color .2s, box-shadow .2s;
    }
    .dept-item:hover { border-color: #0d6efd; box-shadow: 0 4px 12px rgba(13,110,253,.08); }
    .dept-icon {
      width: 40px; height: 40px;
      background: #e7f0ff;
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem;
      flex-shrink: 0;
    }
    .dept-name { font-weight: 600; font-size: .88rem; color: #0b1f45; }
    .dept-sub  { font-size: .72rem; color: #6c757d; }

    /* ── Access Panel ── */
    .access-section { padding: 70px 0; background: #f8f9fb; }
    .access-card {
      background: #fff;
      border: 1px solid #e5e9f0;
      border-radius: 16px;
      padding: 32px 28px;
      height: 100%;
      text-align: center;
      transition: transform .2s, box-shadow .2s;
    }
    .access-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,.08); }
    .access-emoji { font-size: 2.5rem; margin-bottom: 14px; }
    .access-role  { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 700; color: #0b1f45; margin-bottom: 4px; }
    .access-sub   { font-size: .8rem; color: #6c757d; margin-bottom: 16px; }
    .access-list  { list-style: none; padding: 0; text-align: left; margin-bottom: 20px; }
    .access-list li {
      font-size: .8rem; color: #444;
      padding: 4px 0;
      display: flex; align-items: center; gap: 7px;
      border-bottom: 1px solid #f5f5f5;
    }
    .access-list li:last-child { border-bottom: none; }
    .access-list li i { color: #0d6efd; font-size: .7rem; }
    .btn-access {
      display: block;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 600;
      font-size: .85rem;
      text-decoration: none;
      text-align: center;
      transition: transform .15s;
    }
    .btn-access:hover { transform: translateY(-1px); }
    .btn-access-blue   { background: #0d6efd; color: #fff; }
    .btn-access-teal   { background: #0891b2; color: #fff; }
    .btn-access-green  { background: #059669; color: #fff; }

    /* Patient notice */
    .patient-notice {
      background: #fffbeb;
      border: 1px solid #fde68a;
      border-radius: 10px;
      padding: 16px 20px;
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }
    .patient-notice i { color: #d97706; font-size: 1.1rem; margin-top: 2px; flex-shrink: 0; }

    /* ── Emergency Banner ── */
    .emergency {
      background: #dc2626;
      color: #fff;
      padding: 16px 0;
      text-align: center;
    }
    .emergency a { color: #fff; font-weight: 700; }

    /* ── Footer ── */
    .site-footer {
      background: #0b1f45;
      color: rgba(255,255,255,.55);
      padding: 40px 0 20px;
      font-size: .82rem;
    }
    .footer-brand {
      font-family: 'Syne', sans-serif;
      font-size: 1.2rem;
      color: #fff;
      margin-bottom: 8px;
    }
    .footer-brand span { color: #5b9eff; }
    .footer-links { list-style: none; padding: 0; }
    .footer-links li { margin-bottom: 6px; }
    .footer-links a { color: rgba(255,255,255,.55); text-decoration: none; }
    .footer-links a:hover { color: #fff; }
    .footer-divider { border-color: rgba(255,255,255,.1); margin: 24px 0 16px; }

    @media (max-width: 768px) {
      .nav-links-main { display: none; }
      .topbar .d-flex { flex-direction: column; gap: 4px; }
    }
  </style>
</head>
<body>

<!-- ── Top Info Bar ── -->
<div class="topbar">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="d-flex gap-4">
        <span><i class="bi bi-telephone-fill"></i>(02) 8123-4567</span>
        <span><i class="bi bi-envelope-fill"></i>info@hcare.com</span>
        <span><i class="bi bi-clock-fill"></i>Mon–Sat: 7AM–9PM | Emergency: 24/7</span>
      </div>
      <div>
        <a href="<?= $siteUrl ?>/auth/login.php">
          <i class="bi bi-person-circle"></i> Patient Portal
        </a>
      </div>
    </div>
  </div>
</div>

<!-- ── Main Navbar ── -->
<nav class="main-nav">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="<?= $siteUrl ?>" class="nav-brand">
      🏥 H<span>Care</span> Hospital
    </a>
    <div class="nav-links-main">
      <a href="#services">Services</a>
      <a href="#departments">Departments</a>
      <a href="#access">Patient Portal</a>
      <a href="<?= $siteUrl ?>/contact.php">Contact</a>
      <a href="<?= $siteUrl ?>/auth/login.php" class="btn-login">
        <i class="bi bi-box-arrow-in-right me-1"></i> Login
      </a>
    </div>
    <!-- Mobile login -->
    <a href="<?= $siteUrl ?>/auth/login.php" class="btn-login d-md-none">Login</a>
  </div>
</nav>

<!-- ── Hero ── -->
<section class="hero">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="hero-tag">
          <i class="bi bi-shield-check-fill"></i>
          Trusted Healthcare Since 2020
        </div>
        <h1>Your Health Is Our Priority</h1>
        <p>HCare Hospital provides comprehensive healthcare services with a team of dedicated professionals. Our digital system keeps your health records safe, organized, and accessible anytime.</p>
        <div class="hero-btns">
          <a href="<?= $siteUrl ?>/auth/login.php" class="btn-white">
            <i class="bi bi-person-circle"></i> Patient Login
          </a>
          <a href="<?= $siteUrl ?>/contact.php" class="btn-outline-white">
            <i class="bi bi-telephone"></i> Contact Us
          </a>
        </div>
      </div>
      <div class="col-lg-5 offset-lg-1">
        <div class="hero-info-box">
          <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:12px">
            Hospital Information
          </div>
          <?php
          $infos = [
            ['bi-geo-alt-fill',    'Address',        '123 Hospital Road, Quezon City'],
            ['bi-telephone-fill',  'Emergency',       '(02) 8123-4567 — Available 24/7'],
            ['bi-clock-fill',      'Clinic Hours',    'Mon–Sat: 7:00 AM – 9:00 PM'],
            ['bi-envelope-fill',   'Email',           'info@hcare.com'],
            ['bi-shield-check-fill','Health System',  'Powered by GuardVAX HMS'],
          ];
          foreach ($infos as $info): ?>
          <div class="hero-info-item">
            <div class="hero-info-icon">
              <i class="bi <?= $info[0] ?>" style="color:#90c2ff"></i>
            </div>
            <div>
              <div class="hero-info-label"><?= $info[1] ?></div>
              <div class="hero-info-val"><?= $info[2] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── Emergency Banner ── -->
<div class="emergency">
  <div class="container">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Emergency?</strong> Call our 24/7 hotline:
    <a href="tel:028123-4567">(02) 8123-4567</a>
    &nbsp;|&nbsp; Emergency Room: <strong>Building A, Ground Floor</strong>
  </div>
</div>

<!-- ── Services ── -->
<section class="services" id="services">
  <div class="container">
    <div class="row align-items-end mb-5">
      <div class="col-lg-6">
        <div class="sec-label">What We Offer</div>
        <h2 class="sec-title">Our Hospital Services</h2>
        <p class="sec-sub">Comprehensive healthcare services managed through our digital health system for accurate and efficient patient care.</p>
      </div>
    </div>
    <div class="row g-3">
      <?php
      $services = [
        ['#dbeafe','#1d4ed8','bi-syringe',              'Vaccination Services',      'Complete immunization records and scheduling for all vaccine types including COVID-19, Flu, Hepatitis, and more.'],
        ['#d1fae5','#065f46','bi-calendar2-check-fill', 'Appointment Scheduling',    'Book and manage your medical appointments across all hospital departments easily through our system.'],
        ['#fef3c7','#92400e','bi-hospital-fill',        'Patient Admissions',        'Streamlined admission and discharge process with room assignment, bed tracking, and department routing.'],
        ['#ede9fe','#5b21b6','bi-capsule-pill',         'Prescription Management',   'Digital prescription records ensuring accurate medicine dispensing and complete medication history.'],
        ['#fee2e2','#991b1b','bi-eyedropper',           'Laboratory Services',       'Request and receive lab test results digitally with normal range indicators and abnormal flagging.'],
        ['#ecfdf5','#064e3b','bi-file-earmark-pdf-fill','Health Reports & PDF',      'Download your personal vaccination report and complete health records as a professional PDF anytime.'],
        ['#fce7f3','#831843','bi-receipt',              'Billing & Payment',         'Transparent billing with itemized statements supporting Cash, PhilHealth, HMO, and credit card payments.'],
        ['#cffafe','#164e63','bi-file-medical-fill',    'Medical Records',           'Centralized digital health records for consultations, diagnoses, and treatment history always accessible.'],
      ];
      foreach ($services as $s): ?>
      <div class="col-sm-6 col-lg-3">
        <div class="service-card">
          <div class="service-icon" style="background:<?= $s[0] ?>">
            <i class="bi <?= $s[2] ?>" style="color:<?= $s[1] ?>"></i>
          </div>
          <div class="service-title"><?= $s[3] ?></div>
          <p class="service-desc"><?= $s[4] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Departments ── -->
<section class="depts" id="departments">
  <div class="container">
    <div class="row align-items-end mb-4">
      <div class="col-lg-6">
        <div class="sec-label">Hospital Departments</div>
        <h2 class="sec-title">Our Departments</h2>
        <p class="sec-sub">Specialized departments staffed by experienced healthcare professionals.</p>
      </div>
    </div>
    <div class="row g-3">
      <?php
      $depts = [
        ['🏥','Outpatient',        'General consultations & check-ups',       'Bldg A, GF'],
        ['🚨','Emergency',         '24/7 emergency medical services',          'Bldg A, GF'],
        ['👶','Pediatrics',        'Child health and development',             'Bldg B, 2F'],
        ['❤️','Internal Medicine', 'Adult internal medicine & chronic disease','Bldg B, 3F'],
        ['🤱','Obstetrics',        'Maternal health & prenatal care',          'Bldg C, 2F'],
        ['🔬','Laboratory',        'Diagnostic tests & results',               'Bldg A, 1F'],
        ['💊','Pharmacy',          'Medicine dispensing & consultations',      'Bldg A, GF'],
        ['🩻','Radiology',         'X-ray, ultrasound & imaging services',     'Bldg D, 1F'],
        ['🔪','Surgery',           'Surgical procedures & post-op recovery',   'Bldg C, 3F'],
        ['💉','Vaccination Unit',  'Immunization & vaccine services',          'Bldg A, 1F'],
      ];
      foreach ($depts as $d): ?>
      <div class="col-sm-6 col-lg-3">
        <div class="dept-item">
          <div class="dept-icon"><?= $d[0] ?></div>
          <div>
            <div class="dept-name"><?= $d[1] ?></div>
            <div class="dept-sub"><?= $d[2] ?></div>
            <div class="dept-sub" style="color:#0d6efd;font-weight:500">📍 <?= $d[3] ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Access Panel ── -->
<section class="access-section" id="access">
  <div class="container">
    <div class="text-center mb-5">
      <div class="sec-label">Patient Portal</div>
      <h2 class="sec-title">Access Your Health Records</h2>
      <p class="sec-sub mx-auto">Login to the GuardVAX digital health system to view your records, appointments, and more.</p>
    </div>

    <div class="row g-4 mb-4">
      <!-- Admin -->
      <div class="col-md-4">
        <div class="access-card">
          <div class="access-emoji">👑</div>
          <div class="access-role">Administrator</div>
          <div class="access-sub">Full system management access</div>
          <ul class="access-list">
            <li><i class="bi bi-check-circle-fill"></i> Manage all users & roles</li>
            <li><i class="bi bi-check-circle-fill"></i> View system-wide reports</li>
            <li><i class="bi bi-check-circle-fill"></i> Manage inventory & billing</li>
            <li><i class="bi bi-check-circle-fill"></i> Full audit trail access</li>
            <li><i class="bi bi-check-circle-fill"></i> Department management</li>
          </ul>
          <a href="<?= $siteUrl ?>/auth/login.php" class="btn-access btn-access-blue">
            <i class="bi bi-box-arrow-in-right me-1"></i> Admin Login
          </a>
        </div>
      </div>

      <!-- Nurse -->
      <div class="col-md-4">
        <div class="access-card" style="border-color:#bfdbfe">
          <div class="access-emoji">🩺</div>
          <div class="access-role">Nurse / Staff</div>
          <div class="access-sub">Patient care and data management</div>
          <ul class="access-list">
            <li><i class="bi bi-check-circle-fill"></i> Register & manage patients</li>
            <li><i class="bi bi-check-circle-fill"></i> Record vaccinations</li>
            <li><i class="bi bi-check-circle-fill"></i> Manage admissions</li>
            <li><i class="bi bi-check-circle-fill"></i> Write prescriptions</li>
            <li><i class="bi bi-check-circle-fill"></i> Request lab tests</li>
          </ul>
          <div class="d-flex flex-column gap-2">
            <a href="<?= $siteUrl ?>/auth/login.php" class="btn-access btn-access-teal">
              <i class="bi bi-box-arrow-in-right me-1"></i> Nurse Login
            </a>
            <a href="<?= $siteUrl ?>/auth/signup.php" class="btn-access"
               style="background:#e7f0ff;color:#0d6efd;border:1px solid #bfdbfe">
              <i class="bi bi-person-plus me-1"></i> Register as Nurse
            </a>
          </div>
        </div>
      </div>

      <!-- Patient -->
      <div class="col-md-4">
        <div class="access-card">
          <div class="access-emoji">🧑‍⚕️</div>
          <div class="access-role">Patient</div>
          <div class="access-sub">Personal health portal</div>
          <ul class="access-list">
            <li><i class="bi bi-check-circle-fill"></i> View vaccination history</li>
            <li><i class="bi bi-check-circle-fill"></i> Check appointments</li>
            <li><i class="bi bi-check-circle-fill"></i> View lab results</li>
            <li><i class="bi bi-check-circle-fill"></i> Check prescriptions</li>
            <li><i class="bi bi-check-circle-fill"></i> Download PDF reports</li>
          </ul>
          <a href="<?= $siteUrl ?>/auth/login.php" class="btn-access btn-access-green">
            <i class="bi bi-box-arrow-in-right me-1"></i> Patient Login
          </a>
        </div>
      </div>
    </div>

    <!-- Patient notice -->
    <div class="patient-notice">
      <i class="bi bi-info-circle-fill"></i>
      <div>
        <strong style="color:#92400e">New Patient?</strong>
        <span style="color:#78350f;font-size:.85rem">
          Patient accounts are created by our nursing staff at registration.
          Please visit the <strong>Nurse Station</strong> at the hospital with a valid ID.
          Your login credentials will be sent to your email address after registration.
          &nbsp;<a href="<?= $siteUrl ?>/contact.php" style="color:#d97706;font-weight:600">
            Contact us →
          </a>
        </span>
      </div>
    </div>
  </div>
</section>

<!-- ── Footer ── -->
<footer class="site-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="footer-brand">🏥 H<span>Care</span> Hospital</div>
        <p>Providing quality healthcare services with compassion and advanced digital health management.</p>
        <p class="mt-2" style="font-size:.75rem;color:rgba(255,255,255,.3)">
          Powered by <strong style="color:#5b9eff">GuardVAX HMS</strong>
        </p>
      </div>
      <div class="col-lg-2">
        <div style="color:#fff;font-weight:600;margin-bottom:12px;font-size:.85rem">Quick Links</div>
        <ul class="footer-links">
          <li><a href="#services">Services</a></li>
          <li><a href="#departments">Departments</a></li>
          <li><a href="#access">Patient Portal</a></li>
          <li><a href="<?= $siteUrl ?>/contact.php">Contact Us</a></li>
        </ul>
      </div>
      <div class="col-lg-3">
        <div style="color:#fff;font-weight:600;margin-bottom:12px;font-size:.85rem">Patient Portal</div>
        <ul class="footer-links">
          <li><a href="<?= $siteUrl ?>/auth/login.php">Patient Login</a></li>
          <li><a href="<?= $siteUrl ?>/auth/login.php">Nurse Login</a></li>
          <li><a href="<?= $siteUrl ?>/auth/signup.php">Nurse Registration</a></li>
          <li><a href="<?= $siteUrl ?>/auth/forgot_password.php">Forgot Password</a></li>
        </ul>
      </div>
      <div class="col-lg-3">
        <div style="color:#fff;font-weight:600;margin-bottom:12px;font-size:.85rem">Contact</div>
        <ul class="footer-links" style="list-style:none;padding:0">
          <li style="margin-bottom:6px"><i class="bi bi-telephone-fill me-2" style="color:#5b9eff"></i>(02) 8123-4567</li>
          <li style="margin-bottom:6px"><i class="bi bi-envelope-fill me-2" style="color:#5b9eff"></i>info@hcare.com</li>
          <li style="margin-bottom:6px"><i class="bi bi-geo-alt-fill me-2" style="color:#5b9eff"></i>123 Hospital Road, QC</li>
          <li><i class="bi bi-clock-fill me-2" style="color:#5b9eff"></i>Mon–Sat: 7AM–9PM</li>
        </ul>
      </div>
    </div>
    <hr class="footer-divider">
    <div class="text-center">
      © <?= date('Y') ?> HCare Hospital — GuardVAX Health Management System. All rights reserved.
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const t = document.querySelector(a.getAttribute('href'));
    if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
  });
});
</script>
</body>
</html>
