<?php
// patient/records.php — Medical Records
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('patient');

$stmt = db()->prepare('SELECT * FROM patients WHERE user_id=?');
$stmt->execute([$user['id']]);
$patient = $stmt->fetch();

$records = [];
if ($patient) {
    $stmt = db()->prepare(
        'SELECT mr.*, u.name AS recorded_by_name
         FROM medical_records mr
         JOIN users u ON u.id = mr.recorded_by
         WHERE mr.patient_id = ?
         ORDER BY mr.record_date DESC'
    );
    $stmt->execute([$patient['id']]);
    $records = $stmt->fetchAll();
}

$typeColors = [
    'Consultation' => 'primary',
    'Lab Result'   => 'info',
    'Diagnosis'    => 'warning',
    'Prescription' => 'success',
    'Other'        => 'secondary',
];
?>
<?php renderHead('Medical Records'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Medical Records', 'Your health and consultation history', 'file-medical'); ?>

<?php if (!$patient): ?>
  <div class="alert alert-warning">Patient profile not found.</div>
<?php elseif (empty($records)): ?>
  <div class="text-center py-5 text-muted">
    <i class="bi bi-file-medical" style="font-size:3rem;opacity:.3"></i>
    <p class="mt-3">No medical records on file.</p>
  </div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($records as $rec): ?>
    <div class="col-12">
      <div class="card card-gvx">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
              <span class="badge bg-<?= $typeColors[$rec['record_type']] ?? 'secondary' ?> me-2">
                <?= sanitize($rec['record_type']) ?>
              </span>
              <strong><?= sanitize($rec['title']) ?></strong>
            </div>
            <div class="text-muted small">
              <i class="bi bi-calendar2 me-1"></i><?= formatDate($rec['record_date']) ?>
              &bull; Dr./RN <?= sanitize($rec['recorded_by_name']) ?>
            </div>
          </div>
          <p class="mb-0 text-muted" style="white-space:pre-line"><?= sanitize($rec['description']) ?></p>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php renderFooter(); ?>
