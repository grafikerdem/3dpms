<?php
session_start();

// Veritabanı yapılandırma dosyasının varlığını kontrol et, yoksa kuruluma yönlendir.
if (!file_exists(__DIR__ . '/database.php')) {
    header('Location: install/index.php');
    exit;
}
require_once __DIR__ . '/database.php';

/**
 * Kullanıcı girişi yapar.
 * @param string $username
 * @param string $password
 * @return bool
 */
function login($username, $password) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        // Geliştirme aşamasında hatayı göster, production'da logla.
        die("Giriş hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Kullanıcı çıkışı yapar.
 */
function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

/**
 * Kullanıcının giriş yapıp yapmadığını kontrol eder.
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Sayfaları korumak için kullanılır. Giriş yapılmamışsa login sayfasına yönlendirir.
 */
function check_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
?>