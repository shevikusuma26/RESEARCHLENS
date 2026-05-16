<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lupa Password — ResearchLens</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;min-height:100vh;background:linear-gradient(135deg,#eef2ff,#e0f2fe,#f0f4ff);display:flex;align-items:center;justify-content:center;padding:20px}
.card{width:100%;max-width:420px;background:#fff;border-radius:20px;padding:38px;box-shadow:0 16px 48px rgba(99,102,241,0.12)}
.logo{display:flex;align-items:center;gap:10px;margin-bottom:26px}
.logo-icon{width:38px;height:38px;background:linear-gradient(135deg,#6366f1,#06b6d4);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff}
.logo-text{font-size:17px;font-weight:800;color:#1e293b}
h1{font-size:21px;font-weight:800;color:#1e293b;margin-bottom:6px}
.sub{font-size:13px;color:#64748b;line-height:1.6;margin-bottom:22px}
.alert-ok{background:#f0fdf4;border:1px solid rgba(16,185,129,0.25);color:#047857;padding:11px 14px;border-radius:9px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:7px}
.alert-err{background:#fef2f2;border:1px solid rgba(239,68,68,0.25);color:#dc2626;padding:11px 14px;border-radius:9px;font-size:13px;margin-bottom:16px}
.label{font-size:12px;font-weight:600;color:#475569;display:block;margin-bottom:5px}
.input-wrap{position:relative}
.input{width:100%;background:#f8faff;border:1.5px solid #e2e8f0;border-radius:9px;padding:11px 16px 11px 40px;color:#1e293b;font-size:14px;font-family:'Inter',sans-serif;outline:none;transition:all 0.2s}
.input:focus{border-color:#6366f1;background:#fff;box-shadow:0 0 0 3px rgba(99,102,241,0.1)}
.input::placeholder{color:#cbd5e1}
.input-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;pointer-events:none}
.btn{width:100%;padding:13px;background:linear-gradient(135deg,#6366f1,#06b6d4);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;margin-top:16px;transition:all 0.2s;box-shadow:0 4px 14px rgba(99,102,241,0.3)}
.btn:hover{transform:translateY(-1px)}
.btn:disabled{opacity:0.7;cursor:not-allowed;transform:none}
.back{display:flex;align-items:center;justify-content:center;gap:6px;color:#6366f1;text-decoration:none;font-size:13px;font-weight:600;margin-top:18px}
.back:hover{color:#4f46e5}
</style>
</head>
<body>
<div class="card">
    <div class="logo"><div class="logo-icon"><i class="fas fa-microscope"></i></div><div class="logo-text">ResearchLens</div></div>
    <h1>Lupa Password?</h1>
    <p class="sub">Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mereset password.</p>
    @if(session('status'))<div class="alert-ok"><i class="fas fa-check-circle"></i> {{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert-err">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>@endif
    <form method="POST" action="{{ route('password.email') }}" id="f">
        @csrf
        <label class="label">Email Address</label>
        <div class="input-wrap">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email" class="input" placeholder="email@example.com" value="{{ old('email') }}" required>
        </div>
        <button type="submit" class="btn" id="b"><i class="fas fa-paper-plane"></i> Kirim Link Reset</button>
    </form>
    <a href="{{ route('login') }}" class="back"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
</div>
<script>document.getElementById('f').addEventListener('submit',function(){const b=document.getElementById('b');b.disabled=true;b.innerHTML='<i class="fas fa-spinner fa-spin"></i> Mengirim...';});</script>
</body></html>
