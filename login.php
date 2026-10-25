<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/login-throttle.php';
require_once __DIR__ . '/academy-users.php';

if (!empty($_SESSION['hyamax_academy_auth'])) {
    header('Location: academy.php');
    exit;
}

/** ตรวจรหัสผ่าน — ถ้าไม่มี username นี้ก็ยังคำนวณ hash ให้ใช้เวลาเท่ากัน เพื่อไม่ให้เดาได้ว่ามี user ไหนบ้าง */
function academy_verify(string $username, string $password): bool
{
    $user = ACADEMY_USERS[$username] ?? null;
    $salt = hex2bin($user['salt'] ?? str_repeat('0', 32));
    // ความยาว 64 = ตัวอักษร hex (32 ไบต์) เพราะ raw_output = false
    $computed = hash_pbkdf2('sha256', $password, $salt, ACADEMY_PBKDF2_ITERATIONS, 64, false);
    return $user !== null && hash_equals($user['hash'], $computed);
}

$error = '';
$submittedUser = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedUser = is_string($_POST['username'] ?? null) ? substr(trim($_POST['username']), 0, 100) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    if (!csrf_valid()) {
        // ฟอร์มไม่ได้ส่งมาจากหน้านี้ของเราเอง (หรือเปิดค้างไว้จน session หมดอายุ)
        $error = 'หน้านี้เปิดค้างไว้นานเกินไป กรุณาลองเข้าสู่ระบบอีกครั้ง';
    } elseif (($left = throttle_seconds_left($submittedUser)) > 0) {
        $error = 'ใส่รหัสผิดหลายครั้งเกินไป กรุณาลองใหม่ในอีก ' . (int)ceil($left / 60) . ' นาที';
    } elseif (strlen($password) <= 1024 && academy_verify($submittedUser, $password)) {
        throttle_reset($submittedUser);
        session_regenerate_id(true);
        unset($_SESSION['csrf']);
        $_SESSION['hyamax_academy_auth'] = true;
        $_SESSION['hyamax_academy_user'] = $submittedUser;
        $_SESSION['created'] = time();
        header('Location: academy.php');
        exit;
    } else {
        throttle_record_failure($submittedUser);
        $left = throttle_seconds_left($submittedUser);
        $error = $left > 0
            ? 'ใส่รหัสผิดหลายครั้งเกินไป กรุณาลองใหม่ในอีก ' . (int)ceil($left / 60) . ' นาที'
            : 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
    }
}
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8" />
<title>Login | Hyamax Academy</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="fonts.css">
<link rel="stylesheet" href="site-base.css">
<style nonce="<?= $cspNonce ?>">
.login-hero{padding-bottom:clamp(64px,10vw,100px);}
.login-hero-inner{align-items:center;}
.login-hero .hero-portrait{
  --bleed:-60px;
  transform:scale(.95);transform-origin:bottom right;
}
.login-hero .hero-portrait img{
  -webkit-mask-image:linear-gradient(180deg, #000 45%, transparent 78%);
  mask-image:linear-gradient(180deg, #000 45%, transparent 78%);
}
.login-card-wrap{
  position:relative;z-index:3;
  margin-top:clamp(28px,4vw,40px);
  display:flex;justify-content:center;
}
.login-card{
  position:relative;width:100%;max-width:400px;
  background:#FFFFFF;border-radius:22px;padding:clamp(24px,3.6vw,34px);
  box-shadow:var(--shadow);
  display:flex;flex-direction:column;gap:18px;
}
.login-card::before{
  content:"";position:absolute;inset:-14px;
  background:rgba(255,255,255,.45);border-radius:32px;z-index:-1;
}
.login-field{display:flex;flex-direction:column;gap:8px;text-align:left;}
.login-field span{
  font-family:var(--sans);font-weight:700;font-size:.78rem;letter-spacing:.06em;
  text-transform:uppercase;color:var(--ink);
}
.login-field input{
  font-family:var(--thai);font-size:.98rem;color:var(--ink);
  border:1px solid var(--line-strong);border-radius:10px;padding:13px 16px;
  background:var(--paper);outline:none;transition:border-color .2s ease;
}
.login-field input:focus{border-color:var(--accent);}
.login-error{
  margin:0;font-size:.85rem;color:#C0392B;font-family:var(--thai);text-align:left;
}
.login-submit{
  margin-top:8px;width:100%;background:var(--accent);color:#FFFFFF;
  padding:14px 24px;font-weight:700;letter-spacing:.04em;
}
.login-submit:hover{background:var(--accent-warm);transform:none;}
.login-submit:disabled{opacity:.7;cursor:progress;}

.login-head{display:flex;align-items:flex-start;gap:14px;text-align:left;padding-bottom:16px;border-bottom:1px solid var(--line-strong);}
.login-head-icon{
  flex:none;width:42px;height:42px;border-radius:12px;background:var(--accent-soft);color:var(--accent);
  display:flex;align-items:center;justify-content:center;
}
.login-head-icon svg{width:22px;height:22px;}
.login-head h2{font-family:var(--thai);font-weight:700;font-size:1.08rem;color:var(--ink);margin:0;line-height:1.35;}
.login-head p{font-family:var(--thai);font-size:.84rem;color:var(--ink-soft);margin:4px 0 0;line-height:1.5;}

.login-pass{position:relative;display:flex;margin-top:-10px;}
.login-pass input{flex:1;padding-right:52px;}
.login-pass-toggle{
  position:absolute;top:50%;right:6px;transform:translateY(-50%);
  width:40px;height:40px;border:none;background:none;border-radius:8px;cursor:pointer;
  color:var(--ink-soft);display:flex;align-items:center;justify-content:center;
}
.login-pass-toggle:hover,.login-pass-toggle:focus-visible{color:var(--accent);background:var(--accent-soft);}
.login-pass-toggle svg{width:20px;height:20px;}
.login-pass-toggle .eye-off{display:none;}
.login-pass-toggle[aria-pressed="true"] .eye{display:none;}
.login-pass-toggle[aria-pressed="true"] .eye-off{display:block;}
.login-caps{margin:-8px 0 0;font-family:var(--thai);font-size:.8rem;color:#B9770E;text-align:left;}

.login-error{
  display:flex;gap:8px;align-items:flex-start;padding:10px 12px;border-radius:10px;
  background:#FDEDEC;border:1px solid #F5B7B1;line-height:1.5;
}
.login-error svg{flex:none;width:16px;height:16px;margin-top:2px;}

.login-trust{
  display:flex;flex-direction:column;gap:8px;padding-top:14px;border-top:1px solid var(--line-strong);
  font-family:var(--thai);font-size:.8rem;color:var(--ink-soft);text-align:left;
}
.login-trust-row{display:flex;align-items:center;gap:8px;}
.login-trust-row svg{flex:none;width:15px;height:15px;color:#1E8449;}
.login-trust a{color:var(--accent);font-weight:600;text-decoration:none;}
.login-trust a:hover{text-decoration:underline;}
@media (max-width:820px){
  .login-card-wrap{margin-top:16px;justify-content:center;}
}
@media (max-width:900px){
  .login-hero .hero-inner{padding:0 24px 40px;min-height:0;}
  .login-hero .hero-copy.reveal.in{transform:none;}
  .login-hero .hero-eyebrow{font-size:1.05rem;}
  .login-hero .hero-title{font-size:clamp(2.6rem,13vw,3.4rem);}
  .login-hero .hero-tagline{font-size:1.05rem;}
  .login-hero .hero-portrait{display:none;}
}
</style>
</head>
<body>

<header class="nav">
  <div class="nav-row">
    <a class="brand" href="index.html">
      <span class="brand-mark en">HYAMAX<sup>&reg;</sup></span>
    </a>
    <nav class="nav-links" id="navLinks">
      <a href="index.html">HOME</a>
      <a href="index.html#about">ABOUT</a>
      <a href="index.html#product-line">Product</a>
      <a href="index.html#clinics">Find a Clinic</a>
      <a href="index.html#event">EVENT</a>
      <a class="btn btn-primary" href="login.php">Login</a>
    </nav>
    <div class="nav-cta">
      <a class="btn btn-primary" href="login.php">Login</a>
      <button class="nav-toggle" id="navToggle" aria-label="เปิดเมนู" aria-expanded="false" aria-controls="navLinks">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<main id="main">
<section class="hero login-hero" id="top">
  <div class="hero-inner login-hero-inner">
    <div class="hero-copy reveal in">
      <span class="hero-eyebrow">Program</span>
      <h1 class="hero-title">HYAMAX<sup>&reg;</sup></h1>
      <p class="hero-tagline">&ldquo;Swiss Hyaluronic Acid Filler&rdquo;</p>

      <div class="login-card-wrap">
        <form class="login-card" id="loginForm" method="post" action="login.php">
          <?= csrf_field() ?>
          <div class="login-head">
            <span class="login-head-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 3v6c0 5-3.4 9.3-8 11-4.6-1.7-8-6-8-11V5l8-3z"/><path d="M9 12l2 2 4-4"/></svg>
            </span>
            <div>
              <h2>เข้าสู่ระบบ Hyamax Academy</h2>
              <p>สำหรับแพทย์และบุคลากรทางการแพทย์ที่ได้รับบัญชีจาก HYAMAX เท่านั้น</p>
            </div>
          </div>
          <label class="login-field">
            <span>User name</span>
            <input type="text" name="username" autocomplete="username" autocapitalize="none" spellcheck="false" maxlength="100" value="<?= htmlspecialchars($submittedUser, ENT_QUOTES, 'UTF-8') ?>" required>
          </label>
          <label class="login-field" for="loginPassword">
            <span>Password</span>
          </label>
          <div class="login-pass">
            <input type="password" id="loginPassword" name="password" autocomplete="current-password" maxlength="1024" required>
            <button type="button" class="login-pass-toggle" id="passToggle" aria-label="แสดงรหัสผ่าน" aria-pressed="false" aria-controls="loginPassword">
              <svg class="eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.4 10.4 0 0112 19c-7 0-11-7-11-7a19 19 0 015.1-5.9M9.9 4.2A9.6 9.6 0 0112 4c7 0 11 7 11 7a19 19 0 01-2.2 3.2M1 1l22 22"/><path d="M14.1 14.1a3 3 0 01-4.2-4.2"/></svg>
            </button>
          </div>
          <p class="login-caps" id="capsWarning" hidden>Caps Lock เปิดอยู่</p>
<?php if ($error !== ''): ?>
          <p class="login-error" id="loginError" role="alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 7v6M12 17h.01"/></svg>
            <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
          </p>
<?php endif; ?>
          <button type="submit" class="btn login-submit" id="loginSubmit">LOGIN</button>
          <div class="login-trust">
<?php if ($isHttps): ?>
            <div class="login-trust-row">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>
              <span>เชื่อมต่อแบบเข้ารหัส (HTTPS) ข้อมูลของคุณถูกส่งอย่างปลอดภัย</span>
            </div>
<?php endif; ?>
            <div class="login-trust-row">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2l8 3v6c0 5-3.4 9.3-8 11-4.6-1.7-8-6-8-11V5l8-3z"/></svg>
              <span>ใส่รหัสผิด 5 ครั้ง ระบบจะล็อกชั่วคราว 15 นาทีเพื่อป้องกันการเดารหัส</span>
            </div>
            <div class="login-trust-row">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 015.8 1c0 2-3 3-3 3M12 17h.01"/></svg>
              <span>ลืมรหัสผ่านหรือยังไม่มีบัญชี? <a href="index.html#contact">ติดต่อทีม HYAMAX ทาง LINE</a></span>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="hero-portrait">
    <img src="HYAMAX-white-web.png" alt="Hyamax model">
  </div>
</section>
</main>

<script nonce="<?= $cspNonce ?>">
(function(){
  var pass = document.getElementById("loginPassword");
  var passToggle = document.getElementById("passToggle");
  var caps = document.getElementById("capsWarning");
  var form = document.getElementById("loginForm");
  var submit = document.getElementById("loginSubmit");

  passToggle.addEventListener("click", function(){
    var show = pass.type === "password";
    pass.type = show ? "text" : "password";
    passToggle.setAttribute("aria-pressed", show ? "true" : "false");
    passToggle.setAttribute("aria-label", show ? "ซ่อนรหัสผ่าน" : "แสดงรหัสผ่าน");
    pass.focus();
  });

  function checkCaps(ev){
    if(ev.getModifierState){ caps.hidden = !ev.getModifierState("CapsLock"); }
  }
  pass.addEventListener("keydown", checkCaps);
  pass.addEventListener("keyup", checkCaps);
  pass.addEventListener("blur", function(){ caps.hidden = true; });

  form.addEventListener("submit", function(){
    pass.type = "password";
    submit.disabled = true;
    submit.textContent = "กำลังตรวจสอบ...";
  });
  window.addEventListener("pageshow", function(){
    submit.disabled = false;
    submit.textContent = "LOGIN";
  });

  var toggle = document.getElementById("navToggle");
  var links = document.getElementById("navLinks");
  if(toggle && links){
    toggle.addEventListener("click", function(){
      var open = links.classList.toggle("open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }
})();
</script>
</body>
</html>
