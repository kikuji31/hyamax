<?php
// เริ่ม session ด้วย cookie ที่ปลอดภัย — ทุกไฟล์ที่ใช้ session ต้อง require ไฟล์นี้แทนการเรียก session_start() เอง
require_once __DIR__ . '/security.php';
deny_direct_access(__FILE__);

const SESSION_IDLE_TIMEOUT = 8 * 3600;   // ไม่ได้ใช้งานเกิน 8 ชั่วโมง ต้อง login ใหม่
const SESSION_MAX_LIFETIME = 7 * 86400;  // login ค้างไว้นานสุด 7 วัน แม้จะใช้งานตลอด

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
session_name('HYAMAXSESS');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$now = time();
if ((isset($_SESSION['last_seen']) && $now - $_SESSION['last_seen'] > SESSION_IDLE_TIMEOUT)
    || (isset($_SESSION['created']) && $now - $_SESSION['created'] > SESSION_MAX_LIFETIME)) {
    $_SESSION = [];
    session_regenerate_id(true);
}
$_SESSION['last_seen'] = $now;
$_SESSION['created'] = $_SESSION['created'] ?? $now;
