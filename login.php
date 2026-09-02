<?php
session_start();

if (!empty($_SESSION['hyamax_academy_auth'])) {
    header('Location: academy.php');
    exit;
}

const AUTH_USER = 'doctor';
const AUTH_SALT_HEX = '6588f387997c07b7362a872412d37967';
const AUTH_HASH_HEX = 'a144300f0d0358381d9b069423e51b5dfaf164fcc3224b0d0fca8191dba05fc5';
const AUTH_ITERATIONS = 100000;

$error = false;
$submittedUser = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedUser = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $salt = hex2bin(AUTH_SALT_HEX);
    $computed = hash_pbkdf2('sha256', $password, $salt, AUTH_ITERATIONS, 32, false);

    if ($submittedUser === AUTH_USER && hash_equals(AUTH_HASH_HEX, $computed)) {
        session_regenerate_id(true);
        $_SESSION['hyamax_academy_auth'] = true;
        header('Location: academy.php');
        exit;
    }

    $error = true;
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
<style>
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
        <form class="login-card" id="loginForm" method="post" action="login.php" novalidate>
          <label class="login-field">
            <span>User name</span>
            <input type="text" name="username" autocomplete="username" value="<?= htmlspecialchars($submittedUser) ?>" required>
          </label>
          <label class="login-field">
            <span>Password</span>
            <input type="password" name="password" autocomplete="current-password" required>
          </label>
          <p class="login-error" id="loginError"<?= $error ? '' : ' hidden' ?>>ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง</p>
          <button type="submit" class="btn login-submit">LOGIN</button>
        </form>
      </div>
    </div>
  </div>
  <div class="hero-portrait">
    <img src="HYAMAX-white.png" alt="Hyamax model" onerror="this.style.display='none'">
  </div>
</section>
</main>

<script>
(function(){
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
