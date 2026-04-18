<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us — GuardVAX</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background: #f0f4f9; }
    .contact-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.08); padding: 40px; max-width: 600px; margin: 60px auto; }
    .contact-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
    .info-item { display: flex; align-items: flex-start; gap: 14px; padding: 16px 0; border-bottom: 1px solid #f0f0f0; }
    .info-item:last-child { border-bottom: none; }
  </style>
</head>
<body>
<div class="contact-card">
  <div class="text-center mb-4">
    <span style="font-size:2.5rem">🛡️</span>
    <h2 class="fw-bold mt-2" style="color:#0b1f45">Contact HCARE Hospital</h2>
    <p class="text-muted">Need help with your account? Reach out to us.</p>
  </div>

  <div class="info-item">
    <div class="contact-icon" style="background:#dbeafe"><i class="bi bi-telephone-fill" style="color:#1d4ed8"></i></div>
    <div>
      <div class="fw-semibold">Phone / Hotline</div>
      <div class="text-muted">(02) 8123-4567</div>
      <div class="text-muted small">Monday–Friday, 8:00 AM – 5:00 PM</div>
    </div>
  </div>

  <div class="info-item">
    <div class="contact-icon" style="background:#d1fae5"><i class="bi bi-envelope-fill" style="color:#065f46"></i></div>
    <div>
      <div class="fw-semibold">Email</div>
      <div class="text-muted">support@HCARE.com</div>
      <div class="text-muted small">We reply within 24 hours</div>
    </div>
  </div>

  <div class="info-item">
    <div class="contact-icon" style="background:#fef3c7"><i class="bi bi-geo-alt-fill" style="color:#92400e"></i></div>
    <div>
      <div class="fw-semibold">Location</div>
      <div class="text-muted">123 Hospital Road, Quezon City</div>
      <div class="text-muted small">Nurse Station — Ground Floor</div>
    </div>
  </div>

  <div class="info-item">
    <div class="contact-icon" style="background:#ede9fe"><i class="bi bi-clock-fill" style="color:#5b21b6"></i></div>
    <div>
      <div class="fw-semibold">Why contact us?</div>
      <div class="text-muted small mt-1">If you registered online but your patient profile is not yet set up, please visit the nurse station with a valid ID so we can complete your registration in the system.</div>
    </div>
  </div>

  <div class="mt-4 d-flex gap-2 justify-content-center">
    <a href="http://localhost/guardvax/auth/login.php" class="btn btn-primary">
      <i class="bi bi-box-arrow-in-right me-1"></i> Back to Login
    </a>
    <a href="http://localhost/guardvax/" class="btn btn-outline-secondary">
      <i class="bi bi-house me-1"></i> Homepage
    </a>
  </div>
</div>
</body>
</html>