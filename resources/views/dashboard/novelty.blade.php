@extends('layouts.dashboard')
@section('title','Novelty & Rekomendasi')
@section('page-title','Novelty & Rekomendasi')
@section('styles')
<style>
.select-wrap select{width:100%;background:#fff;border:1.5px solid var(--border);border-radius:9px;padding:11px 14px;color:var(--text-primary);font-size:13.5px;font-family:'Inter',sans-serif;outline:none;transition:all 0.2s}
.select-wrap select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,102,241,0.1)}

.rec-card{border-radius:16px;padding:20px;margin-bottom:16px;border:1.5px solid;position:relative;overflow:hidden}
.rec-card.innovation{background:linear-gradient(135deg,#fff5f5,#fff);border-color:rgba(239,68,68,0.2)}
.rec-card.enhancement{background:linear-gradient(135deg,#fffbeb,#fff);border-color:rgba(245,158,11,0.2)}
.rec-card.strength{background:linear-gradient(135deg,#f0fdf4,#fff);border-color:rgba(16,185,129,0.2)}
.rec-card.technology{background:linear-gradient(135deg,#eef2ff,#fff);border-color:rgba(99,102,241,0.2)}

.rec-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.suggestion-item{display:flex;align-items:flex-start;gap:10px;padding:10px 14px;background:rgba(255,255,255,0.7);border-radius:10px;margin-bottom:8px;font-size:13.5px;color:var(--text-primary);border:1px solid rgba(255,255,255,0.5)}

.gauge-circle{position:relative;width:100%;max-width:180px;aspect-ratio:1/1;margin:0 auto 20px}
.gauge-circle svg{width:100%;height:100%;transform:rotate(-90deg)}
.gauge-text{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;width:80%;display:flex;flex-direction:column;align-items:center;justify-content:center}
#gaugeVal{font-size:min(32px, 8vw);font-weight:900;line-height:1.1;word-break:break-all}

.loading-spinner{width:50px;height:50px;border:3px solid rgba(99,102,241,0.15);border-top-color:var(--accent);border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 14px}

@media (max-width: 768px) {
    #resultsGrid { grid-template-columns: 1fr !important; }
    .gauge-circle { max-width: 140px; }
    #gaugeVal { font-size: 24px; }
}
</style>
@endsection

@section('content')
<div class="card animate-in" style="margin-bottom:24px">
    <div style="margin-bottom:16px">
        <div style="font-size:16px;font-weight:800;margin-bottom:4px"><i class="fas fa-lightbulb" style="color:var(--accent);margin-right:8px"></i>AI Novelty Analysis</div>
        <div style="font-size:13px;color:var(--text-muted)">Berdasarkan hasil perbandingan dengan repository internasional, sistem AI kami memberikan skor keunikan dan saran inovasi untuk penelitian Anda.</div>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
        <div class="select-wrap" style="flex:1;min-width:240px">
            <label class="form-label">Pilih Proyek</label>
            <select id="projectSelect">
                <option value="">-- Pilih proyek --</option>
                @foreach($projects as $p)
                <option value="{{ $p->id }}">{{ Str::limit($p->title,80) }}</option>
                @endforeach
            </select>
        </div>
        <button id="analyzeBtn" class="btn btn-primary" onclick="runAnalysis()" style="height:44px;padding:0 24px">
            <i class="fas fa-robot"></i> Hitung Novelty
        </button>
    </div>
</div>

<div id="loadingState" style="display:none;text-align:center;padding:80px 20px">
    <div class="loading-spinner"></div>
    <div style="font-size:16px;font-weight:800">Menjalankan Engine AI...</div>
    <div style="font-size:13px;color:var(--text-muted);margin-top:5px">Menganalisis celah penelitian dan merumuskan saran inovasi teknis</div>
</div>

<div id="resultsSection" style="display:none">
    <div style="display:grid;grid-template-columns:300px 1fr;gap:24px;align-items:start" id="resultsGrid">
        <!-- Score Column -->
        <div>
            <div class="card" style="text-align:center;padding:30px">
                <div style="font-size:14px;font-weight:800;margin-bottom:24px;text-transform:uppercase;letter-spacing:1px">Novelty Index</div>
                <div class="gauge-circle">
                    <svg width="180" height="180" viewBox="0 0 180 180">
                        <circle cx="90" cy="90" r="80" fill="none" stroke="var(--bg-base)" stroke-width="14"/>
                        <circle id="gaugeArc" cx="90" cy="90" r="80" fill="none" stroke="var(--accent)" stroke-width="14" stroke-linecap="round" stroke-dasharray="502" stroke-dashoffset="502" style="transition:stroke-dashoffset 1.5s ease-out, stroke 0.5s ease"/>
                    </svg>
                    <div class="gauge-text">
                        <div id="gaugeVal">—</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:4px">PERCENTAGE</div>
                    </div>
                </div>
                <div id="noveltyBadge" class="badge" style="padding:8px 20px;font-size:13px;font-weight:700;margin-bottom:12px">—</div>
                <div id="noveltyDesc" style="font-size:13px;color:var(--text-muted);line-height:1.6;font-style:italic">—</div>
            </div>

            <!-- Legend -->
            <div class="card" style="margin-top:16px;padding:16px">
                <div style="font-size:12px;font-weight:800;margin-bottom:12px">INTERPRETASI SKOR</div>
                <div style="display:flex;flex-direction:column;gap:8px">
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-secondary)">
                        <span style="width:8px;height:8px;border-radius:50%;background:var(--accent-green)"></span>
                        <strong>70-100:</strong> Sangat Inovatif
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-secondary)">
                        <span style="width:8px;height:8px;border-radius:50%;background:var(--accent-amber)"></span>
                        <strong>40-69:</strong> Perlu Diferensiasi
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-secondary)">
                        <span style="width:8px;height:8px;border-radius:50%;background:var(--accent-red)"></span>
                        <strong>0-39:</strong> High Duplicate Risk
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations Column -->
        <div id="recList"></div>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" style="text-align:center;padding:100px 20px">
    <div style="width:80px;height:80px;background:var(--accent-purple-l);border-radius:24px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:var(--accent-purple)"><i class="fas fa-brain"></i></div>
    <div style="font-size:18px;font-weight:800;margin-bottom:10px">Siap Menganalisis Inovasi</div>
    <p style="font-size:14px;color:var(--text-muted);max-width:350px;margin:0 auto">Pilih proyek Anda untuk melihat skor keunikan dan mendapatkan rekomendasi teknologi dari AI.</p>
</div>
@endsection

@section('scripts')
<script>
const ICONS = {innovation:'fa-rocket', enhancement:'fa-wand-magic-sparkles', strength:'fa-check-double', technology:'fa-microchip'};
const COLORS = {innovation:'var(--accent-red)', enhancement:'var(--accent-amber)', strength:'var(--accent-green)', technology:'var(--accent)'};

async function runAnalysis() {
    const pid = document.getElementById('projectSelect').value;
    if(!pid){ alert('Pilih proyek terlebih dahulu!'); return; }
    
    document.getElementById('emptyState').style.display='none';
    document.getElementById('resultsSection').style.display='none';
    document.getElementById('loadingState').style.display='block';
    document.getElementById('analyzeBtn').disabled=true;
    
    try {
        const r = await fetch('{{ route("analyze.novelty") }}',{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
            },
            body:JSON.stringify({project_id:pid})
        });
        const json = await r.json();
        if(!json.success) throw new Error(json.message);
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
    const s = data.novelty_score;
    const circ = 2 * Math.PI * 80;
    const arc = document.getElementById('gaugeArc');
    const clr = s >= 70 ? 'var(--accent-green)' : (s >= 40 ? 'var(--accent-amber)' : 'var(--accent-red)');
    
    arc.style.strokeDashoffset = circ - (s/100 * circ);
    arc.style.stroke = clr;
    
    const gVal = document.getElementById('gaugeVal');
    gVal.textContent = s + '%';
    gVal.style.color = clr;
    
    const badge = document.getElementById('noveltyBadge');
    badge.style.background = s >= 70 ? 'var(--accent-green-l)' : (s >= 40 ? 'var(--accent-amber-l)' : 'var(--accent-red-l)');
    badge.style.color = clr;
    badge.textContent = s >= 70 ? 'HIGH NOVELTY' : (s >= 40 ? 'MEDIUM NOVELTY' : 'LOW NOVELTY');
    
    document.getElementById('noveltyDesc').textContent = s >= 70 
        ? '"Topik ini memiliki potensi kontribusi ilmiah yang besar."' 
        : (s >= 40 ? '"Cukup orisinal, namun pertimbangkan saran pengembangan di samping."' : '"Sangat disarankan untuk melakukan pivoting atau penambahan fitur unik."');
    
    const list = document.getElementById('recList');
    list.innerHTML = '';
    
    (data.recommendations || []).forEach((rec, i) => {
        const suggs = Array.isArray(rec.suggestions) ? rec.suggestions : [];
        const clrRec = COLORS[rec.type] || 'var(--accent)';
        
        list.innerHTML += `
        <div class="rec-card ${rec.type} animate-in" style="animation-delay: ${i*0.1}s">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px">
                <div class="rec-icon" style="background:rgba(255,255,255,0.8);color:${clrRec};box-shadow:0 4px 12px rgba(0,0,0,0.05)">
                    <i class="fas ${ICONS[rec.type] || 'fa-lightbulb'}"></i>
                </div>
                <div>
                    <div style="font-size:15px;font-weight:800;color:var(--text-primary)">${rec.badge || rec.type}</div>
                    <div style="font-size:12.5px;color:var(--text-secondary);margin-top:2px">${rec.message || ''}</div>
                </div>
            </div>
            <div>
                ${suggs.map(s => `
                    <div class="suggestion-item">
                        <i class="fas fa-circle-check" style="color:${clrRec};margin-top:3px;font-size:14px"></i>
                        <span>${s}</span>
                    </div>
                `).join('')}
            </div>
        </div>`;
    });
    
    document.getElementById('resultsSection').style.display='block';
    window.scrollTo({ top: document.getElementById('resultsSection').offsetTop - 50, behavior: 'smooth' });
}
</script>
@endsection
