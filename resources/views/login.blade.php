<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DashAdmin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <div class="container">
        <div class="row m-5">
            <div class="col-12 d-flex justify-content-center">
                <div class="login-right mt-5">
                    <div class="login-form-wrap fade-up">
                        <!-- Mobile brand icon -->
                        <div class="d-flex d-md-none align-items-center gap-2 mb-4">
                            <div
                                style="width:36px;height:36px;background:rgba(99,102,241,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--primary-color);">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                            <span style="font-weight:700;font-size:1.1rem;color:var(--text-main);">DashAdmin</span>
                        </div>

                        <div class="mb-4">
                            <h4 class="fw-bold mb-1" style="color:var(--text-main);letter-spacing:-0.02em;">Welcome back
                                👋</h4>
                            <p class="mb-0" style="color:var(--text-muted);font-size:0.9rem;">Sign in to your account
                                to continue.
                            </p>
                        </div>

                        <form id="loginForm" novalidate>
                            <div class="mb-3">
                                <label class="form-label" for="email">Email address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email"
                                        placeholder="admin@example.com" value="admin@example.com" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0" for="password">Password</label>
                                    <a href="#" class="text-decoration-none small"
                                        style="color:var(--primary-color);font-weight:500;">Forgot password?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" placeholder="••••••••"
                                        value="password123" required>
                                    <button type="button" class="input-group-text" id="togglePassword"
                                        title="Toggle visibility" style="cursor:pointer;">
                                        <i class="fa-regular fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4 d-flex align-items-center justify-content-between">
                                <div class="form-check mb-0">
                                    <input type="checkbox" class="form-check-input" id="rememberMe" checked>
                                    <label class="form-check-label" for="rememberMe"
                                        style="font-size:0.875rem;">Remember me</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3" id="loginBtn">
                                <span id="btnText">Sign In</span>
                                <i class="fa-solid fa-spinner fa-spin d-none" id="btnSpinner"></i>
                            </button>

                            <div class="text-center" style="color:var(--text-muted);font-size:0.85rem;">
                                Don't have an account? <a href="#"
                                    style="color:var(--primary-color);font-weight:600;text-decoration:none;">Contact
                                    Support</a>
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
        });
    </script>
</body>

</html>
