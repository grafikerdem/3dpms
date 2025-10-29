<?php
require_once 'includes/auth.php';
check_login();
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $project_id = $_POST['project_id'];
    $part_name = $_POST['part_name'];
    $printer_id = $_POST['printer_id'];
    $material_id = $_POST['material_id'];
    $material_amount_grams = $_POST['material_amount_grams'];
    $print_time_hours = $_POST['print_time_hours'];
    $design_time_minutes = $_POST['design_time_minutes'] ?? 0;
    $setup_time_minutes = $_POST['setup_time_minutes'] ?? 0;
    $postprocess_time_minutes = $_POST['postprocess_time_minutes'] ?? 0;

    // Maliyeti hesapla
    $total_cost = calculate_part_cost(
        $material_id,
        $material_amount_grams,
        $printer_id,
        $print_time_hours,
        $design_time_minutes,
        $setup_time_minutes,
        $postprocess_time_minutes
    );

    // Veritabanına ekle
    $stmt = $pdo->prepare(
        "INSERT INTO project_parts (project_id, part_name, printer_id, material_id, material_amount_grams, print_time_hours, design_time_minutes, setup_time_minutes, postprocess_time_minutes, total_cost) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $project_id,
        $part_name,
        $printer_id,
        $material_id,
        $material_amount_grams,
        $print_time_hours,
        $design_time_minutes,
        $setup_time_minutes,
        $postprocess_time_minutes,
        $total_cost
    ]);

    // Proje detayları sayfasına geri yönlendir
    header('Location: project_details.php?id=' . $project_id);
    exit;
} else {
    // POST isteği değilse, proje listesine yönlendir
    header('Location: projects.php');
    exit;
}
?>