@extends('layouts.admin')

@push('styles')
<style>
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
.doc-hero-left { display:flex; align-items:center; gap:1.1rem; z-index:1; }
.doc-hero-icon {
    width:54px; height:54px; border-radius:15px;
    background:rgba(255,255,255,.15);
    border:1.5px solid rgba(255,255,255,.25);
    display:grid; place-items:center; flex-shrink:0;
}
.doc-hero-title { font-size:1.3rem; font-weight:800; color:#fff; }
.doc-hero-sub   { font-size:.82rem; color:rgba(255,255,255,.72); margin-top:3px; }
.doc-count-pill {
    background:rgba(255,255,255,.18);
    border:1px solid rgba(255,255,255,.28);
    border-radius:12px;
    padding:.5rem 1.1rem;
    text-align:center;
    z-index:1;
}
.doc-count-num   { font-size:1.6rem; font-weight:800; color:#fff; }
.doc-count-label { font-size:.72rem; color:rgba(255,255,255,.7); font-weight:600; }

/* ── Grid ── */
.doc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.2rem;
}

/* ── Document card ── */
.doc-card {
    background: #fff;
    border: 1.5px solid var(--line);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px var(--teal-shadow);
    transition: box-shadow 180ms, transform 180ms;
    display: flex;
    flex-direction: column;
}
.doc-card:hover {
    box-shadow: 0 8px 28px var(--teal-shadow);
    transform: translateY(-2px);
}
.doc-card-stripe {
    height: 4px;
    background: linear-gradient(90deg, var(--teal), var(--teal-mid));
}
.doc-card-body { padding: 1.1rem 1.3rem; flex:1; }
.doc-card-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: var(--teal-soft);
    display: grid; place-items: center;
    margin-bottom: 0.9rem;
}
.doc-card-icon svg { color: var(--teal); }
.doc-card-title {
    font-size: 0.93rem;
    font-weight: 700;
    color: #1e2c2c;
    margin-bottom: 0.35rem;
    word-break: break-word;
}
.doc-card-meta {
    font-size: 0.75rem;
    color: #9bb0b0;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 0.8rem;
}
.doc-card-footer {
    padding: 0.8rem 1.3rem;
    border-top: 1px solid var(--line);
    display: flex;
    justify-content: flex-end;
}
.doc-view-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.9rem;
    border-radius: 8px;
    border: 1.5px solid var(--line);
    background: #fff;
    font: inherit;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--text);
    cursor: pointer;
    transition: 140ms;
}
.doc-view-btn:hover {
    border-color: var(--teal);
    color: var(--teal);
}

/* ── Search ── */
.search-bar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #fff;
    border: 1.5px solid var(--line);
    border-radius: 12px;
    padding: 0.55rem 1rem;
    margin-bottom: 1.2rem;
    box-shadow: 0 1px 6px var(--teal-shadow);
}
.search-bar input {
    border: none;
    background: transparent;
    outline: none;
    flex: 1;
    font-size: 0.88rem;
    color: #1e2c2c;
}
.search-bar svg { flex-shrink: 0; }

/* ── Empty ── */
.empty-state {
    text-align:center;
    padding:3rem 1rem;
    color:#9bb0b0;
    font-size:0.9rem;
}
.empty-state svg { margin-bottom:1rem; opacity:.45; }

/* ── Viewer modal ── */
.viewer-overlay {
    display: none;
    position: fixed; inset:0;
    background: rgba(10,30,30,0.55);
    z-index: 999;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}
