@extends('admin.layouts.app')
@section('title', 'Edit Admin')

@push('styles')
<style>
    .form-card { background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 2rem; }
    .form-card .section-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #adb5bd; margin-bottom: 1rem; margin-top: 1.5rem; }
    .form-label { font-weight: 600; font-size: 0.88rem; color: #495057; }
    .form-control, .form-select { border-radius: 12px; border: 1.5px solid #e0e0e0; padding: 0.65rem 1rem; font-size: 0.92rem; }
    .form-control:focus, .form-select:focus { border-color: #0f3c91; box-shadow: 0 0 0 3px rgba(15,60,145,0.1); }
    .form-control.is-invalid { border-color: #dc3545; }
    .input-group-text { background: #f8f9fb; border-radius: 0 12px 12px 0; border: 1.5px solid #e0e0e0; border-left: none; color: #adb5bd; cursor: pointer; }
    .input-group .form-control { border-right: none; border-radius: 12px 0 0 12px; }
    .btn-submit { background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border: none; border-radius: 30px; padding: 0.7rem 2rem; font-weight: 600; }
    .btn-submit:hover { opacity: 0.9; color: white; }
    .btn-cancel { border-radius: 30px; padding: 0.7rem 1.5rem; font-weight: 500; color: #6c757d; background: #f0f0f0; border: none; }
    .page-breadcrumb { font-size: 0.85rem; color: #6c757d; margin-bottom: 1rem; }
    .page-breadcrumb a { color: #0f3c91; text-decoration: none; }
    .pw-hint { font-size: 0.8rem; color: #adb5bd; margin-top: 4px; }
    .page-header { background: linear-gradient(135deg, #0f3c91, #1a4da8); border-radius: 20px; color: white; padding: 1.5rem 2rem; margin-bottom: 1.5rem; }
    .page-header h2 { font-weight: 700; margin: 0; font-size: 1.6rem; }
    .page-header p { color: rgba(255,255,255,0.8); margin: 0; font-size: 0.9rem; }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fas fa-user-edit me-2"></i>Edit Admin Account</h2>
            <p>Update the details for <strong>{{ $user->name }}</strong>.</p>
        </div>
        <a href="{{ route('admin.superadmin.admins.index') }}" class="btn btn-light rounded-pill px-4">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="form-card">

    @if($errors->any())
        <div class="alert alert-danger rounded-4">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li style="font-size:0.88rem;">{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.superadmin.admins.update', $user->id) }}">
        @csrf @method('PUT')

        <div class="row g-4">
            <div class="col-12 col-md-6">
                <div class="section-title">Account Details</div>
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="superadmin" {{ old('role', $user->role) === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="section-title">Change Password</div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank to keep current password">
                        
                  
                    <div class="pw-hint">Min. 8 characters, upper &amp; lowercase, at least one number.</div>
                    @error('password')<div class="text-danger mt-1" style="font-size:0.82rem;">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                   
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repeat new password">
                      
                    
                </div>
            </div>
        </div>

        <hr class="my-4">
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-submit"><i class="fas fa-save me-1"></i> Save Changes</button>
            <a href="{{ route('admin.superadmin.admins.index') }}" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function togglePw(fieldId, icon) {
    const input = document.getElementById(fieldId);
    const i = icon.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; i.classList.replace('fa-eye','fa-eye-slash'); }
    else { input.type = 'password'; i.classList.replace('fa-eye-slash','fa-eye'); }
}
</script>
@endpush