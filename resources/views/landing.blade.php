<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ResearchLens — AI Academic Analytics Platform</title>
<meta name="description" content="Platform analisis kemiripan penelitian berbasis AI. Deteksi duplikasi, tren penelitian, dan rekomendasi inovasi untuk tugas akhir.">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--blue:#6366f1;--cyan:#06b6d4;--purple:#8b5cf6;--green:#10b981;--text:#1e293b;--muted:#64748b;--border:rgba(99,102,241,0.12)}
body{font-family:'Inter',sans-serif;background:#f8faff;color:var(--text);overflow-x:hidden}

/* NAV */
nav{position:fixed;top:0;width:100%;z-index:100;background:rgba(255,255,255,0.92);backdrop-filter:blur(16px);border-bottom:1px solid var(--border);padding:0 5%}
.nav-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;height:64px}
.nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.nav-logo-icon{width:36px;height:36px;background:linear-gradient(135deg,var(--blue),var(--cyan));border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:15px;color:#fff}
.nav-logo-text{font-size:17px;font-weight:800;color:var(--text)}
.nav-links{display:flex;align-items:center;gap:6px}
.nav-link{color:var(--muted);text-decoration:none;font-size:14px;font-weight:500;padding:7px 14px;border-radius:8px;transition:all 0.2s}
.nav-link:hover{color:var(--text);background:rgba(99,102,241,0.07)}
.nav-btn{background:linear-gradient(135deg,var(--blue),var(--cyan));color:#fff;padding:8px 20px;border-radius:9px;font-size:14px;font-weight:700;text-decoration:none;box-shadow:0 4px 12px rgba(99,102,241,0.28);transition:all 0.2s}
.nav-btn:hover{transform:translateY(-1px);box-shadow:0 7px 18px rgba(99,102,241,0.38)}
.nav-btn-ghost{background:#fff;color:var(--blue);border:1.5px solid rgba(99,102,241,0.25);padding:7px 18px;border-radius:9px;font-size:14px;font-weight:600;text-decoration:none;transition:all 0.2s}
.nav-btn-ghost:hover{border-color:var(--blue);background:rgba(99,102,241,0.05)}

/* HERO */
.hero{min-height:100vh;display:flex;align-items:center;padding:90px 5% 60px;background:linear-gradient(160deg,#f8faff 0%,#eef2ff 40%,#e0f2fe 100%);position:relative;overflow:hidden}
.hero::before{content:'';position:absolute;top:-100px;right:-100px;width:600px;height:600px;background:radial-gradient(circle,rgba(99,102,241,0.08),transparent 70%);pointer-events:none}
.hero::after{content:'';position:absolute;bottom:-80px;left:-60px;width:500px;height:500px;background:radial-gradient(circle,rgba(6,182,212,0.07),transparent 70%);pointer-events:none}
.hero-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;position:relative;z-index:2}
.hero-tag{display:inline-flex;align-items:center;gap:7px;padding:6px 14px;background:#fff;border:1.5px solid rgba(99,102,241,0.2);border-radius:20px;font-size:12px;font-weight:600;color:var(--blue);margin-bottom:18px;box-shadow:0 2px 8px rgba(99,102,241,0.1)}
.hero-dot{width:7px;height:7px;background:var(--cyan);border-radius:50%;animation:pulse 2s ease-in-out infinite}
@keyframes pulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:0.6}}
h1{font-size:50px;font-weight:900;line-height:1.1;color:var(--text);margin-bottom:18px}
.grad-text{background:linear-gradient(135deg,var(--blue),var(--cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hero-desc{font-size:16px;color:var(--muted);line-height:1.7;margin-bottom:30px;max-width:480px}
.hero-btns{display:flex;gap:12px;flex-wrap:wrap}
.btn-hero{display:inline-flex;align-items:center;gap:8px;padding:14px 28px;border-radius:11px;font-size:15px;font-weight:700;text-decoration:none;transition:all 0.25s}
.btn-hero-primary{background:linear-gradient(135deg,var(--blue),var(--cyan));color:#fff;box-shadow:0 6px 20px rgba(99,102,241,0.32)}
.btn-hero-primary:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(99,102,241,0.42)}
.btn-hero-ghost{background:#fff;color:var(--text);border:1.5px solid var(--border);box-shadow:0 2px 8px rgba(0,0,0,0.06)}
.btn-hero-ghost:hover{border-color:var(--blue);color:var(--blue)}
.hero-visual .demo-card{background:#fff;border:1px solid var(--border);border-radius:18px;padding:22px;box-shadow:0 12px 40px rgba(99,102,241,0.12)}
.demo-header{display:flex;align-items:center;gap:8px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border)}
.demo-dot{width:10px;height:10px;border-radius:50%}
.bar-row{display:flex;align-items:center;gap:10px;margin-bottom:10px}
.bar-label{font-size:12px;color:var(--muted);width:76px;flex-shrink:0}
.bar-track{flex:1;height:7px;background:#f1f5f9;border-radius:20px;overflow:hidden}
.bar-fill{height:100%;border-radius:20px}
.bar-val{font-size:12px;font-weight:700;width:36px;text-align:right}
.float-badge{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;margin:3px}

/* STATS STRIP */
.stats{background:#fff;padding:40px 5%;border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.stats-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);gap:24px;text-align:center}
.stat-num{font-size:36px;font-weight:900;background:linear-gradient(135deg,var(--blue),var(--cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:4px}
.stat-desc{font-size:13px;color:var(--muted)}

/* FEATURES */
.section{padding:80px 5%}
.section-inner{max-width:1200px;margin:0 auto}
.section-tag{display:inline-flex;align-items:center;gap:6px;padding:5px 14px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.2);border-radius:20px;font-size:12px;color:var(--blue);font-weight:600;margin-bottom:14px}
.section-title{font-size:34px;font-weight:800;margin-bottom:12px;line-height:1.2}
.section-sub{font-size:15px;color:var(--muted);max-width:540px;line-height:1.6;margin-bottom:50px}
.feat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.feat-card{background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:26px;transition:all 0.3s}
.feat-card:hover{border-color:rgba(99,102,241,0.35);box-shadow:0 8px 28px rgba(99,102,241,0.1);transform:translateY(-4px)}
.feat-icon{width:50px;height:50px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:16px}
.feat-title{font-size:16px;font-weight:700;margin-bottom:9px}
.feat-desc{font-size:13px;color:var(--muted);line-height:1.6}

/* PROCESS */
.process{padding:80px 5%;background:linear-gradient(135deg,#f0f4ff,#e0f2fe)}
.process-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-top:48px}
.step-card{background:#fff;border-radius:14px;padding:22px;text-align:center;border:1px solid var(--border);box-shadow:0 2px 12px rgba(99,102,241,0.06)}
.step-num{width:50px;height:50px;background:linear-gradient(135deg,var(--blue),var(--cyan));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:#fff;margin:0 auto 14px;box-shadow:0 4px 14px rgba(99,102,241,0.3)}
.step-title{font-size:14px;font-weight:700;margin-bottom:6px}
.step-desc{font-size:12px;color:var(--muted);line-height:1.5}

/* CTA */
.cta{padding:80px 5%;text-align:center}
.cta-box{max-width:680px;margin:0 auto;background:#fff;border-radius:22px;padding:52px 40px;border:1.5px solid var(--border);box-shadow:0 12px 40px rgba(99,102,241,0.1)}
.cta-title{font-size:36px;font-weight:800;margin-bottom:12px}
.cta-sub{font-size:15px;color:var(--muted);margin-bottom:28px;line-height:1.6}
.cta-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}

/* FOOTER */
footer{background:#fff;padding:50px 5% 28px;border-top:1px solid var(--border)}
.footer-inner{max-width:1200px;margin:0 auto}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:40px;margin-bottom:36px}
.footer-desc{font-size:13px;color:var(--muted);line-height:1.6;margin-top:8px}
.footer-head{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px}
.footer-link{display:block;font-size:13px;color:#94a3b8;text-decoration:none;margin-bottom:7px;transition:color 0.2s}
.footer-link:hover{color:var(--blue)}
.footer-bottom{border-top:1px solid var(--border);padding-top:22px;display:flex;justify-content:space-between;font-size:12px;color:#94a3b8}

/* RESPONSIVE */
@media(max-width:900px){.hero-inner{grid-template-columns:1fr}.hero-visual{display:none}.feat-grid{grid-template-columns:1fr 1fr}.stats-inner{grid-template-columns:repeat(2,1fr)}.process-grid{grid-template-columns:repeat(2,1fr)}.footer-grid{grid-template-columns:1fr}}
@media(max-width:600px){h1{font-size:34px}.feat-grid{grid-template-columns:1fr}.nav-link{display:none}.process-grid{grid-template-columns:1fr}}

@keyframes fadeInUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
.anim{opacity:0;animation:fadeInUp 0.65s ease forwards}
.d1{animation-delay:0.1s}.d2{animation-delay:0.2s}.d3{animation-delay:0.3s}.d4{animation-delay:0.4s}
</style>
</head>
<body>

<nav>
    <div class="nav-inner">
        <a href="{{ route('landing') }}" class="nav-logo">
            <div class="nav-logo-icon"><i class="fas fa-microscope"></i></div>
            <span class="nav-logo-text">ResearchLens</span>
        </a>
        <div class="nav-links">
            <a href="#features" class="nav-link">Fitur</a>
            <a href="#process" class="nav-link">Cara Kerja</a>
            @auth
                <a href="{{ route('dashboard') }}" class="nav-btn">Dashboard <i class="fas fa-arrow-right"></i></a>
            @else
                <a href="{{ route('login') }}" class="nav-btn-ghost">Masuk</a>
                <a href="{{ route('register') }}" class="nav-btn">Daftar Gratis</a>
            @endauth
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-inner">
        <div>
            <div class="hero-tag anim d1"><span class="hero-dot"></span> Powered by Cosine Similarity AI</div>
            <h1 class="anim d2">Analisis <span class="grad-text">Penelitian</span><br>Lebih Cerdas</h1>
            <p class="hero-desc anim d3">ResearchLens membantu mahasiswa menganalisis kemiripan topik tugas akhir, mendeteksi duplikasi, dan mendapatkan rekomendasi inovasi berbasis AI.</p>
            <div class="hero-btns anim d4">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-hero btn-hero-primary"><i class="fas fa-gauge-high"></i> Buka Dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="btn-hero btn-hero-primary"><i class="fas fa-rocket"></i> Mulai Gratis</a>
                    <a href="#features" class="btn-hero btn-hero-ghost"><i class="fas fa-play-circle"></i> Pelajari Fitur</a>
                @endauth
            </div>
        </div>
        <div class="hero-visual anim d3">
            <div class="demo-card">
                <div class="demo-header">
                    <div class="demo-dot" style="background:#ef4444"></div>
                    <div class="demo-dot" style="background:#f59e0b"></div>
                    <div class="demo-dot" style="background:#10b981"></div>
                    <span style="font-size:12px;color:var(--muted);margin-left:6px">Similarity Analysis Result</span>
                </div>
                <div style="font-size:13px;font-weight:600;color:var(--muted);margin-bottom:14px">"Implementasi BERT untuk Deteksi Hoaks di Media Sosial"</div>
                <div class="bar-row"><div class="bar-label">Title</div><div class="bar-track"><div class="bar-fill" style="width:18%;background:var(--blue)"></div></div><div class="bar-val" style="color:var(--blue)">18%</div></div>
                <div class="bar-row"><div class="bar-label">Abstract</div><div class="bar-track"><div class="bar-fill" style="width:24%;background:var(--cyan)"></div></div><div class="bar-val" style="color:var(--cyan)">24%</div></div>
                <div class="bar-row"><div class="bar-label">Keywords</div><div class="bar-track"><div class="bar-fill" style="width:15%;background:var(--purple)"></div></div><div class="bar-val" style="color:var(--purple)">15%</div></div>
                <div class="bar-row"><div class="bar-label">Overall</div><div class="bar-track"><div class="bar-fill" style="width:21%;background:var(--green)"></div></div><div class="bar-val" style="color:var(--green)">21%</div></div>
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);display:flex;flex-wrap:wrap">
                    <span class="float-badge" style="background:#f0fdf4;color:var(--green);border:1px solid rgba(16,185,129,0.2)"><i class="fas fa-star"></i> Novelty: 86%</span>
                    <span class="float-badge" style="background:#eef2ff;color:var(--blue);border:1px solid rgba(99,102,241,0.2)"><i class="fas fa-check"></i> Low Similarity</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS -->
<div class="stats">
    <div class="stats-inner">
        <div><div class="stat-num">100+</div><div class="stat-desc">Proyek Dianalisis</div></div>
        <div><div class="stat-num">50+</div><div class="stat-desc">Peneliti Aktif</div></div>
        <div><div class="stat-num">95%</div><div class="stat-desc">Akurasi Deteksi</div></div>
        <div><div class="stat-num">12</div><div class="stat-desc">Kategori Penelitian</div></div>
    </div>
</div>

<!-- FEATURES -->
<section class="section" id="features">
    <div class="section-inner">
        <div class="section-tag"><i class="fas fa-sparkles"></i> Core Features</div>
        <h2 class="section-title">Semua yang Anda Butuhkan<br><span class="grad-text">untuk Riset Terbaik</span></h2>
        <p class="section-sub">Platform lengkap untuk analisis similarity, deteksi inovasi, dan monitoring tren akademik.</p>
        <div class="feat-grid">
            <div class="feat-card">
                <div class="feat-icon" style="background:#eef2ff;color:var(--blue)"><i class="fas fa-brain"></i></div>
                <div class="feat-title">Smart Similarity Detection</div>
                <div class="feat-desc">Cosine Similarity menganalisis judul, abstrak, kata kunci, dan metode untuk deteksi duplikasi yang presisi.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon" style="background:#ecfeff;color:var(--cyan)"><i class="fas fa-chart-line"></i></div>
                <div class="feat-title">Novelty Scoring</div>
                <div class="feat-desc">Skor orisinalitas dihitung otomatis berdasarkan perbandingan dengan seluruh proyek di platform.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon" style="background:#f5f3ff;color:var(--purple)"><i class="fas fa-lightbulb"></i></div>
                <div class="feat-title">AI Recommendations</div>
                <div class="feat-desc">Rekomendasi inovasi: teknologi baru, metodologi alternatif, dan arah penelitian yang relevan.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon" style="background:#f0fdf4;color:var(--green)"><i class="fas fa-fire-flame-curved"></i></div>
                <div class="feat-title">Research Trends</div>
                <div class="feat-desc">Analisis tren topik populer, kata kunci naik daun, dan distribusi penelitian per kategori.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon" style="background:#fdf2f8;color:#ec4899"><i class="fas fa-bell"></i></div>
                <div class="feat-title">Notifikasi Real-time</div>
                <div class="feat-desc">Notifikasi otomatis saat analisis selesai, peringatan similarity tinggi, dan update skor novelty.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon" style="background:#eef2ff;color:var(--blue)"><i class="fas fa-shield-halved"></i></div>
                <div class="feat-title">Secure REST API</div>
                <div class="feat-desc">API aman dengan Sanctum authentication. Integrasi mudah dengan sistem akademik lainnya.</div>
            </div>
        </div>
    </div>
</section>

<!-- PROCESS -->
<section class="process" id="process">
    <div class="section-inner">
        <div style="text-align:center">
            <div class="section-tag" style="display:inline-flex"><i class="fas fa-gears"></i> Cara Kerja</div>
            <h2 class="section-title" style="margin-bottom:8px">Proses Analisis <span class="grad-text">Otomatis</span></h2>
            <p style="font-size:15px;color:var(--muted)">Dari teks mentah hingga skor similarity dalam hitungan detik.</p>
        </div>
        <div class="process-grid">
            <div class="step-card"><div class="step-num">1</div><div class="step-title">Lowercase & Cleaning</div><div class="step-desc">Teks dikonversi ke huruf kecil, tanda baca dan URL dihapus</div></div>
            <div class="step-card"><div class="step-num">2</div><div class="step-title">Tokenize & Stopword</div><div class="step-desc">Teks dipotong menjadi token dan kata umum dihapus</div></div>
            <div class="step-card"><div class="step-num">3</div><div class="step-title">Stemming & Vectorize</div><div class="step-desc">Kata disederhanakan ke bentuk dasar lalu divektorisasi TF</div></div>
            <div class="step-card"><div class="step-num">4</div><div class="step-title">Cosine Similarity</div><div class="step-desc">Sudut antar vektor dihitung untuk skor kemiripan akhir</div></div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="cta-box">
        <div class="section-tag" style="display:inline-flex;margin-bottom:16px"><i class="fas fa-rocket"></i> Mulai Sekarang</div>
        <h2 class="cta-title">Siap Membuat Penelitian <span class="grad-text">yang Lebih Inovatif?</span></h2>
        <p class="cta-sub">Bergabung dengan ratusan mahasiswa yang menggunakan ResearchLens untuk memastikan orisinalitas penelitian mereka.</p>
        @guest
        <div class="cta-btns">
            <a href="{{ route('register') }}" class="btn-hero btn-hero-primary"><i class="fas fa-user-plus"></i> Daftar Gratis</a>
            <a href="{{ route('login') }}" class="btn-hero btn-hero-ghost"><i class="fas fa-right-to-bracket"></i> Sudah punya akun</a>
        </div>
        @endguest
        @auth
        <a href="{{ route('dashboard') }}" class="btn-hero btn-hero-primary" style="display:inline-flex;margin:0 auto"><i class="fas fa-gauge-high"></i> Buka Dashboard</a>
        @endauth
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <div class="footer-grid">
            <div>
                <div style="display:flex;align-items:center;gap:9px;margin-bottom:4px">
                    <div style="width:30px;height:30px;background:linear-gradient(135deg,var(--blue),var(--cyan));border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;color:#fff"><i class="fas fa-microscope"></i></div>
                    <span style="font-size:15px;font-weight:800">ResearchLens</span>
                </div>
                <p class="footer-desc">Platform AI untuk analisis kemiripan penelitian akademik. Mendukung SDG 4: Quality Education.</p>
            </div>
            <div>
                <div class="footer-head">Platform</div>
                <a href="#features" class="footer-link">Fitur Utama</a>
                <a href="#process" class="footer-link">Cara Kerja</a>
                <a href="{{ route('register') }}" class="footer-link">Daftar</a>
                <a href="{{ route('login') }}" class="footer-link">Login</a>
            </div>
            <div>
                <div class="footer-head">Support</div>
                <a href="#" class="footer-link">Dokumentasi</a>
                <a href="#" class="footer-link">FAQ</a>
                <a href="#" class="footer-link">Kebijakan Privasi</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} ResearchLens. All rights reserved.</span>
            <span>Built with Laravel & Cosine Similarity AI</span>
        </div>
    </div>
</footer>
</body>
</html>
