<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/academy-users.php';
deny_direct_access(__FILE__);

// หน้าหลัง login ห้ามเก็บแคช — กด Back หลัง logout จะไม่เห็นเนื้อหาเดิม
header('Cache-Control: no-store, max-age=0');

// ต้อง login แล้ว และบัญชียังอยู่ใน academy-users.php (ลบบรรทัดผู้ใช้ = ตัดสิทธิ์ทันที)
$user = $_SESSION['hyamax_academy_user'] ?? '';
if (empty($_SESSION['hyamax_academy_auth']) || !is_string($user) || !array_key_exists($user, ACADEMY_USERS)) {
    $_SESSION = [];
    header('Location: login.php');
    exit;
}
