<?php
$page_title = 'Genel Ayarlar';
require_once 'includes/header.php';

// Ayarları güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings_to_update = [
        'currency', 
        'designer_rate', 
        'operator_rate', 
        'electricity_cost', 
        'markup'
    ];
    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    foreach ($settings_to_update as $key) {
        if (isset($_POST[$key])) {
            $stmt->execute([$_POST[$key], $key]);
        }
    }
    // Başarı mesajı için bir parametre ile yönlendir
    header('Location: settings.php?success=1');
    exit;
}

// Mevcut ayarları al
$settings_stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

?>

<h1 class="mb-4">Genel Ayarlar</h1>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Ayarlar başarıyla güncellendi.</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="settings.php">
            <h5 class="card-title mb-3">Maliyet ve Fiyatlandırma</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="designer_rate" class="form-label">Tasarımcı Saatlik Ücreti</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" id="designer_rate" name="designer_rate" value="<?php echo htmlspecialchars($settings['designer_rate'] ?? ''); ?>">
                        <span class="input-group-text"><?php echo htmlspecialchars($settings['currency'] ?? '₺'); ?> / saat</span>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="operator_rate" class="form-label">Operatör Saatlik Ücreti</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" id="operator_rate" name="operator_rate" value="<?php echo htmlspecialchars($settings['operator_rate'] ?? ''); ?>">
                        <span class="input-group-text"><?php echo htmlspecialchars($settings['currency'] ?? '₺'); ?> / saat</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="electricity_cost" class="form-label">Elektrik Maliyeti (kWh)</label>
                     <div class="input-group">
                        <input type="number" step="0.01" class="form-control" id="electricity_cost" name="electricity_cost" value="<?php echo htmlspecialchars($settings['electricity_cost'] ?? ''); ?>">
                        <span class="input-group-text"><?php echo htmlspecialchars($settings['currency'] ?? '₺'); ?> / kWh</span>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="markup" class="form-label">Genel Kar Marjı</label>
                    <div class="input-group">
                        <input type="number" step="1" class="form-control" id="markup" name="markup" value="<?php echo htmlspecialchars($settings['markup'] ?? ''); ?>">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
            <hr>
            <h5 class="card-title mb-3">Genel</h5>
            <div class="row">
                 <div class="col-md-6 mb-3">
                    <label for="currency" class="form-label">Para Birimi</label>
                    <select class="form-select" id="currency" name="currency">
                        <option value="₺" <?php echo ($settings['currency'] ?? '') === '₺' ? 'selected' : ''; ?>>Türk Lirası (₺)</option>
                        <option value="$" <?php echo ($settings['currency'] ?? '') === '$' ? 'selected' : ''; ?>>Dolar ($)</option>
                        <option value="€" <?php echo ($settings['currency'] ?? '') === '€' ? 'selected' : ''; ?>>Euro (€)</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-primary">Ayarları Kaydet</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>