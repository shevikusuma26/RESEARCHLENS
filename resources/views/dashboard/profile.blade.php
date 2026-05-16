@extends('layouts.dashboard')
@section('title','Profil Saya')
@section('page-title','Profil Saya')
@section('styles')
<style>
.profile-grid{display:grid;grid-template-columns:280px 1fr;gap:20px}
.section-divider{border:none;border-top:1px solid var(--border);margin:20px 0}
.pw-bar{height:4px;border-radius:4px;background:var(--bg-base);overflow:hidden;margin-top:6px;border:1px solid var(--border)}
.pw-fill{height:100%;border-radius:4px;width:0;transition:width 0.3s,background 0.3s}
.pw-label{font-size:11px;color:var(--text-muted);margin-top:4px}
.avatar-wrap{position:relative;display:inline-block;cursor:pointer}
.avatar-overlay{position:absolute;inset:0;background:rgba(99,102,241,0.6);border-radius:50%;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;color:#fff;font-size:18px}
.avatar-wrap:hover .avatar-overlay{opacity:1}
@media(max-width:900px){.profile-grid{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
<div class="profile-grid animate-in">
    <!-- Left Column -->
    <div style="display:flex;flex-direction:column;gap:18px">
        <!-- Avatar & Info -->
        <div class="card" style="text-align:center">
            <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" id="photoForm">
                @csrf
                <label for="photoInput" style="cursor:pointer">
                    <div class="avatar-wrap">
                        <img src="{{ $user->profile_photo_url }}" alt="Avatar" id="avatarImg" style="width:96px;height:96px;border-radius:50%;object-fit:cover;border:3px solid var(--accent)">
                        <div class="avatar-overlay"><i class="fas fa-camera"></i></div>
                    </div>
                </label>
                <input type="file" id="photoInput" name="profile_photo" accept="image/*" style="display:none" onchange="previewPhoto(this)">
            </form>
            <div style="font-size:17px;font-weight:800;margin-top:12px">{{ $user->name }}</div>
            <div style="font-size:13px;color:var(--text-muted)">{{ $user->email }}</div>
            <span class="badge {{ $user->isAdmin()?'badge-purple':'badge-blue' }}" style="margin-top:8px;padding:5px 14px">
                <i class="fas {{ $user->isAdmin()?'fa-shield-halved':'fa-graduation-cap' }}"></i> {{ ucfirst($user->role) }}
            </span>
            <hr class="section-divider">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;text-align:left">
                <div style="padding:10px;background:var(--accent-light);border-radius:9px;text-align:center">
                    <div style="font-size:20px;font-weight:800;color:var(--accent)">{{ $projects->count() }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">Proyek</div>
                </div>
                <div style="padding:10px;background:var(--accent-green-l);border-radius:9px;text-align:center">
                    <div style="font-size:20px;font-weight:800;color:var(--accent-green)">{{ $projects->where('status','analyzed')->count() }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">Teranalisis</div>
                </div>
            </div>
            @if($user->student_id)
            <div style="margin-top:12px;padding:10px;background:var(--bg-base);border-radius:9px;text-align:left">
                <div style="font-size:10px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.5px">NIM</div>
                <div style="font-size:13px;font-weight:600;margin-top:2px">{{ $user->student_id }}</div>
            </div>
            @endif
            @if($user->bio)
            <div style="margin-top:10px;padding:10px;background:var(--bg-base);border-radius:9px;text-align:left">
                <div style="font-size:10px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.5px">Bio</div>
                <div style="font-size:12px;color:var(--text-secondary);margin-top:2px;line-height:1.5">{{ $user->bio }}</div>
            </div>
            @endif
        </div>
        <!-- Recent Projects mini -->
        <div class="card">
            <div style="font-size:13px;font-weight:700;margin-bottom:12px">Proyek Terakhir</div>
            @foreach($projects->take(4) as $p)
            <div style="padding:9px 0;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px">
                <div style="font-size:12px;font-weight:500;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $p->title }}</div>
                <span class="badge {{ $p->novelty_score>=70?'badge-green':($p->novelty_score>=40?'badge-amber':'badge-red') }}" style="flex-shrink:0;font-size:10px">{{ $p->novelty_score }}%</span>
            </div>
            @endforeach
            @if($projects->isEmpty())
            <div style="text-align:center;color:var(--text-muted);font-size:12px;padding:12px">Belum ada proyek.</div>
            @endif
        </div>
    </div>

    <!-- Right Column -->
    <div style="display:flex;flex-direction:column;gap:18px">
        <!-- Edit Profile -->
        <div class="card">
            <div style="font-size:15px;font-weight:700;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--border)">
                <i class="fas fa-user-pen" style="color:var(--accent);margin-right:8px"></i>Edit Profil
            </div>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div style="grid-column:1/-1">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name',$user->name) }}" required>
                    </div>
                    <div>
                        <label class="form-label">NIM / Student ID</label>
                        <input type="text" name="student_id" class="form-input" value="{{ old('student_id',$user->student_id) }}" placeholder="NIM123456">
                    </div>
                    <div>
                        <label class="form-label">Nomor HP</label>
                        <input type="text" name="phone" class="form-input" value="{{ old('phone',$user->phone) }}" placeholder="08xx-xxxx-xxxx">
                    </div>
                    <div style="grid-column:1/-1">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" class="form-input" rows="3" placeholder="Ceritakan tentang diri Anda...">{{ old('bio',$user->bio) }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:16px"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="card">
            <div style="font-size:15px;font-weight:700;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--border)">
                <i class="fas fa-lock" style="color:var(--accent-purple);margin-right:8px"></i>Ubah Password
            </div>
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf @method('PUT')
                <div style="display:grid;gap:14px">
                    <div>
                        <label class="form-label">Password Saat Ini *</label>
                        <input type="password" name="current_password" class="form-input" placeholder="Masukkan password lama" required>
                    </div>
                    <div>
                        <label class="form-label">Password Baru * <span style="color:var(--text-muted);font-weight:400">(min. 8 karakter)</span></label>
                        <input type="password" name="new_password" class="form-input" id="newPw" placeholder="Password baru" required oninput="checkPw(this.value)">
                        <div class="pw-bar"><div class="pw-fill" id="pwFill"></div></div>
                        <div class="pw-label" id="pwLabel">Masukkan password baru</div>
                    </div>
                    <div>
                        <label class="form-label">Konfirmasi Password *</label>
                        <input type="password" name="new_password_confirmation" class="form-input" placeholder="Ulangi password baru" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:16px"><i class="fas fa-shield-halved"></i> Ubah Password</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewPhoto(input) {
    if(input.files&&input.files[0]){
        const r=new FileReader();
        r.onload=e=>document.getElementById('avatarImg').src=e.target.result;
        r.readAsDataURL(input.files[0]);
        document.getElementById('photoForm').submit();
    }
}
function checkPw(v) {
    let s=0;
    if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
    const c=['','#ef4444','#f59e0b','#06b6d4','#10b981'],l=['','Lemah','Sedang','Kuat','Sangat Kuat'],p=[0,25,50,75,100];
    document.getElementById('pwFill').style.cssText=`width:${p[s]}%;background:${c[s]||'#ef4444'}`;
    document.getElementById('pwLabel').textContent=l[s]||'';
    document.getElementById('pwLabel').style.color=c[s]||'#94a3b8';
}
</script>
@endsection
