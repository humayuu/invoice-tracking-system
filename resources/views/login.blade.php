<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InvoiceTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

</head>

<body>
    <div class="container">
        <div class="row m-5">
            <div class="col-12 d-flex justify-content-center">
                <div class="login-right mt-5">
                    <div class="login-form-wrap fade-up">

                        <!-- Brand Heading with Icon -->
                        <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
                            <i class="fa-solid fa-chart-line fs-1 sidebar-brand-icon"></i>
                            <h4 class="fw-bold mb-0">InvoiceTracker</h4>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3" role="alert">
                                <i class="fa-solid fa-circle-check"></i>
                                <span style="font-size: 0.875rem;">
                                    {{ session('status') }}
                                </span>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3" role="alert">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span style="font-size: 0.875rem;">{{ session('error') }}</span>
                            </div>
                        @endif

                        <!-- Mobile brand icon -->
                        <div class="d-flex d-md-none align-items-center gap-2 mb-4">
                            <div
                                style="width:36px;height:36px;background:rgba(99,102,241,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--primary-color);">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                            <span style="font-weight:700;font-size:1.1rem;color:var(--text-main);">invoiceTracker</span>
                        </div>

                        <form method="POST" action="{{ route('login.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="email">Email address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="Enter Your Email" autofocus value="{{ old('email') }}">
                                </div>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter Your Password">
                                    <button type="button" class="input-group-text" id="togglePassword"
                                        title="Toggle visibility" style="cursor:pointer;">
                                        <i class="fa-regular fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <div class="form-check mb-0">
                                    <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                    <label class="form-check-label" for="rememberMe"
                                        style="font-size:0.875rem;">Remember me</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold mb-3" id="loginBtn">
                                <span id="btnText">Sign In</span>
                                <i class="fa-solid fa-spinner fa-spin d-none" id="btnSpinner"></i>
                            </button>

                            <p class="text-center mb-0 small" style="color:var(--text-muted);">
                                Accounts are created by an administrator.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // Password visibility toggle
            document.getElementById('togglePassword').addEventListener('click', () => {
                const pwInput = document.getElementById('password');
                const toggleIcon = document.getElementById('toggleIcon');
                if (pwInput.type === 'password') {
                    pwInput.type = 'text';
                    toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    pwInput.type = 'password';
                    toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });

            document.querySelector('form').addEventListener('submit', () => {
                document.getElementById('btnText').classList.add('d-none');
                document.getElementById('btnSpinner').classList.remove('d-none');
                document.getElementById('loginBtn').disabled = true;
            });

        });
    </script>
</body>

</html>
