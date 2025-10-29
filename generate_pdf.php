<?php
$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: projects.php');
    exit;
}

// Veritabanı bağlantısı
require_once 'includes/database.php';

// Proje bilgilerini al
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("Proje bulunamadı.");
}

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
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teklif - <?php echo htmlspecialchars($project['project_number']); ?></title>
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 32px;
        }
        .header p {
            margin: 10px 0;
            font-size: 14px;
        }
        .company-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 15px;
            background: #f8f9fa;
        }
        .company-info > div {
            width: 48%;
        }
        .company-info strong {
            display: block;
            margin-bottom: 5px;
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table th {
            background-color: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        table tr:hover {
            background-color: #f8f9fa;
        }
        .summary {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .summary table {
            width: 100%;
        }
        .summary td {
            text-align: right;
            padding: 8px;
        }
        .summary .total {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #007bff;
            background-color: #e7f3ff;
        }
        .footer {
            clear: both;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ccc;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn-print:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button class="btn-print no-print" onclick="window.print()">🖨️ Yazdır / PDF Olarak Kaydet</button>

    <div class="header">
        <h1>TEKLİF</h1>
        <p>
            <strong>Proje No:</strong> <?php echo htmlspecialchars($project['project_number']); ?><br>
            <strong>Tarih:</strong> <?php echo date('d.m.Y'); ?>
        </p>
    </div>

    <div class="company-info">
        <div>
            <strong>Teklifi Veren:</strong>
            <p>
                3D Print Studio<br>
                [Stüdyo Adresiniz]<br>
                Tel: [Telefon]<br>
                E-posta: [E-posta]
            </p>
        </div>
        <div>
            <strong>Müşteri:</strong>
            <p><?php echo htmlspecialchars($project['customer_name']); ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="30%">Parça Adı</th>
                <th width="40%">Açıklama</th>
                <th width="25%" style="text-align:right;">Tutar</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($parts)): ?>
                <tr><td colspan="4" style="text-align:center;">Bu projeye henüz parça eklenmemiş.</td></tr>
            <?php else: ?>
                <?php $counter = 1; foreach ($parts as $part): ?>
                <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><?php echo htmlspecialchars($part['part_name']); ?></td>
                    <td>
                        Yazıcı: <?php echo htmlspecialchars($part['printer_name']); ?><br>
                        Malzeme: <?php echo htmlspecialchars($part['material_brand'] . ' ' . $part['material_type']); ?><br>
                        <small>Miktar: <?php echo $part['material_amount_grams']; ?>g | Süre: <?php echo $part['print_time_hours']; ?>h</small>
                    </td>
                    <td style="text-align:right;"><?php echo number_format($part['total_cost'], 2) . ' ' . $currency; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td>Ara Toplam:</td>
                <td><?php echo number_format($subtotal, 2) . ' ' . $currency; ?></td>
            </tr>
            <tr>
                <td>Kar Marjı (<?php echo $markup; ?>%):</td>
                <td><?php echo number_format($markup_amount, 2) . ' ' . $currency; ?></td>
            </tr>
            <tr class="total">
                <td>GENEL TOPLAM:</td>
                <td><?php echo number_format($total_quote, 2) . ' ' . $currency; ?></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>
            <strong>Not:</strong> <?php echo htmlspecialchars($project['notes'] ?? 'Bu teklif 30 gün süreyle geçerlidir.'); ?><br><br>
            Bu teklif elektronik ortamda oluşturulmuştur ve yasal bir geçerliliği vardır.
        </p>
    </div>

    <script>
        // Yazdır butonuna tıklandığında tarayıcı yazdırma penceresini aç
        document.querySelector('.btn-print').addEventListener('click', function() {
            window.print();
        });
    </script>
</body>
</html>
