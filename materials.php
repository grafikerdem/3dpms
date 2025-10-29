<?php
$page_title = 'Malzeme Yönetimi';
require_once 'includes/header.php';

// --- ACTION HANDLING ---
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

// Para birimini al
$stmt_settings = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'currency'");
$currency = $stmt_settings->fetchColumn() ?? '₺';

// POST isteğini işle (Ekleme/Güncelleme)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = $_POST['brand'];
    $type = $_POST['type'];
    $color = $_POST['color'];
    $diameter = $_POST['diameter'] ?: null;
    $unit_price = $_POST['unit_price'];
    $stock_amount = $_POST['stock_amount'];
    $stock_unit = $_POST['stock_unit'];
    $low_stock_threshold = $_POST['low_stock_threshold'];
    $id = $_POST['id'] ?? 0;

    if ($id) { // Güncelleme
        $stmt = $pdo->prepare("UPDATE materials SET brand=?, type=?, color=?, diameter=?, unit_price=?, stock_amount=?, stock_unit=?, low_stock_threshold=? WHERE id=?");
        $stmt->execute([$brand, $type, $color, $diameter, $unit_price, $stock_amount, $stock_unit, $low_stock_threshold, $id]);
    } else { // Ekleme
        $stmt = $pdo->prepare("INSERT INTO materials (brand, type, color, diameter, unit_price, stock_amount, stock_unit, low_stock_threshold) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$brand, $type, $color, $diameter, $unit_price, $stock_amount, $stock_unit, $low_stock_threshold]);
    }
    header('Location: materials.php');
    exit;
}

// Silme işlemini yap
if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM materials WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: materials.php');
    exit;
}

// --- VIEW HANDLING ---

if ($action === 'add' || $action === 'edit') {
    $material = null;
    if ($action === 'edit' && $id) {
        $stmt = $pdo->prepare("SELECT * FROM materials WHERE id = ?");
        $stmt->execute([$id]);
        $material = $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>
    <h1 class="mb-4"><?php echo $material ? 'Malzemeyi Düzenle' : 'Yeni Malzeme Ekle'; ?></h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="materials.php">
                <input type="hidden" name="id" value="<?php echo $material['id'] ?? 0; ?>">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="brand" class="form-label">Marka</label>
                        <input type="text" class="form-control" id="brand" name="brand" value="<?php echo htmlspecialchars($material['brand'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="type" class="form-label">Tip (PLA, PETG, ABS, Resin etc.)</label>
                        <input type="text" class="form-control" id="type" name="type" value="<?php echo htmlspecialchars($material['type'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="color" class="form-label">Renk</label>
                        <input type="text" class="form-control" id="color" name="color" value="<?php echo htmlspecialchars($material['color'] ?? ''); ?>">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="unit_price" class="form-label">Birim Fiyatı (<?php echo $currency; ?>)</label>
                        <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" value="<?php echo $material['unit_price'] ?? ''; ?>" required>
                        <div class="form-text">Fiyat, aşağıdaki stok birimi başına olmalıdır (Örn: 1 kg filament fiyatı).</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="stock_amount" class="form-label">Stok Miktarı</label>
                        <input type="number" step="0.01" class="form-control" id="stock_amount" name="stock_amount" value="<?php echo $material['stock_amount'] ?? ''; ?>" required>
                    </div>
                     <div class="col-md-3 mb-3">
                        <label for="stock_unit" class="form-label">Stok Birimi</label>
                        <select class="form-select" id="stock_unit" name="stock_unit">
                            <option value="kg" <?php echo ($material['stock_unit'] ?? '') === 'kg' ? 'selected' : ''; ?>>kg (Kilogram)</option>
                            <option value="g" <?php echo ($material['stock_unit'] ?? '') === 'g' ? 'selected' : ''; ?>>g (Gram)</option>
                            <option value="L" <?php echo ($material['stock_unit'] ?? '') === 'L' ? 'selected' : ''; ?>>L (Litre)</option>
                            <option value="adet" <?php echo ($material['stock_unit'] ?? '') === 'adet' ? 'selected' : ''; ?>>Adet</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="low_stock_threshold" class="form-label">Düşük Stok Eşiği</label>
                        <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" value="<?php echo $material['low_stock_threshold'] ?? 2; ?>" required>
                        <div class="form-text">Bu miktarın altına düşünce uyarı verilir.</div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="diameter" class="form-label">Çap (mm)</label>
                        <input type="number" step="0.01" class="form-control" id="diameter" name="diameter" value="<?php echo $material['diameter'] ?? '1.75'; ?>">
                        <div class="form-text">Sadece filamentler için geçerlidir.</div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="materials.php" class="btn btn-secondary">İptal</a>
                    <button type="submit" class="btn btn-primary"><?php echo $material ? 'Güncelle' : 'Kaydet'; ?></button>
                </div>
            </form>
        </div>
    </div>
<?php
} else { // Liste görünümü (default)
    $stmt = $pdo->query("SELECT * FROM materials ORDER BY brand, type ASC");
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Malzeme Yönetimi</h1>
        <a href="materials.php?action=add" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Yeni Malzeme Ekle</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Marka</th>
                        <th>Tip</th>
                        <th>Renk</th>
                        <th>Stok Durumu</th>
                        <th>Birim Fiyat</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($materials)): ?>
                        <tr><td colspan="6" class="text-center p-4">Henüz malzeme eklenmemiş.</td></tr>
                    <?php else: ?>
                        <?php foreach ($materials as $material):
                            $is_low_stock = $material['stock_amount'] <= $material['low_stock_threshold'];
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($material['brand']); ?></strong></td>
                                <td><?php echo htmlspecialchars($material['type']); ?></td>
                                <td><?php echo htmlspecialchars($material['color']); ?></td>
                                <td>
                                    <span class="fs-5 <?php echo $is_low_stock ? 'text-danger' : '' ; ?>">
                                        <?php echo $material['stock_amount'] . ' ' . htmlspecialchars($material['stock_unit']); ?>
                                    </span>
                                    <?php if ($is_low_stock): ?>
                                        <i class="bi bi-exclamation-triangle-fill text-danger" title="Stok düşük!"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($material['unit_price'], 2) . ' ' . htmlspecialchars($currency) . ' / ' . htmlspecialchars($material['stock_unit']); ?></td>
                                <td class="text-end">
                                    <a href="materials.php?action=edit&id=<?php echo $material['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <a href="materials.php?action=delete&id=<?php echo $material['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu malzemeyi silmek istediğinizden emin misiniz?');"><i class="bi bi-trash"></i></a>
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