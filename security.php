<?php
// ส่วนกลางด้านความปลอดภัย — session.php เรียกไฟล์นี้ก่อนเสมอ ทุกหน้า PHP จึงได้ครบอัตโนมัติ

/** ไฟล์ส่วนประกอบ (ไม่ใช่หน้าเว็บ) เรียกตอนต้นไฟล์ เพื่อให้คนเปิดตรงๆ ทาง URL ได้ 404 */
function deny_direct_access(string $file): void
{
    if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath($file)) {
        http_response_code(404);
        exit;
    }
}
deny_direct_access(__FILE__);

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

// nonce ใช้ใส่ใน <style> / <script> ของหน้า PHP — โค้ดที่ไม่มี nonce (เช่นถูกแทรกเข้ามา) จะไม่ถูกรัน
$cspNonce = base64_encode(random_bytes(18));

header("Content-Security-Policy: default-src 'self'; "
    . "script-src 'self' 'nonce-$cspNonce'; "
    . "style-src 'self' 'nonce-$cspNonce'; "
    . "img-src 'self' data:; font-src 'self' data:; "
    . "media-src 'self' https:; "
    . "frame-src https://www.youtube-nocookie.com https://player.vimeo.com; "
    . "connect-src 'self'; object-src 'none'; base-uri 'self'; "
    . "form-action 'self'; frame-ancestors 'none'");
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
header('Cross-Origin-Opener-Policy: same-origin');
if ($isHttps) {
    header('Strict-Transport-Security: max-age=31536000');
}
header_remove('X-Powered-By');

function csrf_token(): string
{
    if (empty($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(): bool
{
    $sent = $_POST['csrf'] ?? '';
    return is_string($sent) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $sent);
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}
