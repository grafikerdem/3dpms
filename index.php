<?php
$page_title = 'Dashboard';
require_once 'includes/header.php';

// Dashboard kartları için verileri çek
$active_jobs = $pdo->query("SELECT COUNT(*) FROM project_parts WHERE production_status = 'Baskıda'")->fetchColumn();
$low_stock_materials = $pdo->query("SELECT COUNT(*) FROM materials WHERE stock_amount <= low_stock_threshold")->fetchColumn();
$waiting_approval = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'Teklif'")->fetchColumn();

// Yazıcı kullanım oranı için veri çek
$printer_utilization_stmt = $pdo->query("SELECT p.name, COUNT(pp.id) as job_count FROM printers p LEFT JOIN project_parts pp ON p.id = pp.scheduled_printer_id GROUP BY p.id");
$printer_data = $printer_utilization_stmt->fetchAll(PDO::FETCH_ASSOC);
$printer_labels = json_encode(array_column($printer_data, 'name'));
$printer_job_counts = json_encode(array_column($printer_data, 'job_count'));

?>

<h1 class="mb-4">Dashboard</h1>

<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card text-white bg-primary shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-1 fw-bold"><?php echo $active_jobs; ?></div>
                        <div>Aktif İşler</div>
                    </div>
                    <i class="bi bi-printer" style="font-size: 3.5rem; opacity: 0.5;"></i>
                </div>
            </div>
             <a href="production.php" class="card-footer text-white text-decoration-none d-flex justify-content-between">
                <span>Detayları Gör</span> <i class="bi bi-arrow-right-circle"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card text-white bg-warning shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-1 fw-bold"><?php echo $low_stock_materials; ?></div>
                        <div>Düşük Stok</div>
                    </div>
                    <i class="bi bi-box-seam" style="font-size: 3.5rem; opacity: 0.5;"></i>
                </div>
            </div>
             <a href="materials.php" class="card-footer text-white text-decoration-none d-flex justify-content-between">
                <span>Detayları Gör</span> <i class="bi bi-arrow-right-circle"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card text-white bg-danger shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-1 fw-bold">0</div>
                        <div>Bakım Gerekli</div>
                    </div>
                    <i class="bi bi-tools" style="font-size: 3.5rem; opacity: 0.5;"></i>
                </div>
            </div>
            <a href="printers.php" class="card-footer text-white text-decoration-none d-flex justify-content-between">
                <span>Detayları Gör</span> <i class="bi bi-arrow-right-circle"></i>
            </a>
        </div>
    </div>
     <div class="col-lg-3 col-md-6 mb-4">
        <div class="card text-white bg-success shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-1 fw-bold"><?php echo $waiting_approval; ?></div>
                        <div>Onay Bekleyen</div>
                    </div>
                    <i class="bi bi-patch-check" style="font-size: 3.5rem; opacity: 0.5;"></i>
                </div>
            </div>
             <a href="projects.php" class="card-footer text-white text-decoration-none d-flex justify-content-between">
                <span>Detayları Gör</span> <i class="bi bi-arrow-right-circle"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-7 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">Aylık Kâr/Zarar</div>
            <div class="card-body">
                <canvas id="monthlyProfitChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">Yazıcı Kullanım Oranları (İş Sayısı)</div>
            <div class="card-body">
                <canvas id="printerUtilizationChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Aylık Kâr Grafiği (Örnek Veri)
    const profitCtx = document.getElementById('monthlyProfitChart').getContext('2d');
    new Chart(profitCtx, {
        type: 'line',
        data: {
            labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran'],
            datasets: [{
                label: 'Bu Ayki Kâr',
                data: [1250, 1900, 3000, 5000, 2400, 3300],
                borderColor: '#198754',
                tension: 0.1
            }]
        }
    });

    // Yazıcı Kullanım Grafiği
    const printerCtx = document.getElementById('printerUtilizationChart').getContext('2d');
    new Chart(printerCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo $printer_labels; ?>,
            datasets: [{
                label: 'Atanan İş Sayısı',
                data: <?php echo $printer_job_counts; ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ]
            }]
        }
    });
});
</script>