@extends('admin.layouts.app')

@section('title', 'Students')

@section('content')
<h2 class="mb-4">Student Management</h2>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Students</h5>
        <form method="GET" class="d-flex w-25" action="{{ route('admin.students') }}">
            <input type="search" name="search" class="form-control me-2" 
                   placeholder="Search students..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student No</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>Clearance Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>{{ $student->student_no }}</td>
                        <td>{{ $student->user->name }}</td>
                        <td>{{ $student->course }}</td>
                        <td>{{ $student->year_level }}</td>
                        <td>{{ ucfirst($student->clearance_status) }}</td>
                        <td>
    <button class="btn btn-sm btn-info view-student" data-id="{{ $student->id }}">
        View
    </button>
</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No students found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>
<!-- Student Details Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentModalLabel">Student Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Student No:</strong> <span id="modalStudentNo"></span></p>
        <p><strong>Name:</strong> <span id="modalStudentName"></span></p>
        <p><strong>Email:</strong> <span id="modalStudentEmail"></span></p>
        <p><strong>Course:</strong> <span id="modalStudentCourse"></span></p>
        <p><strong>Year Level:</strong> <span id="modalStudentYear"></span></p>
        <p><strong>Clearance Status:</strong> <span id="modalStudentStatus"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentModal = new bootstrap.Modal(document.getElementById('studentModal'));

    document.querySelectorAll('.view-student').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.dataset.id;

            fetch(`/admin/students/${studentId}/json`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalStudentNo').textContent = data.student_no;
                    document.getElementById('modalStudentName').textContent = data.name;
                    document.getElementById('modalStudentEmail').textContent = data.email;
                    document.getElementById('modalStudentCourse').textContent = data.course;
                    document.getElementById('modalStudentYear').textContent = data.year_level;
                    document.getElementById('modalStudentStatus').textContent = data.clearance_status;

                    studentModal.show();
                })
                .catch(err => console.error(err));
        });
    });
});
</script>

@endsection
