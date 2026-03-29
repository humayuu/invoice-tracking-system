<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - InvoiceTracker</title>
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

                {{-- Envelope Icon --}}
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 75px; height: 75px;">
                        <i class="fa-solid fa-envelope-circle-check text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>

                {{-- Title --}}
                <h5 class="text-center fw-bold mb-1">Verify Your Email Address</h5>
                <p class="text-center text-muted mb-4" style="font-size: 0.9rem;">
                    Thanks for signing up! Before getting started, please verify
                    your email address by clicking the link we just sent to you.
                </p>

                {{-- Success Alert --}}
                @if (session('status') == 'verification-sent')
                    <div class="alert alert-success d-flex align-items-center gap-2 py-2" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        <span style="font-size: 0.875rem;">
                            A new verification link has been sent to your email address.
                        </span>
                    </div>
                @endif

                {{-- Info Note --}}
                <div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-4">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span style="font-size: 0.85rem;">
                        Didn't receive the email? Check your spam folder or click resend below.
                    </span>
                </div>

                {{-- Resend Button --}}
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fa-solid fa-paper-plane me-2"></i>
                        Resend Verification Email
                    </button>
                </form>

                {{-- Divider --}}
                <div class="d-flex align-items-center my-3">
                    <hr class="flex-grow-1">
                    <span class="px-2 text-muted" style="font-size: 0.8rem;">or</span>
                    <hr class="flex-grow-1">
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary w-100">
                        <i class="fa-solid fa-right-from-bracket me-2"></i>
                        Logout
                    </button>
                </form>

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
