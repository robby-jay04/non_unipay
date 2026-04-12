<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
class StudentController extends Controller
{
 // StudentController.php - profile()
public function profile(Request $request) {
    $student = $request->user()
        ->student()
        ->with(['user', 'clearance', 'payments'])
        ->first();

    if (!$student) {
        return response()->json(['message' => 'Student profile not found'], 404);
    }

    // profile_picture is now a full Cloudinary URL, no need to convert
    return response()->json($student);
}
public function index(Request $request)
{
    $query = Student::with('user');

    // Apply search filter (name or student number)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('student_no', 'like', "%{$search}%")
              ->orWhereHas('user', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    // Apply course filter
    if ($request->filled('course')) {
        $query->where('course', $request->course);
    }

    // Paginate results (15 per page)
    $students = $query->orderBy('created_at', 'desc')
                      ->paginate(15)
                      ->appends($request->only(['search', 'course'])); // preserve filters in pagination links

    // Get distinct courses for the dropdown
    $courses = Student::distinct()->pluck('course')->filter()->values();

    return view('admin.students', compact('students', 'courses'));
}


  public function updateProfile(Request $request)
{
    $student = $request->user()->student;

    if (!$student) {
        return response()->json(['message' => 'Student profile not found'], 404);
    }

    // ✅ Cooldown check — once every 3 days
    $cooldownDays = 3;
    if ($student->last_profile_update) {
        $nextAllowed = \Carbon\Carbon::parse($student->last_profile_update)
                        ->addDays($cooldownDays);
        if (now()->lt($nextAllowed)) {
            $daysLeft = now()->diffInDays($nextAllowed, false);
            return response()->json([
                'success' => false,
                'message' => "You can only update your profile once every {$cooldownDays} days. Please try again in {$daysLeft} day(s).",
                'next_allowed' => $nextAllowed->toDateString(),
            ], 429);
        }
    }

    $validated = $request->validate([
        'contact'    => 'sometimes|string|unique:students,contact,' . $student->id,
        'course'     => 'sometimes|string',
        'year_level' => 'sometimes|integer|min:1|max:5',
        'email'      => 'sometimes|email|unique:users,email,' . $request->user()->id,
    ], [
        'contact.unique' => 'This contact number is already used by another student.',
        'email.unique'   => 'This email is already registered to another account.',
        'email.email'    => 'Please enter a valid email address.',
    ]);

    // Update email on users table if provided
    if (isset($validated['email'])) {
        $request->user()->update(['email' => $validated['email']]);
        unset($validated['email']);
    }

    // ✅ Save update timestamp
    $validated['last_profile_update'] = now();

    $student->update($validated);
    $student->load('user');

    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully.',
        'next_allowed_update' => now()->addDays($cooldownDays)->toDateString(),
        'student' => $student,
    ]);
}
    public function paymentHistory(Request $request)
    {
        $student = $request->user()->student;
        $payments = $student->payments()
            ->with('transaction')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($payments);
    }
    public function uploadProfilePicture(Request $request)
{
    $request->validate([
        'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $student = $request->user()->student;

    $cooldownDays = 1;
    if ($student->last_picture_update) {
        $nextAllowed = \Carbon\Carbon::parse($student->last_picture_update)
                        ->addDays($cooldownDays);
        if (now()->lt($nextAllowed)) {
            $daysLeft = now()->diffInDays($nextAllowed, false);
            return response()->json([
                'success' => false,
                'message' => "You can only update your profile picture once every {$cooldownDays} days. Please try again in {$daysLeft} day(s).",
            ], 429);
        }
    }

    if ($request->hasFile('profile_picture')) {
        $uploaded = cloudinary()->upload($request->file('profile_picture')->getRealPath(), [
            'folder' => 'non-unipay/profile_pictures',
            'public_id' => 'profile_' . $student->id . '_' . time(),
        ]);

        $url = $uploaded->getSecurePath();

        $student->profile_picture = $url;
        $student->last_picture_update = now();
        $student->save();

        return response()->json([
            'success' => true,
            'profile_picture' => $url,
            'message' => 'Profile picture updated',
        ]);
    }

    return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
}
}