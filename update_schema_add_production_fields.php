<?php
// Veritabanı şemasını güncellemek için migration scripti
// Bu scripti yalnızca BİR KEZ çalıştırın

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Veritabanı Şeması Güncelleniyor...</h2>";

// Veritabanı bağlantısı
require_once 'includes/database.php';

try {
    $columns_to_add = [
        "`production_status` varchar(50) DEFAULT 'Bekliyor'",
        "`scheduled_printer_id` int(11) DEFAULT NULL",
        "`scheduled_start_time` datetime DEFAULT NULL",
        "`scheduled_end_time` datetime DEFAULT NULL",
        "`actual_start_time` datetime DEFAULT NULL",
        "`actual_end_time` datetime DEFAULT NULL",
        "`failure_reason` text DEFAULT NULL",
        "`qc_status` varchar(50) DEFAULT NULL"
    ];
    
    foreach ($columns_to_add as $column_def) {
        // Kolon adını çıkar (backtickleri ve virgülleri temizle)
        preg_match('/`([^`]+)`/', $column_def, $matches);
        $column_name = $matches[1] ?? 'unknown';
        
        try {
            $pdo->exec("ALTER TABLE `project_parts` ADD COLUMN $column_def");
            echo "<p style='color: green;'>✓ Kolon eklendi: $column_name</p>";
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate column')) {
                echo "<p style='color: orange;'>⚠ Kolon zaten var: $column_name</p>";
            } else {
                echo "<p style='color: red;'>❌ Hata ($column_name): " . $e->getMessage() . "</p>";
            }
        }
    }
    
    // Foreign key ekle
    try {
        $pdo->exec("
        ALTER TABLE `project_parts` 
        ADD CONSTRAINT `fk_scheduled_printer` 
        FOREIGN KEY (`scheduled_printer_id`) 
        REFERENCES `printers`(`id`) ON DELETE SET NULL
        ");
        echo "<p style='color: green;'>✓ Foreign key başarıyla eklendi!</p>";
    } catch (PDOException $e) {
        // Foreign key zaten varsa hata verme
        if (!str_contains($e->getMessage(), 'Duplicate')) {
            echo "<p style='color: orange;'>⚠ Foreign key eklenirken uyarı: " . $e->getMessage() . "</p>";
        }
    }
    
    // Mevcut verilere production_status ekle
    $pdo->exec("UPDATE `project_parts` SET `production_status` = 'Bekliyor' WHERE `production_status` IS NULL");
    echo "<p style='color: green;'>✓ Mevcut veriler güncellendi!</p>";
    
    echo "<hr><h3 style='color: green;'>✅ Güncelleme tamamlandı!</h3>";
    echo "<p><a href='index.php'>Dashboard'a git</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Hata: " . $e->getMessage() . "</p>";
}

?>

