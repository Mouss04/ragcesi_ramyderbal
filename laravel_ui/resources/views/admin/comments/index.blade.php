@extends('layouts.admin')

@push('styles')
<style>
/* ── Hero ── */
.cm-hero {
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
.cm-hero::before {
    content:''; position:absolute; top:-50px; right:-50px;
    width:220px; height:220px; border-radius:50%;
    background:rgba(255,255,255,.07);
}
.cm-hero-left  { display:flex; align-items:center; gap:1.1rem; z-index:1; }
.cm-hero-icon  {
    width:54px; height:54px; border-radius:15px;
    background:rgba(255,255,255,.15);
    border:1.5px solid rgba(255,255,255,.25);
    display:grid; place-items:center; flex-shrink:0;
}
.cm-hero-title { font-size:1.3rem; font-weight:800; color:#fff; }
.cm-hero-sub   { font-size:.82rem; color:rgba(255,255,255,.72); margin-top:3px; }
.cm-count-pill {
    background:rgba(255,255,255,.18);
    border:1px solid rgba(255,255,255,.28);
    border-radius:12px;
    padding:.5rem 1.1rem;
    text-align:center; z-index:1;
}
.cm-count-num   { font-size:1.6rem; font-weight:800; color:#fff; }
.cm-count-label { font-size:.72rem; color:rgba(255,255,255,.7); font-weight:600; }

/* ── Filter tabs ── */
.cm-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.2rem;
    flex-wrap: wrap;
}
.cm-tab {
    padding: 0.4rem 1rem;
    border-radius: 9px;
    border: 1.5px solid var(--line);
    background: #fff;
    font: inherit;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--muted);
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: 140ms;
}
.cm-tab:hover, .cm-tab.active {
    border-color: var(--teal);
    color: var(--teal);
    background: var(--teal-soft);
}
.cm-tab-count {
    background: currentColor;
    color: #fff;
    border-radius: 5px;
    padding: 0 5px;
    font-size: .68rem;
    opacity: 0.85;
}

/* ── Table ── */
.cm-table-wrap {
    background: #fff;
    border: 1.5px solid var(--line);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px var(--teal-shadow);
}
.cm-table { width: 100%; border-collapse: collapse; }
.cm-table th {
    padding: 0.75rem 1rem;
    background: var(--teal-soft);
    text-align: left;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--muted);
    border-bottom: 1.5px solid var(--line);
}
.cm-table td {
    padding: 0.85rem 1rem;
    font-size: 0.85rem;
    border-bottom: 1px solid var(--line);
    vertical-align: top;
}
.cm-table tr:last-child td { border-bottom: none; }
.cm-table tr:hover td { background: #fafcfc; }

/* ── Status badge ── */
.badge-pending  { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
.badge-approved { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.badge-rejected { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.status-badge {
    display:inline-block; padding:2px 8px; border-radius:6px;
    font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em;
}

/* ── Actions ── */
.btn-approve, .btn-reject {
    display:inline-flex; align-items:center; gap:0.3rem;
    padding:0.3rem 0.75rem; border-radius:7px; border:none;
    font:inherit; font-size:0.78rem; font-weight:700; cursor:pointer; transition:140ms;
}
.btn-approve { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.btn-approve:hover { background:#dcfce7; }
.btn-reject  { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.btn-reject:hover  { background:#fee2e2; }

/* ── Moderation info ── */
.cm-mod-info { font-size:.75rem; color:#9bb0b0; margin-top:3px; }

/* ── Comment text ── */
.cm-content {
    max-width: 320px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.55;
    word-break: break-word;
    color: #1e2c2c;
}

/* ── Empty ── */
.cm-empty {
    text-align:center; padding:3rem 1rem;
    color:#9bb0b0; font-size:0.9rem;
}
.cm-empty svg { margin-bottom:1rem; opacity:.4; }

/* ── Flash ── */
.flash-success {
    background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;
    padding:0.75rem 1rem; border-radius:10px; font-size:0.84rem; font-weight:600;
    margin-bottom:1.2rem;
}
.flash-error {
    background:#fef2f2; color:#dc2626; border:1px solid #fecaca;
    padding:0.75rem 1rem; border-radius:10px; font-size:0.84rem; font-weight:600;
    margin-bottom:1.2rem;
}
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="cm-hero">
    <div class="cm-hero-left">
        <div class="cm-hero-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </div>
        <div>
            <div class="cm-hero-title">{{ __('Comment history & moderation') }}</div>
            <div class="cm-hero-sub">{{ __('Review, approve or reject user comments') }}</div>
        </div>
    </div>
    <div class="cm-count-pill">
        <div class="cm-count-num">{{ $comments->total() }}</div>
        <div class="cm-count-label">{{ __('Comments') }}</div>
    </div>
</div>

{{-- Flash messages --}}
@if(session('status'))
    <div class="flash-success">{{ session('status') }}</div>
@endif
@if($errors->has('comment'))
    <div class="flash-error">{{ $errors->first('comment') }}</div>
@endif

{{-- Filter tabs --}}
<div class="cm-tabs">
    <a href="{{ route('admin.comments.index') }}"
       class="cm-tab {{ !$status ? 'active' : '' }}">
        {{ __('All') }}
        <span class="cm-tab-count">{{ ($counts['PENDING'] ?? 0) + ($counts['APPROVED'] ?? 0) + ($counts['REJECTED'] ?? 0) }}</span>
    </a>
    <a href="{{ route('admin.comments.index', ['status' => 'PENDING']) }}"
       class="cm-tab {{ $status === 'PENDING' ? 'active' : '' }}">
        {{ __('Pending') }}
        <span class="cm-tab-count">{{ $counts['PENDING'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.comments.index', ['status' => 'APPROVED']) }}"
       class="cm-tab {{ $status === 'APPROVED' ? 'active' : '' }}">
        {{ __('Approved') }}
        <span class="cm-tab-count">{{ $counts['APPROVED'] ?? 0 }}</span>
    </a>
    <a href="{{ route('admin.comments.index', ['status' => 'REJECTED']) }}"
       class="cm-tab {{ $status === 'REJECTED' ? 'active' : '' }}">
        {{ __('Rejected') }}
        <span class="cm-tab-count">{{ $counts['REJECTED'] ?? 0 }}</span>
    </a>
</div>

{{-- Table --}}
@if($comments->isEmpty())
    <div class="cm-table-wrap">
        <div class="cm-empty">
            <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#9bb0b0" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <p>{{ __('No comment found.') }}</p>
        </div>
    </div>
@else
    <div class="cm-table-wrap">
        <table class="cm-table">
            <thead>
                <tr>
                    <th>{{ __('Document') }}</th>
                    <th>{{ __('Author') }}</th>
                    <th>{{ __('Comment') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Moderation') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comments as $comment)
                    @php
                        $badgeClass = match($comment->status) {
                            'APPROVED' => 'badge-approved',
                            'REJECTED' => 'badge-rejected',
                            default    => 'badge-pending',
                        };
                        $statusLabel = match($comment->status) {
                            'APPROVED' => __('Approved'),
                            'REJECTED' => __('Rejected'),
                            default    => __('Pending'),
                        };
                        $roleLabel = match($comment->author->role ?? '') {
                            'admin'      => __('Administrator'),
                            'supervisor' => __('Supervisor'),
                            default      => __('Employee'),
                        };
                    @endphp
                    <tr id="cm-row-{{ $comment->id }}">
                        {{-- Document --}}
                        <td style="max-width:150px;">
                            <div style="font-weight:700;word-break:break-word;font-size:0.83rem;">{{ $comment->document->title ?? '—' }}</div>
                            <div style="font-size:.72rem;color:#9bb0b0;margin-top:2px;">
                                {{ $comment->created_at->locale(app()->getLocale())->isoFormat('D MMM YYYY, HH:mm') }}
                            </div>
                        </td>

                        {{-- Author --}}
                        <td style="white-space:nowrap;">
                            <div style="font-weight:700;">{{ $comment->author->name ?? '—' }}</div>
                            <div style="font-size:.72rem;color:#9bb0b0;margin-top:2px;">{{ $roleLabel }}</div>
                        </td>

                        {{-- Comment text --}}
                        <td>
                            <div class="cm-content">{{ $comment->content }}</div>
                        </td>

                        {{-- Status --}}
                        <td style="white-space:nowrap;">
                            <span class="status-badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                        </td>

                        {{-- Moderation info --}}
                        <td>
                            @if($comment->moderator && $comment->moderated_at)
                                <div style="font-size:.8rem;font-weight:600;color:#1e2c2c;">{{ $comment->moderator->name }}</div>
                                <div class="cm-mod-info">
                                    {{ $comment->moderated_at->locale(app()->getLocale())->isoFormat('D MMM YYYY, HH:mm') }}
                                </div>
                            @else
                                <span style="color:#9bb0b0;font-size:.8rem;">—</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td style="white-space:nowrap;">
                            @if($comment->status === 'PENDING')
                                <form class="moderation-form" data-action="approve" method="POST" action="{{ route('admin.comments.approve', $comment) }}" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-approve">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                        {{ __('Approve') }}
                                    </button>
                                </form>
                                <form class="moderation-form" data-action="reject" method="POST" action="{{ route('admin.comments.reject', $comment) }}" style="display:inline;margin-left:0.4rem;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-reject">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                        {{ __('Reject') }}
                                    </button>
                                </form>
                            @else
                                <span style="color:#9bb0b0;font-size:.8rem;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($comments->hasPages())
        <div style="margin-top:1.2rem;display:flex;justify-content:flex-end;">
            {{ $comments->links() }}
        </div>
    @endif
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    /* ── Indexing indicator (in header) ── */
    var indexIndicator = document.getElementById('rag-index-indicator');
    var indexBar       = document.getElementById('rag-index-bar');
    var indexPct       = document.getElementById('rag-index-pct');
    var barAnim        = null;

    function setBarPct(p) {
        var rounded = Math.round(p);
        if (indexBar) indexBar.style.width = rounded + '%';
        if (indexPct) indexPct.textContent  = rounded + '%';
    }

    function startIndexing() {
        if (!indexIndicator) return;
        if (barAnim) { clearInterval(barAnim); barAnim = null; }
        setBarPct(0);
        indexBar.style.transition = 'none';
        indexIndicator.style.display = 'flex';
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                indexBar.style.transition = 'width .35s ease';
                var pct = 0;
                barAnim = setInterval(function () {
                    pct = Math.min(pct + (88 - pct) * 0.06, 88);
                    setBarPct(pct);
                }, 300);
            });
        });
    }

    function finishIndexing() {
        if (!indexIndicator) return;
        if (barAnim) { clearInterval(barAnim); barAnim = null; }
        setBarPct(100);
        setTimeout(function () {
            indexIndicator.style.display = 'none';
            setBarPct(0);
        }, 550);
    }

    document.querySelectorAll('.moderation-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var btn = form.querySelector('button[type="submit"]');
            var isApprove = form.dataset.action === 'approve';
            btn.disabled = true;

            if (isApprove) { startIndexing(); }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(new FormData(form)),
            })
            .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
            .then(function (res) {
                if (isApprove) { finishIndexing(); }
                if (res.ok) {
                    var row = form.closest('tr');
                    /* Update status badge */
                    var badge = row.querySelector('.status-badge');
                    if (badge) {
                        badge.classList.remove('badge-pending', 'badge-approved', 'badge-rejected');
                        if (isApprove) {
                            badge.classList.add('badge-approved');
                            badge.textContent = '{{ __("Approved") }}';
                        } else {
                            badge.classList.add('badge-rejected');
                            badge.textContent = '{{ __("Rejected") }}';
                        }
                    }
                    /* Hide action buttons */
                    var actionsTd = form.closest('td');
                    if (actionsTd) {
                        actionsTd.innerHTML = '<span style="color:#9bb0b0;font-size:.8rem;">—</span>';
                    }
                    /* Dismiss related notifications from the bell dropdown */
                    if (res.data.dismissed_ids && res.data.dismissed_ids.length) {
                        res.data.dismissed_ids.forEach(function (id) {
                            var notifEl = document.querySelector('[data-id="' + id + '"]');
                            if (notifEl) { notifEl.remove(); }
                        });
                        updateNotifBadge();
                    }
                    showModerationToast(isApprove ? 'success' : 'error', res.data.message || '');
                } else {
                    btn.disabled = false;
                    showModerationToast('error', res.data.error || '{{ __("An error occurred.") }}');
                }
            })
            .catch(function () {
                if (isApprove) { finishIndexing(); }
                btn.disabled = false;
                showModerationToast('error', '{{ __("An error occurred.") }}');
            });
        });
    });

    function updateNotifBadge() {
        var remaining = document.querySelectorAll('#notifList .notif-item.unread').length;
        var badge = document.getElementById('notifBadge');
        if (remaining === 0) {
            if (badge) badge.remove();
            var btn = document.getElementById('notifBtn');
            if (btn) btn.classList.remove('has-notif');
            /* Show empty state if list is now empty */
            var list = document.getElementById('notifList');
            if (list && list.children.length === 0) {
                list.innerHTML =
                    '<div class="notif-empty">' +
                        '<div class="notif-empty-icon">' +
                            '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color:var(--teal);">' +
                                '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>' +
                                '<path d="M13.73 21a2 2 0 0 1-3.46 0"/>' +
                            '</svg>' +
                        '</div>' +
                        '<p>{{ __("All caught up!") }}</p>' +
                        '<span>{{ __("No new notifications") }}</span>' +
                    '</div>';
            }
        } else {
            if (badge) badge.textContent = remaining > 99 ? '99+' : remaining;
        }
    }

    function showModerationToast(type, msg) {
        var wrap = document.getElementById('notifToastWrap');
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.style.cssText = 'position:fixed;bottom:1.4rem;right:1.4rem;z-index:9999;display:flex;flex-direction:column;gap:.6rem;pointer-events:none;';
            document.body.appendChild(wrap);
        }
        var isSuccess = type === 'success';
        var color = isSuccess ? '#15803d' : '#dc2626';
        var toast = document.createElement('div');
        toast.style.cssText = 'display:flex;align-items:center;gap:0.75rem;background:#fff;border:1.5px solid #dde8e8;border-left:4px solid ' + color + ';border-radius:14px;padding:0.85rem 1rem;box-shadow:0 10px 40px rgba(0,0,0,.13);min-width:280px;max-width:340px;pointer-events:all;';
        toast.innerHTML =
            '<div style="flex:1;font-size:.84rem;font-weight:600;color:' + color + ';">' + msg + '</div>' +
            '<button style="background:none;border:none;cursor:pointer;padding:0;color:#9bb0b0;flex-shrink:0;">' +
                '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
            '</button>';
        toast.querySelector('button').addEventListener('click', function () { toast.remove(); });
        wrap.appendChild(toast);
        setTimeout(function () { toast.remove(); }, 5000);
    }
});
</script>
@endpush