.viewer-overlay.open { display: flex; }
.viewer-modal {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    width: 100%;
    max-width: 900px;
    height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.viewer-header {
    padding: 1rem 1.4rem;
    border-bottom: 1.5px solid #edf2f2;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}
.viewer-title { font-size:1rem; font-weight:700; color:#1e2c2c; flex:1; }
.viewer-close {
    width:32px; height:32px; border-radius:8px;
    border: 1px solid #dde8e8;
    background:#fff;
    display:grid; place-items:center;
    cursor:pointer;
    transition:140ms ease;
}
.viewer-close:hover { background:#fef2f2; border-color:#fecaca; }
.viewer-body {
    flex:1;
    overflow: hidden;
    padding: 0;
    font-size: 0.88rem;
    line-height: 1.8;
    color: #1e2c2c;
    background: #f9fbfb;
    display: flex;
    flex-direction: column;
}
.viewer-body.text-mode {
    overflow-y: auto;
    padding: 1.5rem;
    white-space: pre-wrap;
    word-break: break-word;
}
.viewer-footer {
    padding: 0.9rem 1.4rem;
    border-top: 1.5px solid #edf2f2;
    text-align: right;
}
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="doc-hero">
    <div class="doc-hero-left">
        <div class="doc-hero-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        </div>
        <div>
            <div class="doc-hero-title">{{ __('Document library') }}</div>
            <div class="doc-hero-sub">{{ __('Browse and consult all indexed documents') }}</div>
        </div>
    </div>
    <div class="doc-count-pill">
        <div class="doc-count-num">{{ $documents->count() }}</div>
        <div class="doc-count-label">{{ __('Documents') }}</div>
    </div>
</div>

{{-- Search --}}
<div class="search-bar">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9bb0b0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <input type="text" id="doc-search" placeholder="{{ __('Search a document…') }}" oninput="filterDocs()" autocomplete="off">
</div>

{{-- Grid --}}
@if($documents->isEmpty())
    <div class="empty-state">
        <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#9bb0b0" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
        </svg>
        <p>{{ __('No document available at the moment.') }}</p>
    </div>
@else
    <div class="doc-grid" id="doc-grid">
        @foreach($documents as $doc)
            @php
                $ext = pathinfo($doc->file_path, PATHINFO_EXTENSION);
                $extColors = ['pdf' => '#dc2626', 'txt' => '#059669', 'md' => '#7c3aed', 'jpg' => '#d97706', 'jpeg' => '#d97706', 'png' => '#d97706', 'gif' => '#d97706', 'webp' => '#d97706', 'svg' => '#d97706'];
                $extColor  = $extColors[strtolower($ext)] ?? ($siteSetting->theme_color ?? '#0c7070');
                $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                $isImage   = in_array(strtolower($ext), $imageExts);
            @endphp
            <div class="doc-card" data-title="{{ strtolower($doc->title) }}">
                <div class="doc-card-stripe"></div>
                <div class="doc-card-body">
                    <div class="doc-card-icon">
                        @if($isImage)
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        @else
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        @endif
                    </div>
                    <div class="doc-card-title">{{ $doc->title }}</div>
                    <div class="doc-card-meta">
                        <span style="background:{{ $extColor }}22;color:{{ $extColor }};border-radius:5px;padding:1px 6px;font-weight:700;text-transform:uppercase;font-size:.7rem;">{{ strtoupper($ext) }}</span>
                        {{ $doc->created_at->locale(app()->getLocale())->isoFormat('D MMM YYYY') }}
                    </div>
                </div>
                <div class="doc-card-footer">
                    <button onclick="openViewer({{ $doc->id }}, {{ Js::from($doc->title) }}, {{ Js::from(strtolower($ext)) }}, {{ Js::from($doc->description) }})" class="doc-view-btn">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                        {{ __('Consult') }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Viewer modal --}}
<div class="viewer-overlay" id="viewer-overlay" onclick="closeViewerOutside(event)">
    <div class="viewer-modal">
        <div class="viewer-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--teal);flex-shrink:0;">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            <div class="viewer-title" id="viewer-title">Document</div>
            <button class="viewer-close" onclick="closeViewer()" title="{{ __('Close') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="viewer-body" id="viewer-body">
            <div style="text-align:center;padding:2rem;color:#9bb0b0;">{{ __('Loading…') }}</div>
        </div>
        <div class="viewer-footer">
            <button onclick="closeViewer()"
                style="padding:0.5rem 1.2rem;border-radius:9px;border:1.5px solid #dde8e8;background:#fff;font:inherit;font-size:0.84rem;font-weight:600;cursor:pointer;">
                {{ __('Close') }}
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function filterDocs() {
    const q = document.getElementById('doc-search').value.toLowerCase();
    document.querySelectorAll('#doc-grid .doc-card').forEach(card => {
        card.style.display = card.dataset.title.includes(q) ? '' : 'none';
    });
}

function openViewer(id, title, ext, description) {
    document.getElementById('viewer-title').textContent = title;
    const body = document.getElementById('viewer-body');
    body.innerHTML = '';
    body.classList.remove('text-mode');
    body.style.padding    = '';
    body.style.alignItems = '';
    body.style.justifyContent = '';
    body.style.overflow   = '';
    body.style.flexDirection = '';
    body.style.gap        = '';
    document.getElementById('viewer-overlay').classList.add('open');

    const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    if (ext === 'pdf') {
        const iframe = document.createElement('iframe');
        iframe.src = '/employee/documents/' + id + '/view';
        iframe.style.cssText = 'width:100%;height:100%;flex:1;border:none;display:block;';
        body.style.padding = '0';
        body.appendChild(iframe);
    } else if (imageExts.includes(ext)) {
        body.style.padding       = '1rem';
        body.style.alignItems    = 'flex-start';
        body.style.justifyContent = 'center';
        body.style.overflow      = 'auto';
        body.style.flexDirection = 'column';
        body.style.gap           = '1rem';

        const img = document.createElement('img');
        img.src = '/employee/documents/' + id + '/view';
        img.alt = title;
        img.style.cssText = 'max-width:100%;object-fit:contain;border-radius:8px;align-self:center;';
        body.appendChild(img);

        if (description) {
            const descBox = document.createElement('div');
            descBox.style.cssText = [
                'width:100%',
                'background:#f0f9f9',
                'border:1.5px solid #b8e2e2',
                'border-radius:12px',
                'padding:1rem 1.2rem',
                'font-size:0.85rem',
                'line-height:1.7',
                'color:#1e2c2c',
            ].join(';');

            const label = document.createElement('div');
            label.style.cssText = 'font-weight:700;color:var(--teal,#0c7070);margin-bottom:0.4rem;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.05em;';
            label.textContent = '{{ __("Image description (VLM)") }}';

            const text = document.createElement('p');
            text.style.margin = '0';
            text.textContent = description;

            descBox.appendChild(label);
            descBox.appendChild(text);
            body.appendChild(descBox);
        }
    } else {
        body.classList.add('text-mode');
        body.innerHTML = '<div style="text-align:center;padding:2rem;color:#9bb0b0;">{{ __("Loading…") }}</div>';

        fetch('/employee/documents/' + id + '/content', {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        })
        .then(r => r.json())
        .then(data => {
            if (data.content) {
                body.textContent = data.content;
            } else if (data.error) {
                body.innerHTML = '<span style="color:#dc2626;">' + data.error + '</span>';
            }
        })
        .catch(() => {
            body.innerHTML = '<span style="color:#dc2626;">{{ __("Unable to load the document.") }}</span>';
        });
    }
}

function closeViewer() {
    document.getElementById('viewer-overlay').classList.remove('open');
}
function closeViewerOutside(e) {
    if (e.target === document.getElementById('viewer-overlay')) closeViewer();
}
</script>
@endpush
