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

                    @if (session('status') === 'photo-updated')
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i> Photo updated successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mx-auto mb-4 position-relative" style="width: 120px; height: 120px;">
                        <img src="{{ Auth::user()->profile_photo_path
                            ? Storage::url(Auth::user()->profile_photo_path)
                            : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff&size=120' }}"
                            alt="Avatar" class="rounded-circle w-100 h-100 object-fit-cover shadow-sm" id="avatarPreview">

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
                    </div>

                    <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-muted small mb-0">{{ Auth::user()->email }}</p>

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
                    <div class="card-header border-bottom">
                        <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold" id="info-tab" data-bs-toggle="tab"
                                    data-bs-target="#info-pane" type="button" role="tab">
                                    <i class="fa-solid fa-user me-2"></i>Personal Info
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="password-tab" data-bs-toggle="tab"
                                    data-bs-target="#password-pane" type="button" role="tab">
                                    <i class="fa-solid fa-lock me-2"></i>Change Password
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="profileTabContent">

                            <div class="tab-pane fade show active" id="info-pane" role="tabpanel">

                                @if (session('status') === 'profile-updated')
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fa-solid fa-circle-check me-2"></i>
                                        Profile updated successfully.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form method="POST" action="/user/profile-information">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
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

                                        <div class="col-12 mt-4">
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="fa-solid fa-check me-2"></i>Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="password-pane" role="tabpanel">

                                @if (session('status') === 'password-updated')
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fa-solid fa-circle-check me-2"></i>
                                        Password changed successfully.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form method="POST" action="/user/password">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
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

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Confirm New Password</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                autocomplete="new-password" required>
                                        </div>

                                        <div class="col-12 mt-4">
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="fa-solid fa-check me-2"></i>Update Password
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->updatePassword->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const passwordTab = new bootstrap.Tab(document.getElementById('password-tab'));
                passwordTab.show();
            });
        </script>
    @endif

    <script>
        document.getElementById('photoInput')?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);

                document.getElementById('photoForm').submit();
            }
        });
    </script>

@endsection
