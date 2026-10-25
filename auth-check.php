<?php
require __DIR__ . '/session.php';
require __DIR__ . '/academy-users.php';

// ต้อง login แล้ว และบัญชียังอยู่ใน academy-users.php (ลบบรรทัดผู้ใช้ = ตัดสิทธิ์ทันที)
$user = $_SESSION['hyamax_academy_user'] ?? '';
if (empty($_SESSION['hyamax_academy_auth']) || !is_string($user) || !array_key_exists($user, ACADEMY_USERS)) {
    $_SESSION = [];
    header('Location: login.php');
    exit;
}
