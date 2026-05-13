@extends('admin.layouts.app')

@section('title', 'Announcements')

@section('content')

{{-- ── Page Header ─────────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0" style="color: var(--text-primary);">Announcements</h2>
        <small style="color: var(--text-muted);">Create and manage student announcements</small>
    </div>
    <button class="btn btn-primary d-flex align-items-center gap-2"
            style="border-radius:12px; font-weight:600; padding:.6rem 1.2rem;"
            onclick="openCreateModal()">
        <i class="fas fa-plus"></i> New Announcement
    </button>
</div>

{{-- ── Flash messages ───────────────────────────────────────────────────── --}}
@if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4"
         style="border-radius:14px; border:none; background:rgba(76,175,80,.12); color:#2e7d32;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- ── Stat mini-cards ─────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    @foreach([
        ['Total',     $stats['total'],     'fa-bullhorn',         '#0f3c91', 'rgba(15,60,145,.1)'],
        ['Published', $stats['published'], 'fa-rss',              '#4caf50', 'rgba(76,175,80,.1)'],
        ['Important', $stats['important'], 'fa-star',             '#f4b414', 'rgba(244,180,20,.1)'],
        ['Urgent',    $stats['urgent'],    'fa-exclamation-circle','#dc3545', 'rgba(220,53,69,.1)'],
    ] as [$label, $count, $icon, $color, $bg])
    <div class="col-xl-3 col-md-6">
        <div class="ann-mini-stat">
            <div class="ann-mini-icon" style="background:{{ $bg }};">
                <i class="fas {{ $icon }}" style="color:{{ $color }};"></i>
            </div>
            <div>
                <div class="ann-mini-label">{{ $label }}</div>
                <div class="ann-mini-value" style="color:{{ $color }};">{{ $count }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Filter bar ───────────────────────────────────────────────────────── --}}
<div class="ann-filter-bar mb-3">
    <form method="GET" action="{{ route('admin.announcements.index') }}"
          class="d-flex flex-wrap gap-2 align-items-center">
        <div class="ann-search-wrap">
            <i class="fas fa-search ann-search-icon"></i>
            <input type="text" name="search" class="ann-search-input"
                   placeholder="Search announcements…"
                   value="{{ request('search') }}">
        </div>
        <select name="priority" class="ann-filter-select">
            <option value="">All priorities</option>
            <option value="normal"    {{ request('priority') === 'normal'    ? 'selected' : '' }}>Normal</option>
            <option value="important" {{ request('priority') === 'important' ? 'selected' : '' }}>Important</option>
            <option value="urgent"    {{ request('priority') === 'urgent'    ? 'selected' : '' }}>Urgent</option>
        </select>
        <button type="submit" class="btn btn-primary" style="border-radius:10px; font-size:.85rem; padding:.45rem 1rem;">
            <i class="fas fa-filter me-1"></i>Filter
        </button>
        @if(request()->hasAny(['search','priority']))
            <a href="{{ route('admin.announcements.index') }}"
               class="btn btn-outline-secondary" style="border-radius:10px; font-size:.85rem; padding:.45rem 1rem;">
                <i class="fas fa-times me-1"></i>Clear
            </a>
        @endif
    </form>
</div>

{{-- ── Announcements list ───────────────────────────────────────────────── --}}
@if($announcements->isEmpty())
    <div class="ann-empty">
        <i class="fas fa-bullhorn"></i>
        <h5>No announcements yet</h5>
        <p>Click "New Announcement" to create your first one.</p>
        <button class="btn btn-primary" style="border-radius:12px;" onclick="openCreateModal()">
            <i class="fas fa-plus me-2"></i>Create Announcement
        </button>
    </div>
