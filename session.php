<?php
// เริ่ม session ด้วย cookie ที่ปลอดภัย — ทุกไฟล์ที่ใช้ session ต้อง require ไฟล์นี้แทนการเรียก session_start() เอง

const SESSION_IDLE_TIMEOUT = 8 * 3600; // ไม่ได้ใช้งานเกิน 8 ชั่วโมง ต้อง login ใหม่

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_name('HYAMAXSESS');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

if (isset($_SESSION['last_seen']) && time() - $_SESSION['last_seen'] > SESSION_IDLE_TIMEOUT) {
    $_SESSION = [];
    session_regenerate_id(true);
}
$_SESSION['last_seen'] = time();
