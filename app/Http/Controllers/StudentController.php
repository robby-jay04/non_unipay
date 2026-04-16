<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
    $query = Student::with(['user', 'clearance']);

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('student_no', 'like', "%{$search}%")
              ->orWhereHas('user', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    if ($request->filled('course')) {
        $query->where('course', $request->course);
    }

    if ($request->filled('year_level')) {
        $query->where('year_level', $request->year_level);
    }

    if ($request->filled('clearance_status')) {
        $query->whereHas('clearance', function ($q) use ($request) {
            $q->where('status', $request->clearance_status);
        });
    }

    $students = $query->orderBy('created_at', 'desc')
                      ->paginate(15)
                      ->appends($request->only(['search', 'course', 'year_level', 'clearance_status']));

    $courses          = Student::distinct()->pluck('course')->filter()->values();
    $yearLevels       = Student::distinct()->pluck('year_level')->filter()->sort()->values();
    $clearanceStatuses = ['cleared', 'not_cleared'];

    return view('admin.students', compact('students', 'courses', 'yearLevels', 'clearanceStatuses'));
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
            'public_id' => 'non-unipay/profile_pictures/profile_' . $student->id . '_' . time(),
        ]);

        $publicId = $uploaded->getPublicId();
        $url = "https://res.cloudinary.com/drzsvhpfk/image/upload/{$publicId}";

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
public function destroy($id)
{
    try {
        $student = Student::with('user', 'payments', 'clearance')->findOrFail($id);

        // Optional: Check for related records to prevent accidental deletion
        if ($student->payments()->exists() || $student->clearance()->exists()) {
            return back()->with('error', 'Cannot delete student because they have existing payments or clearance records.');
        }

        // Delete the associated user (this will also delete the student if foreign key cascades)
        if ($student->user) {
            $student->user->delete();
        } else {
            $student->delete();
        }

        return redirect()->route('admin.students')->with('success', 'Student deleted successfully.');
    } catch (\Exception $e) {
        Log::error('Admin delete student error: ' . $e->getMessage());
        return back()->with('error', 'Failed to delete student: ' . $e->getMessage());
    }
}
}