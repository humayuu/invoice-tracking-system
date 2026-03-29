<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - InvoiceTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow" style="max-width: 460px; width: 100%;">
            <div class="card-body p-5">

                {{-- Brand --}}
                <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
                    <i class="fa-solid fa-chart-line fs-1 text-primary"></i>
                    <h4 class="fw-bold mb-0">InvoiceTracker</h4>
                </div>

                <hr class="mb-4">

                {{-- Icon --}}
                <div class="text-center mb-4">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 75px; height: 75px;">
                        <i class="fa-solid fa-shield-halved text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>

                {{-- Title --}}
                <h5 class="text-center fw-bold mb-1">Reset Password</h5>
                <p class="text-center text-muted mb-4" style="font-size: 0.9rem;">
                    Enter your new password below to reset your account password.
                </p>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3" role="alert">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span style="font-size: 0.875rem;">{{ $errors->first() }}</span>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    {{-- Hidden Token --}}
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">
                            <i class="fa-solid fa-envelope me-1 text-primary"></i>
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}"
                            class="form-control form-control-lg @error('email') is-invalid @enderror"
                            placeholder="Enter your email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">
                            <i class="fa-solid fa-lock me-1 text-primary"></i>
                            New Password
                        </label>
                        <input type="password" id="password" name="password"
                            class="form-control form-control-lg @error('password') is-invalid @enderror"
                            placeholder="Enter new password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">
                            <i class="fa-solid fa-lock me-1 text-primary"></i>
                            Confirm New Password
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                            placeholder="Confirm new password" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn btn-success w-100 btn-lg mb-3">
                        <i class="fa-solid fa-key me-2"></i>
                        Reset Password
                    </button>

                </form>

                {{-- Divider --}}
                <div class="d-flex align-items-center my-3">
                    <hr class="flex-grow-1">
                    <span class="px-2 text-muted" style="font-size: 0.8rem;">or</span>
                    <hr class="flex-grow-1">
                </div>

                {{-- Back to Login --}}
                <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Back to Login
                </a>

            </div>

            {{-- Footer --}}
            <div class="card-footer text-center text-muted py-3" style="font-size: 0.8rem;">
                &copy; {{ date('Y') }} InvoiceTracker. All rights reserved.
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
