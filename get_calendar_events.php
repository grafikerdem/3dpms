<?php
header('Content-Type: application/json');
require_once 'includes/database.php';

// Takvim için zamanlanmış işleri ve kaynak olarak yazıcıları çek

// Kaynaklar (Yazıcılar)
$printers_stmt = $pdo->query("SELECT id, name as title FROM printers");
$resources = $printers_stmt->fetchAll(PDO::FETCH_ASSOC);

// Olaylar (Zamanlanmış Parçalar)
$events_stmt = $pdo->prepare(
    "SELECT 
        pp.id, 
        CONCAT(pr.project_number, ': ', pp.part_name) as title, 
        pp.scheduled_start_time as start, 
        pp.scheduled_end_time as end,
        pp.scheduled_printer_id as resourceId,
        pp.production_status
     FROM project_parts pp
     JOIN projects pr ON pp.project_id = pr.id
     WHERE pp.scheduled_start_time IS NOT NULL AND pp.scheduled_printer_id IS NOT NULL"
);
$events_stmt->execute();
$events = $events_stmt->fetchAll(PDO::FETCH_ASSOC);

// Duruma göre olayları renklendir
foreach ($events as &$event) {
    switch ($event['production_status']) {
        case 'Baskıda':
            $event['color'] = '#0d6efd'; // Mavi
            break;
        case 'Bitti':
            $event['color'] = '#198754'; // Yeşil
            break;
        case 'Başarısız':
            $event['color'] = '#dc3545'; // Kırmızı
            break;
    }
}

// FullCalendar'ın anlayacağı formatta birleştir
$output = [
    'resources' => $resources,
    'events' => $events
];

echo json_encode($output);
?>
