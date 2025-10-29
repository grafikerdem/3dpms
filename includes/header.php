<?php
require_once __DIR__ . '/auth.php';
check_login(); // Bu başlığı içeren tüm sayfaları koru
?>
<!DOCTYPE html>
<html lang="tr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? '3DPMS'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Özel stil dosyası -->
</head>
<body>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <!-- Toast'lar buraya dinamik olarak eklenecek -->
</div>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">3D Print Studio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="projects.php">Projeler</a></li>
                <li class="nav-item"><a class="nav-link" href="printers.php">Yazıcılar</a></li>
                <li class="nav-item"><a class="nav-link" href="materials.php">Malzemeler</a></li>
                <li class="nav-item"><a class="nav-link" href="production.php">Üretim</a></li>
                <li class="nav-item"><a class="nav-link" href="reports.php">Raporlar</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                 <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="theme-switcher" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-theme-toggle>
                        <i class="bi bi-sun-fill"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="theme-switcher">
                        <li><button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light"><i class="bi bi-sun-fill me-2"></i> Açık</button></li>
                        <li><button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark"><i class="bi bi-moon-stars-fill me-2"></i> Koyu</button></li>
                        <li><button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto"><i class="bi bi-circle-half me-2"></i> Otomatik</button></li>
                    </ul>
                </li>
                 <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Ayarlar</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Çıkış Yap</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container-fluid p-4">
