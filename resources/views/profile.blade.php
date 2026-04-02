@extends('layout')

@section('title')
    Profile
@endsection

@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
        <h4 class="fw-bold mb-4">Account Settings</h4>

        <div class="row g-4">

            {{-- LEFT: Avatar Card --}}
            <div class="col-lg-4">
                <div class="card h-100 mb-0 text-center p-4">

                    @if (session('status') === 'photo-updated')
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i> Photo updated.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mx-auto mb-4 position-relative" style="width: 120px; height: 120px;">

                        {{-- Google user ka avatar Google se aayega --}}
                        @if (Auth::user()->isGoogleUser())
                            <img src="{{ Auth::user()->google_avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff&size=120' }}"
                                alt="Avatar" class="rounded-circle w-100 h-100 object-fit-cover shadow-sm"
                                id="avatarPreview">
                        @else
                            <img src="{{ Auth::user()->profile_photo_path
                                ? Storage::url(Auth::user()->profile_photo_path)
                                : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff&size=120' }}"
                                alt="Avatar" class="rounded-circle w-100 h-100 object-fit-cover shadow-sm"
                                id="avatarPreview">
                        @endif

                        {{-- Camera button sirf non-google users ke liye --}}
                        @if (!Auth::user()->isGoogleUser())
                            <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data"
                                id="photoForm">
                                @csrf
                                <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none">
                                <button type="button"
                                    class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 shadow"
                                    style="width:32px; height:32px; padding:0;"
                                    onclick="document.getElementById('photoInput').click()">
                                    <i class="fa-solid fa-camera"></i>
                                </button>
                            </form>
                        @else
                            {{-- Google badge --}}
                            <span class="position-absolute bottom-0 end-0" title="Google Account"
                                style="width:32px; height:32px;">
                                <img src="https://www.google.com/favicon.ico"
                                    style="width:24px; height:24px; border-radius:50%; background:#fff; padding:2px;">
                            </span>
                        @endif

                    </div>

                    <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-muted small mb-0">{{ Auth::user()->email }}</p>

                    @if (Auth::user()->isGoogleUser())
                        <p class="text-muted small mt-1">
                            <i class="fa-brands fa-google me-1 text-danger"></i>
                            Google se connected
                        </p>
                    @endif

                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <span class="badge bg-success bg-opacity-10 text-success py-2 px-3">Active Account</span>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Tabs Card --}}
            <div class="col-lg-8">
                <div class="card h-100 mb-0">
                    <div class="card-header border-bottom">
                        <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold" id="info-tab" data-bs-toggle="tab"
                                    data-bs-target="#info-pane" type="button" role="tab">
                                    Personal Info
                                </button>
                            </li>

                            {{-- Password tab sirf normal users ke liye --}}
                            @if (!Auth::user()->isGoogleUser())
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold" id="password-tab" data-bs-toggle="tab"
                                        data-bs-target="#password-pane" type="button" role="tab">
                                        Change Password
                                    </button>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="profileTabContent">

                            {{-- ===== TAB 1: Personal Info ===== --}}
                            <div class="tab-pane fade show active" id="info-pane" role="tabpanel">

                                @if (session('status') === 'profile-updated')
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fa-solid fa-circle-check me-2"></i>
                                        Profile updated successfully.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                {{-- Google user ko name/email edit nahi karne dete --}}
                                @if (Auth::user()->isGoogleUser())
                                    <div class="alert alert-info">
                                        <i class="fa-brands fa-google me-2"></i>
                                        Aapka account Google se connected hai. Name aur Email Google account se manage hota
                                        hai.
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" value="{{ Auth::user()->name }}"
                                                disabled>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" value="{{ Auth::user()->email }}"
                                                disabled>
                                        </div>
                                    </div>
                                @else
                                    <form method="POST" action="/user/profile-information">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" name="name"
                                                    class="form-control @error('name', 'updateProfileInformation') is-invalid @enderror"
                                                    value="{{ old('name', Auth::user()->name) }}" autocomplete="name"
                                                    autofocus>
                                                @error('name', 'updateProfileInformation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email Address</label>
                                                <input type="email" name="email"
                                                    class="form-control @error('email', 'updateProfileInformation') is-invalid @enderror"
                                                    value="{{ old('email', Auth::user()->email) }}" autocomplete="email">
                                                @error('email', 'updateProfileInformation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 mt-4">
                                                <button type="submit" class="btn btn-primary px-4">
                                                    Save Changes
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>

                            {{-- ===== TAB 2: Change Password (sirf normal users) ===== --}}
                            @if (!Auth::user()->isGoogleUser())
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
                                                <label class="form-label">Current Password</label>
                                                <input type="password" name="current_password"
                                                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                                    autocomplete="current-password">
                                                @error('current_password', 'updatePassword')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">New Password</label>
                                                <input type="password" name="password"
                                                    class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                                    autocomplete="new-password">
                                                @error('password', 'updatePassword')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Confirm New Password</label>
                                                <input type="password" name="password_confirmation" class="form-control"
                                                    autocomplete="new-password">
                                            </div>
                                            <div class="col-12 mt-4">
                                                <button type="submit" class="btn btn-primary px-4">
                                                    Update Password
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

    {{-- Password tab reopen on error --}}
    @if ($errors->updatePassword->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tab = new bootstrap.Tab(document.getElementById('password-tab'));
                tab.show();
            });
        </script>
    @endif

    {{-- Photo preview + auto submit --}}
    @if (!Auth::user()->isGoogleUser())
        <script>
            document.getElementById('photoInput')?.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        document.getElementById('avatarPreview').src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                    document.getElementById('photoForm').submit();
                }
            });
        </script>
    @endif

@endsection
