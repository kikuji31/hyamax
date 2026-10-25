<?php
require_once __DIR__ . '/session.php';

// logout ได้เฉพาะจากปุ่มในเว็บเราเอง (POST + CSRF) — ลิงก์จากเว็บอื่นมาสั่ง logout ไม่ได้
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) {
    header('Location: ' . (empty($_SESSION['hyamax_academy_auth']) ? 'login.php' : 'academy.php'));
    exit;
}

$_SESSION = [];
$params = session_get_cookie_params();
setcookie(session_name(), '', [
    'expires'  => time() - 3600,
    'path'     => $params['path'],
    'secure'   => $params['secure'],
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_destroy();
header('Location: login.php');
exit;
