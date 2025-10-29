<?php
require_once 'includes/database.php';

echo "<p>Veritabanı şeması güncelleniyor...</p>";

try {
    $sql = "
    ALTER TABLE `project_parts` 
    ADD COLUMN `scheduled_printer_id` INT(11) NULL DEFAULT NULL AFTER `total_cost`,
    ADD COLUMN `scheduled_start_time` DATETIME NULL DEFAULT NULL AFTER `scheduled_printer_id`,
    ADD COLUMN `scheduled_end_time` DATETIME NULL DEFAULT NULL AFTER `scheduled_start_time`,
    ADD COLUMN `production_status` VARCHAR(50) NULL DEFAULT NULL AFTER `scheduled_end_time`,
    ADD CONSTRAINT `fk_scheduled_printer` FOREIGN KEY (`scheduled_printer_id`) REFERENCES `printers`(`id`) ON DELETE SET NULL;
    ";

    $pdo->exec($sql);

    echo "<p style='color:green;'>✓ `project_parts` tablosu başarıyla güncellendi.</p>";
    echo "<p><b>Bu dosyayı şimdi güvenle silebilirsiniz.</b></p>";

} catch (PDOException $e) {
    // Hata genellikle kolonlar zaten varsa oluşur, bu yüzden görmezden gelinebilir.
    if ($e->getCode() === '42S21') { // Column already exists
         echo "<p style='color:orange;'>Uyarı: Kolonlar zaten mevcut görünüyor. Değişiklik yapılmadı.</p>";
         echo "<p><b>Bu dosyayı şimdi güvenle silebilirsiniz.</b></p>";
    } else {
        die("<p style='color:red;'>X Şema güncelleme hatası: " . $e->getMessage() . "</p>");
    }
}

?>