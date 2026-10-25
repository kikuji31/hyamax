<?php
// จำกัดการเดารหัสผ่าน 2 ชั้น (เก็บเป็นไฟล์เล็กๆ ในโฟลเดอร์ temp ของเซิร์ฟเวอร์ — เว็บนี้ไม่มีฐานข้อมูล)
//   ต่อ IP:      ผิด 5 ครั้งใน 15 นาที  -> ล็อก IP นั้น 15 นาที
//   ต่อบัญชี:    ผิด 10 ครั้งใน 15 นาที -> ล็อกบัญชีนั้น 15 นาที (กันการเดาจากหลาย IP พร้อมกัน)
require_once __DIR__ . '/security.php';
deny_direct_access(__FILE__);

const LOGIN_WINDOW   = 15 * 60;
const LOGIN_LOCKOUT  = 15 * 60;
const LOGIN_MAX_IP   = 5;
const LOGIN_MAX_USER = 10;

/** คีย์ที่ใช้นับสำหรับการ login ครั้งนี้: [คีย์ => จำนวนครั้งที่ยอมให้ผิด] */
function throttle_keys(string $username): array
{
    return [
        'ip:' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') => LOGIN_MAX_IP,
        'user:' . strtolower($username)                 => LOGIN_MAX_USER,
    ];
}

function throttle_file(string $key): string
{
    $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'hyamax-login';
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }
    return $dir . DIRECTORY_SEPARATOR . hash('sha256', $key) . '.json';
}

function throttle_read(string $key): array
{
    $raw = @file_get_contents(throttle_file($key));
    $data = $raw ? json_decode($raw, true) : null;
    return is_array($data) ? $data : ['fails' => [], 'locked_until' => 0];
}

/** วินาทีที่ยังถูกล็อกอยู่ (0 = login ได้) */
function throttle_seconds_left(string $username): int
{
    $left = 0;
    foreach (array_keys(throttle_keys($username)) as $key) {
        $left = max($left, (int)(throttle_read($key)['locked_until'] ?? 0) - time());
    }
    return $left;
}

function throttle_record_failure(string $username): void
{
    $now = time();
    foreach (throttle_keys($username) as $key => $max) {
        $data = throttle_read($key);
        $data['fails'] = array_values(array_filter(
            $data['fails'] ?? [],
            function ($t) use ($now) { return $t > $now - LOGIN_WINDOW; }
        ));
        $data['fails'][] = $now;
        if (count($data['fails']) >= $max) {
            $data['locked_until'] = $now + LOGIN_LOCKOUT;
            $data['fails'] = [];
        }
        @file_put_contents(throttle_file($key), json_encode($data), LOCK_EX);
    }
}

function throttle_reset(string $username): void
{
    foreach (array_keys(throttle_keys($username)) as $key) {
        @unlink(throttle_file($key));
    }
}