@else
    <div class="ann-list">
        @foreach($announcements as $ann)
        @php
            $priorityMeta = [
                'urgent'    => ['color' => '#dc3545', 'bg' => 'rgba(220,53,69,.1)',  'icon' => 'fa-exclamation-circle', 'label' => 'Urgent'],
                'important' => ['color' => '#f4b414', 'bg' => 'rgba(244,180,20,.1)','icon' => 'fa-star',               'label' => 'Important'],
                'normal'    => ['color' => '#0f3c91', 'bg' => 'rgba(15,60,145,.1)', 'icon' => 'fa-bullhorn',           'label' => 'Normal'],
            ][$ann->priority] ?? ['color' => '#0f3c91', 'bg' => 'rgba(15,60,145,.1)', 'icon' => 'fa-bullhorn', 'label' => 'Normal'];
            $audienceLabel = match($ann->audience) {
                'course'     => 'Course: ' . $ann->audience_value,
                'year_level' => 'Year '   . $ann->audience_value,
                default      => 'All Students',
            };
        @endphp
        <div class="ann-card {{ !$ann->is_published ? 'ann-card--draft' : '' }}">
            <div class="ann-card-stripe" style="background:{{ $priorityMeta['color'] }};"></div>

            <div class="ann-card-body">
                <div class="ann-card-top">
                    <div class="ann-badge-row">
                        <span class="ann-badge" style="background:{{ $priorityMeta['bg'] }}; color:{{ $priorityMeta['color'] }};">
                            <i class="fas {{ $priorityMeta['icon'] }}" style="font-size:.7rem;"></i>
                            {{ $priorityMeta['label'] }}
                        </span>
                        <span class="ann-badge ann-badge--audience">
                            <i class="fas fa-users" style="font-size:.7rem;"></i>
                            {{ $audienceLabel }}
                        </span>
                        @if(!$ann->is_published)
                            <span class="ann-badge ann-badge--draft">
                                <i class="fas fa-eye-slash" style="font-size:.7rem;"></i> Draft
                            </span>
                        @endif
                    </div>
                    <div class="ann-actions">
                        <button class="ann-action-btn ann-action-btn--edit"
                                onclick="openEditModal({{ $ann->id }})"
                                title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        {{-- Delete now opens a modal instead of a browser confirm() --}}
                        <button class="ann-action-btn ann-action-btn--delete"
                                title="Delete"
                                onclick="openDeleteModal(
                                    {{ $ann->id }},
                                    {{ json_encode($ann->title) }},
                                    '{{ route('admin.announcements.destroy', $ann) }}'
                                )">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <h6 class="ann-card-title">{{ $ann->title }}</h6>
                <p class="ann-card-body-text">{{ Str::limit($ann->body, 160) }}</p>

                <div class="ann-card-footer">
                    <span class="ann-meta">
                        <i class="fas fa-user-circle"></i> {{ $ann->creator->name ?? 'Admin' }}
                    </span>
                    <span class="ann-meta">
                        <i class="fas fa-clock"></i> {{ $ann->created_at->format('M d, Y · h:i A') }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-3">
        {{ $announcements->links() }}
    </div>
@endif

{{-- ════════════════════════════════════════════════════════════════════════
     CREATE / EDIT MODAL
     ════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="announcementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:24px; overflow:hidden;">

            <div class="modal-header" style="border-radius:24px 24px 0 0;">
                <h5 class="modal-title" id="announcementModalTitle">
                    <i class="fas fa-bullhorn me-2"></i>New Announcement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="announcementForm" method="POST">
                @csrf
                <span id="methodSpoofField"></span>

                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Title</label>
                        <input type="text" class="form-control" name="title" id="annTitle"
                               placeholder="e.g. Exam Schedule Update" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Message Body</label>
                        <textarea class="form-control" name="body" id="annBody" rows="5"
                                  placeholder="Write your announcement here…" required></textarea>
                        <small class="text-muted" id="annBodyCounter">0 / 2000 characters</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Priority</label>
                            <select class="form-select" name="priority" id="annPriority">
                                <option value="normal">📢 Normal</option>
                                <option value="important">⭐ Important</option>
                                <option value="urgent">🚨 Urgent</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Audience</label>
                            <select class="form-select" name="audience" id="annAudience"
                                    onchange="toggleAudienceValue()">
                                <option value="all">👥 All Students</option>
                                <option value="course">🎓 By Course</option>
                                <option value="year_level">📅 By Year Level</option>
                            </select>
                        </div>

                        <div class="col-md-4" id="audienceValueWrap" style="display:none;">
                            <label class="form-label fw-semibold" style="font-size:.85rem;" id="audienceValueLabel">Value</label>
                            <select class="form-select" name="audience_value" id="audienceCourseSelect" style="display:none;">
                                <option value="">Select course…</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->code }}">{{ $course->code }} – {{ $course->name }}</option>
                                @endforeach
                            </select>
                            <select class="form-select" name="audience_value" id="audienceYearSelect" style="display:none;">
                                <option value="">Select year…</option>
                                @foreach(['1','2','3','4'] as $yr)
                                    <option value="{{ $yr }}">Year {{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-4 p-3"
                         style="background:var(--input-bg); border-radius:12px; border:1px solid var(--border-color);">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_published"
                                   id="annPublished" value="1" checked style="width:2.5rem; height:1.25rem;">
                            <label class="form-check-label fw-semibold ms-2" for="annPublished" style="font-size:.9rem;">
                                Publish immediately &amp; notify students
                            </label>
                        </div>
                        <div style="margin-left:auto; font-size:.78rem; color:var(--text-muted);">
                            <i class="fas fa-info-circle me-1"></i>Unpublished = draft only
                        </div>
                    </div>

                </div>

                <div class="modal-footer" style="border-top:1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            style="border-radius:10px;">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="annSubmitBtn"
                            style="border-radius:10px; min-width:140px;">
                        <i class="fas fa-paper-plane me-2"></i>
                        <span id="annSubmitLabel">Publish</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     DELETE CONFIRMATION MODAL
     ════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content" style="border-radius:24px; overflow:hidden; border:none;">

            <div class="modal-body p-4 pt-5 text-center">
                {{-- Red icon circle --}}
                <div class="del-icon-wrap mx-auto mb-4">
                    <i class="fas fa-trash-alt"></i>
                </div>

                <h5 class="fw-bold mb-1" style="color:var(--text-primary);">
                    Delete Announcement?
                </h5>
                <p class="mb-2" style="color:var(--text-muted); font-size:.84rem;">
                    You're about to permanently delete:
                </p>

                {{-- Announcement title preview --}}
                <div class="del-title-preview mb-3">
                    <i class="fas fa-bullhorn me-2" style="color:#dc3545; opacity:.7;"></i>
                    <span id="deleteAnnouncementTitle">—</span>
                </div>

                <p style="color:var(--text-muted); font-size:.78rem; line-height:1.6;">
                    Students will no longer see this announcement.<br>
                    <strong style="color:var(--text-primary);">This action cannot be undone.</strong>
                </p>
            </div>

            <div class="modal-footer justify-content-center gap-2 pb-4 pt-0"
                 style="border-top:none;">
                <button type="button"
                        class="btn btn-outline-secondary"
                        style="border-radius:10px; min-width:120px; font-weight:500;"
                        data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="btn btn-danger"
                            style="border-radius:10px; min-width:120px; font-weight:600;">
                        <i class="fas fa-trash-alt me-1"></i> Delete
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection


@push('styles')
<style>
/* Mini stats */
.ann-mini-stat {
    background: var(--bg-main); border-radius: .875rem; padding: .9rem 1rem;
    box-shadow: var(--card-shadow); display: flex; align-items: center; gap: 12px;
    transition: transform .2s, box-shadow .2s;
}
.ann-mini-stat:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(15,60,145,.1) !important; }
.ann-mini-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
.ann-mini-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .06em; font-weight: 600; color: var(--text-muted); }
.ann-mini-value { font-size: 1.1rem; font-weight: 700; }

/* Filter bar */
.ann-filter-bar { display: flex; align-items: center; }
.ann-search-wrap { position: relative; flex: 1; max-width: 340px; }
.ann-search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: .85rem; pointer-events: none; }
.ann-search-input { width: 100%; padding: .5rem .75rem .5rem 2.25rem; border: 1px solid var(--border-color); border-radius: 10px; background: var(--input-bg); color: var(--text-primary); font-size: .85rem; }
.ann-search-input:focus { outline: none; border-color: #0f3c91; box-shadow: 0 0 0 3px rgba(15,60,145,.1); }
.ann-filter-select { padding: .45rem .75rem; border: 1px solid var(--border-color); border-radius: 10px; background: var(--input-bg); color: var(--text-primary); font-size: .85rem; min-width: 160px; }

/* Cards */
.ann-list { display: flex; flex-direction: column; gap: .75rem; }
.ann-card { background: var(--bg-main); border-radius: 16px; box-shadow: var(--card-shadow); display: flex; overflow: hidden; border: 1px solid var(--border-color); transition: box-shadow .2s, transform .2s; }
.ann-card:hover { box-shadow: 0 8px 24px rgba(15,60,145,.12) !important; transform: translateY(-2px); }
.ann-card--draft { opacity: .72; }
.ann-card-stripe { width: 5px; flex-shrink: 0; }
.ann-card-body { flex: 1; padding: 1rem 1.25rem; }
.ann-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: .5rem; gap: .5rem; flex-wrap: wrap; }
.ann-badge-row { display: flex; align-items: center; gap: .4rem; flex-wrap: wrap; }
.ann-badge { display: inline-flex; align-items: center; gap: 4px; font-size: .7rem; font-weight: 600; padding: 3px 10px; border-radius: 30px; }
.ann-badge--audience { background: rgba(15,60,145,.08); color: #0f3c91; }
body.dark .ann-badge--audience { background: rgba(59,130,246,.15); color: #93c5fd; }
.ann-badge--draft { background: rgba(100,116,139,.12); color: #64748b; }
.ann-actions { display: flex; gap: .4rem; flex-shrink: 0; }
.ann-action-btn { width: 34px; height: 34px; border-radius: 9px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-muted); cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: center; font-size: .85rem; }
.ann-action-btn--edit:hover   { background: #0f3c91; color: #fff; border-color: #0f3c91; }
.ann-action-btn--delete:hover { background: #dc3545; color: #fff; border-color: #dc3545; }
.ann-card-title { font-size: .95rem; font-weight: 700; color: var(--text-primary); margin-bottom: .35rem; }
.ann-card-body-text { font-size: .83rem; color: var(--text-secondary); line-height: 1.55; margin-bottom: .65rem; }
.ann-card-footer { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.ann-meta { font-size: .72rem; color: var(--text-muted); display: flex; align-items: center; gap: 5px; }

/* Empty state */
.ann-empty { text-align: center; padding: 4rem 2rem; background: var(--bg-main); border-radius: 20px; box-shadow: var(--card-shadow); }
.ann-empty i { font-size: 3rem; color: var(--text-muted); opacity: .4; display: block; margin-bottom: 1rem; }
.ann-empty h5 { font-weight: 700; color: var(--text-primary); }
.ann-empty p  { color: var(--text-muted); font-size: .9rem; margin-bottom: 1.5rem; }

/* Publish toggle */
.form-check-input:checked { background-color: #0f3c91; border-color: #0f3c91; }
body.dark .form-check-input:checked { background-color: #3b82f6; border-color: #3b82f6; }

/* ── Delete modal ─────────────────────────────────────────────────────── */
.del-icon-wrap {
    width: 76px; height: 76px; border-radius: 50%;
    background: rgba(220,53,69,.1);
    display: flex; align-items: center; justify-content: center;
    /* subtle pulse ring */
    box-shadow: 0 0 0 8px rgba(220,53,69,.06), 0 0 0 16px rgba(220,53,69,.03);
}
.del-icon-wrap i { font-size: 1.9rem; color: #dc3545; }

.del-title-preview {
    display: inline-flex; align-items: center;
    font-size: .87rem; font-weight: 600;
    color: var(--text-primary);
    background: var(--input-bg);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: .55rem 1rem;
    max-width: 100%; word-break: break-word;
    text-align: left;
}

/* Mobile */
@media (max-width: 767.98px) {
    .ann-filter-bar { flex-direction: column; align-items: stretch; gap: .5rem; }
    .ann-search-wrap { max-width: 100%; }
    .ann-filter-select { width: 100%; }
    .ann-card-top { flex-direction: column; align-items: flex-start; }
    .ann-actions { align-self: flex-end; }
    .modal-footer .btn { width: 100% !important; }
}
</style>
@endpush


@push('scripts')
<script>
const BASE_ANNOUNCEMENT_URL = "{{ url('admin/announcements') }}";
const ROUTES = {
    store:   "{{ route('admin.announcements.store') }}",
    show:    (id) => `${BASE_ANNOUNCEMENT_URL}/${id}`,
    update:  (id) => `${BASE_ANNOUNCEMENT_URL}/${id}`,
};
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

let editingId = null;
const annModal    = new bootstrap.Modal(document.getElementById('announcementModal'));
const delModal    = new bootstrap.Modal(document.getElementById('deleteModal'));
const form        = document.getElementById('announcementForm');
const titleEl     = document.getElementById('annTitle');
const bodyEl      = document.getElementById('annBody');
const priorityEl  = document.getElementById('annPriority');
const audienceEl  = document.getElementById('annAudience');
const publishEl   = document.getElementById('annPublished');
const submitLbl   = document.getElementById('annSubmitLabel');
const methodEl    = document.getElementById('methodSpoofField');
const counter     = document.getElementById('annBodyCounter');

// ── Character counter ─────────────────────────────────────────────────────
bodyEl.addEventListener('input', () => {
    counter.textContent = `${bodyEl.value.length} / 2000 characters`;
});

// ── Audience value toggle ─────────────────────────────────────────────────
function toggleAudienceValue() {
    const val     = audienceEl.value;
    const wrap    = document.getElementById('audienceValueWrap');
    const course  = document.getElementById('audienceCourseSelect');
    const yearSel = document.getElementById('audienceYearSelect');
    const lbl     = document.getElementById('audienceValueLabel');

    course.style.display  = 'none';
    yearSel.style.display = 'none';
    course.removeAttribute('name');
    yearSel.removeAttribute('name');

    if (val === 'course') {
        wrap.style.display   = '';
        course.style.display = '';
        course.setAttribute('name', 'audience_value');
        lbl.textContent = 'Course';
    } else if (val === 'year_level') {
        wrap.style.display    = '';
        yearSel.style.display = '';
        yearSel.setAttribute('name', 'audience_value');
        lbl.textContent = 'Year Level';
    } else {
        wrap.style.display = 'none';
    }
}

// ── Open CREATE modal ─────────────────────────────────────────────────────
function openCreateModal() {
    editingId = null;
    document.getElementById('announcementModalTitle').innerHTML =
        '<i class="fas fa-bullhorn me-2"></i>New Announcement';
    submitLbl.textContent = 'Publish';
    methodEl.innerHTML    = '';
    form.action           = ROUTES.store;

    titleEl.value       = '';
    bodyEl.value        = '';
    priorityEl.value    = 'normal';
    audienceEl.value    = 'all';
    publishEl.checked   = true;
    counter.textContent = '0 / 2000 characters';
    toggleAudienceValue();

    annModal.show();
}

// ── Open EDIT modal ───────────────────────────────────────────────────────
async function openEditModal(id) {
    editingId = id;
    document.getElementById('announcementModalTitle').innerHTML =
        '<i class="fas fa-edit me-2"></i>Edit Announcement';
    submitLbl.textContent = 'Save Changes';
    methodEl.innerHTML    = '<input type="hidden" name="_method" value="PUT">';
    form.action           = ROUTES.update(id);

    try {
        const res = await fetch(ROUTES.show(id), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const ann = await res.json();

        titleEl.value       = ann.title;
        bodyEl.value        = ann.body;
        priorityEl.value    = ann.priority;
        audienceEl.value    = ann.audience;
        publishEl.checked   = !!ann.is_published;
        counter.textContent = `${ann.body.length} / 2000 characters`;

        toggleAudienceValue();

        if (ann.audience === 'course') {
            document.getElementById('audienceCourseSelect').value = ann.audience_value ?? '';
        } else if (ann.audience === 'year_level') {
            document.getElementById('audienceYearSelect').value = ann.audience_value ?? '';
        }
    } catch {
        alert('Failed to load announcement data.');
        return;
    }

    annModal.show();
}

// ── Open DELETE modal ─────────────────────────────────────────────────────
function openDeleteModal(id, title, actionUrl) {
    document.getElementById('deleteAnnouncementTitle').textContent = title;
    document.getElementById('deleteForm').action = actionUrl;
    delModal.show();
}

// ── Form submit ───────────────────────────────────────────────────────────
form.addEventListener('submit', async function (e) {
    e.preventDefault();

    const btn = document.getElementById('annSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving…';

    const formData = new FormData(form);
    if (!publishEl.checked) formData.set('is_published', '0');

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
            body: formData,
        });

        if (res.redirected) {
            window.location.href = res.url;
        } else if (res.ok) {
            window.location.reload();
        } else {
            alert('Something went wrong. Please check the form.');
        }
    } catch {
        alert('Network error. Please try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<i class="fas fa-paper-plane me-2"></i><span id="annSubmitLabel">${editingId ? 'Save Changes' : 'Publish'}</span>`;
    }
});
</script>
@endpush