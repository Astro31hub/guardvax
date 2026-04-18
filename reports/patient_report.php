<?php
// reports/patient_report.php — Individual Patient Vaccination PDF
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../reports/pdf_helper.php';

startSecureSession();
if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/auth/login.php');
    exit;
}

$currentUser = getCurrentUser();
$role        = $currentUser['role_name'];

// Determine patient ID
if ($role === 'patient') {
    // Patient downloads their own report
    $stmt = db()->prepare('SELECT id FROM patients WHERE user_id=?');
    $stmt->execute([$currentUser['id']]);
    $row = $stmt->fetch();
    $patientId = $row['id'] ?? 0;
} else {
    // Nurse/Admin selects patient
    $patientId = (int) ($_GET['patient_id'] ?? 0);
}

if (!$patientId) {
    die('<div style="padding:2rem;font-family:sans-serif;color:red">Error: Patient not specified or not found.</div>');
}

// Fetch patient
$stmt = db()->prepare(
    'SELECT p.*, u.name, u.email, rn.name AS registered_by_name
     FROM patients p
     JOIN users u ON u.id = p.user_id
     LEFT JOIN users rn ON rn.id = p.registered_by
     WHERE p.id = ?'
);
$stmt->execute([$patientId]);
$patient = $stmt->fetch();

if (!$patient) die('<div style="padding:2rem;font-family:sans-serif;color:red">Patient not found.</div>');

// Fetch vaccinations
$stmt = db()->prepare(
    'SELECT v.*, vk.name AS vaccine_name, vk.manufacturer, nu.name AS nurse_name
     FROM vaccinations v
     JOIN vaccines vk ON vk.id = v.vaccine_id
     JOIN users nu ON nu.id = v.administered_by
     WHERE v.patient_id = ?
     ORDER BY v.date_given ASC'
);
$stmt->execute([$patientId]);
$vaccinations = $stmt->fetchAll();

// Fetch medical records
$stmt = db()->prepare(
    'SELECT mr.*, u.name AS recorded_by_name
     FROM medical_records mr
     JOIN users u ON u.id = mr.recorded_by
     WHERE mr.patient_id = ?
     ORDER BY mr.record_date DESC'
);
$stmt->execute([$patientId]);
$records = $stmt->fetchAll();

auditLog('REPORT_GENERATED', 'patients', $patientId, "Patient PDF report for: {$patient['patient_code']}");

// ── Build HTML ────────────────────────────────────────────────
$vacc_rows = '';
foreach ($vaccinations as $i => $v) {
    $row = ($i % 2 === 0) ? '' : 'background:#f8f9fa;';
    $nextDose = $v['next_dose_date'] ? date('M d, Y', strtotime($v['next_dose_date'])) : '—';
    $vacc_rows .= <<<HTML
<tr style="{$row}">
  <td>{$i}</td>
  <td><strong>{$v['vaccine_name']}</strong><br><small style="color:#888">{$v['manufacturer']}</small></td>
  <td><span class="badge badge-primary">Dose {$v['dose_number']}</span></td>
  <td>{$v['date_given']}</td>
  <td>{$v['site']}</td>
  <td>{$v['batch_number']}</td>
  <td>{$nextDose}</td>
  <td>{$v['nurse_name']}</td>
</tr>
HTML;
}
if (!$vacc_rows) {
    $vacc_rows = '<tr><td colspan="8" style="text-align:center;color:#888;padding:16px">No vaccination records found.</td></tr>';
}

$record_rows = '';
foreach ($records as $rec) {
    $record_rows .= <<<HTML
<tr>
  <td>{$rec['record_date']}</td>
  <td>{$rec['record_type']}</td>
  <td><strong>{$rec['title']}</strong></td>
  <td style="max-width:200px">{$rec['description']}</td>
  <td>{$rec['recorded_by_name']}</td>
</tr>
HTML;
}
if (!$record_rows) {
    $record_rows = '<tr><td colspan="5" style="text-align:center;color:#888;padding:16px">No medical records found.</td></tr>';
}

$age       = calculateAge($patient['date_of_birth']);
$dob       = date('F d, Y', strtotime($patient['date_of_birth']));
$vaccCount = count($vaccinations);
$recCount  = count($records);

