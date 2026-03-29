<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>

<body>

    <div class="d-flex w-100 vh-100 overflow-hidden">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header d-flex align-items-center px-4 fw-bold fs-5">
                <i class="fa-solid fa-chart-line sidebar-brand-icon"></i>
                <a href="{{ url('/dashboard') }}"><span class="sidebar-brand-text">InvoiceTracker</span></a>
            </div>
            <div class="sidebar-menu flex-grow-1 overflow-auto py-3">
                <ul>
                    <li><a href="{{ url('/dashboard') }}" class="sidebar-link"><i
                                class="fa-solid fa-house fa-fw"></i><span>Dashboard</span></a></li>

                    <li>
                        <a href="{{ route('sales.index') }}"class="sidebar-link" aria-expanded="false">
                            <i class="fa-solid fa-cart-shopping fa-fw"></i><span>Sales</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('purchase.index') }}"class="sidebar-link" aria-expanded="false">
                            <i class="fa-solid fa-bag-shopping fa-fw"></i><span>Purchase</span>
                        </a>
                    </li>

                    <li><a href="{{ route('client.index') }}" class="sidebar-link"><i
                                class="fa-solid fa-users fa-fw"></i><span>Clients</span></a></li>
                    <li><a href="{{ route('supplier.index') }}" class="sidebar-link"><i
                                class="fa-solid fa-truck-field fa-fw"></i><span>Suppliers</span></a></li>

                    <li>
                        <a href="#reportsSubmenu" data-bs-toggle="collapse" class="sidebar-link" aria-expanded="false">
                            <i class="fa-solid fa-file-invoice fa-fw"></i><span>Reports</span>
                            <i class="fa-solid fa-chevron-down toggle-icon"></i>
                        </a>
                        <ul class="collapse sidebar-submenu" id="reportsSubmenu">
                            <li><a href="reports-sales.html" class="sidebar-link"><span>Sales Summery</span></a></li>
                            <li><a href="reports-purchase.html" class="sidebar-link"><span>Purchase Summery</span></a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="main-content d-flex flex-column flex-grow-1 min-vh-0">
            <!-- Topbar -->
            <header class="topbar d-flex align-items-center justify-content-between px-4 bg-body shadow-sm z-2">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebar-toggle" aria-label="Toggle Sidebar"><i class="fa-solid fa-bars"></i></button>
                    <form class="search-form">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Search globally..." aria-label="Search">
                    </form>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <div class="dropdown">
                        <button class="topbar-action" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-bell"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fw-bold lh-1 px-1 py-1">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2" style="width: 250px;">
                            <li>
                                <h6 class="dropdown-header">Notifications</h6>
                            </li>
                            <li><a class="dropdown-item py-2" href="#"><i
                                        class="fa-solid fa-circle-info text-primary me-2"></i> Over Due Invoice</a>
                            </li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false"
                            role="button">
                            <div class="rounded-circle bg-dark bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                                style="width:50px; height:50px; font-size:18px;">
                                <div ...>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                            </div>
                            <div class="d-none d-sm-flex flex-column">
                                <span class="user-name">{{ Auth::user()->name }}</span>
                            </div>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile-change-password.html"><i
                                        class="fa-solid fa-user me-2 text-muted"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="profile-change-password.html"><i
                                        class="fa-solid fa-key me-2 text-muted"></i> Change Password</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger"><i
                                            class="fa-solid fa-right-from-bracket me-2"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            @yield('main')

        </main>

    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/js/data-samples.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/charts.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>

</html>
