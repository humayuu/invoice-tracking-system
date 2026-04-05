@extends('layout')

@section('title')
    Profile
@endsection

@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
        <h4 class="fw-bold mb-4">Account Settings</h4>

        <div class="row g-4">

            {{-- LEFT COLUMN: Profile Card --}}
            <div class="col-lg-4">
                <div class="card h-100 mb-0 text-center p-4">

                    {{-- Success Message: Photo Updated --}}
                    @if (session('status') === 'photo-updated')
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i> Photo updated successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Avatar Display Section --}}
                    <div class="mx-auto mb-4 position-relative" style="width: 120px; height: 120px;">

                        @if (Auth::user()->isGoogleUser())
                            {{-- Google User: Display Google Avatar --}}
                            <img src="{{ Auth::user()->google_avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff&size=120' }}"
                                alt="Avatar" class="rounded-circle w-100 h-100 object-fit-cover shadow-sm"
                                id="avatarPreview">
                        @else
                            {{-- Regular User: Display Uploaded Photo or Default --}}
                            <img src="{{ Auth::user()->profile_photo_path
                                ? Storage::url(Auth::user()->profile_photo_path)
                                : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff&size=120' }}"
                                alt="Avatar" class="rounded-circle w-100 h-100 object-fit-cover shadow-sm"
                                id="avatarPreview">
                        @endif

                        @if (!Auth::user()->isGoogleUser())
                            {{-- Photo Upload Button (Only for Non-Google Users) --}}
                            <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data"
                                id="photoForm">
                                @csrf
                                <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none">
                                <button type="button"
                                    class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 shadow"
                                    style="width:32px; height:32px; padding:0;"
                                    onclick="document.getElementById('photoInput').click()" title="Upload Photo">
                                    <i class="fa-solid fa-camera"></i>
                                </button>
                            </form>
                        @else
                            {{-- Google Account Badge --}}
                            <span class="position-absolute bottom-0 end-0" title="Connected with Google"
                                style="width:32px; height:32px;">
                                <img src="https://www.google.com/favicon.ico" alt="Google"
                                    style="width:24px; height:24px; border-radius:50%; background:#fff; padding:2px;">
                            </span>
                        @endif

                    </div>

                    {{-- User Info Display --}}
                    <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-muted small mb-0">{{ Auth::user()->email }}</p>

                    @if (Auth::user()->isGoogleUser())
                        <p class="text-muted small mt-1">
                            <i class="fa-brands fa-google me-1 text-danger"></i>
                            Connected via Google
                        </p>
                    @endif

                    {{-- Account Status Badge --}}
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <span class="badge bg-success bg-opacity-10 text-success py-2 px-3">
                            <i class="fa-solid fa-check-circle me-1"></i>Active Account
                        </span>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Settings Form --}}
            <div class="col-lg-8">
                <div class="card h-100 mb-0">
                    {{-- Tab Navigation --}}
                    <div class="card-header border-bottom">
                        <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold" id="info-tab" data-bs-toggle="tab"
                                    data-bs-target="#info-pane" type="button" role="tab">
                                    <i class="fa-solid fa-user me-2"></i>Personal Info
                                </button>
                            </li>

                            {{-- Password Tab (Only for Non-Google Users) --}}
                            @if (!Auth::user()->isGoogleUser())
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold" id="password-tab" data-bs-toggle="tab"
                                        data-bs-target="#password-pane" type="button" role="tab">
                                        <i class="fa-solid fa-lock me-2"></i>Change Password
                                    </button>
                                </li>
                            @endif
                        </ul>
                    </div>

                    {{-- Tab Content --}}
                    <div class="card-body p-4">
                        <div class="tab-content" id="profileTabContent">

                            {{-- TAB 1: Personal Information --}}
                            <div class="tab-pane fade show active" id="info-pane" role="tabpanel">

                                {{-- Success Message: Profile Updated --}}
                                @if (session('status') === 'profile-updated')
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fa-solid fa-circle-check me-2"></i>
                                        Profile updated successfully.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if (Auth::user()->isGoogleUser())
                                    {{-- Google User: Read-Only Info --}}
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        <i class="fa-brands fa-google me-2"></i>
                                        <strong>Google Connected Account:</strong> Your name and email are managed by your
                                        Google account.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Full Name</label>
                                            <input type="text" class="form-control" value="{{ Auth::user()->name }}"
                                                disabled>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Email Address</label>
                                            <input type="email" class="form-control" value="{{ Auth::user()->email }}"
                                                disabled>
                                        </div>
                                    </div>
                                @else
                                    {{-- Regular User: Editable Info Form --}}
                                    <form method="POST" action="/user/profile-information">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-3">
                                            {{-- Full Name Field --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Full Name</label>
                                                <input type="text" name="name"
                                                    class="form-control @error('name', 'updateProfileInformation') is-invalid @enderror"
                                                    value="{{ old('name', Auth::user()->name) }}" autocomplete="name"
                                                    autofocus required>
                                                @error('name', 'updateProfileInformation')
                                                    <div class="invalid-feedback d-block">
                                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            {{-- Email Field --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Email Address</label>
                                                <input type="email" name="email"
                                                    class="form-control @error('email', 'updateProfileInformation') is-invalid @enderror"
                                                    value="{{ old('email', Auth::user()->email) }}" autocomplete="email"
                                                    required>
                                                @error('email', 'updateProfileInformation')
                                                    <div class="invalid-feedback d-block">
                                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            {{-- Save Button --}}
                                            <div class="col-12 mt-4">
                                                <button type="submit" class="btn btn-primary px-4">
                                                    <i class="fa-solid fa-check me-2"></i>Save Changes
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>

                            {{-- TAB 2: Change Password (Only for Non-Google Users) --}}
                            @if (!Auth::user()->isGoogleUser())
                                <div class="tab-pane fade" id="password-pane" role="tabpanel">

                                    {{-- Success Message: Password Updated --}}
                                    @if (session('status') === 'password-updated')
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="fa-solid fa-circle-check me-2"></i>
                                            Password changed successfully.
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    {{-- Change Password Form --}}
                                    <form method="POST" action="/user/password">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-3">
                                            {{-- Current Password Field --}}
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Current Password</label>
                                                <input type="password" name="current_password"
                                                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                                    autocomplete="current-password" required>
                                                @error('current_password', 'updatePassword')
                                                    <div class="invalid-feedback d-block">
                                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            {{-- New Password Field --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">New Password</label>
                                                <input type="password" name="password"
                                                    class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                                    autocomplete="new-password" required>
                                                @error('password', 'updatePassword')
                                                    <div class="invalid-feedback d-block">
                                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            {{-- Confirm Password Field --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Confirm New Password</label>
                                                <input type="password" name="password_confirmation" class="form-control"
                                                    autocomplete="new-password" required>
                                            </div>

                                            {{-- Update Button --}}
                                            <div class="col-12 mt-4">
                                                <button type="submit" class="btn btn-primary px-4">
                                                    <i class="fa-solid fa-check me-2"></i>Update Password
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript: Auto-show password tab on validation errors --}}
    @if ($errors->updatePassword->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const passwordTab = new bootstrap.Tab(document.getElementById('password-tab'));
                passwordTab.show();
            });
        </script>
    @endif

    {{-- JavaScript: Photo preview and auto-submit on selection --}}
    @if (!Auth::user()->isGoogleUser())
        <script>
            document.getElementById('photoInput')?.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    // Validate file is an image
                    if (!file.type.startsWith('image/')) {
                        alert('Please select a valid image file.');
                        return;
                    }

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = e => {
                        document.getElementById('avatarPreview').src = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    // Auto-submit form
                    document.getElementById('photoForm').submit();
                }
            });
        </script>
    @endif

@endsection
