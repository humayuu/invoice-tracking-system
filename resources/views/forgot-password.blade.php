<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - InvoiceTracker</title>
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

                {{-- Lock Icon --}}
                <div class="text-center mb-4">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 75px; height: 75px;">
                        <i class="fa-solid fa-lock text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>

                {{-- Title --}}
                <h5 class="text-center fw-bold mb-1">Forgot Password?</h5>
                <p class="text-center text-muted mb-4" style="font-size: 0.9rem;">
                    No worries! Enter your email address and we'll send you
                    a link to reset your password.
                </p>

                {{-- Success Alert --}}
                @if (session('status'))
                    <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        <span style="font-size: 0.875rem;">
                            {{ session('status') }}
                        </span>
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3" role="alert">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span style="font-size: 0.875rem;">
                            {{ $errors->first() }}
                        </span>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold">
                            <i class="fa-solid fa-envelope me-1 text-primary"></i>
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="form-control form-control-lg @error('email') is-invalid @enderror"
                            placeholder="Enter your email address" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">
                        <i class="fa-solid fa-paper-plane me-2"></i>
                        Send Reset Link
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
