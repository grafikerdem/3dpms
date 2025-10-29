<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3DPMS - Kurulum Sihirbazı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4>3D Print Studio Management System - Kurulum Sihirbazı</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Adım 1: Sunucu Kontrolü</h5>
                        <?php
                        $php_version = phpversion();
                        $mysql_enabled = extension_loaded('mysqli');
                        $is_writable_includes = is_writable('../includes');

                        $php_ok = version_compare($php_version, '8.0', '>=');
                        $all_ok = $php_ok && $mysql_enabled && $is_writable_includes;
                        ?>
                        <ul class="list-group mb-4">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                PHP Versiyonu >= 8.0
                                <?php if ($php_ok): ?>
                                    <span class="badge bg-success">✓ (<?php echo $php_version; ?>)</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">X (<?php echo $php_version; ?>)</span>
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                MySQLi Eklentisi Aktif
                                <?php if ($mysql_enabled): ?>
                                    <span class="badge bg-success">✓</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">X</span>
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                /includes klasörü yazılabilir
                                <?php if ($is_writable_includes): ?>
                                    <span class="badge bg-success">✓</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">X</span>
                                <?php endif; ?>
                            </li>
                        </ul>

                        <?php if ($all_ok): ?>
                            <form action="create_tables.php" method="POST">
                                <h5 class="mt-4">Adım 2: Veritabanı Yapılandırması</h5>
                                <div class="mb-3">
                                    <label for="db_host" class="form-label">Veritabanı Sunucusu</label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_name" class="form-label">Veritabanı Adı</label>
                                    <input type="text" class="form-control" id="db_name" name="db_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_user" class="form-label">Veritabanı Kullanıcı Adı</label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_pass" class="form-label">Veritabanı Şifresi</label>
                                    <input type="password" class="form-control" id="db_pass" name="db_pass">
                                </div>
                                <hr>
                                <h5 class="mt-4">Adım 3: Yönetici Hesabı</h5>
                                 <div class="mb-3">
                                    <label for="admin_user" class="form-label">Yönetici Kullanıcı Adı</label>
                                    <input type="text" class="form-control" id="admin_user" name="admin_user" required>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_pass" class="form-label">Yönetici Şifresi</label>
                                    <input type="password" class="form-control" id="admin_pass" name="admin_pass" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Kurulumu Başlat</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-danger" role="alert">
                                Lütfen sunucu yapılandırmanızı kontrol edin ve devam etmeden önce tüm gereksinimlerin karşılandığından emin olun.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
