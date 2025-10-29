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

// Durumu 'Onaylandı' olan ve henüz zamanlanmamış parçaları al
$stmt = $pdo->prepare(
    "SELECT pp.*, pr.project_number, pr.customer_name 
     FROM project_parts pp 
     JOIN projects pr ON pp.project_id = pr.id 
     WHERE pr.status = 'Onaylandı' AND pp.scheduled_start_time IS NULL 
     ORDER BY pr.created_at ASC"
);
$stmt->execute();
$waiting_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$printers = $pdo->query("SELECT id, name FROM printers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- FullCalendar için gerekli CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

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

<div class="card shadow-sm">
    <div class="card-header">Yazıcı Takvimi</div>
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<!-- FullCalendar için gerekli JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'resourceTimelineDay',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
        },
        editable: true,
        resourceAreaHeaderContent: 'Yazıcılar',
        resources: 'get_calendar_events.php', // Sadece kaynakları (yazıcılar) çeker
        events: 'get_calendar_events.php',    // Sadece olayları (işler) çeker
        height: 'auto',
        locale: 'tr'
    });
    calendar.render();
});
</script>
