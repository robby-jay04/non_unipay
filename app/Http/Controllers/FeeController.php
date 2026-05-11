<?php

namespace App\Http\Controllers;

use App\Models\ExamPeriod;
use App\Models\Fee;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentFee;
use App\Services\ClearanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Notification;
class FeeController extends Controller
{
    

    protected $clearanceService;

    public function __construct(ClearanceService $clearanceService)
    {
        $this->clearanceService = $clearanceService;
    }

    public function index(Request $request)
{
    $query = Fee::query();

    // Apply filters
    if ($request->filled('school_year')) {
        $query->where('school_year_id', $request->school_year);
    }

    if ($request->filled('semester')) {
        $query->where('semester_id', $request->semester);
    }

    if ($request->filled('exam_period')) {
        $query->where('exam_period', $request->exam_period);
    }

    $fees = $query->orderBy('school_year_id', 'desc')
                  ->orderBy('type', 'asc')
                  ->get();

    // ✅ Correct semesters for the selected school year
    $semesters = Semester::when($request->school_year, function ($q) use ($request) {
            $q->where('school_year_id', $request->school_year);
        }, function ($q) {
            // Default to current school year's semesters if no filter
            $currentSY = SchoolYear::where('is_current', true)->first();
            if ($currentSY) {
                $q->where('school_year_id', $currentSY->id);
            }
        })
        ->orderBy('name')
        ->get();

    $schoolYears = SchoolYear::orderBy('name', 'desc')->get();
    $examPeriods = ['Prelim', 'Midterm', 'Semi-Final', 'Finals'];

    return view('admin.fees.index', compact('fees', 'schoolYears', 'semesters', 'examPeriods'));
}
    public function getTotalFees()
    {
        $total = Fee::currentSchoolYear()->sum('amount');
        return response()->json([
            'success'   => true,
            'total'     => $total,
            'formatted' => '₱' . number_format($total, 2),
        ]);
    }

    public function show($id)
    {
        $fee = Fee::findOrFail($id);
        return response()->json(['success' => true, 'fee' => $fee]);
    }

    public function getByType($type)
    {
        $fees  = Fee::currentSchoolYear()->byType($type)->get();
        $total = $fees->sum('amount');
        return response()->json([
            'success' => true,
            'type'    => $type,
            'fees'    => $fees,
            'total'   => $total,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'type'        => 'required|in:tuition,miscellaneous,exam',
            'semester'    => 'nullable|string',
            'school_year' => 'required|string',
            'course'      => 'nullable|string',
        ]);

        Fee::create($validated);

        return redirect()->route('admin.fees.index')
                         ->with('success', 'Fee created successfully!');
    }

