<?php
// Genel fonksiyonlar

/**
 * Bir projenin parçasının toplam maliyetini hesaplar.
 *
 * @param int $material_id
 * @param float $material_amount_grams
 * @param int $printer_id
 * @param float $print_time_hours
 * @param int $design_time_minutes
 * @param int $setup_time_minutes
 * @param int $postprocess_time_minutes
 * @return float
 */
function calculate_part_cost(
    $material_id,
    $material_amount_grams,
    $printer_id,
    $print_time_hours,
    $design_time_minutes,
    $setup_time_minutes,
    $postprocess_time_minutes
) {
    global $pdo;

    // 1. Ayarları al
    $settings_stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $designer_rate = $settings['designer_rate'] ?? 0;
    $operator_rate = $settings['operator_rate'] ?? 0;
    $electricity_cost = $settings['electricity_cost'] ?? 0;

    // 2. Malzeme Maliyetini Hesapla
    $material_stmt = $pdo->prepare("SELECT unit_price, stock_unit FROM materials WHERE id = ?");
    $material_stmt->execute([$material_id]);
    $material = $material_stmt->fetch(PDO::FETCH_ASSOC);

    $material_price_per_gram = 0;
    if ($material) {
        if ($material['stock_unit'] === 'kg') {
            $material_price_per_gram = $material['unit_price'] / 1000;
        } elseif ($material['stock_unit'] === 'g') {
            $material_price_per_gram = $material['unit_price'];
        }
        // Not: Litre (reçine) için birim dönüşümü gerekirse buraya eklenebilir.
    }
    $total_material_cost = $material_amount_grams * $material_price_per_gram;

    // 3. Makine Maliyetini Hesapla
    $printer_stmt = $pdo->prepare("SELECT purchase_price, lifespan_hours, power_consumption_watts FROM printers WHERE id = ?");
    $printer_stmt->execute([$printer_id]);
    $printer = $printer_stmt->fetch(PDO::FETCH_ASSOC);

    $machine_hour_cost = 0;
    if ($printer) {
        $amortization_per_hour = $printer['lifespan_hours'] > 0 ? ($printer['purchase_price'] / $printer['lifespan_hours']) : 0;
        $energy_cost_per_hour = ($printer['power_consumption_watts'] / 1000) * $electricity_cost;
        $machine_hour_cost = $amortization_per_hour + $energy_cost_per_hour;
    }
    $total_machine_cost = $print_time_hours * $machine_hour_cost;

    // 4. İşçilik Maliyetini Hesapla
    $design_cost = ($design_time_minutes / 60) * $designer_rate;
    $operator_cost = (($setup_time_minutes + $postprocess_time_minutes) / 60) * $operator_rate;
    $total_labor_cost = $design_cost + $operator_cost;

    // 5. Toplam Parça Maliyeti
    $total_part_cost = $total_material_cost + $total_machine_cost + $total_labor_cost;

    return $total_part_cost;
}
?>