$html = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8">
HTML;
$html .= pdfStyles();
$html .= '</head><body>';
$html .= pdfHeader('Patient Vaccination Report', 'Personal Health Record');

$html .= <<<HTML
<!-- Patient Info -->
<div class="section-title">Patient Information</div>
<table style="margin-bottom:16px">
  <tr>
    <td width="50%">
      <div class="info-grid">
        <div class="info-row"><div class="info-label">Full Name</div><div class="info-value"><strong>{$patient['name']}</strong></div></div>
        <div class="info-row"><div class="info-label">Patient Code</div><div class="info-value">{$patient['patient_code']}</div></div>
        <div class="info-row"><div class="info-label">Date of Birth</div><div class="info-value">{$dob} (Age {$age})</div></div>
        <div class="info-row"><div class="info-label">Gender</div><div class="info-value">{$patient['gender']}</div></div>
        <div class="info-row"><div class="info-label">Blood Type</div><div class="info-value">{$patient['blood_type']}</div></div>
      </div>
    </td>
    <td width="5%"></td>
    <td width="45%">
      <div class="info-grid">
        <div class="info-row"><div class="info-label">Email</div><div class="info-value">{$patient['email']}</div></div>
        <div class="info-row"><div class="info-label">Phone</div><div class="info-value">{$patient['phone']}</div></div>
        <div class="info-row"><div class="info-label">Address</div><div class="info-value">{$patient['address']}</div></div>
        <div class="info-row"><div class="info-label">Allergies</div><div class="info-value">{$patient['allergies']}</div></div>
        <div class="info-row"><div class="info-label">Emergency Contact</div><div class="info-value">{$patient['emergency_contact_name']} — {$patient['emergency_contact_phone']}</div></div>
      </div>
    </td>
  </tr>
</table>

<!-- Summary Stats -->
<table style="margin-bottom:16px">
  <tr>
    <td style="background:#e7f0ff;border-radius:6px;padding:10px;text-align:center;width:33%">
      <div style="font-size:22px;font-weight:700;color:#0d6efd">{$vaccCount}</div>
      <div style="font-size:10px;color:#555">Total Vaccinations</div>
    </td>
    <td width="2%"></td>
    <td style="background:#d1e7dd;border-radius:6px;padding:10px;text-align:center;width:33%">
      <div style="font-size:22px;font-weight:700;color:#198754">{$recCount}</div>
      <div style="font-size:10px;color:#555">Medical Records</div>
    </td>
    <td width="2%"></td>
    <td style="background:#fff3cd;border-radius:6px;padding:10px;text-align:center;width:30%">
      <div style="font-size:22px;font-weight:700;color:#664d03">{$age}</div>
      <div style="font-size:10px;color:#555">Years Old</div>
    </td>
  </tr>
</table>

<!-- Vaccination History -->
<div class="section-title">Vaccination History</div>
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Vaccine</th>
      <th>Dose</th>
      <th>Date Given</th>
      <th>Site</th>
      <th>Batch #</th>
      <th>Next Dose</th>
      <th>Administered By</th>
    </tr>
  </thead>
  <tbody>
    {$vacc_rows}
  </tbody>
</table>

<!-- Medical Records -->
<div class="section-title" style="margin-top:24px">Medical Records</div>
<table>
  <thead>
    <tr>
      <th>Date</th>
      <th>Type</th>
      <th>Title</th>
      <th>Description</th>
      <th>Recorded By</th>
    </tr>
  </thead>
  <tbody>
    {$record_rows}
  </tbody>
</table>

<!-- Notes -->
<div class="section-title" style="margin-top:20px">Clinical Notes</div>
<div style="border:1px solid #dee2e6;border-radius:6px;padding:12px;font-size:10px;color:#555;min-height:40px">
  {$patient['notes']}
</div>

<div class="footer">
  GuardVAX Hospital Vaccination &amp; Health Data Management System &nbsp;|&nbsp;
  This document is confidential and intended solely for the patient and authorized healthcare providers.
  &nbsp;|&nbsp; Printed: 
HTML;
$html .= date('F d, Y h:i A');
$html .= '</div></body></html>';

$filename = 'GuardVAX_' . $patient['patient_code'] . '_Report_' . date('Ymd') . '.pdf';
generatePdf($html, $filename);
