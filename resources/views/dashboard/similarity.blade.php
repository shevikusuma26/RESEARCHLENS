@extends('layouts.dashboard')
@section('title','Analisis Similarity')
@section('page-title','Analisis Similarity')
@section('styles')
<style>
.select-wrap select{width:100%;background:#fff;border:1.5px solid var(--border);border-radius:9px;padding:11px 14px;color:var(--text-primary);font-size:13.5px;font-family:'Inter',sans-serif;outline:none;transition:all 0.2s}
.select-wrap select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,102,241,0.1)}

/* Academic Card Styling */
.academic-card{background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;margin-bottom:20px;transition:all 0.3s;position:relative;overflow:hidden}
.academic-card:hover{border-color:var(--accent);box-shadow:var(--shadow-hover);transform:translateY(-2px)}
.academic-card::before{content:"";position:absolute;top:0;left:0;width:4px;height:100%;background:var(--accent);opacity:0}
.academic-card:hover::before{opacity:1}

.source-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;background:var(--accent-light);color:var(--accent);border-radius:8px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px}
.paper-title{font-size:18px;font-weight:800;color:var(--text-primary);line-height:1.4;margin-bottom:10px;display:block;text-decoration:none}
.paper-title:hover{color:var(--accent)}
.authors-list{font-size:13px;color:var(--text-secondary);margin-bottom:12px;display:flex;align-items:center;gap:8px}

.similarity-stats{display:flex;gap:20px;margin:16px 0;padding:16px;background:var(--bg-base);border-radius:12px;border:1px solid var(--border)}
.stat-item{flex:1}
.stat-label{font-size:10px;color:var(--text-muted);text-transform:uppercase;font-weight:700;margin-bottom:4px}
.stat-value{font-size:16px;font-weight:800;color:var(--text-primary)}

.abstract-preview{font-size:13.5px;color:var(--text-secondary);line-height:1.6;margin-bottom:16px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;transition:all 0.3s}
.abstract-preview.expanded{display:block;-webkit-line-clamp:unset}

.kw-chip-common{display:inline-block;padding:4px 10px;background:var(--accent-cyan-l);border:1px solid rgba(6,182,212,0.15);border-radius:20px;font-size:11px;color:var(--accent-cyan);margin:2px;font-weight:600}

.loading-spinner{width:52px;height:52px;border:3px solid rgba(99,102,241,0.15);border-top-color:var(--accent);border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 16px}

.btn-view-paper{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;background:var(--bg-base);border:1.5px solid var(--border);border-radius:10px;color:var(--text-primary);font-size:12px;font-weight:600;text-decoration:none;transition:all 0.2s}
.btn-view-paper:hover{background:var(--accent);color:#fff;border-color:var(--accent)}

.toggle-abstract{background:none;border:none;color:var(--accent);font-size:12px;font-weight:600;cursor:pointer;padding:0;margin-bottom:16px}
</style>
@endsection

@section('content')
<!-- Select Panel -->
<div class="card animate-in" style="margin-bottom:20px">
    <div style="margin-bottom:14px">
        <div style="font-size:16px;font-weight:800;margin-bottom:4px"><i class="fas fa-microscope" style="color:var(--accent);margin-right:8px"></i>Analisis Repository Internasional</div>
        <div style="font-size:13px;color:var(--text-muted)">Bandingkan proyek Anda dengan ribuan penelitian nyata dari IEEE, Elsevier, Springer, dan repository akademik terkemuka lainnya.</div>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
        <div class="select-wrap" style="flex:1;min-width:240px">
            <label class="form-label">Pilih Proyek Anda</label>
            <select id="projectSelect">
                <option value="">-- Pilih proyek --</option>
                @foreach($projects as $p)
                <option value="{{ $p->id }}">{{ Str::limit($p->title,80) }}</option>
                @endforeach
            </select>
        </div>
        <button id="analyzeBtn" class="btn btn-primary" onclick="runAnalysis()" style="height:44px;padding:0 24px">
            <i class="fas fa-magnifying-glass"></i> Cari & Analisis
        </button>
    </div>
</div>

<!-- Loading -->
<div id="loadingState" style="display:none;text-align:center;padding:80px 20px">
    <div class="loading-spinner"></div>
    <div style="font-size:16px;font-weight:800;color:var(--text-primary)">Menghubungkan ke Repository Akademik...</div>
    <div style="font-size:13px;color:var(--text-muted);margin-top:5px">Mengambil metadata penelitian nyata dan menghitung Cosine Similarity</div>
</div>

<!-- Results -->
<div id="resultsSection" style="display:none">
    <!-- Overview -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;margin-bottom:24px">
        <div class="card" style="text-align:center;border-top:4px solid var(--accent-red)">
            <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;font-weight:700;text-transform:uppercase">Max Similarity</div>
            <div id="maxSim" style="font-size:32px;font-weight:800;color:var(--accent-red)">—</div>
        </div>
        <div class="card" style="text-align:center;border-top:4px solid var(--accent)">
            <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;font-weight:700;text-transform:uppercase">Novelty Score</div>
            <div id="noveltyScore" style="font-size:32px;font-weight:800;color:var(--accent)">—</div>
        </div>
        <div class="card" style="text-align:center;border-top:4px solid var(--accent-green)">
            <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;font-weight:700;text-transform:uppercase">Sumber Relevan</div>
            <div id="totalComp" style="font-size:32px;font-weight:800;color:var(--accent-green)">—</div>
        </div>
    </div>

    <!-- Ranked List -->
    <div id="rankedList"></div>
</div>

<!-- Empty State -->
<div id="emptyState" style="text-align:center;padding:100px 20px">
    <div style="width:80px;height:80px;background:var(--accent-light);border-radius:24px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:var(--accent)"><i class="fas fa-search-plus"></i></div>
    <div style="font-size:18px;font-weight:800;margin-bottom:10px">Belum Ada Analisis</div>
    <p style="font-size:14px;color:var(--text-muted);max-width:400px;margin:0 auto 24px">Pilih proyek tugas akhir Anda di atas untuk mulai membandingkan dengan publikasi akademik nyata dari repository global.</p>
</div>
@endsection

@section('scripts')
<script>
async function runAnalysis() {
    const pid = document.getElementById('projectSelect').value;
    if(!pid){ alert('Pilih proyek terlebih dahulu!'); return; }
    
    document.getElementById('emptyState').style.display='none';
    document.getElementById('resultsSection').style.display='none';
    document.getElementById('loadingState').style.display='block';
    document.getElementById('analyzeBtn').disabled=true;
    
    try {
        const r = await fetch('{{ route("analyze.similarity") }}',{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
            },
            body:JSON.stringify({project_id:pid})
        });
        const json = await r.json();
        if(!json.success) throw new Error(json.message||'Analisis gagal');
        renderResults(json.data);
    } catch(e){ 
        alert('Error: '+e.message); 
        document.getElementById('emptyState').style.display='block'; 
    } finally { 
        document.getElementById('loadingState').style.display='none'; 
        document.getElementById('analyzeBtn').disabled=false; 
    }
}

