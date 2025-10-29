<?php
$page_title = 'Proje Yönetimi';
require_once 'includes/header.php';

// --- ACTION HANDLING ---
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

// POST isteğini işle (Ekleme/Güncelleme)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $status = $_POST['status'];
    $notes = $_POST['notes'] ?? null;
    $id = $_POST['id'] ?? 0;

    if ($id) { // Güncelleme
        $stmt = $pdo->prepare("UPDATE projects SET customer_name=?, status=?, notes=? WHERE id=?");
        $stmt->execute([$customer_name, $status, $notes, $id]);
        
        // Proje durumuna göre parça durumlarını güncelle
        $part_status = 'Bekliyor'; // Varsayılan
        if ($status == 'Teklif') $part_status = 'Bekliyor';
        elseif ($status == 'Onaylandı') $part_status = 'Bekliyor';
        elseif ($status == 'Üretimde') $part_status = 'Baskıda';
        elseif ($status == 'Kalite Kontrol') $part_status = 'Bitti';
        elseif ($status == 'Tamamlandı') $part_status = 'Bitti';
        elseif ($status == 'İptal Edildi') $part_status = 'Başarısız';
        
        // Parça durumlarını güncelle
        $update_parts = $pdo->prepare("UPDATE project_parts SET production_status = ? WHERE project_id = ?");
        $update_parts->execute([$part_status, $id]);
        
        // Debug: Kaç parça güncellendi
        $affected_rows = $update_parts->rowCount();
        
        // Toast bildirimi için redirect
        $toast_message = "Proje durumu '{$status}' olarak güncellendi. {$affected_rows} parça etkilendi.";
        $toast_type = ($status == 'Tamamlandı') ? 'success' : 'info';
        header("Location: projects.php?toast=" . urlencode($toast_message) . "&toast_type={$toast_type}");
        exit;
    } else { // Ekleme
        // Otomatik Proje Numarası Oluştur: PROJE-YIL-SIRA
        $year = date('Y');
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE project_number LIKE ?");
        $stmt->execute(["PROJE-{$year}-%"]);
        $count = $stmt->fetchColumn() + 1;
        $project_number = sprintf("PROJE-%s-%03d", $year, $count);

        $stmt = $pdo->prepare("INSERT INTO projects (project_number, customer_name, status, notes) VALUES (?, ?, ?, ?)");
        $stmt->execute([$project_number, $customer_name, $status, $notes]);
        
        // Toast bildirimi için redirect
        $toast_message = "Yeni proje '{$project_number}' başarıyla oluşturuldu.";
        header("Location: projects.php?toast=" . urlencode($toast_message) . "&toast_type=success");
        exit;
    }
}

// Silme işlemini yap
if ($action === 'delete' && $id) {
    // İlişkili parçaları da silmek için transaction kullanılabilir, şimdilik sadece projeyi siliyoruz.
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    
    // Toast bildirimi için redirect
    $toast_message = "Proje başarıyla silindi.";
    header("Location: projects.php?toast=" . urlencode($toast_message) . "&toast_type=success");
    exit;
}

// --- VIEW HANDLING ---

if ($action === 'add' || $action === 'edit') {
    $project = null;
    if ($action === 'edit' && $id) {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    $statuses = ['Teklif', 'Onaylandı', 'Üretimde', 'Kalite Kontrol', 'İptal Edildi', 'Tamamlandı'];
?>
    <h1 class="mb-4"><?php echo $project ? 'Projeyi Düzenle' : 'Yeni Proje Oluştur'; ?></h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="projects.php">
                <input type="hidden" name="id" value="<?php echo $project['id'] ?? 0; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer_name" class="form-label">Müşteri Adı</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($project['customer_name'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Proje Durumu</label>
                        <select class="form-select" id="status" name="status">
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?php echo $status; ?>" <?php echo ($project['status'] ?? 'Teklif') === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Proje Notları</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4"><?php echo htmlspecialchars($project['notes'] ?? ''); ?></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="projects.php" class="btn btn-secondary">İptal</a>
                    <button type="submit" class="btn btn-primary"><?php echo $project ? 'Güncelle' : 'Kaydet'; ?></button>
                </div>
            </form>
        </div>
    </div>
<?php
} else { // Liste görünümü (default)
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Proje Yönetimi</h1>
        <a href="projects.php?action=add" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Yeni Proje Oluştur</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Proje No</th>
                        <th>Müşteri</th>
                        <th>Durum</th>
                        <th>Oluşturma Tarihi</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr><td colspan="5" class="text-center p-4">Henüz proje oluşturulmamış.</td></tr>
                    <?php else: ?>
                        <?php foreach ($projects as $project):
                            // Duruma göre renk belirle
                            $status_color = match($project['status']) {
                                'Teklif' => 'secondary',
                                'Onaylandı' => 'info',
                                'Üretimde' => 'primary',
                                'Kalite Kontrol' => 'warning',
                                'Tamamlandı' => 'success',
                                'İptal Edildi' => 'danger',
                                default => 'light',
                            };
                        ?>
                            <tr>
                                <td><a href="project_details.php?id=<?php echo $project['id']; ?>"><strong><?php echo htmlspecialchars($project['project_number']); ?></strong></a></td>
                                <td><?php echo htmlspecialchars($project['customer_name']); ?></td>
                                <td><span class="badge bg-<?php echo $status_color; ?>"><?php echo htmlspecialchars($project['status']); ?></span></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($project['created_at'])); ?></td>
                                <td class="text-end">
                                    <a href="project_details.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i> Detay</a>
                                    <a href="projects.php?action=edit&id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <a href="projects.php?action=delete&id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu projeyi ve tüm parçalarını silmek istediğinizden emin misiniz?');"><i class="bi bi-trash"></i></a>
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