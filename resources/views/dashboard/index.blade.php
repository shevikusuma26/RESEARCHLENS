@extends('layouts.dashboard')
@section('title','Dashboard')
@section('page-title','Dashboard Overview')
@section('styles')
<style>
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:22px}
.charts-grid{display:grid;grid-template-columns:2fr 1fr;gap:18px;margin-bottom:22px}
.bottom-grid{display:grid;grid-template-columns:2fr 1fr;gap:18px}
.chart-wrap{position:relative;height:240px}
.project-row{display:flex;align-items:flex-start;gap:12px;padding:13px 0;border-bottom:1px solid var(--border)}
.project-row:last-child{border-bottom:none}
.proj-num{width:26px;height:26px;border-radius:7px;background:var(--accent-light);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;margin-top:2px}
.alert-row{display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--accent-red-l);border:1px solid rgba(239,68,68,0.15);border-radius:9px;margin-bottom:7px}
@media(max-width:1200px){.stats-grid{grid-template-columns:repeat(2,1fr)}.charts-grid,.bottom-grid{grid-template-columns:1fr}}
@media(max-width:600px){.stats-grid{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
<!-- Welcome Banner -->
<div style="background:linear-gradient(135deg,#6366f1,#8b5cf6,#06b6d4);border-radius:16px;padding:22px 28px;margin-bottom:22px;color:#fff;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px" class="animate-in d1">
    <div>
        <div style="font-size:11px;font-weight:600;opacity:0.8;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px">Selamat Datang</div>
        <div style="font-size:22px;font-weight:800;margin-bottom:4px">{{ auth()->user()->name }} 👋</div>
        <div style="font-size:13px;opacity:0.85">Platform Analisis Penelitian Akademik berbasis AI</div>
    </div>
    <div style="display:flex;gap:20px;text-align:center">
        <div><div style="font-size:26px;font-weight:800">{{ $totalProjects }}</div><div style="font-size:11px;opacity:0.8">Proyek</div></div>
        <div style="width:1px;background:rgba(255,255,255,0.3)"></div>
        <div><div style="font-size:26px;font-weight:800">{{ $avgNovelty }}%</div><div style="font-size:11px;opacity:0.8">Avg Novelty</div></div>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card animate-in d1">
        <div><div class="stat-label">Total Proyek</div><div class="stat-value" data-count="{{ $totalProjects }}">0</div><div style="font-size:11px;color:var(--accent);margin-top:5px;font-weight:500">{{ $analyzedProjects }} teranalisis</div></div>
        <div class="stat-icon si-blue"><i class="fas fa-folder-open"></i></div>
    </div>
    <div class="stat-card animate-in d2">
        <div><div class="stat-label">Avg. Novelty Score</div><div class="stat-value" style="color:var(--accent-purple)" data-count="{{ $avgNovelty }}">0</div><div style="font-size:11px;color:var(--text-muted);margin-top:5px">dari 100%</div></div>
        <div class="stat-icon si-purple"><i class="fas fa-lightbulb"></i></div>
    </div>
    <div class="stat-card animate-in d3">
        <div><div class="stat-label">Avg. Similarity</div><div class="stat-value" style="color:var(--accent-cyan)" data-count="{{ $avgSimilarity }}">0</div><div style="font-size:11px;color:var(--text-muted);margin-top:5px">rata-rata kesamaan</div></div>
        <div class="stat-icon si-cyan"><i class="fas fa-code-compare"></i></div>
    </div>
    <div class="stat-card animate-in d4">
        <div><div class="stat-label">Notifikasi Baru</div><div class="stat-value" style="color:var(--accent-pink)" data-count="{{ $unreadNotifications }}">0</div><div style="font-size:11px;color:var(--text-muted);margin-top:5px">belum dibaca</div></div>
        <div class="stat-icon si-pink"><i class="fas fa-bell"></i></div>
    </div>
</div>

<!-- Charts -->
<div class="charts-grid animate-in d2">
    <div class="card">
        <div class="section-header">
            <div><div class="section-title">Tren Penelitian</div><div class="section-sub">Aktivitas proyek 6 bulan terakhir</div></div>
            <span class="badge badge-blue"><i class="fas fa-chart-line"></i> Monthly</span>
        </div>
        <div class="chart-wrap"><canvas id="trendChart"></canvas></div>
    </div>
    <div class="card">
        <div class="section-header">
            <div><div class="section-title">Kategori Penelitian</div><div class="section-sub">Distribusi topik</div></div>
        </div>
        <div class="chart-wrap" style="height:210px"><canvas id="catChart"></canvas></div>
        @foreach($categoryStats->take(4) as $c)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:5px 0;font-size:12px;border-bottom:1px solid var(--border)">
            <span style="color:var(--text-secondary)">{{ Str::limit($c->category_name,28) }}</span>
            <span class="badge badge-blue">{{ $c->final_projects_count }}</span>
        </div>
        @endforeach
    </div>
</div>

<!-- Bottom Grid -->
<div class="bottom-grid animate-in d3">
    <!-- Recent Projects -->
    <div class="card">
        <div class="section-header">
            <div><div class="section-title">Proyek Terbaru</div><div class="section-sub">5 proyek terakhir Anda</div></div>
            <a href="{{ route('dashboard.projects') }}" class="btn btn-outline" style="padding:6px 14px;font-size:12px">Lihat Semua <i class="fas fa-arrow-right"></i></a>
        </div>
        @forelse($projects as $i => $p)
        <div class="project-row">
            <div class="proj-num">{{ $i+1 }}</div>
            <div style="flex:1">
                <div style="font-size:13.5px;font-weight:600;margin-bottom:3px">{{ Str::limit($p->title,60) }}</div>
                <div style="font-size:11px;color:var(--text-muted);display:flex;gap:10px">
                    <span><i class="fas fa-tag" style="margin-right:3px"></i>{{ $p->category?->category_name ?? 'N/A' }}</span>
                    <span><i class="fas fa-calendar" style="margin-right:3px"></i>{{ $p->created_at->format('d M Y') }}</span>
                </div>
            </div>
            <div style="text-align:right;flex-shrink:0">
                <div class="badge {{ $p->novelty_score>=70?'badge-green':($p->novelty_score>=40?'badge-amber':'badge-red') }}">{{ $p->novelty_score }}%</div>
                <div style="font-size:10px;color:var(--text-muted);margin-top:3px">novelty</div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:40px;color:var(--text-muted)">
            <i class="fas fa-folder-open" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.3"></i>
            <p style="font-size:13px;margin-bottom:14px">Belum ada proyek</p>
            <a href="{{ route('dashboard.projects') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Proyek</a>
        </div>
        @endforelse
    </div>

    <!-- Right Panel -->
    <div style="display:flex;flex-direction:column;gap:18px">
        <!-- Quick Actions -->
        <div class="card">
            <div class="section-title" style="margin-bottom:14px">Aksi Cepat</div>
            <a href="{{ route('dashboard.projects') }}" class="quick-action">
                <div class="qa-icon si-blue"><i class="fas fa-plus"></i></div>
                <div><div style="font-size:13px;font-weight:600">Tambah Proyek Baru</div><div style="font-size:11px;color:var(--text-muted)">Submit proposal tugas akhir</div></div>
            </a>
            <a href="{{ route('dashboard.similarity') }}" class="quick-action">
                <div class="qa-icon si-cyan"><i class="fas fa-code-compare"></i></div>
                <div><div style="font-size:13px;font-weight:600">Analisis Similarity</div><div style="font-size:11px;color:var(--text-muted)">Jalankan cosine similarity</div></div>
            </a>
            <a href="{{ route('dashboard.novelty') }}" class="quick-action">
                <div class="qa-icon si-purple"><i class="fas fa-lightbulb"></i></div>
                <div><div style="font-size:13px;font-weight:600">Rekomendasi Novelty</div><div style="font-size:11px;color:var(--text-muted)">Saran inovasi berbasis AI</div></div>
            </a>
        </div>

        <!-- High Similarity Alerts -->
        @if($highSimilarityAlerts->count() > 0)
        <div class="card">
            <div class="section-title" style="margin-bottom:14px">
                <i class="fas fa-triangle-exclamation" style="color:var(--accent-amber);margin-right:6px"></i>Peringatan Similarity
            </div>
            @foreach($highSimilarityAlerts as $al)
            <div class="alert-row">
                <div style="flex:1">
                    <div style="font-size:12px;font-weight:600">{{ Str::limit($al->project?->title??'Project',38) }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">vs {{ Str::limit($al->comparedProject?->title??'Other',34) }}</div>
                </div>
                <span class="badge badge-red">{{ number_format($al->similarity_percentage,1) }}%</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
const palette = ['#6366f1','#06b6d4','#8b5cf6','#10b981','#f59e0b','#ec4899'];
new Chart(document.getElementById('trendChart'),{
    type:'line',
    data:{
        labels:@json($monthlyTrend->pluck('month')),
        datasets:[{label:'Proyek',data:@json($monthlyTrend->pluck('count')),borderColor:'#6366f1',backgroundColor:'rgba(99,102,241,0.08)',borderWidth:2.5,tension:0.4,fill:true,pointBackgroundColor:'#6366f1',pointRadius:4,pointHoverRadius:7}]
    },
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{color:'rgba(0,0,0,0.04)'},ticks:{color:'#94a3b8',font:{size:11}}},y:{grid:{color:'rgba(0,0,0,0.04)'},ticks:{color:'#94a3b8',font:{size:11}},beginAtZero:true}}}
});
new Chart(document.getElementById('catChart'),{
    type:'doughnut',
    data:{labels:@json($categoryStats->pluck('category_name')),datasets:[{data:@json($categoryStats->pluck('final_projects_count')),backgroundColor:palette,borderColor:'#fff',borderWidth:3,hoverOffset:6}]},
    options:{responsive:true,maintainAspectRatio:false,cutout:'70%',plugins:{legend:{display:false}}}
});
</script>
@endsection
