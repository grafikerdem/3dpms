<?php
$page_title = 'Üretim Planlama';
require_once 'includes/header.php';

// --- İŞ ATAMA İSTEĞİNİ İŞLE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_job'])) {
    $part_id = $_POST['part_id'];
    $printer_id = $_POST['printer_id'];

    // Parça bilgilerini al
    $part_stmt = $pdo->prepare("SELECT project_id, print_time_hours FROM project_parts WHERE id = ?");
    $part_stmt->execute([$part_id]);
    $part = $part_stmt->fetch(PDO::FETCH_ASSOC);

    if ($part) {
        $start_time = new DateTime();
        $end_time = (clone $start_time)->add(new DateInterval('PT' . floor($part['print_time_hours']) . 'H' . round(($part['print_time_hours'] - floor($part['print_time_hours'])) * 60) . 'M'));

        // Parçayı güncelle
        $update_stmt = $pdo->prepare(
            "UPDATE project_parts SET scheduled_printer_id = ?, scheduled_start_time = ?, scheduled_end_time = ?, production_status = 'Baskıda' WHERE id = ?"
        );
        $update_stmt->execute([$printer_id, $start_time->format('Y-m-d H:i:s'), $end_time->format('Y-m-d H:i:s'), $part_id]);

        // Proje durumunu 'Üretimde' olarak güncelle
        $pdo->prepare("UPDATE projects SET status = 'Üretimde' WHERE id = ?")->execute([$part['project_id']]);
    }

    header('Location: production.php');
    exit;
}