    public function create()
{
    $schoolYears       = SchoolYear::orderBy('name', 'desc')->get();
    $currentSchoolYear = SchoolYear::where('is_current', true)->first();
    $currentSemester   = Semester::where('is_current', true)->first();
    $courses           = Course::orderBy('code')->get();   // ✅ dynamic from DB

    return view('admin.fees.index', compact(
        'schoolYears',
        'currentSchoolYear',
        'currentSemester',
        'courses'
    ));
}
public function adminIndex(Request $request)
{
    $query = Fee::with(['schoolYear', 'semester']);

    // Apply filters
    if ($request->filled('school_year')) {
        $query->where('school_year_id', $request->school_year);
    }

    if ($request->filled('semester')) {
        $query->where('semester_id', $request->semester);
    }

    if ($request->filled('exam_period')) {
        $query->where('exam_period', $request->exam_period);
    }

    $fees = $query->orderBy('school_year_id', 'desc')
                  ->orderBy('type', 'asc')
                  ->get();

    // ✅ Correct semesters for the selected school year
    $semesters = Semester::when($request->school_year, function ($q) use ($request) {
            $q->where('school_year_id', $request->school_year);
        }, function ($q) {
            $currentSY = SchoolYear::where('is_current', true)->first();
            if ($currentSY) {
                $q->where('school_year_id', $currentSY->id);
            }
        })
        ->orderBy('name')
        ->get();

    $schoolYears = SchoolYear::orderBy('name', 'desc')->get();
    $examPeriods = ['Prelim', 'Midterm', 'Semi-Final', 'Finals'];
    $courses = Course::orderBy('code')->get();

    // ✅ Add current school year and semester for modal pre-selection
    $currentSchoolYear = SchoolYear::where('is_current', true)->first();
    $currentSemester = Semester::where('is_current', true)->first();

    return view('admin.fees.index', compact(
        'fees', 
        'schoolYears', 
        'semesters', 
        'examPeriods', 
        'courses', 
        'currentSchoolYear',
        'currentSemester'
    ));
}

public function storeWeb(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0',
            'type'           => 'required|in:tuition,miscellaneous,exam',
            'course'         => 'nullable|string',
            'semester_id'    => 'nullable|exists:semesters,id',
            'school_year_id' => 'required|exists:school_years,id',
            'exam_period_id' => 'nullable|exists:exam_periods,id',
        ]);

        $semesterId   = !empty($validated['semester_id'])    ? $validated['semester_id']    : null;
        $examPeriodId = !empty($validated['exam_period_id']) ? $validated['exam_period_id'] : null;

        $schoolYear = SchoolYear::find($validated['school_year_id']);
        $semester   = $semesterId   ? Semester::find($semesterId)     : null;
        $examPeriod = $examPeriodId ? ExamPeriod::find($examPeriodId) : null;

        Fee::create([
            'name'           => $validated['name'],
            'amount'         => $validated['amount'],
            'type'           => $validated['type'],
            'course'         => !empty($validated['course']) ? $validated['course'] : null,
            'school_year'    => $schoolYear->name,
            'semester'       => $semester?->name,
            'semester_id'    => $semester?->id,
            'school_year_id' => $schoolYear->id,
            'exam_period'    => $examPeriod?->name,
            'exam_period_id' => $examPeriod?->id,
        ]);

        // ✅ Re-sync all clearances since a new fee was added
        $this->clearanceService->bulkUpdateClearances();

        return redirect()->route('admin.fees.index')
                         ->with('success', 'Fee created successfully.');
    }

    public function edit(Fee $fee)
{
    $schoolYears       = SchoolYear::orderBy('name', 'desc')->get();
    $currentSchoolYear = SchoolYear::where('is_current', true)->first();
    $courses           = Course::orderBy('code')->get();   // ✅ dynamic from DB

    return view('admin.fees.index', compact(
        'fee',
        'schoolYears',
        'currentSchoolYear',
        'courses'
    ));
}

     public function updateWeb(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0',
            'type'           => 'required|in:tuition,miscellaneous,exam',
            'semester_id'    => 'nullable|exists:semesters,id',
            'school_year_id' => 'required|exists:school_years,id',
            'exam_period_id' => 'nullable|exists:exam_periods,id',
            'course'         => 'nullable|string',
        ]);

        $semesterId   = !empty($validated['semester_id'])    ? $validated['semester_id']    : null;
        $examPeriodId = !empty($validated['exam_period_id']) ? $validated['exam_period_id'] : null;

        $semester   = $semesterId   ? Semester::find($semesterId)     : null;
        $schoolYear = SchoolYear::find($validated['school_year_id']);
        $examPeriod = $examPeriodId ? ExamPeriod::find($examPeriodId) : null;

        // ── Capture old amount before update for notification message ─────────
        $oldAmount = (float) $fee->amount;
        $oldName   = $fee->name;

        $fee->update([
            'name'           => $validated['name'],
            'amount'         => $validated['amount'],
            'type'           => $validated['type'],
            'semester'       => $semester?->name,
            'school_year'    => $schoolYear->name,
            'semester_id'    => $semester?->id,
            'school_year_id' => $schoolYear->id,
            'exam_period'    => $examPeriod?->name,
            'exam_period_id' => $examPeriod?->id,
            'course'         => !empty($validated['course']) ? $validated['course'] : null,
        ]);

        $this->clearanceService->bulkUpdateClearances();

        // ── Notify affected students only if the amount changed ───────────────
        $newAmount = (float) $validated['amount'];

        if ($oldAmount !== $newAmount) {
            $this->notifyStudentsOfFeeChange(
                fee: $fee,
                feeName: $validated['name'],
                oldAmount: $oldAmount,
                newAmount: $newAmount,
                schoolYearId: $schoolYear->id,
                semesterId: $semester?->id,
                course: !empty($validated['course']) ? $validated['course'] : null,
            );
        }

        return redirect()->route('admin.fees.index')
                         ->with('success', 'Fee updated successfully.');
    }

    /**
     * Notify students enrolled in the same school year / semester / course
     * that a fee amount has been updated.
     */
    private function notifyStudentsOfFeeChange(
        Fee $fee,
        string $feeName,
        float $oldAmount,
        float $newAmount,
        int $schoolYearId,
        ?int $semesterId,
        ?string $course,
    ): void {
        // Build query for students who belong to this fee's scope
        $studentQuery = Student::whereHas('user') // must have a user account
            ->when($course, fn($q) => $q->where('course', $course));

        $students = $studentQuery->with('user')->get();

        $direction = $newAmount > $oldAmount ? 'increased' : 'decreased';

        $message = "The fee \"{$feeName}\" has been {$direction} from "
                 . '₱' . number_format($oldAmount, 2)
                 . ' to ₱' . number_format($newAmount, 2) . '.';

        // Only notify students who have this fee in a pending/partial state
        // i.e. students who haven't fully paid it yet
        $fullyPaidStudentIds = DB::table('fee_payment')
            ->join('payments', 'payments.id', '=', 'fee_payment.payment_id')
            ->where('payments.status', 'paid')
            ->where('fee_payment.fee_id', $fee->id)
            ->groupBy('payments.student_id')
            ->havingRaw('SUM(fee_payment.amount) >= ?', [$newAmount])
            ->pluck('payments.student_id')
            ->toArray();

        $notifications = [];
        $now = now();

        foreach ($students as $student) {
            // Skip students who have already fully paid this fee at the new amount
            if (in_array($student->id, $fullyPaidStudentIds)) {
                continue;
            }

            $notifications[] = [
                'user_id'    => $student->user->id,
                'type'       => 'fee_updated',
                'message'    => $message,
                'data'       => json_encode([
                    'fee_id'     => $fee->id,
                    'fee_name'   => $feeName,
                    'old_amount' => $oldAmount,
                    'new_amount' => $newAmount,
                ]),
                'is_read'    => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($notifications)) {
            Notification::insert($notifications); // bulk insert — efficient
        }
    }


    
    public function update(Request $request, $id)
    {
        $fee       = Fee::findOrFail($id);
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'amount'      => 'sometimes|numeric|min:0',
            'type'        => 'sometimes|in:tuition,miscellaneous,exam',
            'semester'    => 'nullable|string',
            'school_year' => 'sometimes|string',
            'course'      => 'nullable|string',
        ]);

        $fee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fee updated successfully',
            'fee'     => $fee,
        ]);
    }

   public function destroy(Fee $fee)
{
    try {

        // Delete related fee_payment records
        DB::table('fee_payment')
            ->where('fee_id', $fee->id)
            ->delete();

        // Delete related student fees
        StudentFee::where('fee_id', $fee->id)
            ->delete();

        // Delete notifications if needed
        Notification::where('type', 'fee_updated')
            ->whereJsonContains('data->fee_id', $fee->id)
            ->delete();

        // Finally delete fee
        $fee->delete();

        // Recompute clearances
        $this->clearanceService->bulkUpdateClearances();

        return redirect()
            ->route('admin.fees.index')
            ->with('success', 'Fee deleted successfully.');

    } catch (\Exception $e) {

        return redirect()
            ->back()
            ->with('error', $e->getMessage());
    }
}

    
   public function breakdown()
    {
        $student           = auth()->user()->student;
        $currentSemester   = Semester::where('is_current', true)->first();
        $currentSchoolYear = SchoolYear::where('is_current', true)->first();
        $currentExamPeriod = $currentSemester
                                ? ExamPeriod::where('semester_id', $currentSemester->id)
                                            ->where('is_current', true)
                                            ->first()
                                : null;
 
        if (!$currentSemester || !$currentSchoolYear) {
            return response()->json([
                'success' => false,
                'message' => 'No active semester or school year set.',
            ], 404);
        }
 
        $fees = Fee::where('school_year_id', $currentSchoolYear->id)
                   ->where('semester_id', $currentSemester->id)
                   ->where(function ($q) use ($currentExamPeriod) {
                       if ($currentExamPeriod) {
                           $q->whereNull('exam_period_id')
                             ->orWhere('exam_period_id', $currentExamPeriod->id);
                       } else {
                           $q->whereNull('exam_period_id');
                       }
                   })
                   ->where(function ($q) use ($student) {
                       $q->where('course', $student->course)
                         ->orWhereNull('course');
                   })
                   ->get();
 
        if ($fees->isEmpty()) {
            return response()->json([
                'success'   => true,
                'breakdown' => [
                    'tuition'           => ['fees' => [], 'total' => 0],
                    'miscellaneous'     => ['fees' => [], 'total' => 0],
                    'exam'              => ['fees' => [], 'total' => 0],
                    'grand_total'       => 0,
                    'total_paid'        => 0,
                    'remaining_balance' => 0,
                    'status'            => 'no_fees',
                ],
            ]);
        }
 
        $feeIds = $fees->pluck('id');
 
        // ── Per-fee paid amounts from confirmed payments only ─────────────────
        // Uses fee_payment pivot table's `amount` column (what was paid per fee per payment)
        $paidPerFee = DB::table('fee_payment')
            ->join('payments', 'payments.id', '=', 'fee_payment.payment_id')
            ->where('payments.student_id', $student->id)
            ->where('payments.status', 'paid')
            ->whereIn('fee_payment.fee_id', $feeIds)
            ->groupBy('fee_payment.fee_id')
            ->select('fee_payment.fee_id', DB::raw('SUM(fee_payment.amount) as total_paid'))
            ->pluck('total_paid', 'fee_id')
            ->map(fn($v) => (float) $v);
 
        // ── Annotate each fee with paid_amount, remaining, payment_status ─────
        $annotatedFees = $fees->map(function ($fee) use ($paidPerFee) {
            $currentAmount = (float) $fee->amount;
            $paidAmount    = (float) ($paidPerFee[$fee->id] ?? 0);
            $remaining     = max($currentAmount - $paidAmount, 0);
 
            if ($paidAmount <= 0) {
                $feeStatus = 'unpaid';
            } elseif ($remaining <= 0) {
                $feeStatus = 'paid';
            } else {
                $feeStatus = 'partial'; // fee amount was raised after payment
            }
 
            return array_merge($fee->toArray(), [
                'paid_amount'    => $paidAmount,
                'remaining'      => $remaining,
                'payment_status' => $feeStatus,
            ]);
        });
 
        // ── Grand totals ──────────────────────────────────────────────────────
        $grandTotal       = (float) $fees->sum('amount');
        $totalPaid        = (float) $paidPerFee->sum();
        $remainingBalance = max($grandTotal - $totalPaid, 0);
 
        if ($totalPaid <= 0) {
            $status = 'pending';
        } elseif ($remainingBalance <= 0) {
            $status = 'cleared';
        } else {
            $status = 'partial';
        }
 
        $byType = $annotatedFees->groupBy('type');
 
        $breakdown = [
            'tuition' => [
                'fees'  => $byType->get('tuition', collect())->values(),
                'total' => (float) ($byType->get('tuition', collect())->sum('amount')),
            ],
            'miscellaneous' => [
                'fees'  => $byType->get('miscellaneous', collect())->values(),
                'total' => (float) ($byType->get('miscellaneous', collect())->sum('amount')),
            ],
            'exam' => [
                'fees'  => $byType->get('exam', collect())->values(),
                'total' => (float) ($byType->get('exam', collect())->sum('amount')),
            ],
            'grand_total'       => $grandTotal,
            'total_paid'        => $totalPaid,
            'remaining_balance' => $remainingBalance,
            'status'            => $status,
        ];
 
        return response()->json(['success' => true, 'breakdown' => $breakdown]);
    }
}