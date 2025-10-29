<?php
require_once 'includes/auth.php';
check_login();

// --- ÖNEMLİ ---
// Bu script, Dompdf kütüphanesini gerektirir.
// Lütfen Composer ile kurun: `composer require dompdf/dompdf`
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die("Hata: Dompdf kütüphanesi bulunamadı. Lütfen proje ana dizininde 'composer require dompdf/dompdf' komutunu çalıştırın.");
}
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: projects.php');
    exit;
}

// Veritabanından tüm proje verilerini çek
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) { die("Proje bulunamadı."); }

$parts_stmt = $pdo->prepare("SELECT pp.*, p.name as printer_name, m.brand as material_brand, m.type as material_type FROM project_parts pp JOIN printers p ON pp.printer_id = p.id JOIN materials m ON pp.material_id = m.id WHERE pp.project_id = ? ORDER BY pp.id ASC");
$parts_stmt->execute([$id]);
$parts = $parts_stmt->fetchAll(PDO::FETCH_ASSOC);

$settings_stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$currency = $settings['currency'] ?? '₺';
$markup = $settings['markup'] ?? 0;

$subtotal = array_sum(array_column($parts, 'total_cost'));
$markup_amount = $subtotal * ($markup / 100);
$total_quote = $subtotal + $markup_amount;

// --- PDF için HTML oluşturmaya başla ---
$html = '<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><title>Teklif - ' . $project['project_number'] . '</title><style>' .
'body { font-family: \'DejaVu Sans\', sans-serif; font-size: 12px; color: #333; }' .
'.container { width: 100%; margin: 0 auto; }' .
'h1 { color: #000; }' .
'.header, .footer { text-align: center; }' .
'.details table, .parts-table, .summary table { width: 100%; border-collapse: collapse; }' .
'.details td { padding: 10px; vertical-align: top; }' .
'.parts-table th, .parts-table td { border: 1px solid #ccc; padding: 8px; }' .
'.parts-table th { background-color: #f2f2f2; }' .
'.summary { float: right; width: 45%; margin-top: 20px; }' .
'.summary th, .summary td { padding: 8px; } .summary th { text-align: right; } .summary .total { font-weight: bold; font-size: 1.3em; border-top: 2px solid #000; } ' .
'</style></head><body><div class="container">' .
'<div class="header"><h1>TEKLİF</h1><p><strong>Proje No:</strong> ' . $project['project_number'] . '<br><strong>Tarih:</strong> ' . date('d.m.Y') . '</p></div>' .
'<div class="details"><table><tr><td style="width:50%;"><strong>Teklifi Veren:</strong><br>3D Print Studio<br>Stüdyo Adresi<br>Vergi No: 1234567890</td><td style="width:50%;"><strong>Müşteri:</strong><br>' . htmlspecialchars($project['customer_name']) . '</td></tr></table></div>' .
'<table class="parts-table"><thead><tr><th>#</th><th>Parça Adı</th><th>Açıklama</th><th style="text-align:right;">Tutar</th></tr></thead><tbody>';

$part_counter = 1;
foreach ($parts as $part) {
    $html .= '<tr><td>' . $part_counter++ . '</td><td>' . htmlspecialchars($part['part_name']) . '</td><td>Baskı: ' . htmlspecialchars($part['printer_name']) . ' | Malzeme: ' . htmlspecialchars($part['material_brand'] . ' ' . $part['material_type']) . '</td><td style="text-align:right;">' . number_format($part['total_cost'], 2) . ' ' . $currency . '</td></tr>';
}

$html .= '</tbody></table>' .
'<div class="summary"><table>' .
'<tr><th>Ara Toplam:</th><td style="text-align:right;">' . number_format($subtotal, 2) . ' ' . $currency . '</td></tr>' .
'<tr><th>Kar Marjı (%' . $markup . '):</th><td style="text-align:right;">' . number_format($markup_amount, 2) . ' ' . $currency . '</td></tr>' .
'<tr class="total"><th>GENEL TOPLAM:</th><td style="text-align:right;">' . number_format($total_quote, 2) . ' ' . $currency . '</td></tr>' .
'</table></div></div></body></html>';
// --- HTML sonu ---

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans'); // Türkçe karakterler için önemli

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// PDF'i tarayıcıda göster
$filename = "Teklif_" . $project['project_number'] . ".pdf";
$dompdf->stream($filename, ['Attachment' => 0]);
?>
