<?php
// reports/pdf_helper.php — dompdf PDF Generator Helper
// GuardVAX Hospital System

function generatePdf(string $html, string $filename = 'report.pdf', bool $stream = true): void
{
    $dompdfPath = __DIR__ . '/../vendor/autoload.php';

    // ── dompdf via Composer (recommended) ─────────────────────
    if (file_exists($dompdfPath)) {
        require_once $dompdfPath;

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        if ($stream) {
            $dompdf->stream($filename, ['Attachment' => true]);
        } else {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            echo $dompdf->output();
        }
        exit;
    }

    // ── Fallback: plain HTML output if dompdf not installed ───
    echo '<!DOCTYPE html><html><head>
    <meta charset="UTF-8">
    <title>PDF Preview (dompdf not installed)</title>
    <style>
      body { font-family: Arial, sans-serif; padding: 20px; }
      .notice { background:#fff3cd;border:1px solid #ffc107;padding:12px;border-radius:6px;margin-bottom:20px; }
    </style>
    </head><body>
    <div class="notice">
      <strong>⚠ dompdf not installed.</strong><br>
      Run <code>composer require dompdf/dompdf</code> in the <code>guardvax/</code> directory,<br>
      or run <code>bash install_dompdf.sh</code>.<br>
      Showing HTML preview instead:
    </div>' . $html . '</body></html>';
}

function pdfHeader(string $title, string $subtitle = ''): string
{
    $date = date('F d, Y \a\t h:i A');
    return <<<HTML
<div style="background:#0d6efd;color:#fff;padding:20px 24px;border-radius:6px 6px 0 0;margin-bottom:0">
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <div style="font-size:22px;font-weight:700;letter-spacing:-0.5px">🛡️ GuardVAX</div>
        <div style="font-size:11px;opacity:.8;margin-top:2px">Hospital Vaccination &amp; Health Data System</div>
      </td>
      <td style="text-align:right">
        <div style="font-size:14px;font-weight:600">{$title}</div>
        <div style="font-size:10px;opacity:.8">{$subtitle}</div>
      </td>
    </tr>
  </table>
</div>
<div style="background:#e7f0ff;padding:8px 24px;font-size:10px;color:#555;margin-bottom:20px">
  Generated: {$date} &nbsp;|&nbsp; Confidential — For authorized personnel only
</div>
HTML;
}

function pdfStyles(): string
{
    return <<<CSS
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; color:#222; background:#fff; }
  table { width:100%; border-collapse:collapse; }
  th { background:#0d6efd; color:#fff; padding:7px 10px; text-align:left; font-size:10px; font-weight:600; }
  td { padding:6px 10px; border-bottom:1px solid #e5e5e5; vertical-align:top; }
  tr:nth-child(even) td { background:#f8f9fa; }
  .badge { display:inline-block; padding:2px 8px; border-radius:20px; font-size:9px; font-weight:600; }
  .badge-success { background:#d1e7dd; color:#0a3622; }
  .badge-primary { background:#cfe2ff; color:#084298; }
  .badge-warning { background:#fff3cd; color:#664d03; }
  .section-title { font-size:13px; font-weight:700; color:#0d6efd; border-bottom:2px solid #0d6efd; padding-bottom:4px; margin:18px 0 10px; }
  .info-grid { display:table; width:100%; border:1px solid #dee2e6; border-radius:6px; overflow:hidden; }
  .info-row { display:table-row; }
  .info-label { display:table-cell; background:#f8f9fa; padding:6px 12px; font-weight:600; color:#555; width:35%; font-size:10px; border-bottom:1px solid #dee2e6; }
  .info-value { display:table-cell; padding:6px 12px; border-bottom:1px solid #dee2e6; font-size:10px; }
  .footer { margin-top:30px; text-align:center; font-size:9px; color:#888; border-top:1px solid #dee2e6; padding-top:10px; }
</style>
CSS;
}
