@extends('layouts.dashboard')
@section('title','Admin Panel')
@section('page-title','Admin Panel')
@section('styles')
<style>
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:22px}
.charts-grid{display:grid;grid-template-columns:2fr 1fr;gap:18px;margin-bottom:22px}
.bottom-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.chart-wrap{position:relative;height:230px}
.user-row{display:flex;align-items:center;gap:10px;padding:11px 0;border-bottom:1px solid var(--border)}
.user-row:last-child{border-bottom:none}
.alert-row{display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--accent-red-l);border:1px solid rgba(239,68,68,0.15);border-radius:9px;margin-bottom:7px}
@media(max-width:1200px){.stats-grid{grid-template-columns:repeat(2,1fr)}.charts-grid,.bottom-grid{grid-template-columns:1fr}}
@media(max-width:600px){.stats-grid{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
<!-- Admin Badge -->
<div style="background:linear-gradient(135deg,#f0f4ff,#e0f2fe);border:1px solid rgba(99,102,241,0.2);border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;color:var(--accent)" class="animate-in d1">
    <i class="fas fa-shield-halved" style="font-size:16px"></i>
    <span><strong>Mode Admin</strong> — Anda memiliki akses penuh ke seluruh statistik dan manajemen platform.</span>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card animate-in d1">
        <div><div class="stat-label">Total Mahasiswa</div><div class="stat-value" data-count="{{ $totalUsers }}">0</div><div style="font-size:11px;color:var(--text-muted);margin-top:5px">pengguna terdaftar</div></div>
        <div class="stat-icon si-blue"><i class="fas fa-users"></i></div>
    </div>
    <div class="stat-card animate-in d2">
        <div><div class="stat-label">Total Proyek</div><div class="stat-value" data-count="{{ $totalProjects }}">0</div><div style="font-size:11px;color:var(--text-muted);margin-top:5px">di semua pengguna</div></div>
        <div class="stat-icon si-cyan"><i class="fas fa-folder-open"></i></div>
    </div>
    <div class="stat-card animate-in d3">
        <div><div class="stat-label">Analisis Similarity</div><div class="stat-value" data-count="{{ $totalAnalysis }}">0</div><div style="font-size:11px;color:var(--text-muted);margin-top:5px">perbandingan selesai</div></div>
        <div class="stat-icon si-purple"><i class="fas fa-code-compare"></i></div>
    </div>
    <div class="stat-card animate-in d4">
        <div><div class="stat-label">Avg. Novelty Score</div><div class="stat-value" style="color:var(--accent-green)" data-count="{{ $avgNovelty }}">0</div><div style="font-size:11px;color:var(--text-muted);margin-top:5px">rata-rata platform</div></div>
        <div class="stat-icon si-green"><i class="fas fa-lightbulb"></i></div>
    </div>
</div>

<!-- Charts -->
<div class="charts-grid animate-in d2">
    <div class="card">
        <div class="section-header">
            <div><div class="section-title">Tren Aktivitas Penelitian</div><div class="section-sub">Submisi proyek per bulan</div></div>
            <span class="badge badge-purple"><i class="fas fa-chart-bar"></i> 6 Bulan</span>
        </div>
        <div class="chart-wrap"><canvas id="trendChart"></canvas></div>
    </div>
    <div class="card">
        <div class="section-header">
            <div><div class="section-title">Distribusi Kategori</div><div class="section-sub">{{ $totalCategories }} kategori aktif</div></div>
        </div>
        <div class="chart-wrap" style="height:200px"><canvas id="catChart"></canvas></div>
    </div>
</div>

<!-- Bottom Grid -->
<div class="bottom-grid animate-in d3">
    <div class="card">
        <div class="section-header">
            <div><div class="section-title"><i class="fas fa-triangle-exclamation" style="color:var(--accent-amber);margin-right:6px"></i>Peringatan Similarity Tinggi</div><div class="section-sub">Pasangan dengan similarity ≥70%</div></div>
            <span class="badge badge-red">{{ $highSimilarityAlerts->count() }}</span>
        </div>
        @forelse($highSimilarityAlerts as $al)
        <div class="alert-row">
            <div style="flex:1"><div style="font-size:12px;font-weight:600">{{ Str::limit($al->project?->title??'—',38) }}</div><div style="font-size:11px;color:var(--text-muted)">vs. {{ Str::limit($al->comparedProject?->title??'—',34) }}</div></div>
            <span class="badge badge-red">{{ number_format($al->similarity_percentage,1) }}%</span>
        </div>
        @empty
        <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:13px">
            <i class="fas fa-check-circle" style="font-size:28px;color:var(--accent-green);display:block;margin-bottom:8px"></i>
            Tidak ada peringatan similarity tinggi.
        </div>
        @endforelse
    </div>
    <div class="card">
        <div class="section-header">
            <div><div class="section-title"><i class="fas fa-user-plus" style="color:var(--accent-cyan);margin-right:6px"></i>Pengguna Terbaru</div><div class="section-sub">Mahasiswa baru terdaftar</div></div>
        </div>
        @foreach($recentUsers as $u)
        <div class="user-row">
            <img src="{{ $u->profile_photo_url }}" alt="" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid var(--border)">
            <div style="flex:1"><div style="font-size:13px;font-weight:600">{{ $u->name }}</div><div style="font-size:11px;color:var(--text-muted)">{{ $u->email }}</div></div>
            <span class="badge badge-blue">{{ $u->final_projects_count }} proyek</span>
        </div>
        @endforeach
    </div>
</div>

<!-- Projects Table -->
<div class="card animate-in d4" style="margin-top:20px">
    <div class="section-header">
        <div><div class="section-title">Proyek Terbaru — Semua Pengguna</div><div class="section-sub">Submisi terbaru di platform</div></div>
    </div>
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>Judul</th><th>Mahasiswa</th><th>Kategori</th><th>Novelty</th><th>Similarity</th><th>Tanggal</th></tr></thead>
            <tbody>
                @foreach($recentProjects as $p)
                <tr>
                    <td style="max-width:240px"><div style="font-weight:500">{{ Str::limit($p->title,52) }}</div></td>
                    <td><div style="display:flex;align-items:center;gap:7px"><img src="{{ $p->user?->profile_photo_url }}" alt="" style="width:24px;height:24px;border-radius:50%;object-fit:cover"><span style="font-size:13px">{{ $p->user?->name??'—' }}</span></div></td>
                    <td><span class="badge badge-purple">{{ $p->category?->category_name??'—' }}</span></td>
                    <td><span class="badge {{ $p->novelty_score>=70?'badge-green':($p->novelty_score>=40?'badge-amber':'badge-red') }}">{{ $p->novelty_score }}%</span></td>
                    <td><span class="badge badge-cyan">{{ $p->similarity_score }}%</span></td>
                    <td style="color:var(--text-muted);font-size:12px">{{ $p->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
const pal = ['#6366f1','#06b6d4','#8b5cf6','#10b981','#f59e0b','#ec4899','#3b82f6','#14b8a6'];
new Chart(document.getElementById('trendChart'),{
    type:'bar',
    data:{labels:@json($monthlyTrend->pluck('month')),datasets:[{label:'Proyek',data:@json($monthlyTrend->pluck('count')),backgroundColor:'rgba(99,102,241,0.15)',borderColor:'#6366f1',borderWidth:2,borderRadius:7}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{color:'#94a3b8',font:{size:11}}},y:{grid:{color:'rgba(0,0,0,0.04)'},ticks:{color:'#94a3b8',font:{size:11}},beginAtZero:true}}}
});
new Chart(document.getElementById('catChart'),{
    type:'doughnut',
    data:{labels:@json($topCategories->pluck('category_name')),datasets:[{data:@json($topCategories->pluck('final_projects_count')),backgroundColor:pal,borderColor:'#fff',borderWidth:3,hoverOffset:6}]},
    options:{responsive:true,maintainAspectRatio:false,cutout:'68%',plugins:{legend:{position:'bottom',labels:{color:'#64748b',font:{size:10},padding:8,boxWidth:10}}}}
});
</script>
@endsection
