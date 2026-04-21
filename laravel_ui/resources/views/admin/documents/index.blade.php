@extends('layouts.admin')

@push('styles')
<style>
/* ═══════════════════════════════════════════════
   DOCUMENTS PAGE
═══════════════════════════════════════════════ */

/* ── Hero ── */
.doc-hero {
    background: linear-gradient(135deg, var(--teal) 0%, var(--teal-mid) 55%, var(--teal-light) 100%);
    border-radius: 18px;
    padding: 1.5rem 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 28px var(--teal-shadow);
    margin-bottom: 1.6rem;
}
.doc-hero::before {
    content:''; position:absolute; top:-50px; right:-50px;
    width:220px; height:220px; border-radius:50%;
    background:rgba(255,255,255,.07);
}
.doc-hero::after {
    content:''; position:absolute; bottom:-30px; right:180px;
    width:120px; height:120px; border-radius:50%;
    background:rgba(255,255,255,.05);
}
.doc-hero-left { display:flex; align-items:center; gap:1.1rem; z-index:1; }
.doc-hero-icon {
    width:54px; height:54px; border-radius:15px;
    background:rgba(255,255,255,.15);
    border:1.5px solid rgba(255,255,255,.25);
    display:grid; place-items:center; flex-shrink:0;
}
.doc-hero-title { font-size:1.3rem; font-weight:800; color:#fff; }
.doc-hero-sub   { font-size:.82rem; color:rgba(255,255,255,.72); margin-top:3px; }
.doc-hero-stats { display:flex; gap:1.2rem; z-index:1; }
.doc-hero-stat {
    background:rgba(255,255,255,.15);
    border:1px solid rgba(255,255,255,.22);
    border-radius:12px;
    padding:.5rem 1rem;
    text-align:center;
}
.doc-hero-stat-num  { font-size:1.4rem; font-weight:800; color:#fff; }
.doc-hero-stat-label{ font-size:.7rem; color:rgba(255,255,255,.7); font-weight:600; }

/* ── Page layout ── */
.doc-layout {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 1.4rem;
    align-items: start;
}
@media(max-width:820px){ .doc-layout { grid-template-columns:1fr; } }

/* ── Card base ── */
.doc-card {
    background: #fff;
    border: 1.5px solid #e4efef;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 2px 12px var(--teal-shadow);
}
.doc-card-head {
    padding: 1.1rem 1.4rem;
    border-bottom: 1.5px solid #edf2f2;
    display: flex;
    align-items: center;
    gap: .8rem;
}
.doc-card-head-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: grid; place-items: center; flex-shrink: 0;
}
.doc-card-head-title { font-size:.95rem; font-weight:800; color:#1e2c2c; }
.doc-card-head-sub   { font-size:.75rem; color:#7a9a9a; margin-top:2px; }
.doc-card-body { padding:1.4rem; }

/* ── Upload form ── */
.doc-field { margin-bottom:1.1rem; }
.doc-field label {
    display:block; font-size:.8rem; font-weight:700;
    color:#3a5858; margin-bottom:.4rem;
}
.doc-field input[type="text"] {
    width:100%; border:1.5px solid #d0e2e2; border-radius:11px;
    padding:.65rem .9rem; font:inherit; font-size:.88rem;
    color:#1e2c2c; background:#fafefe; outline:none;
    transition:border-color 150ms, box-shadow 150ms;
    box-sizing:border-box;
}
.doc-field input[type="text"]:focus {
    border-color:var(--teal);
    box-shadow: 0 0 0 3px var(--teal-shadow);
    background:#fff;
}

/* Drop zone */
.doc-dropzone {
    border: 2px dashed #c0d8d8;
    border-radius: 14px;
    padding: 1.6rem 1rem;
    text-align: center;
    cursor: pointer;
    transition: border-color 140ms, background 140ms;
    background: #fafefe;
    position: relative;
}
.doc-dropzone:hover, .doc-dropzone.drag-over {
    border-color: var(--teal);
    background: var(--teal-soft);
}
.doc-dropzone input[type="file"] {
    position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;
}
.doc-dropzone-icon {
    width:48px; height:48px; border-radius:13px;
    background:var(--teal-soft); display:grid; place-items:center;
    margin:0 auto .7rem;
    color: var(--teal);
}
.doc-dropzone-title { font-size:.88rem; font-weight:700; color:#2a4848; }
.doc-dropzone-sub   { font-size:.74rem; color:#7a9898; margin-top:.3rem; }
.doc-dropzone-file  {
    margin-top:.7rem; display:none;
    background:#e8f7f7; border-radius:8px;
    padding:.4rem .7rem; font-size:.78rem; font-weight:600;
    color:var(--teal); align-items:center; gap:.4rem;
}
.doc-dropzone-file.show { display:inline-flex; }

/* Upload button */
.doc-upload-btn {
    width:100%; padding:.7rem; border-radius:12px;
    background:linear-gradient(135deg,var(--teal),var(--teal-mid));
    border:none; font:.88rem/1 inherit; font-weight:700; color:#fff;
    cursor:pointer; box-shadow:0 3px 10px var(--teal-shadow);
    display:flex; align-items:center; justify-content:center; gap:.5rem;
    transition:opacity 140ms, transform 120ms;
    margin-top:1.1rem;
}
.doc-upload-btn:hover   { opacity:.9; transform:translateY(-1px); }
.doc-upload-btn:active  { transform:translateY(0); }
.doc-upload-btn:disabled{ opacity:.5; cursor:not-allowed; transform:none; }

/* Progress bar */
.doc-progress-wrap { margin-top:1rem; display:none; }
.doc-progress-header {
    display:flex; justify-content:space-between;
    font-size:.75rem; font-weight:600; color:#4a7070;
    margin-bottom:.4rem;
}
.doc-progress-track {
    height:8px; border-radius:99px;
    background:#e4efef; overflow:hidden;
}
.doc-progress-bar {
    height:100%; width:0%;
    background:linear-gradient(90deg,var(--teal),var(--teal-mid),var(--teal-light));
    border-radius:99px;
    transition:width 220ms linear;
}
.doc-progress-steps {
    display:flex; gap:.5rem; flex-wrap:wrap;
    margin-top:.7rem;
}
.doc-step {
    display:inline-flex; align-items:center; gap:.3rem;
    font-size:.72rem; font-weight:600; color:#9bb0b0;
    padding:.2rem .6rem; border-radius:6px;
    background:#f4fafa; border:1px solid #e0ecec;
    transition:color 200ms, background 200ms, border-color 200ms;
}
.doc-step.active { color:var(--teal); background:var(--teal-soft); border-color:#b8e2e2; }
.doc-step.done   { color:#16a34a; background:#f0fdf4; border-color:#bbf7d0; }
.doc-step-dot    { width:6px; height:6px; border-radius:50%; background:currentColor; }

/* ── Alerts ── */
.doc-alert {
    display:flex; gap:.8rem; padding:1rem 1.1rem;
    border-radius:12px; font-size:.84rem; margin-bottom:1.2rem;
    border-width:1.5px; border-style:solid;
}
.doc-alert.success { background:#f0fdf4; border-color:#86efac; color:#15803d; }
.doc-alert.error   { background:#fff1f2; border-color:#fca5a5; color:#dc2626; }
.doc-alert-icon { flex-shrink:0; margin-top:1px; }

/* ── Documents table / list ── */
.doc-toolbar {
    padding:.9rem 1.1rem;
    border-bottom:1.5px solid #edf2f2;
    display:flex; align-items:center; gap:.8rem;
}
.doc-search {
    flex:1; border:1.5px solid #d0e2e2; border-radius:10px;
    padding:.45rem .8rem; font:inherit; font-size:.84rem;
    color:#1e2c2c; background:#fafefe; outline:none;
    transition:border-color 140ms;
}
.doc-search:focus { border-color:var(--teal); }
.doc-count {
    font-size:.75rem; font-weight:700; color:#7a9a9a;
    background:#f4fafa; border:1px solid #e0ecec;
    border-radius:8px; padding:.25rem .65rem; white-space:nowrap;
}

/* Doc rows */
.doc-list { display:flex; flex-direction:column; }
.doc-row {
    display:flex; align-items:center; gap:1rem;
    padding:.95rem 1.4rem;
    border-bottom:1px solid #f0f5f5;
    transition:background 120ms;
}
.doc-row:last-child { border-bottom:none; }
.doc-row:hover { background:#fafefe; }
.doc-row-icon {
    width:40px; height:40px; border-radius:11px;
    display:grid; place-items:center; flex-shrink:0;
}
.doc-row-icon.pdf  { background:#fff1f0; color:#ef4444; }
.doc-row-icon.txt  { background:#f0f9ff; color:#0284c7; }
.doc-row-icon.md   { background:#faf5ff; color:#7c3aed; }
.doc-row-icon.img  { background:#fff7ed; color:#ea580c; }
.doc-row-info { flex:1; min-width:0; }
.doc-row-title {
    font-size:.88rem; font-weight:700; color:#1e2c2c;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.doc-row-meta {
    font-size:.73rem; color:#7a9898; margin-top:2px;
    display:flex; gap:.6rem; flex-wrap:wrap;
}
.doc-row-badge {
    display:inline-flex; align-items:center;
    background:#e8f7f7; border-radius:5px;
    padding:.1rem .4rem; font-size:.68rem; font-weight:700;
    color:var(--teal); text-transform:uppercase; letter-spacing:.03em;
}
.doc-row-date { font-size:.74rem; color:#9bb0b0; white-space:nowrap; }

/* Empty state */
.doc-empty {
    padding:3rem 1.5rem;
    display:flex; flex-direction:column; align-items:center;
    gap:.8rem; text-align:center; color:#9bb0b0;
}
.doc-empty-icon {
    width:64px; height:64px; border-radius:18px;
    background:var(--teal-soft); display:grid; place-items:center;
}
.doc-empty-title { font-size:.95rem; font-weight:700; color:#4a6666; }
.doc-empty-sub   { font-size:.8rem; max-width:260px; line-height:1.5; }

/* ── Danger button ── */
.doc-danger-btn {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.4rem .85rem; border-radius:9px;
    background:#fff1f2; border:1.5px solid #fca5a5;
    color:#dc2626; font:.8rem/1 inherit; font-weight:700;
    cursor:pointer; transition:background 120ms, border-color 120ms;
    white-space:nowrap;
}
.doc-danger-btn:hover { background:#fee2e2; border-color:#f87171; }

/* ── Per-row delete button ── */
.doc-row-delete-btn {
    flex-shrink:0; width:30px; height:30px; border-radius:8px;
    background:transparent; border:1.5px solid transparent;
    color:#c0c8c8; cursor:pointer; display:grid; place-items:center;
    transition:background 120ms, border-color 120ms, color 120ms;
    opacity:0;
}
.doc-row:hover .doc-row-delete-btn { opacity:1; }
.doc-row-delete-btn:hover { background:#fff1f2; border-color:#fca5a5; color:#dc2626; }

/* ── Confirm modal ── */
.confirm-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(0,0,0,.45); z-index:900;
    align-items:center; justify-content:center;
}
.confirm-overlay.open { display:flex; }
.confirm-modal {
    background:#fff; border-radius:18px; padding:2rem 2rem 1.6rem;
    max-width:400px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,.2);
    text-align:center;
}
.confirm-modal-icon {
    width:56px; height:56px; border-radius:16px;
    background:#fff1f2; display:grid; place-items:center;
    margin:0 auto .9rem; color:#dc2626;
}
.confirm-modal-title { font-size:1.05rem; font-weight:800; color:#1e2c2c; margin-bottom:.5rem; }
.confirm-modal-sub   { font-size:.84rem; color:#6a8a8a; line-height:1.55; margin-bottom:1.4rem; }
.confirm-modal-actions { display:flex; gap:.7rem; justify-content:center; }
.confirm-cancel-btn {
    flex:1; padding:.65rem; border-radius:11px;
    border:1.5px solid #d0e2e2; background:#fff;
    font:.88rem/1 inherit; font-weight:600; color:#4a7070;
    cursor:pointer; transition:background 120ms;
}
.confirm-cancel-btn:hover { background:#f4fafa; }
.confirm-confirm-btn {
    flex:1; padding:.65rem; border-radius:11px;
    border:none; background:#dc2626;
    font:.88rem/1 inherit; font-weight:700; color:#fff;
    cursor:pointer; transition:opacity 120ms;
}
.confirm-confirm-btn:hover { opacity:.88; }
</style>
@endpush

@section('content')

{{-- ── Hero ── --}}
<div class="doc-hero">
    <div class="doc-hero-left">
        <div class="doc-hero-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
        </div>
        <div>
            <div class="doc-hero-title">{{ __('Document base') }}</div>
            <div class="doc-hero-sub">{{ __('Manage files indexed by the RAG pipeline') }}</div>
        </div>
    </div>
    <div class="doc-hero-stats">
        <div class="doc-hero-stat">
            <div class="doc-hero-stat-num">{{ $documents->count() }}</div>
            <div class="doc-hero-stat-label">Documents</div>
        </div>
        
    </div>
</div>

{{-- ── Alerts ── --}}
@if(session('status'))
<div class="doc-alert success">
    <div class="doc-alert-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div>{{ session('status') }}</div>
</div>
@endif

@if($errors->any())
<div class="doc-alert error">
    <div class="doc-alert-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div>{{ $errors->first() }}</div>
</div>
@endif

{{-- ── Two-column layout ── --}}
<div class="doc-layout">

    {{-- LEFT: Upload card --}}
    <div class="doc-card">
        <div class="doc-card-head">
            <div class="doc-card-head-icon" style="background:#e6f4f4;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.29"/></svg>
            </div>
            <div>
                <div class="doc-card-head-title">{{ __('Import a document') }}</div>
                <div class="doc-card-head-sub">{{ __('Automatic indexing after upload') }}</div>
            </div>
        </div>
        <div class="doc-card-body">
            <form id="upload-form" method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="doc-field">
                    <label for="title">{{ __('Document title') }}</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}"
                           placeholder="Ex: Rapport annuel 2025" required>
                </div>

                <div class="doc-field">
                    <label>{{ __('File') }} <span style="color:#9bb0b0;font-weight:500;">(PDF, TXT, MD, JPG, PNG, GIF, WEBP · max 50 Mo)</span></label>
                    <div class="doc-dropzone" id="drop-zone">
                        <input type="file" id="file" name="file" accept="application/pdf,text/plain,.md,image/jpeg,image/png,image/gif,image/webp" required
                               onchange="onFileSelect(this)">
                        <div class="doc-dropzone-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="doc-dropzone-title">{{ __('Drag your file here') }}</div>
                        <div class="doc-dropzone-sub">{{ __('or click to browse') }}</div>
                        <div class="doc-dropzone-file" id="file-name-badge">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span id="file-name-text"></span>
                        </div>
                    </div>
                </div>

                <button class="doc-upload-btn" type="submit" id="upload-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.29"/></svg>
                    {{ __('Import and index') }}
                </button>

                {{-- Progress --}}
                <div class="doc-progress-wrap" id="upload-progress-wrap">
                    <div class="doc-progress-header">
                        <span id="progress-status">{{ __('Uploading…') }}</span>
                        <span id="upload-progress-label">0%</span>
                    </div>
                    <div class="doc-progress-track">
                        <div class="doc-progress-bar" id="upload-progress-bar"></div>
                    </div>
                    <div class="doc-progress-steps">
                        <div class="doc-step active" id="step-upload">
                            <div class="doc-step-dot"></div> {{ __('Upload') }}
                        </div>
                        <div class="doc-step" id="step-index">
                            <div class="doc-step-dot"></div> {{ __('Indexing') }}
                        </div>
                        <div class="doc-step" id="step-done">
                            <div class="doc-step-dot"></div> {{ __('Done') }}
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- RIGHT: Documents list --}}
    <div class="doc-card">
        <div class="doc-card-head">
            <div class="doc-card-head-icon" style="background:#e6f4f4;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            </div>
            <div>
                <div class="doc-card-head-title">{{ __('Indexed documents') }}</div>
                <div class="doc-card-head-sub">{{ $documents->count() }} {{ __('file(s) in the base') }}</div>
            </div>
        </div>

        <div class="doc-toolbar">
            <input type="text" class="doc-search" id="doc-search"
                   placeholder="{{ __('Search a document…') }}" oninput="filterDocs(this.value)">
            <div class="doc-count" id="doc-count">{{ $documents->count() }} {{ __('result(s)') }}</div>
            @if(auth()->user()->isAdmin() && $documents->count() > 0)
            <button class="doc-danger-btn" onclick="openConfirm()" type="button">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                {{ __('Clear all') }}
            </button>
            @endif
        </div>

        <div class="doc-list" id="doc-list">
            @forelse($documents as $document)
                @php
                    $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                    $imageExts = ['jpg','jpeg','png','gif','webp'];
                    $iconClass = in_array($ext, ['pdf','txt','md']) ? $ext : (in_array($ext, $imageExts) ? 'img' : 'pdf');
                @endphp
                <div class="doc-row" data-title="{{ strtolower($document->title) }}">
                    <div class="doc-row-icon {{ $iconClass }}">
                        @if($ext === 'pdf')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        @elseif($ext === 'md')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                        @elseif(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        @else
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        @endif
                    </div>
                    <div class="doc-row-info">
                        <div class="doc-row-title">{{ $document->title }}</div>
                        <div class="doc-row-meta">
                            <span class="doc-row-badge">{{ strtoupper($ext) }}</span>
                            <span>{{ basename($document->file_path) }}</span>
                            @if($document->description)
                                <span style="background:#e6f4f4;color:#0c7070;border-radius:5px;padding:1px 6px;font-size:.68rem;font-weight:600;">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:2px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    VLM
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="doc-row-date">
                        {{ $document->created_at?->format('d/m/Y') }}<br>
                        <span style="color:#b8c8c8;">{{ $document->created_at?->format('H:i') }}</span>
                    </div>
                    @if(auth()->user()->isAdmin())
                    <button class="doc-row-delete-btn"
                            onclick="openDocConfirm({{ $document->id }}, '{{ addslashes($document->title) }}')"
                            type="button"
                            title="{{ __('Delete') }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                    @endif
                </div>
            @empty
                <div class="doc-empty">
                    <div class="doc-empty-icon">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--teal)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div class="doc-empty-title">{{ __('No document imported') }}</div>
                    <div class="doc-empty-sub">{{ __('Use the form on the left to add your first file.') }}</div>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── Confirm clear-all modal ── --}}
@if(auth()->user()->isAdmin())
<div class="confirm-overlay" id="confirm-overlay">
    <div class="confirm-modal">
        <div class="confirm-modal-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="confirm-modal-title">{{ __('Delete all documents?') }}</div>
        <div class="confirm-modal-sub">{{ __('This will permanently delete all indexed documents and the vector index for your company. This action cannot be undone.') }}</div>
        <div class="confirm-modal-actions">
            <button class="confirm-cancel-btn" onclick="closeConfirm()" type="button">{{ __('Cancel') }}</button>
            <form method="POST" action="{{ route('admin.documents.destroyAll') }}" style="flex:1">
                @csrf
                @method('DELETE')
                <button class="confirm-confirm-btn" type="submit" style="width:100%">{{ __('Delete all') }}</button>
            </form>
        </div>
    </div>
</div>

{{-- Per-document confirm modal --}}
<div class="confirm-overlay" id="doc-confirm-overlay">
    <div class="confirm-modal">
        <div class="confirm-modal-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </div>
        <div class="confirm-modal-title">{{ __('Delete this document?') }}</div>
        <div class="confirm-modal-sub">
            « <span id="doc-confirm-name"></span> »<br>
            {{ __('The file and its vectors will be permanently removed and the index rebuilt.') }}
        </div>
        <div class="confirm-modal-actions">
            <button class="confirm-cancel-btn" onclick="closeDocConfirm()" type="button">{{ __('Cancel') }}</button>
            <form id="doc-confirm-form" method="POST" action="" style="flex:1">
                @csrf
                @method('DELETE')
                <button class="confirm-confirm-btn" type="submit" style="width:100%">{{ __('Delete') }}</button>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
/* ── File select ── */
function onFileSelect(input) {
    const badge = document.getElementById('file-name-badge');
    const nameEl = document.getElementById('file-name-text');
    if (input.files && input.files[0]) {
        nameEl.textContent = input.files[0].name;
        badge.classList.add('show');
    } else {
        badge.classList.remove('show');
    }
}

/* ── Drag over style ── */
const dropZone = document.getElementById('drop-zone');
if (dropZone) {
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', () => dropZone.classList.remove('drag-over'));
}

/* ── Upload progress ── */
(() => {
    const form   = document.getElementById('upload-form');
    const button = document.getElementById('upload-button');
    const wrap   = document.getElementById('upload-progress-wrap');
    const bar    = document.getElementById('upload-progress-bar');
    const label  = document.getElementById('upload-progress-label');
    const status = document.getElementById('progress-status');
    const stepUpload = document.getElementById('step-upload');
    const stepIndex  = document.getElementById('step-index');
    const stepDone   = document.getElementById('step-done');
    if (!form) return;

    let timerId = null;

    form.addEventListener('submit', () => {
        button.disabled = true;
        button.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg> Envoi en cours…';
        wrap.style.display = 'block';

        let progress = 0;
        timerId = setInterval(() => {
            progress = Math.min(progress + Math.max(0.5, (90 - progress) / 10), 90);
            const r = Math.round(progress);
            bar.style.width = r + '%';
            label.textContent = r + '%';

            if (r >= 30 && r < 70) {
                stepUpload.classList.remove('active'); stepUpload.classList.add('done');
                stepIndex.classList.add('active');
                status.textContent = '{{ __('Indexing in progress…') }}';
            } else if (r >= 70) {
                stepIndex.classList.remove('active'); stepIndex.classList.add('done');
                stepDone.classList.add('active');
                status.textContent = '{{ __('Finalizing…') }}';
            }
        }, 180);
    });

    window.addEventListener('beforeunload', () => { if (timerId) clearInterval(timerId); });
})();

/* ── Spin keyframe ── */
const ks = document.createElement('style');
ks.textContent = '@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}';
document.head.appendChild(ks);

/* ── Search filter ── */
function filterDocs(q) {
    const rows  = document.querySelectorAll('.doc-row');
    const countEl = document.getElementById('doc-count');
    let visible = 0;
    rows.forEach(row => {
        const match = !q || row.dataset.title.includes(q.toLowerCase());
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    if (countEl) countEl.textContent = visible + ' {{ __('result(s)') }}';
}

/* ── Confirm clear-all modal ── */
function openConfirm() {
    document.getElementById('confirm-overlay')?.classList.add('open');
}
function closeConfirm() {
    document.getElementById('confirm-overlay')?.classList.remove('open');
}
document.getElementById('confirm-overlay')?.addEventListener('click', function(e) {
    if (e.target === this) closeConfirm();
});

/* ── Per-document confirm modal ── */
function openDocConfirm(id, title) {
    const baseUrl = '{{ url('admin/documents') }}';
    document.getElementById('doc-confirm-name').textContent = title;
    document.getElementById('doc-confirm-form').action = baseUrl + '/' + id;
    document.getElementById('doc-confirm-overlay')?.classList.add('open');
}
function closeDocConfirm() {
    document.getElementById('doc-confirm-overlay')?.classList.remove('open');
}
document.getElementById('doc-confirm-overlay')?.addEventListener('click', function(e) {
    if (e.target === this) closeDocConfirm();
});
</script>
@endpush

@endsection