// Henüz zamanlanmamış ve bekleyen parçaları al
$stmt = $pdo->prepare(
    "SELECT pp.*, pr.project_number, pr.customer_name, pr.status as project_status
     FROM project_parts pp 
     JOIN projects pr ON pp.project_id = pr.id 
     WHERE pp.scheduled_start_time IS NULL 
     AND pp.production_status IN ('Bekliyor', 'Baskıda')
     AND pr.status NOT IN ('Tamamlandı', 'İptal Edildi')
     ORDER BY pr.created_at ASC"
);
$stmt->execute();
$waiting_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$printers = $pdo->query("SELECT id, name FROM printers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="mb-4">Üretim Planlama</h1>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header">Bekleyen İşler</div>
            <div class="card-body">
                <table class="table table-sm table-hover align-middle">
                    <tbody>
                        <?php if (empty($waiting_jobs)): ?>
                            <tr><td class="text-center p-3">Üretim için bekleyen iş bulunmuyor.</td></tr>
                        <?php else: ?>
                            <?php foreach ($waiting_jobs as $job): ?>
                                <tr>
                                    <td><a href="project_details.php?id=<?php echo $job['project_id']; ?>"><strong><?php echo htmlspecialchars($job['project_number']); ?></strong></a></td>
                                    <td><?php echo htmlspecialchars($job['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($job['part_name']); ?></td>
                                    <td><?php echo htmlspecialchars($job['print_time_hours']); ?> saat</td>
                                    <td style="width: 300px;">
                                        <form method="POST" action="production.php" class="d-flex">
                                            <input type="hidden" name="part_id" value="<?php echo $job['id']; ?>">
                                            <select class="form-select form-select-sm" name="printer_id" required>
                                                <option value="">Yazıcı Seç...</option>
                                                <?php foreach ($printers as $printer): ?>
                                                    <option value="<?php echo $printer['id']; ?>"><?php echo htmlspecialchars($printer['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" name="assign_job" class="btn btn-sm btn-primary ms-2">Ata</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Aktif Üretim İşleri</span>
        <button class="btn btn-sm btn-outline-primary" onclick="toggleView()">📅 Takvim Görünümü</button>
    </div>
    <div class="card-body" id="jobsListView">
        <?php 
        // Zamanlanmış işleri al (sadece aktif olanlar)
        $scheduled_stmt = $pdo->prepare(
            "SELECT pp.*, pr.project_number, pr.customer_name, p.name as printer_name
             FROM project_parts pp 
             JOIN projects pr ON pp.project_id = pr.id
             LEFT JOIN printers p ON pp.scheduled_printer_id = p.id
             WHERE pp.scheduled_start_time IS NOT NULL 
             AND pp.scheduled_printer_id IS NOT NULL
             AND pr.status NOT IN ('Tamamlandı', 'İptal Edildi')
             ORDER BY pp.scheduled_start_time ASC"
        );
        $scheduled_stmt->execute();
        $scheduled_jobs = $scheduled_stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        
        <?php if (empty($scheduled_jobs)): ?>
            <p class="text-muted text-center p-3">Takvimde görüntülenecek iş bulunmuyor.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Proje No</th>
                            <th>Müşteri</th>
                            <th>Parça Adı</th>
                            <th>Yazıcı</th>
                            <th>Başlangıç</th>
                            <th>Bitiş</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scheduled_jobs as $job): ?>
                            <tr>
                                <td><a href="project_details.php?id=<?php echo $job['project_id']; ?>"><strong><?php echo htmlspecialchars($job['project_number']); ?></strong></a></td>
                                <td><?php echo htmlspecialchars($job['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($job['part_name']); ?></td>
                                <td><?php echo htmlspecialchars($job['printer_name'] ?? 'N/A'); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($job['scheduled_start_time'])); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($job['scheduled_end_time'])); ?></td>
                                <td>
                                    <?php 
                                    $job_status_color = 'primary';
                                    if ($job['production_status'] == 'Bitti') $job_status_color = 'success';
                                    elseif ($job['production_status'] == 'Başarısız') $job_status_color = 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $job_status_color; ?>"><?php echo htmlspecialchars($job['production_status']); ?></span>
                                </td>
                                <td>
                                    <a href="project_details.php?id=<?php echo $job['project_id']; ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i> Detay</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Takvim Görünümü (Başlangıçta gizli) -->
<div class="card shadow-sm" id="calendarView" style="display: none;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Üretim Takvimi</span>
        <button class="btn btn-sm btn-outline-primary" onclick="toggleView()">📋 Liste Görünümü</button>
    </div>
    <div class="card-body">
        <?php
        // Bugünün tarihini ve geçmiş 7 günü al
        $today = new DateTime();
        $days = [];
        for ($i = -7; $i <= 7; $i++) {
            $date = (clone $today)->modify("$i days");
            $days[] = $date->format('Y-m-d');
        }
        
        // Yazıcıları al
        $all_printers = $pdo->query("SELECT id, name FROM printers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="table-responsive">
            <table class="table table-sm table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 120px;">Yazıcı</th>
                        <?php foreach ($days as $day): ?>
                            <?php 
                            $day_date = DateTime::createFromFormat('Y-m-d', $day);
                            $is_today = $day == $today->format('Y-m-d');
                            $day_label = $day_date->format('d.m D');
                            ?>
                            <th class="<?php echo $is_today ? 'table-warning' : ''; ?>" style="min-width: 100px;">
                                <?php echo $day_label; ?>
                                <?php if ($is_today): ?>
                                    <div class="badge bg-warning">Bugün</div>
                                <?php endif; ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_printers as $printer): ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($printer['name']); ?></td>
                            <?php foreach ($days as $day): ?>
                                <td class="p-1" style="height: 60px; vertical-align: middle;">
                                    <?php
                                    // Bu yazıcıda bu gün iş var mı kontrol et
                                    $day_start = $day . ' 00:00:00';
                                    $day_end = $day . ' 23:59:59';
                                    
                                    $day_jobs = array_filter($scheduled_jobs, function($job) use ($printer, $day_start, $day_end) {
                                        $job_start = $job['scheduled_start_time'];
                                        $job_end = $job['scheduled_end_time'];
                                        $is_same_printer = $job['scheduled_printer_id'] == $printer['id'];
                                        $overlaps = ($job_start <= $day_end && $job_end >= $day_start);
                                        return $is_same_printer && $overlaps;
                                    });
                                    
                                    if (!empty($day_jobs)):
                                        foreach ($day_jobs as $day_job):
                                            $status_bg = match($day_job['production_status']) {
                                                'Baskıda' => 'bg-primary',
                                                'Bitti' => 'bg-success',
                                                'Başarısız' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                    ?>
                                        <div class="<?php echo $status_bg; ?> text-white rounded p-1 mb-1" style="font-size: 0.75rem;" title="<?php echo htmlspecialchars($day_job['part_name']); ?>">
                                            <?php echo htmlspecialchars($day_job['project_number']); ?>
                                            <br><small><?php echo htmlspecialchars($day_job['part_name']); ?></small>
                                        </div>
                                    <?php
                                        endforeach;
                                    else:
                                        echo '<div class="text-muted" style="font-size: 0.8rem;">-</div>';
                                    endif;
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <small class="text-muted">
                <span class="badge bg-primary">Mavi: Baskıda</span>
                <span class="badge bg-success">Yeşil: Bitti</span>
                <span class="badge bg-danger">Kırmızı: Başarısız</span>
            </small>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
function toggleView() {
    const listView = document.getElementById('jobsListView');
    const calendarView = document.getElementById('calendarView');
    const listContainer = listView.closest('.card');
    
    if (listView.style.display === 'none') {
        // Liste görünümüne geç
        listView.style.display = 'block';
        calendarView.style.display = 'none';
    } else {
        // Takvim görünümüne geç
        listView.style.display = 'none';
        calendarView.style.display = 'block';
    }
}
</script>

<!-- Son 7 Günlük Tamamlanan İşler -->
<div class="card shadow-sm mt-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Tamamlanan İşler</h5>
        <small class="text-muted">Kargoya hazır veya müşteriye teslim edilmeye hazır işler</small>
    </div>
    <div class="card-body">
        <?php 
        // Son 7 gün içinde tamamlanan işleri al
        $completed_stmt = $pdo->prepare(
            "SELECT pp.*, pr.project_number, pr.customer_name, p.name as printer_name, pr.status as project_status
             FROM project_parts pp 
             JOIN projects pr ON pp.project_id = pr.id
             LEFT JOIN printers p ON pp.scheduled_printer_id = p.id
             WHERE pp.production_status = 'Bitti'
             AND pr.status IN ('Tamamlandı', 'Kalite Kontrol')
             ORDER BY pr.created_at DESC"
        );
        $completed_stmt->execute();
        $completed_jobs = $completed_stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        
        <?php if (empty($completed_jobs)): ?>
            <p class="text-muted text-center p-3">Tamamlanan iş bulunmuyor.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Proje</th>
                            <th>Müşteri</th>
                            <th>Parça</th>
                            <th>Yazıcı</th>
                            <th>Tamamlanma Tarihi</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($completed_jobs as $job): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($job['project_number']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($job['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($job['part_name']); ?></td>
                                <td>
                                    <?php if ($job['printer_name']): ?>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($job['printer_name']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($job['actual_end_time']): ?>
                                        <span class="text-success">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            <?php echo date('d.m.Y H:i', strtotime($job['actual_end_time'])); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $status_badge = match($job['project_status']) {
                                        'Tamamlandı' => 'bg-success',
                                        'Kalite Kontrol' => 'bg-warning',
                                        default => 'bg-info'
                                    };
                                    ?>
                                    <span class="badge <?php echo $status_badge; ?>"><?php echo htmlspecialchars($job['project_status']); ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="project_details.php?id=<?php echo $job['project_id']; ?>" class="btn btn-outline-primary" title="Proje Detayı">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="generate_pdf.php?id=<?php echo $job['project_id']; ?>" class="btn btn-outline-success" title="PDF Oluştur" target="_blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Özet İstatistikler -->
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="text-success"><?php echo count($completed_jobs); ?></h5>
                            <small class="text-muted">Tamamlanan Parça</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="text-primary"><?php echo count(array_unique(array_column($completed_jobs, 'project_id'))); ?></h5>
                            <small class="text-muted">Etkilenen Proje</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="text-info"><?php echo count(array_unique(array_column(array_filter($completed_jobs, fn($j) => $j['printer_name']), 'printer_name'))); ?></h5>
                            <small class="text-muted">Kullanılan Yazıcı</small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
