<?php
// จำกัดการเดารหัสผ่านต่อ IP: ผิดครบ LOGIN_MAX_FAILS ครั้งภายใน LOGIN_WINDOW วินาที -> ล็อก LOGIN_LOCKOUT วินาที
// เก็บเป็นไฟล์เล็กๆ ในโฟลเดอร์ temp ของเซิร์ฟเวอร์ (เว็บนี้ไม่มีฐานข้อมูล)

const LOGIN_MAX_FAILS = 5;
const LOGIN_WINDOW    = 15 * 60;
const LOGIN_LOCKOUT   = 15 * 60;

function throttle_file(): string
{
    $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'hyamax-login';
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    return $dir . DIRECTORY_SEPARATOR . hash('sha256', $ip) . '.json';
}

function throttle_read(): array
{
    $raw = @file_get_contents(throttle_file());
    $data = $raw ? json_decode($raw, true) : null;
    return is_array($data) ? $data : ['fails' => [], 'locked_until' => 0];
}

function throttle_write(array $data): void
{
    @file_put_contents(throttle_file(), json_encode($data), LOCK_EX);
}

/** วินาทีที่ยังถูกล็อกอยู่ (0 = login ได้) */
function throttle_seconds_left(): int
{
    $left = (int)(throttle_read()['locked_until'] ?? 0) - time();
    return max(0, $left);
}

function throttle_record_failure(): void
{
    $now = time();
    $data = throttle_read();
    $data['fails'] = array_values(array_filter(
        $data['fails'] ?? [],
        function ($t) use ($now) { return $t > $now - LOGIN_WINDOW; }
    ));
    $data['fails'][] = $now;
    if (count($data['fails']) >= LOGIN_MAX_FAILS) {
        $data['locked_until'] = $now + LOGIN_LOCKOUT;
        $data['fails'] = [];
    }
    throttle_write($data);
}

function throttle_reset(): void
{
    @unlink(throttle_file());
}
