<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Student;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    // ── Web: list page ────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Announcement::with('creator')
            ->orderByDesc('created_at');

        // Simple search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        // Filter by priority
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        $announcements = $query->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total'     => Announcement::count(),
            'urgent'    => Announcement::where('priority', 'urgent')->count(),
            'important' => Announcement::where('priority', 'important')->count(),
            'published' => Announcement::where('is_published', true)->count(),
        ];

        // Available courses for the audience picker
        $courses = \App\Models\Course::orderBy('code')->get(['id', 'code', 'name']);

        return view('admin.announcements.index', compact('announcements', 'stats', 'courses'));
    }

    // ── Web: store ────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
    'title'          => 'required|string|max:255',
    'body'           => 'required|string',
    'priority'       => 'required|in:normal,important,urgent',
    'audience'       => 'required|in:all,course,year_level',
    'audience_value' => 'nullable|string|max:50',
    'is_published'   => 'boolean',
    'due_date'       => 'nullable|date',   // ← add
]);

        // audience_value only makes sense for course / year_level
        if ($data['audience'] === 'all') {
            $data['audience_value'] = null;
        }

        $data['created_by']   = auth()->id();
        $data['is_published'] = $request->boolean('is_published', true);

        DB::transaction(function () use ($data) {
            $announcement = Announcement::create($data);

            if ($announcement->is_published) {
                $this->notifyStudents($announcement);
            }
        });

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement published and students notified.');
    }

    // ── Web: update ───────────────────────────────────────────────────────────

    public function update(Request $request, Announcement $announcement)
    {
       $data = $request->validate([
    'title'          => 'required|string|max:255',
    'body'           => 'required|string',
    'priority'       => 'required|in:normal,important,urgent',
    'audience'       => 'required|in:all,course,year_level',
    'audience_value' => 'nullable|string|max:50',
    'is_published'   => 'boolean',
    'due_date'       => 'nullable|date',   // ← add
]);

        if ($data['audience'] === 'all') {
            $data['audience_value'] = null;
        }

        $wasUnpublished = ! $announcement->is_published;
        $data['is_published'] = $request->boolean('is_published', true);

        $announcement->update($data);

        // If it just got published for the first time (or re-published), notify
        if ($data['is_published'] && $wasUnpublished) {
            $this->notifyStudents($announcement);
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated.');
    }

    // ── Web: destroy ──────────────────────────────────────────────────────────

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted.');
    }

    // ── JSON: single (for edit modal) ─────────────────────────────────────────

    public function show(Announcement $announcement)
    {
        return response()->json($announcement->load('creator'));
    }

    // ── API: student-facing list ──────────────────────────────────────────────

    /**
     * Called by the React Native app at GET /api/announcements
     * Filters by the authenticated student's course / year_level.
     */
    public function apiIndex(Request $request)
    {
        $user    = $request->user();
        $student = $user->student ?? null;

        $query = Announcement::with('creator:id,name')
            ->where('is_published', true)
            ->orderByDesc('created_at');

        // Audience filter
        if ($student) {
            $query->where(function ($q) use ($student) {
                $q->where('audience', 'all')
                  ->orWhere(function ($q2) use ($student) {
                      $q2->where('audience', 'course')
                         ->where('audience_value', $student->course);
                  })
                  ->orWhere(function ($q2) use ($student) {
                      $q2->where('audience', 'year_level')
                         ->where('audience_value', (string) $student->year_level);
                  });
            });
        }

        $paginator = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success'       => true,
            'announcements' => $paginator->items(),
            'pagination'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Push an in-app notification to every student matched by the announcement's
     * audience setting. Uses a chunked query to handle large student counts.
     */
    private function notifyStudents(Announcement $announcement): void
    {
        $priorityLabel = match ($announcement->priority) {
            'urgent'    => '🚨 Urgent',
            'important' => '⭐ Important',
            default     => '📢 New',
        };

        $title = "{$priorityLabel} Announcement";
        $body  = $announcement->title;

        // Build the user-id query
        $userIdQuery = \App\Models\Student::query()
            ->whereHas('user', fn ($q) => $q->where('is_active', true));

        if ($announcement->audience === 'course') {
            $userIdQuery->where('course', $announcement->audience_value);
        } elseif ($announcement->audience === 'year_level') {
            $userIdQuery->where('year_level', $announcement->audience_value);
        }

        $userIdQuery->with('user:id')
            ->chunkById(200, function ($students) use ($title, $body, $announcement) {
                $rows = $students->map(fn ($s) => [
                    'user_id'    => $s->user->id,
                    'type'       => 'announcement',
                    'title'      => $title,
                    'message'    => $body,
                    'data'       => json_encode(['announcement_id' => $announcement->id]),
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray();

                Notification::insert($rows);
            });
    }

public function nearestDue(Request $request)
{
    $user    = $request->user();
    $student = $user->student ?? null;

    $query = Announcement::where('is_published', true)
        ->whereNotNull('due_date')
        ->where(function ($q) {
            // Include overdue ones too so the banner still shows
            $q->where('due_date', '>=', now()->subDays(30))
              ->orWhere('due_date', '>=', now());
        })
        ->orderBy('due_date', 'asc');

    if ($student) {
        $query->where(function ($q) use ($student) {
            $q->where('audience', 'all')
              ->orWhere(function ($q2) use ($student) {
                  $q2->where('audience', 'course')
                     ->where('audience_value', $student->course);
              })
              ->orWhere(function ($q2) use ($student) {
                  $q2->where('audience', 'year_level')
                     ->where('audience_value', (string) $student->year_level);
              });
        });
    }

    $announcement = $query->first();

    return response()->json([
        'success'      => true,
        'announcement' => $announcement ? [
            'id'       => $announcement->id,
            'title'    => $announcement->title,
            'due_date' => $announcement->due_date?->format('Y-m-d'),
        ] : null,
    ]);
}
}