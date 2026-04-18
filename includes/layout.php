<?php
// includes/layout.php — Shared Layout Components
// Call renderHead($title) and renderNav($user) in each page.

function renderHead(string $title, array $extraCss = []): void
{
    $siteName = SITE_NAME;
    $siteUrl  = SITE_URL;
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$title} — {$siteName}</title>
  <!-- Bootstrap 5.3 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
  <!-- GuardVAX CSS -->
  <link rel="stylesheet" href="{$siteUrl}/assets/css/style.css">
HTML;
    foreach ($extraCss as $css) {
        echo "  <link rel=\"stylesheet\" href=\"{$css}\">\n";
    }
    echo "</head>\n<body>\n";
}

function renderNav(array $user): void
{
    $role    = $user['role_name'];
    $name    = htmlspecialchars($user['name']);
    $roleLabel = ucfirst($role);
    $siteUrl = SITE_URL;

    $navLinks = match($role) {
        'admin' => [
            ['href' => '/admin/index.php',          'icon' => 'speedometer2',           'label' => 'Dashboard'],
            ['href' => '/admin/users.php',           'icon' => 'people-fill',            'label' => 'Users'],
            ['href' => '/admin/departments.php',     'icon' => 'building-fill',          'label' => 'Departments'],
            ['href' => '/admin/patients.php',        'icon' => 'person-badge',           'label' => 'Patients'],
            ['href' => '/admin/vaccines.php',        'icon' => 'capsule',                'label' => 'Vaccines'],
            ['href' => '/admin/inventory.php',       'icon' => 'box-seam-fill',          'label' => 'Inventory'],
            ['href' => '/admin/billing.php',         'icon' => 'receipt',                'label' => 'Billing'],
            ['href' => '/admin/medical_records.php', 'icon' => 'file-medical-fill',      'label' => 'Medical Records'],
            ['href' => '/admin/reports.php',         'icon' => 'file-earmark-bar-graph', 'label' => 'Reports'],
            ['href' => '/admin/audit_logs.php',      'icon' => 'journal-text',           'label' => 'Audit Logs'],
            ['href' => '/admin/settings.php',        'icon' => 'gear-fill',              'label' => 'Settings'],
        ],
        'nurse' => [
            ['href' => '/nurse/index.php',           'icon' => 'speedometer2',      'label' => 'Dashboard'],
            ['href' => '/nurse/appointments.php',    'icon' => 'calendar2-check',   'label' => 'Appointments'],
            ['href' => '/nurse/admissions.php',      'icon' => 'hospital-fill',     'label' => 'Admissions'],
            ['href' => '/nurse/patients.php',        'icon' => 'people-fill',       'label' => 'Patients'],
            ['href' => '/nurse/vaccinations.php',    'icon' => 'syringe',           'label' => 'Vaccinations'],
            ['href' => '/nurse/prescriptions.php',   'icon' => 'capsule-pill',      'label' => 'Prescriptions'],
            ['href' => '/nurse/lab_results.php',     'icon' => 'eyedropper',        'label' => 'Lab Results'],
            ['href' => '/nurse/medical_records.php', 'icon' => 'file-medical-fill', 'label' => 'Medical Records'],
            ['href' => '/nurse/search.php',          'icon' => 'search',            'label' => 'Search'],
        ],
        'patient' => [
            ['href' => '/patient/index.php',         'icon' => 'speedometer2',    'label' => 'Dashboard'],
            ['href' => '/patient/profile.php',       'icon' => 'person-circle',   'label' => 'My Profile'],
            ['href' => '/patient/appointments.php',  'icon' => 'calendar2-check', 'label' => 'Appointments'],
            ['href' => '/patient/admissions.php',    'icon' => 'hospital',        'label' => 'Admissions'],
            ['href' => '/patient/vaccinations.php',  'icon' => 'syringe',         'label' => 'Vaccinations'],
            ['href' => '/patient/records.php',       'icon' => 'file-medical',    'label' => 'Medical Records'],
            ['href' => '/patient/prescriptions.php', 'icon' => 'capsule-pill',    'label' => 'Prescriptions'],
            ['href' => '/patient/lab_results.php',   'icon' => 'eyedropper',      'label' => 'Lab Results'],
            ['href' => '/patient/billing.php',       'icon' => 'receipt',         'label' => 'My Bills'],
        ],
        default => [],
    };

    $current = $_SERVER['REQUEST_URI'];
    $links = '';
    foreach ($navLinks as $link) {
        $active = str_contains($current, $link['href']) ? 'active' : '';
        $links .= <<<HTML
        <li class="nav-item">
          <a class="nav-link {$active}" href="{$siteUrl}{$link['href']}">
            <i class="bi bi-{$link['icon']}"></i>
            <span>{$link['label']}</span>
          </a>
        </li>
HTML;
    }

    echo <<<HTML
<div class="gvx-wrapper">
<!-- ── Sidebar ── -->
<aside class="gvx-sidebar" id="gvxSidebar">
  <div class="sidebar-brand">
    <span class="brand-icon">🛡️</span>
    <span class="brand-name">Guard<strong>VAX</strong></span>
  </div>
  <nav class="sidebar-nav">
    <ul class="nav flex-column">
      {$links}
    </ul>
  </nav>
  <div class="sidebar-footer">
    <div class="user-pill">
      <div class="user-avatar">{$name[0]}</div>
      <div class="user-info">
        <div class="user-name">{$name}</div>
        <div class="user-role badge bg-role-{$role}">{$roleLabel}</div>
      </div>
    </div>
    <a href="{$siteUrl}/auth/logout.php" class="btn-logout" title="Logout">
      <i class="bi bi-box-arrow-right"></i>
    </a>
  </div>
</aside>

<!-- ── Main Content ── -->
<main class="gvx-main">
  <!-- Top Bar -->
  <header class="gvx-topbar">
    <button class="sidebar-toggle d-lg-none" onclick="document.getElementById('gvxSidebar').classList.toggle('show')">
      <i class="bi bi-list"></i>
    </button>
    <div class="topbar-title" id="pageTitle"></div>
    <div class="topbar-actions">
      <span class="text-muted small d-none d-md-block">{$name} &bull; {$roleLabel}</span>
    </div>
  </header>
  <div class="gvx-content">
HTML;
}

function renderFooter(): void
{
    $siteUrl = SITE_URL;
    echo <<<HTML
  </div><!-- /.gvx-content -->
</main>
</div><!-- /.gvx-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{$siteUrl}/assets/js/main.js"></script>
</body>
</html>
HTML;
}

function renderPageHeader(string $title, string $subtitle = '', string $icon = 'clipboard2-pulse'): void
{
    echo <<<HTML
    <div class="page-header">
      <div>
        <h1 class="page-title"><i class="bi bi-{$icon}"></i> {$title}</h1>
        <p class="page-subtitle text-muted mb-0">{$subtitle}</p>
      </div>
    </div>
HTML;
}
