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

                        <!-- Mobile brand icon -->
                        <div class="d-flex d-md-none align-items-center gap-2 mb-4">
                            <div
                                style="width:36px;height:36px;background:rgba(99,102,241,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--primary-color);">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                            <span style="font-weight:700;font-size:1.1rem;color:var(--text-main);">invoiceTracker</span>
                        </div>

                        <!-- Google Button -->
                        <a href="{{ route('google.redirect') }}"
                            class="btn w-100 py-2 fw-semibold mb-3 d-flex align-items-center justify-content-center gap-2"
                            style="border: 1px solid #dadce0; background:#fff; color:#3c4043; font-size:0.9rem; border-radius:8px; transition: box-shadow 0.2s;"
                            onmouseover="this.style.boxShadow='0 1px 6px rgba(0,0,0,0.15)'"
                            onmouseout="this.style.boxShadow='none'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20">
                                <path fill="#EA4335"
                                    d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                                <path fill="#4285F4"
                                    d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                                <path fill="#FBBC05"
                                    d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                                <path fill="#34A853"
                                    d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                                <path fill="none" d="M0 0h48v48H0z" />
                            </svg>
                            Continue with Google
                        </a>

                        <!-- Divider -->
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <hr class="flex-grow-1 m-0" style="border-color:#e5e7eb;">
                            <span style="font-size:0.8rem;color:var(--text-muted);white-space:nowrap;">or sign in with
                                email</span>
                            <hr class="flex-grow-1 m-0" style="border-color:#e5e7eb;">
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="email">Email address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="Enter Your Email" autofocus>
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
                                <div class="d-flex justify-content-end align-items-center mt-2">
                                    <a href="{{ route('password.request') }}" class="text-decoration-none small"
                                        style="color:var(--primary-color);font-weight:500;">Forgot password?</a>
                                </div>
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

                            <div class="text-center" style="color:var(--text-muted);font-size:0.85rem;">
                                Don't have an account? <a href="{{ url('/register') }}" class="fs-5">Register</a>
                            </div>
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
