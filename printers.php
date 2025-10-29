<?php
$page_title = 'Yazıcı Yönetimi';
require_once 'includes/header.php';

// --- ACTION HANDLING ---
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

// Ayarları ve para birimini al
$stmt_settings = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('electricity_cost', 'currency')");
$settings = $stmt_settings->fetchAll(PDO::FETCH_KEY_PAIR);
$electricity_cost = $settings['electricity_cost'] ?? 0;
$currency = $settings['currency'] ?? '₺';

// POST isteğini işle (Ekleme/Güncelleme)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $technology = $_POST['technology'];
    $purchase_price = $_POST['purchase_price'];
    $purchase_date = $_POST['purchase_date'];
    $lifespan_hours = $_POST['lifespan_hours'];
    $power_consumption_watts = $_POST['power_consumption_watts'];
    $id = $_POST['id'] ?? 0;

    if ($id) { // Güncelleme
        $stmt = $pdo->prepare("UPDATE printers SET name=?, brand=?, model=?, technology=?, purchase_price=?, purchase_date=?, lifespan_hours=?, power_consumption_watts=? WHERE id=?");
        $stmt->execute([$name, $brand, $model, $technology, $purchase_price, $purchase_date, $lifespan_hours, $power_consumption_watts, $id]);
    } else { // Ekleme
        $stmt = $pdo->prepare("INSERT INTO printers (name, brand, model, technology, purchase_price, purchase_date, lifespan_hours, power_consumption_watts) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $brand, $model, $technology, $purchase_price, $purchase_date, $lifespan_hours, $power_consumption_watts]);
    }
    header('Location: printers.php');
    exit;
}

// Silme işlemini yap
if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM printers WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: printers.php');
    exit;
}

// --- VIEW HANDLING ---

if ($action === 'add' || $action === 'edit') {
    $printer = null;
    if ($action === 'edit' && $id) {
        $stmt = $pdo->prepare("SELECT * FROM printers WHERE id = ?");
        $stmt->execute([$id]);
        $printer = $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>
    <h1 class="mb-4"><?php echo $printer ? 'Yazıcıyı Düzenle' : 'Yeni Yazıcı Ekle'; ?></h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="printers.php">
                <input type="hidden" name="id" value="<?php echo $printer['id'] ?? 0; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Yazıcı Adı (Örn: Bambu Lab P1S - 1)</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($printer['name'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="technology" class="form-label">Teknoloji</label>
                        <select class="form-select" id="technology" name="technology">
                            <option value="FDM" <?php echo ($printer['technology'] ?? '') === 'FDM' ? 'selected' : ''; ?>>FDM</option>
                            <option value="SLA" <?php echo ($printer['technology'] ?? '') === 'SLA' ? 'selected' : ''; ?>>SLA</option>
                            <option value="MJF" <?php echo ($printer['technology'] ?? '') === 'MJF' ? 'selected' : ''; ?>>MJF</option>
                        </select>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="brand" class="form-label">Marka</label>
                        <input type="text" class="form-control" id="brand" name="brand" value="<?php echo htmlspecialchars($printer['brand'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="model" class="form-label">Model</label>
                        <input type="text" class="form-control" id="model" name="model" value="<?php echo htmlspecialchars($printer['model'] ?? ''); ?>">
                    </div>
                </div>
                <hr>
                <p class="text-muted">Maliyet Hesaplama Bilgileri</p>
                <div class="row">
                     <div class="col-md-3 mb-3">
                        <label for="purchase_price" class="form-label">Satın Alma Fiyatı (<?php echo $currency; ?>)</label>
                        <input type="number" step="0.01" class="form-control" id="purchase_price" name="purchase_price" value="<?php echo $printer['purchase_price'] ?? ''; ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="purchase_date" class="form-label">Satın Alma Tarihi</label>
                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?php echo $printer['purchase_date'] ?? ''; ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="lifespan_hours" class="form-label">Ekonomik Ömür (Saat)</label>
                        <input type="number" class="form-control" id="lifespan_hours" name="lifespan_hours" value="<?php echo $printer['lifespan_hours'] ?? 20000; ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="power_consumption_watts" class="form-label">Güç Tüketimi (Watt)</label>
                        <input type="number" class="form-control" id="power_consumption_watts" name="power_consumption_watts" value="<?php echo $printer['power_consumption_watts'] ?? 350; ?>" required>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="printers.php" class="btn btn-secondary">İptal</a>
                    <button type="submit" class="btn btn-primary"><?php echo $printer ? 'Güncelle' : 'Kaydet'; ?></button>
                </div>
            </form>
        </div>
    </div>
<?php
} else { // Liste görünümü (default)
    $stmt = $pdo->query("SELECT * FROM printers ORDER BY name ASC");
    $printers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Yazıcı Yönetimi</h1>
        <a href="printers.php?action=add" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Yeni Yazıcı Ekle</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Yazıcı Adı</th>
                        <th>Marka/Model</th>
                        <th>Teknoloji</th>
                        <th>Saatlik Maliyet</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($printers)): ?>
                        <tr><td colspan="5" class="text-center p-4">Henüz yazıcı eklenmemiş.</td></tr>
                    <?php else: ?>
                        <?php foreach ($printers as $printer):
                            $amortization_per_hour = $printer['lifespan_hours'] > 0 ? ($printer['purchase_price'] / $printer['lifespan_hours']) : 0;
                            $energy_cost_per_hour = ($printer['power_consumption_watts'] / 1000) * $electricity_cost;
                            $total_hourly_cost = $amortization_per_hour + $energy_cost_per_hour;
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($printer['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($printer['brand'] . ' ' . $printer['model']); ?></td>
                                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($printer['technology']); ?></span></td>
                                <td>
                                    <strong class="fs-5"><?php echo number_format($total_hourly_cost, 2) . ' ' . htmlspecialchars($currency); ?></strong>
                                    <small class="d-block text-muted">
                                        (Amortisman: <?php echo number_format($amortization_per_hour, 2); ?> + 
                                        Enerji: <?php echo number_format($energy_cost_per_hour, 2); ?>)
                                    </small>
                                </td>
                                <td class="text-end">
                                    <a href="printers.php?action=edit&id=<?php echo $printer['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <a href="printers.php?action=delete&id=<?php echo $printer['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu yazıcıyı silmek istediğinizden emin misiniz?');"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}

require_once 'includes/footer.php';
?>