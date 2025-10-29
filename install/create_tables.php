<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- HTML Header ---
echo '<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><title>Kurulum Devam Ediyor...</title>';
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">';
echo '</head><body><div class="container mt-5"><div class="row"><div class="col-md-8 offset-md-2"><div class="card"><div class="card-body">';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo '<div class="alert alert-danger">Geçersiz istek. Lütfen kurulumu ilk adımdan başlatın.</div>';
    echo '</div></div></div></div></div></body></html>';
    exit;
}

// 1. Get POST data
$db_host = $_POST['db_host'];
$db_name = $_POST['db_name'];
$db_user = $_POST['db_user'];
$db_pass = $_POST['db_pass'];
$admin_user = $_POST['admin_user'];
$admin_pass = $_POST['admin_pass'];

// --- Step 1: Create database.php config file ---
echo "<h5>Adım 1: `database.php` dosyası oluşturuluyor...</h5>";

$config_content = '<?php' . "\n" . 
'// Veritabanı bağlantı bilgileri' . "\n" . 
"define('DB_HOST', '" . $db_host . "');" . "\n" . 
"define('DB_NAME', '" . $db_name . "');" . "\n" . 
"define('DB_USER', '" . $db_user . "');" . "\n" . 
"define('DB_PASS', '" . $db_pass . "');" . "\n\n" . 
'// PDO ile veritabanı bağlantısı' . "\n" . 
'try {' . "\n" . 
'    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);' . "\n" . 
'    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);' . "\n" . 
'} catch (PDOException $e) {' . "\n" . 
'    die("Veritabanı bağlantısı kurulamadı: " . $e->getMessage());' . "\n" . 
'}' . "\n" . 
'?>';

if (file_put_contents('../includes/database.php', $config_content)) {
    echo '<div class="alert alert-success">✓ `database.php` başarıyla oluşturuldu.</div>';
} else {
    echo '<div class="alert alert-danger">X `includes/database.php` dosyası oluşturulamadı. Klasör izinlerini kontrol edin.</div>';
    echo '</div></div></div></div></div></body></html>';
    exit;
}

// --- Step 2: Connect to the database ---
echo "<h5 class='mt-4'>Adım 2: Veritabanına bağlanılıyor...</h5>";
try {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($mysqli->connect_error) {
        throw new Exception($mysqli->connect_error);
    }
    echo '<div class="alert alert-success">✓ Veritabanı bağlantısı başarılı.</div>';
} catch (Exception $e) {
    echo '<div class="alert alert-danger">X Veritabanı bağlantısı başarısız: ' . $e->getMessage() . '</div>';
    unlink('../includes/database.php');
    echo '</div></div></div></div></div></body></html>';
    exit;
}

// --- Step 3: Create tables ---
echo "<h5 class='mt-4'>Adım 3: Veritabanı tabloları oluşturuluyor...</h5>";

$sql = "
CREATE TABLE `settings` (
  `setting_key` varchar(255) NOT NULL PRIMARY KEY,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `printers` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `brand` varchar(100),
  `model` varchar(100),
  `technology` varchar(50),
  `purchase_price` decimal(10,2) NOT NULL,
  `purchase_date` date NOT NULL,
  `lifespan_hours` int(11) NOT NULL,
  `power_consumption_watts` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `brand` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `color` varchar(50),
  `diameter` decimal(4,2),
  `unit_price` decimal(10,2) NOT NULL,
  `stock_amount` decimal(10,2) NOT NULL,
  `stock_unit` varchar(10) NOT NULL,
  `low_stock_threshold` int(11) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `consumables` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `stock_amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `project_number` varchar(50) NOT NULL UNIQUE,
  `customer_name` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT \'Teklif\',
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `project_parts` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `project_id` int(11) NOT NULL,
  `part_name` varchar(255) NOT NULL,
  `printer_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `material_amount_grams` decimal(10,2) NOT NULL,
  `print_time_hours` decimal(10,2) NOT NULL,
  `design_time_minutes` int(11) DEFAULT 0,
  `setup_time_minutes` int(11) DEFAULT 0,
  `postprocess_time_minutes` int(11) DEFAULT 0,
  `total_cost` decimal(10,2),
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`printer_id`) REFERENCES `printers`(`id`),
  FOREIGN KEY (`material_id`) REFERENCES `materials`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `maintenance_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `printer_id` int(11) NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text NOT NULL,
  FOREIGN KEY (`printer_id`) REFERENCES `printers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11),
  `activity` varchar(255) NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($mysqli->multi_query($sql)) {
    while ($mysqli->next_result()) {;}
    echo '<div class="alert alert-success">✓ Veritabanı tabloları başarıyla oluşturuldu.</div>';
} else {
    echo '<div class="alert alert-danger">X Tablo oluşturma hatası: ' . $mysqli->error . '</div>';
    unlink('../includes/database.php');
    echo '</div></div></div></div></div></body></html>';
    exit;
}

// --- Step 4: Insert default settings ---
echo "<h5 class='mt-4'>Adım 4: Varsayılan ayarlar ekleniyor...</h5>";
$settings_sql = "
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('currency', '₺'),
('designer_rate', '150'),
('operator_rate', '100'),
('electricity_cost', '4.2'),
('markup', '15');
";
if ($mysqli->query($settings_sql)) {
    echo '<div class="alert alert-success">✓ Varsayılan ayarlar başarıyla eklendi.</div>';
} else {
    echo '<div class="alert alert-danger">X Ayar ekleme hatası: ' . $mysqli->error . '</div>';
    unlink('../includes/database.php');
    echo '</div></div></div></div></div></body></html>';
    exit;
}

// --- Step 5: Create admin user ---
echo "<h5 class='mt-4'>Adım 5: Yönetici hesabı oluşturuluyor...</h5>";
$hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare("INSERT INTO `users` (username, password, role) VALUES (?, ?, 'admin')");
$stmt->bind_param("ss", $admin_user, $hashed_password);

if ($stmt->execute()) {
    echo '<div class="alert alert-success">✓ Yönetici hesabı başarıyla oluşturuldu.</div>';
} else {
    echo '<div class="alert alert-danger">X Yönetici oluşturma hatası: ' . $stmt->error . '</div>';
    unlink('../includes/database.php');
    echo '</div></div></div></div></div></body></html>';
    exit;
}

$stmt->close();
$mysqli->close();

// --- Final Step: Success message ---
echo "<hr><div class='alert alert-success mt-4'><h4>🎉 Kurulum Tamamlandı!</h4></div>";
echo "<p><b>Önemli:</b> Kurulum dosyaları güvenlik nedeniyle artık gerekli değildir. Lütfen projenizin ana dizinindeki <code>install</code> klasörünü manuel olarak silin.</p>";
echo '<a href="../login.php" class="btn btn-primary mt-2">Giriş Sayfasına Git</a>';

echo '</div></div></div></div></div></body></html>';
?>