<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — ResearchLens</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;min-height:100vh;background:linear-gradient(135deg,#eef2ff 0%,#e0f2fe 50%,#f0f4ff 100%);display:flex;align-items:center;justify-content:center;padding:20px;position:relative;overflow:hidden}
.bg-blob{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.55;pointer-events:none}
.blob1{width:380px;height:380px;background:radial-gradient(#c7d2fe,transparent);top:-80px;left:-60px}
.blob2{width:320px;height:320px;background:radial-gradient(#bae6fd,transparent);bottom:-60px;right:-40px}
.blob3{width:260px;height:260px;background:radial-gradient(#ddd6fe,transparent);top:50%;right:8%;transform:translateY(-50%)}
.auth-wrap{position:relative;z-index:10;display:grid;grid-template-columns:1fr 1fr;gap:0;width:100%;max-width:920px;background:#fff;border-radius:22px;box-shadow:0 20px 60px rgba(99,102,241,0.14),0 4px 16px rgba(0,0,0,0.06);overflow:hidden}
.auth-left{background:linear-gradient(145deg,#6366f1,#8b5cf6,#06b6d4);padding:48px 40px;display:flex;flex-direction:column;justify-content:center;color:#fff}
.auth-right{padding:44px 40px}
.brand{display:flex;align-items:center;gap:11px;margin-bottom:40px}
.brand-icon{width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;backdrop-filter:blur(8px)}
.brand-name{font-size:20px;font-weight:800}
.brand-sub{font-size:11px;opacity:0.8;margin-top:1px}
.left-title{font-size:28px;font-weight:800;line-height:1.3;margin-bottom:12px}
.left-sub{font-size:14px;opacity:0.85;line-height:1.6;margin-bottom:32px}
.feat-item{display:flex;align-items:center;gap:10px;font-size:13px;opacity:0.9;margin-bottom:12px}
.feat-dot{width:28px;height:28px;background:rgba(255,255,255,0.2);border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0}
.right-logo{display:flex;align-items:center;gap:10px;margin-bottom:28px}
.right-logo-icon{width:38px;height:38px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;box-shadow:0 4px 12px rgba(99,102,241,0.3)}
.right-logo-text{font-size:17px;font-weight:800;color:#1e293b}
h1{font-size:22px;font-weight:800;color:#1e293b;margin-bottom:5px}
.subtitle{font-size:13px;color:#64748b;margin-bottom:22px}
.alert-err{background:#fef2f2;border:1px solid rgba(239,68,68,0.25);color:#dc2626;padding:11px 14px;border-radius:9px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:7px}
.alert-ok{background:#f0fdf4;border:1px solid rgba(16,185,129,0.25);color:#047857;padding:11px 14px;border-radius:9px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:7px}
.field{margin-bottom:14px}
.label{font-size:12px;font-weight:600;color:#475569;display:block;margin-bottom:5px}
.input-wrap{position:relative}
.input{width:100%;background:#f8faff;border:1.5px solid #e2e8f0;border-radius:9px;padding:11px 16px 11px 40px;color:#1e293b;font-size:14px;font-family:'Inter',sans-serif;outline:none;transition:all 0.2s}
.input:focus{border-color:#6366f1;background:#fff;box-shadow:0 0 0 3px rgba(99,102,241,0.1)}
.input::placeholder{color:#cbd5e1}
.input-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;pointer-events:none}
.toggle-pw{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;font-size:13px;padding:0}
.toggle-pw:hover{color:#6366f1}
.row{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
.remember{display:flex;align-items:center;gap:7px;font-size:13px;color:#64748b;cursor:pointer}
.remember input{accent-color:#6366f1;width:14px;height:14px}
.forgot{font-size:13px;color:#6366f1;text-decoration:none;font-weight:500}
.forgot:hover{color:#4f46e5}
.btn-submit{width:100%;padding:13px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s;box-shadow:0 4px 14px rgba(99,102,241,0.35)}
.btn-submit:hover{transform:translateY(-1px);box-shadow:0 7px 20px rgba(99,102,241,0.45)}
.btn-submit:disabled{opacity:0.7;cursor:not-allowed;transform:none}
.link-row{text-align:center;font-size:13px;color:#64748b;margin-top:18px}
.link-row a{color:#6366f1;font-weight:600;text-decoration:none}
.link-row a:hover{color:#4f46e5}
.demo-box{margin-top:16px;padding:11px 14px;background:#f8faff;border:1.5px solid #e2e8f0;border-radius:9px;font-size:12px;color:#64748b;text-align:center}
.demo-box strong{color:#6366f1}
.err-field{font-size:11px;color:#dc2626;margin-top:4px}
@media(max-width:700px){.auth-wrap{grid-template-columns:1fr}.auth-left{display:none}}
</style>
</head>
<body>
<div class="bg-blob blob1"></div><div class="bg-blob blob2"></div><div class="bg-blob blob3"></div>
<div class="auth-wrap">
    <!-- Left Panel -->
    <div class="auth-left">
        <div class="brand">
            <div class="brand-icon"><i class="fas fa-microscope"></i></div>
            <div><div class="brand-name">ResearchLens</div><div class="brand-sub">Academic Analytics Platform</div></div>
        </div>
        <div class="left-title">Analisis Penelitian Lebih Cerdas dengan AI</div>
        <div class="left-sub">Platform deteksi kemiripan topik dan rekomendasi inovasi untuk penelitian akademik Anda.</div>
        <div class="feat-item"><div class="feat-dot"><i class="fas fa-brain"></i></div> Cosine Similarity Analysis</div>
        <div class="feat-item"><div class="feat-dot"><i class="fas fa-lightbulb"></i></div> Novelty Score & Recommendations</div>
        <div class="feat-item"><div class="feat-dot"><i class="fas fa-chart-line"></i></div> Research Trend Analytics</div>
        <div class="feat-item"><div class="feat-dot"><i class="fas fa-shield-halved"></i></div> Secure REST API Access</div>
    </div>
    <!-- Right Panel (Form) -->
    <div class="auth-right">
        <div class="right-logo">
            <div class="right-logo-icon"><i class="fas fa-microscope"></i></div>
            <div><div class="right-logo-text">ResearchLens</div></div>
        </div>
        <h1>Selamat Datang Kembali</h1>
        <p class="subtitle">Masuk ke akun Anda untuk melanjutkan</p>
        @if($errors->any())<div class="alert-err"><i class="fas fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif
        @if(session('success'))<div class="alert-ok"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            <div class="field">
                <label class="label">Email Address</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="input" placeholder="email@example.com" value="{{ old('email') }}" required autocomplete="email">
                </div>
                @error('email')<div class="err-field">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label class="label">Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" class="input" id="pwIn" placeholder="Masukkan password" required style="padding-right:40px">
                    <button type="button" class="toggle-pw" onclick="tgl()"><i class="fas fa-eye" id="pwEye"></i></button>
                </div>
                @error('password')<div class="err-field">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                <label class="remember"><input type="checkbox" name="remember" {{ old('remember')?'checked':'' }}> Ingat saya</label>
                <a href="{{ route('password.request') }}" class="forgot">Lupa password?</a>
            </div>
            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="fas fa-right-to-bracket"></i> Masuk
            </button>
        </form>
        <div class="link-row">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></div>
        <div class="demo-box">Demo Admin: <strong>admin@researchlens.com</strong> / <strong>admin123456</strong></div>
    </div>
</div>
<script>
function tgl(){const i=document.getElementById('pwIn'),e=document.getElementById('pwEye');i.type=i.type==='password'?'text':'password';e.className=i.type==='text'?'fas fa-eye-slash':'fas fa-eye';}
document.getElementById('loginForm').addEventListener('submit',function(){const b=document.getElementById('submitBtn');b.disabled=true;b.innerHTML='<i class="fas fa-spinner fa-spin"></i> Masuk...';});
</script>
</body></html>