function renderResults(data) {
    document.getElementById('maxSim').textContent = data.max_similarity+'%';
    document.getElementById('noveltyScore').textContent = data.novelty_score+'%';
    document.getElementById('totalComp').textContent = data.total_comparisons;
    
    const list = document.getElementById('rankedList');
    list.innerHTML = '';
    
    if(!data.ranked_results?.length){ 
        list.innerHTML='<div style="text-align:center;padding:50px;color:var(--text-muted)">Tidak ditemukan penelitian yang relevan di repository.</div>'; 
    } else {
        data.ranked_results.forEach((item, i)=>{
            const s = item.similarity;
            const p = s.overall_similarity;
            const clr = p >= 70 ? 'var(--accent-red)' : (p >= 40 ? 'var(--accent-amber)' : 'var(--accent-green)');
            
            const authorsStr = Array.isArray(item.authors) ? item.authors.slice(0, 3).join(', ') + (item.authors.length > 3 ? ' et al.' : '') : (item.authors || 'Unknown Authors');
            
            list.innerHTML += `
            <div class="academic-card animate-in" style="animation-delay: ${i*0.1}s">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
                    <span class="source-badge"><i class="fas fa-scroll"></i> ${item.source_name || 'Academic Repository'}</span>
                    <div style="text-align:right">
                        <div style="font-size:24px;font-weight:900;color:${clr}">${p}%</div>
                        <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;font-weight:700">Similarity</div>
                    </div>
                </div>
                
                <a href="${item.source_url}" target="_blank" class="paper-title">${item.title}</a>
                
                <div class="authors-list">
                    <i class="fas fa-user-friends"></i>
                    <span>${authorsStr} • <strong>${item.year || 'N/A'}</strong></span>
                </div>
                
                <div class="abstract-preview" id="abs-${i}">${item.abstract_preview || 'No abstract available.'}</div>
                <button class="toggle-abstract" onclick="toggleAbs(${i})">BACA ABSTRAK LENGKAP <i class="fas fa-chevron-down"></i></button>
                
                <div class="similarity-stats">
                    <div class="stat-item">
                        <div class="stat-label">Judul</div>
                        <div class="stat-value">${s.title_similarity}%</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Abstrak</div>
                        <div class="stat-value">${s.abstract_similarity}%</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Kata Kunci</div>
                        <div class="stat-value">${s.keyword_similarity}%</div>
                    </div>
                </div>
                
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:20px">
                    <div style="display:flex;flex-wrap:wrap;gap:4px">
                        ${(item.common_keywords || []).slice(0,5).map(k=>`<span class="kw-chip-common">${k}</span>`).join('')}
                    </div>
                    <a href="${item.source_url}" target="_blank" class="btn-view-paper">
                        LIHAT SUMBER ASLI <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>`;
        });
    }
    document.getElementById('resultsSection').style.display='block';
    window.scrollTo({ top: document.getElementById('resultsSection').offsetTop - 100, behavior: 'smooth' });
}

function toggleAbs(id) {
    const el = document.getElementById('abs-'+id);
    const btn = el.nextElementSibling;
    el.classList.toggle('expanded');
    btn.innerHTML = el.classList.contains('expanded') ? 'TUTUP ABSTRAK <i class="fas fa-chevron-up"></i>' : 'BACA ABSTRAK LENGKAP <i class="fas fa-chevron-down"></i>';
}
</script>
@endsection
