<?php require __DIR__ . '/auth-check.php'; ?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8" />
<title>Course | Hyamax Academy</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="fonts.css">
<link rel="stylesheet" href="site-base.css">
<style>
.course-band{background:var(--accent);color:#FFFFFF;padding:clamp(96px,13vw,140px) 0 clamp(64px,9vw,96px);}
.course-wrap{max-width:1180px;margin:0 auto;padding:0 clamp(20px,5vw,56px);}
.course-topbar{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:clamp(28px,4vw,40px);}
.course-back{
  font-family:var(--sans);font-weight:700;font-size:.85rem;color:rgba(255,255,255,.8);
  text-decoration:none;display:inline-flex;align-items:center;gap:8px;
}
.course-back:hover{color:#FFFFFF;}
.course-back svg{width:16px;height:16px;}
.academy-logout{
  font-family:var(--sans);font-weight:700;font-size:.8rem;color:rgba(255,255,255,.75);
  text-decoration:none;background:none;border:1px solid rgba(255,255,255,.4);
  border-radius:999px;padding:8px 18px;cursor:pointer;transition:all .2s ease;
}
.academy-logout:hover{color:#FFFFFF;border-color:#FFFFFF;}
.course-title{
  font-family:var(--display);font-weight:800;font-size:clamp(1.6rem,3vw,2.3rem);
  letter-spacing:-.01em;margin-bottom:clamp(32px,5vw,48px);
}

.video-block{position:relative;border-radius:16px;overflow:hidden;margin-bottom:clamp(28px,4vw,40px);}
.video-block-inner{display:flex;align-items:stretch;}
.video-side-label{
  flex:none;writing-mode:vertical-rl;transform:rotate(180deg);
  display:flex;align-items:center;justify-content:center;
  font-family:var(--display);font-weight:800;letter-spacing:.02em;
  font-size:clamp(2rem,4.4vw,3.4rem);color:#FFFFFF;padding:20px 6px;
}
.video-thumb-wrap{position:relative;flex:1 1 auto;}
.video-thumb-wrap img{width:100%;height:100%;object-fit:cover;display:block;filter:var(--grade-photo);}
.video-play{
  position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
  background:none;border:none;cursor:pointer;padding:0;
}
.video-play .ring{
  width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.28);
  display:flex;align-items:center;justify-content:center;transition:transform .2s ease, background .2s ease;
}
.video-play:hover .ring{transform:scale(1.08);background:rgba(255,255,255,.4);}
.video-play svg{width:22px;height:22px;color:#FFFFFF;margin-left:3px;}
.video-meta{padding:14px 4px 0;}
.video-meta p{font-family:var(--sans);font-weight:800;font-size:.78rem;letter-spacing:.03em;color:#FFFFFF;text-transform:uppercase;}
.video-meta p + p{margin-top:4px;font-weight:700;color:rgba(255,255,255,.7);}

.video-block.reverse .video-block-inner{flex-direction:row-reverse;}
.video-caption-label{
  flex:none;display:flex;align-items:flex-end;justify-content:flex-end;
  padding:18px 22px;text-align:right;
}
.video-caption-label .en{
  display:block;font-family:var(--display);font-weight:800;letter-spacing:.01em;
  font-size:clamp(1.5rem,3.4vw,2.6rem);line-height:1.05;color:#FFFFFF;
}

@media (max-width:700px){
  .video-block-inner{flex-direction:column;}
  .video-block.reverse .video-block-inner{flex-direction:column;}
  .video-side-label{writing-mode:horizontal-tb;transform:none;padding:14px 6px;font-size:1.6rem;}
  .video-caption-label{justify-content:flex-start;text-align:left;padding:14px 4px 0;}
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
      <a class="btn btn-primary" href="academy.php">Academy</a>
    </nav>
    <div class="nav-cta">
      <a class="btn btn-primary" href="academy.php">Academy</a>
      <button class="nav-toggle" id="navToggle" aria-label="เปิดเมนู" aria-expanded="false" aria-controls="navLinks">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<main id="main">
<section class="course-band">
  <div class="course-wrap">
    <div class="course-topbar">
      <a class="course-back" href="academy.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        Back to Academy
      </a>
      <a class="academy-logout" href="logout.php">Logout</a>
    </div>

    <h1 class="course-title en" id="courseTitle">Course</h1>

    <div class="video-block">
      <div class="video-block-inner">
        <div class="video-side-label en">Lecture</div>
        <div class="video-thumb-wrap">
          <img src="academy-lecture-thumb.jpg" alt="Lecture video thumbnail">
          <button type="button" class="video-play" data-video-src="" aria-label="เล่นวิดีโอ Lecture">
            <span class="ring"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7L8 5Z"/></svg></span>
          </button>
        </div>
      </div>
      <div class="video-meta">
        <p>By Dr. xxxxxxxxxxxxxxxxxxxxxxx</p>
        <p>Date 11-11-11</p>
      </div>
    </div>

    <div class="video-block reverse">
      <div class="video-block-inner">
        <div class="video-thumb-wrap">
          <img src="academy-demo-thumb.jpg" alt="Live demonstration video thumbnail">
          <button type="button" class="video-play" data-video-src="" aria-label="เล่นวิดีโอ Live Demonstration">
            <span class="ring"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7L8 5Z"/></svg></span>
          </button>
        </div>
        <div class="video-caption-label">
          <span class="en">Live<br>Demonstration</span>
        </div>
      </div>
    </div>
  </div>
</section>
</main>

<script>
(function(){
  var params = new URLSearchParams(window.location.search);
  var title = params.get("title");
  if(title){ document.getElementById("courseTitle").textContent = title; document.title = title + " | Hyamax Academy"; }

  document.querySelectorAll(".video-play").forEach(function(btn){
    btn.addEventListener("click", function(){
      var src = btn.getAttribute("data-video-src");
      if(src){
        window.location.href = src;
      } else {
        alert("วิดีโอนี้จะเปิดให้รับชมเร็วๆ นี้");
      }
    });
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
