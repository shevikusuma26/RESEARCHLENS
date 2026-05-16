<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar — ResearchLens</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;min-height:100vh;background:linear-gradient(135deg,#f0f4ff,#e0f2fe,#f5f3ff);display:flex;align-items:center;justify-content:center;padding:20px;position:relative;overflow:hidden}
.blob{position:absolute;border-radius:50%;filter:blur(70px);opacity:0.5;pointer-events:none}
.b1{width:400px;height:400px;background:radial-gradient(#ddd6fe,transparent);top:-80px;right:-60px}
.b2{width:350px;height:350px;background:radial-gradient(#bae6fd,transparent);bottom:-60px;left:-40px}
.card{position:relative;z-index:10;width:100%;max-width:540px;background:#fff;border-radius:22px;padding:40px;box-shadow:0 20px 60px rgba(99,102,241,0.12),0 4px 16px rgba(0,0,0,0.06)}
.logo{display:flex;align-items:center;gap:10px;margin-bottom:24px}
.logo-icon{width:38px;height:38px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;box-shadow:0 4px 10px rgba(99,102,241,0.3)}
.logo-text{font-size:17px;font-weight:800;color:#1e293b}
h1{font-size:22px;font-weight:800;color:#1e293b;margin-bottom:4px}
.subtitle{font-size:13px;color:#64748b;margin-bottom:20px}
.alert-err{background:#fef2f2;border:1px solid rgba(239,68,68,0.25);color:#dc2626;padding:11px 14px;border-radius:9px;font-size:13px;margin-bottom:16px}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.field{margin-bottom:0}
.label{font-size:12px;font-weight:600;color:#475569;display:block;margin-bottom:5px}
.input-wrap{position:relative}
.input{width:100%;background:#f8faff;border:1.5px solid #e2e8f0;border-radius:9px;padding:11px 16px 11px 40px;color:#1e293b;font-size:14px;font-family:'Inter',sans-serif;outline:none;transition:all 0.2s}
.input:focus{border-color:#6366f1;background:#fff;box-shadow:0 0 0 3px rgba(99,102,241,0.1)}
.input::placeholder{color:#cbd5e1}
.input-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;pointer-events:none}
.toggle-pw{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;font-size:13px}
.toggle-pw:hover{color:#6366f1}
.err-field{font-size:11px;color:#dc2626;margin-top:4px}
.pw-bar{height:4px;border-radius:4px;margin-top:6px;background:#f1f5f9;overflow:hidden}
.pw-fill{height:100%;border-radius:4px;width:0;transition:width 0.3s,background 0.3s}
.pw-label{font-size:11px;color:#94a3b8;margin-top:4px}
.btn-submit{width:100%;padding:13px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s;box-shadow:0 4px 14px rgba(99,102,241,0.35);margin-top:18px}
.btn-submit:hover{transform:translateY(-1px);box-shadow:0 7px 20px rgba(99,102,241,0.45)}
.btn-submit:disabled{opacity:0.7;cursor:not-allowed;transform:none}
.link-row{text-align:center;font-size:13px;color:#64748b;margin-top:16px}
.link-row a{color:#6366f1;font-weight:600;text-decoration:none}
</style>
</head>
<body>
<div class="blob b1"></div><div class="blob b2"></div>
<div class="card">
    <div class="logo">
        <div class="logo-icon"><i class="fas fa-microscope"></i></div>
        <div><div class="logo-text">ResearchLens</div></div>
    </div>
    <h1>Buat Akun Baru</h1>
    <p class="subtitle">Bergabung dan mulai analisis penelitian Anda secara gratis</p>
    @if($errors->any())
    <div class="alert-err">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
    @endif
    <form method="POST" action="{{ route('register') }}" id="regForm">
        @csrf
        <div style="display:grid;gap:14px">
            <div class="field" style="grid-column:1/-1">
                <label class="label">Nama Lengkap *</label>
                <div class="input-wrap">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="name" class="input" placeholder="Nama lengkap Anda" value="{{ old('name') }}" required>
                </div>
                @error('name')<div class="err-field">{{ $message }}</div>@enderror
            </div>
            <div class="grid2">
                <div class="field">
                    <label class="label">NIM <span style="color:#94a3b8;font-weight:400">(opsional)</span></label>
                    <div class="input-wrap">
                        <i class="fas fa-id-card input-icon"></i>
                        <input type="text" name="student_id" class="input" placeholder="NIM123456" value="{{ old('student_id') }}">
                    </div>
                </div>
                <div class="field">
                    <label class="label">No. HP <span style="color:#94a3b8;font-weight:400">(opsional)</span></label>
                    <div class="input-wrap">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="text" name="phone" class="input" placeholder="08xx-xxxx" value="{{ old('phone') }}">
                    </div>
                </div>
            </div>
            <div class="field">
                <label class="label">Email Address *</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="input" placeholder="email@example.com" value="{{ old('email') }}" required>
                </div>
                @error('email')<div class="err-field">{{ $message }}</div>@enderror
            </div>
            <div class="grid2">
                <div class="field">
                    <label class="label">Password *</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" class="input" id="pw1" placeholder="Min. 8 karakter" required oninput="checkPw(this.value)" style="padding-right:38px">
                        <button type="button" class="toggle-pw" onclick="tgl('pw1','e1')"><i class="fas fa-eye" id="e1"></i></button>
                    </div>
                    <div class="pw-bar"><div class="pw-fill" id="pwF"></div></div>
                    <div class="pw-label" id="pwL">Masukkan password</div>
                    @error('password')<div class="err-field">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label class="label">Konfirmasi *</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password_confirmation" class="input" id="pw2" placeholder="Ulangi password" required style="padding-right:38px">
                        <button type="button" class="toggle-pw" onclick="tgl('pw2','e2')"><i class="fas fa-eye" id="e2"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn-submit" id="submitBtn"><i class="fas fa-user-plus"></i> Buat Akun</button>
    </form>
    <div class="link-row">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></div>
</div>
<script>
function tgl(id,ei){const i=document.getElementById(id),e=document.getElementById(ei);i.type=i.type==='password'?'text':'password';e.className=i.type==='text'?'fas fa-eye-slash':'fas fa-eye';}
function checkPw(v){let s=0;if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;const c=['','#ef4444','#f59e0b','#06b6d4','#10b981'],l=['','Lemah','Sedang','Kuat','Sangat Kuat'],p=[0,25,50,75,100];document.getElementById('pwF').style.cssText=`width:${p[s]}%;background:${c[s]||'#ef4444'}`;document.getElementById('pwL').textContent=l[s]||'';document.getElementById('pwL').style.color=c[s]||'#94a3b8';}
document.getElementById('regForm').addEventListener('submit',function(){const b=document.getElementById('submitBtn');b.disabled=true;b.innerHTML='<i class="fas fa-spinner fa-spin"></i> Membuat akun...';});
</script>
</body></html>
