<?php
$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: projects.php');
    exit;
}

require_once 'includes/header.php';

// Proje bilgilerini al
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    echo "<div class='alert alert-danger'>Proje bulunamadı.</div>";
    require_once 'includes/footer.php';
    exit;
}

$page_title = "Proje Detayları: {" . htmlspecialchars($project['project_number']) . "}";

// Projeye ait parçaları al
$parts_stmt = $pdo->prepare("SELECT pp.*, p.name as printer_name, m.brand as material_brand, m.type as material_type FROM project_parts pp JOIN printers p ON pp.printer_id = p.id JOIN materials m ON pp.material_id = m.id WHERE pp.project_id = ? ORDER BY pp.id ASC");
$parts_stmt->execute([$id]);
$parts = $parts_stmt->fetchAll(PDO::FETCH_ASSOC);

// Ayarları, yazıcıları ve malzemeleri form için al
$settings_stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$printers = $pdo->query("SELECT id, name FROM printers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$materials = $pdo->query("SELECT id, brand, type, color FROM materials ORDER BY brand, type ASC")->fetchAll(PDO::FETCH_ASSOC);

$currency = $settings['currency'] ?? '₺';
$markup = $settings['markup'] ?? 0;

// Toplam maliyeti hesapla
$subtotal = 0;
foreach ($parts as $part) {
    $subtotal += $part['total_cost'];
}
$total_quote = $subtotal * (1 + ($markup / 100));

// Duruma göre renk belirle
$status_color = 'light';
if ($project['status'] == 'Teklif') $status_color = 'secondary';
elseif ($project['status'] == 'Onaylandı') $status_color = 'info';
elseif ($project['status'] == 'Üretimde') $status_color = 'primary';
elseif ($project['status'] == 'Kalite Kontrol') $status_color = 'warning';
elseif ($project['status'] == 'Tamamlandı') $status_color = 'success';
elseif ($project['status'] == 'İptal Edildi') $status_color = 'danger';

?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="mb-0"><?php echo htmlspecialchars($project['project_number']); ?></h1>
        <p class="text-muted mb-0">Müşteri: <?php echo htmlspecialchars($project['customer_name']); ?></p>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-<?php echo $status_color; ?> fs-6"><?php echo htmlspecialchars($project['status']); ?></span>
        <?php if ($project['status'] == 'Teklif' && !empty($parts)): ?>
            <form method="POST" action="projects.php?action=edit&id=<?php echo $project['id']; ?>" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($project['customer_name']); ?>">
                <input type="hidden" name="status" value="Onaylandı">
                <input type="hidden" name="notes" value="<?php echo htmlspecialchars($project['notes'] ?? ''); ?>">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Onayla ve Üretime Geç</button>
            </form>
        <?php elseif ($project['status'] == 'Üretimde'): ?>
            <form method="POST" action="projects.php?action=edit&id=<?php echo $project['id']; ?>" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($project['customer_name']); ?>">
                <input type="hidden" name="status" value="Kalite Kontrol">
                <input type="hidden" name="notes" value="<?php echo htmlspecialchars($project['notes'] ?? ''); ?>">
                <button type="submit" class="btn btn-primary"><i class="bi bi-clipboard-check"></i> Kalite Kontrol'e Gönder</button>
            </form>
            <form method="POST" action="projects.php?action=edit&id=<?php echo $project['id']; ?>" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($project['customer_name']); ?>">
                <input type="hidden" name="status" value="Tamamlandı">
                <input type="hidden" name="notes" value="<?php echo htmlspecialchars($project['notes'] ?? ''); ?>">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-all"></i> Tamamla</button>
            </form>
        <?php elseif ($project['status'] == 'Kalite Kontrol'): ?>
            <form method="POST" action="projects.php?action=edit&id=<?php echo $project['id']; ?>" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($project['customer_name']); ?>">
                <input type="hidden" name="status" value="Tamamlandı">
                <input type="hidden" name="notes" value="<?php echo htmlspecialchars($project['notes'] ?? ''); ?>">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-all"></i> Tamamlandı Olarak İşaretle</button>
            </form>
        <?php elseif ($project['status'] == 'Onaylandı'): ?>
            <form method="POST" action="projects.php?action=edit&id=<?php echo $project['id']; ?>" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($project['customer_name']); ?>">
                <input type="hidden" name="status" value="Üretimde">
                <input type="hidden" name="notes" value="<?php echo htmlspecialchars($project['notes'] ?? ''); ?>">
                <button type="submit" class="btn btn-primary"><i class="bi bi-gear"></i> Üretime Başla</button>
            </form>
        <?php endif; ?>
        <a href="generate_pdf.php?id=<?php echo $project['id']; ?>" class="btn btn-outline-primary" target="_blank"><i class="bi bi-printer"></i> Yazdır/PDF</a>
        <a href="projects.php" class="btn btn-secondary">Geri Dön</a>
    </div>
</div>

<hr>

<div class="row g-4">
    <!-- Parça Listesi -->
    <div class="col-lg-8">
        <h4>Proje Parçaları</h4>
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Parça Adı</th>
                            <th>Yazıcı</th>
                            <th>Malzeme</th>
                            <th class="text-end">Maliyet</th>
                            <th class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($parts)): ?>
                            <tr><td colspan="5" class="text-center p-4">Bu projeye henüz parça eklenmemiş.</td></tr>
                        <?php else: ?>
                            <?php foreach ($parts as $part): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($part['part_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($part['printer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($part['material_brand'] . ' ' . $part['material_type']); ?></td>
                                    <td class="text-end"><?php echo number_format($part['total_cost'], 2) . ' ' . $currency; ?></td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <a href="#" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Yeni Parça Ekleme Formu -->
        <div class="card shadow-sm mt-4">
            <div class="card-header">Yeni Parça Ekle</div>
            <div class="card-body">
                <form method="POST" action="handle_part.php">
                    <input type="hidden" name="project_id" value="<?php echo $id; ?>">
                    <div class="mb-3">
                        <label for="part_name" class="form-label">Parça Adı</label>
                        <input type="text" class="form-control" name="part_name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="printer_id" class="form-label">Yazıcı</label>
                            <select class="form-select" name="printer_id" required>
                                <option value="">Seçiniz...</option>
                                <?php foreach ($printers as $printer): ?>
                                    <option value="<?php echo $printer['id']; ?>"><?php echo htmlspecialchars($printer['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="material_id" class="form-label">Malzeme</label>
                            <select class="form-select" name="material_id" required>
                                <option value="">Seçiniz...</option>
                                <?php foreach ($materials as $material): ?>
                                    <option value="<?php echo $material['id']; ?>"><?php echo htmlspecialchars($material['brand'] . ' ' . $material['type'] . ' - ' . $material['color']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="material_amount_grams" class="form-label">Malzeme Miktarı (gram)</label>
                            <input type="number" step="0.1" class="form-control" name="material_amount_grams" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="print_time_hours" class="form-label">Baskı Süresi (saat)</label>
                            <input type="number" step="0.1" class="form-control" name="print_time_hours" required>
                        </div>
                    </div>
                    <hr>
                    <p class="text-muted">İşçilik Süreleri (dakika)</p>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="design_time_minutes" class="form-label">Tasarım/Dilimleme</label>
                            <input type="number" class="form-control" name="design_time_minutes" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="setup_time_minutes" class="form-label">Hazırlık/Başlatma</label>
                            <input type="number" class="form-control" name="setup_time_minutes" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="postprocess_time_minutes" class="form-label">Son İşlem</label>
                            <input type="number" class="form-control" name="postprocess_time_minutes" value="0">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Parçayı Ekle</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Maliyet Özeti -->
    <div class="col-lg-4">
        <h4>Teklif Özeti</h4>
        <div class="card shadow-sm">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Ara Toplam
                        <span><?php echo number_format($subtotal, 2) . ' ' . $currency; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Kar Marjı (%<?php echo $markup; ?>)
                        <span><?php echo number_format($subtotal * ($markup / 100), 2) . ' ' . $currency; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center fs-4 fw-bold">
                        Genel Toplam
                        <span><?php echo number_format($total_quote, 2) . ' ' . $currency; ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
