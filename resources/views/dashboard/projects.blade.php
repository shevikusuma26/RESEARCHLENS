@extends('layouts.dashboard')
@section('title','Proyek Saya')
@section('page-title','Proyek Saya')
@section('styles')
<style>
.proj-card{background:var(--bg-card);border:1.5px solid var(--border);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);transition:all 0.25s;display:flex;flex-direction:column;gap:10px}
.proj-card:hover{box-shadow:var(--shadow-hover);transform:translateY(-2px);border-color:var(--border-hover)}
.kw-chip{display:inline-block;padding:3px 9px;background:var(--accent-light);border:1px solid rgba(99,102,241,0.2);border-radius:20px;font-size:11px;color:var(--accent);margin:2px;font-weight:500}
.filter-bar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:center}
.search-wrap{position:relative;flex:1;min-width:200px}
.search-input{width:100%;background:#fff;border:1.5px solid var(--border);border-radius:9px;padding:9px 14px 9px 36px;font-size:13.5px;font-family:'Inter',sans-serif;outline:none;color:var(--text-primary);transition:all 0.2s}
.search-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,102,241,0.1)}
.search-input::placeholder{color:var(--text-muted)}
.search-icon{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;pointer-events:none}
.filter-select{background:#fff;border:1.5px solid var(--border);border-radius:9px;padding:9px 14px;font-size:13.5px;font-family:'Inter',sans-serif;outline:none;color:var(--text-primary);cursor:pointer;transition:border-color 0.2s}
.filter-select:focus{border-color:var(--accent)}
.upload-area{border:2px dashed var(--border);border-radius:10px;padding:22px;text-align:center;cursor:pointer;transition:all 0.2s;background:var(--bg-base)}
.upload-area:hover,.upload-area.dragging{border-color:var(--accent);background:var(--accent-light)}
.char-hint{font-size:11px;color:var(--text-muted);margin-top:4px}
.kw-preview{display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;min-height:20px}
</style>
@endsection

@section('content')
<!-- Header -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px" class="animate-in">
    <div style="font-size:13px;color:var(--text-muted)">Kelola dan analisis proyek tugas akhir Anda</div>
    <button class="btn btn-primary" onclick="openModal()"><i class="fas fa-plus"></i> Tambah Proyek</button>
</div>

<!-- Filter Bar -->
<div class="filter-bar animate-in d1">
    <div class="search-wrap">
        <i class="fas fa-search search-icon"></i>
        <input type="text" class="search-input" id="searchInput" placeholder="Cari judul proyek..." oninput="filterProjects()">
    </div>
    <select class="filter-select" id="filterCat" onchange="filterProjects()">
        <option value="">Semua Kategori</option>
        @php $cats = \App\Models\Category::orderBy('category_name')->get(); @endphp
        @foreach($cats as $cat)
        <option value="{{ $cat->category_name }}">{{ $cat->category_name }}</option>
        @endforeach
    </select>
    <select class="filter-select" id="filterStatus" onchange="filterProjects()">
        <option value="">Semua Status</option>
        <option value="draft">Draft</option>
        <option value="submitted">Submitted</option>
        <option value="analyzed">Analyzed</option>
    </select>
</div>

<!-- Projects Grid -->
<div id="projectsGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px">
    @forelse($projects as $p)
    <div class="proj-card animate-in"
         data-title="{{ strtolower($p->title) }}"
         data-cat="{{ $p->category?->category_name }}"
         data-status="{{ $p->status }}">
        <div style="display:flex;justify-content:space-between;align-items:flex-start">
            <span class="badge {{ $p->status==='analyzed'?'badge-green':($p->status==='submitted'?'badge-blue':'badge-cyan') }}">
                <i class="fas {{ $p->status==='analyzed'?'fa-check-circle':($p->status==='submitted'?'fa-clock':'fa-pencil') }}"></i>
                {{ ucfirst($p->status??'draft') }}
            </span>
            <span style="font-size:11px;color:var(--text-muted)">{{ $p->created_at->format('d M Y') }}</span>
        </div>
        <div style="font-size:14.5px;font-weight:700;color:var(--text-primary);line-height:1.4">{{ $p->title }}</div>
        @if($p->category)
        <div><span class="badge badge-purple"><i class="fas fa-tag"></i> {{ $p->category->category_name }}</span></div>
        @endif
        <div style="font-size:12.5px;color:var(--text-secondary);line-height:1.5">{{ Str::limit($p->abstract,110) }}</div>
        @if($p->keywords->count())
        <div>@foreach($p->keywords->take(4) as $kw)<span class="kw-chip">{{ $kw->keyword }}</span>@endforeach</div>
        @endif
        <div style="display:flex;gap:10px;align-items:center;padding-top:10px;border-top:1px solid var(--border)">
            <div style="flex:1;text-align:center;padding:8px;background:var(--accent-light);border-radius:8px">
                <div style="font-size:10px;color:var(--text-muted)">Novelty</div>
                <div style="font-size:14px;font-weight:700;color:{{ $p->novelty_score>=70?'var(--accent-green)':($p->novelty_score>=40?'var(--accent-amber)':'var(--accent-red)') }}">{{ $p->novelty_score }}%</div>
            </div>
            <div style="flex:1;text-align:center;padding:8px;background:var(--accent-cyan-l);border-radius:8px">
                <div style="font-size:10px;color:var(--text-muted)">Similarity</div>
                <div style="font-size:14px;font-weight:700;color:var(--accent-cyan)">{{ $p->similarity_score }}%</div>
            </div>
            <div style="display:flex;gap:6px">
                <a href="{{ route('dashboard.similarity') }}" class="btn btn-outline" style="padding:7px 10px;font-size:11px" title="Analisis">
                    <i class="fas fa-code-compare"></i>
                </a>
                <!-- Delete via hidden form -->
                <form method="POST" action="{{ route('projects.destroy', $p->id) }}"
                      onsubmit="return confirm('Hapus proyek ini? Aksi tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding:7px 10px;font-size:11px" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px 20px">
        <div style="width:72px;height:72px;background:var(--accent-light);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:var(--accent)"><i class="fas fa-folder-open"></i></div>
        <div style="font-size:16px;font-weight:700;margin-bottom:6px">Belum ada proyek</div>
        <p style="color:var(--text-muted);font-size:13px;margin-bottom:18px">Tambahkan proyek tugas akhir pertama Anda!</p>
        <button class="btn btn-primary" onclick="openModal()"><i class="fas fa-plus"></i> Tambah Proyek</button>
    </div>
    @endforelse
</div>

<div id="noResults" style="display:none;text-align:center;padding:50px;color:var(--text-muted)">
    <i class="fas fa-search" style="font-size:28px;display:block;margin-bottom:10px;opacity:0.3"></i>
    <p>Tidak ada proyek yang sesuai filter.</p>
</div>

<!-- ── ADD PROJECT MODAL ── -->
<div class="modal-overlay" id="addModal">
    <div class="modal-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;padding-bottom:16px;border-bottom:1px solid var(--border)">
            <div>
                <div style="font-size:17px;font-weight:700">Tambah Proyek Baru</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:2px">Isi detail proyek tugas akhir Anda</div>
            </div>
            <button onclick="closeModal()" style="background:var(--bg-base);border:1px solid var(--border);width:32px;height:32px;border-radius:8px;cursor:pointer;color:var(--text-secondary);font-size:14px"><i class="fas fa-xmark"></i></button>
        </div>

        {{-- Real HTML form → web route, session-authenticated --}}
        <form method="POST" action="{{ route('projects.store') }}"
              enctype="multipart/form-data" id="addProjectForm">
            @csrf
            <div style="display:grid;gap:16px">

                {{-- Kategori --}}
                <div>
                    <label class="form-label">Kategori Penelitian *</label>
                    <select name="category_id" class="form-input" required id="catSelect">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($cats as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div style="font-size:11px;color:var(--accent-red);margin-top:4px">{{ $message }}</div>@enderror
                </div>

                {{-- Judul --}}
                <div>
                    <label class="form-label">Judul Penelitian *</label>
                    <input type="text" name="title" class="form-input"
                           placeholder="Masukkan judul tugas akhir lengkap" required
                           value="{{ old('title') }}">
                    @error('title')<div style="font-size:11px;color:var(--accent-red);margin-top:4px">{{ $message }}</div>@enderror
                </div>

                {{-- Abstrak --}}
                <div>
                    <label class="form-label">
                        Abstrak *
                        <span style="color:var(--text-muted);font-weight:400">(min. 50 karakter)</span>
                    </label>
                    <textarea name="abstract" class="form-input" rows="5"
                              placeholder="Tuliskan abstrak penelitian Anda secara lengkap..."
                              required minlength="50"
                              id="abstractInput" oninput="countChars(this)">{{ old('abstract') }}</textarea>
                    <div class="char-hint"><span id="charCount">0</span> karakter — minimal 50</div>
                    @error('abstract')<div style="font-size:11px;color:var(--accent-red);margin-top:4px">{{ $message }}</div>@enderror
                </div>

                {{-- Metode --}}
                <div>
                    <label class="form-label">Metode Penelitian <span style="color:var(--text-muted);font-weight:400">(opsional)</span></label>
                    <textarea name="research_method" class="form-input" rows="3"
                              placeholder="Deskripsikan metodologi yang digunakan...">{{ old('research_method') }}</textarea>
                </div>

                {{-- Kata Kunci --}}
                <div>
                    <label class="form-label">
                        Kata Kunci
                        <span style="color:var(--text-muted);font-weight:400">(pisahkan dengan koma)</span>
                    </label>
                    <input type="text" id="kwRawInput" class="form-input"
                           placeholder="machine learning, deep learning, NLP"
                           oninput="syncKeywords(this.value)"
                           value="{{ old('keywords_display') }}">
                    <div class="kw-preview" id="kwPreview"></div>
                    {{-- Hidden inputs for keywords[] will be generated by JS --}}
                    <div id="kwHiddenContainer"></div>
                    <div class="char-hint"><span id="kwCount">0</span> kata kunci</div>
                </div>

                {{-- Upload PDF --}}
                <div>
                    <label class="form-label">
                        File Proposal PDF
                        <span style="color:var(--text-muted);font-weight:400">(opsional, maks. 10MB)</span>
                    </label>
                    <div class="upload-area" id="uploadArea"
                         onclick="document.getElementById('fileInput').click()"
                         ondragover="dragOver(event)" ondragleave="dragLeave(event)" ondrop="dropFile(event)">
                        <i class="fas fa-cloud-arrow-up" style="font-size:22px;color:var(--accent);margin-bottom:8px;display:block"></i>
                        <div style="font-size:13px;font-weight:600;color:var(--text-primary)">Klik atau drag & drop file PDF</div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Hanya file .pdf, maksimal 10MB</div>
                        <div id="fileName" style="margin-top:8px;font-size:12px;color:var(--accent);font-weight:600;display:none"></div>
                    </div>
                    <input type="file" id="fileInput" name="proposal_file" accept=".pdf"
                           style="display:none" onchange="showFileName(this)">
                    @error('proposal_file')<div style="font-size:11px;color:var(--accent-red);margin-top:4px">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:22px">
                <button type="button" onclick="closeModal()" class="btn btn-outline" style="flex:1">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:2" id="submitBtn">
                    <i class="fas fa-save"></i> Simpan Proyek
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// ── Modal ──
function openModal()  { document.getElementById('addModal').classList.add('open'); }
function closeModal() { document.getElementById('addModal').classList.remove('open'); }
document.getElementById('addModal').addEventListener('click', e => {
    if (e.target === document.getElementById('addModal')) closeModal();
});

// Auto-open modal if validation failed (old input present)
@if($errors->any())
    openModal();
@endif

// ── Char counter ──
function countChars(el) {
    document.getElementById('charCount').textContent = el.value.length;
}

// ── Keyword sync ──
function syncKeywords(raw) {
    const kws   = raw.split(',').map(k => k.trim()).filter(k => k.length > 0);
    const prev  = document.getElementById('kwPreview');
    const cont  = document.getElementById('kwHiddenContainer');
    const count = document.getElementById('kwCount');

    prev.innerHTML  = kws.map(k =>
        `<span style="display:inline-block;padding:3px 9px;background:var(--accent-light);border:1px solid rgba(99,102,241,0.2);border-radius:20px;font-size:11px;color:var(--accent);margin:2px">${k}</span>`
    ).join('');

    cont.innerHTML  = kws.map((k, i) =>
        `<input type="hidden" name="keywords[]" value="${k}">`
    ).join('');

    count.textContent = kws.length;
}

// ── File upload UI ──
function dragOver(e)  { e.preventDefault(); document.getElementById('uploadArea').classList.add('dragging'); }
function dragLeave()  { document.getElementById('uploadArea').classList.remove('dragging'); }
function dropFile(e)  {
    e.preventDefault(); dragLeave();
    const f = e.dataTransfer.files[0];
    if (f && f.type === 'application/pdf') {
        // Transfer to real input via DataTransfer API
        const dt = new DataTransfer();
        dt.items.add(f);
        document.getElementById('fileInput').files = dt.files;
        showFileNameLabel(f.name);
    } else {
        alert('Hanya file PDF yang diperbolehkan!');
    }
}
function showFileName(input) {
    if (input.files[0]) showFileNameLabel(input.files[0].name);
}
function showFileNameLabel(name) {
    const el = document.getElementById('fileName');
    el.style.display = 'block';
    el.innerHTML = '<i class="fas fa-file-pdf" style="color:#ef4444;margin-right:5px"></i>' + name;
}

// ── Submit loading state ──
document.getElementById('addProjectForm').addEventListener('submit', function(e) {
    // Validate abstrak
    const abs = document.getElementById('abstractInput').value;
    if (abs.length < 50) {
        e.preventDefault();
        alert('Abstrak minimal 50 karakter! Saat ini: ' + abs.length + ' karakter.');
        return;
    }
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
});

// ── Project card filter ──
function filterProjects() {
    const q   = document.getElementById('searchInput').value.toLowerCase();
    const cat = document.getElementById('filterCat').value;
    const st  = document.getElementById('filterStatus').value;
    const cards = document.querySelectorAll('.proj-card');
    let visible = 0;
    cards.forEach(c => {
        const match = (!q || c.dataset.title.includes(q)) &&
                      (!cat || c.dataset.cat === cat) &&
                      (!st  || c.dataset.status === st);
        c.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('noResults').style.display = visible === 0 && cards.length > 0 ? 'block' : 'none';
}
</script>
@endsection
