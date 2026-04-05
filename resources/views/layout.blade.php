<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

</head>

<body>

    <div class="d-flex w-100 vh-100 overflow-hidden">
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

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
                        <a href="{{ route('sales.index') }}" class="sidebar-link" aria-expanded="false">
                            <i class="fa-solid fa-cart-shopping fa-fw"></i><span>Sales</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('purchase.index') }}" class="sidebar-link" aria-expanded="false">
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

        <main class="main-content d-flex flex-column flex-grow-1 min-vh-0">
            <header class="topbar d-flex align-items-center justify-content-between px-4 bg-body shadow-sm z-2">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebar-toggle" aria-label="Toggle Sidebar"><i class="fa-solid fa-bars"></i></button>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <div class="dropdown">
                        <button class="topbar-action" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-bell"></i>

                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fw-bold lh-1 px-1 py-1">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end p-2" style="width: 300px;">
                            <li>
                                <div class="d-flex justify-content-between align-items-center px-3 py-1">
                                    <h6 class="dropdown-header p-0 m-0">Notifications</h6>

                                    @if (auth()->user()->unreadNotifications->count() > 0)
                                        <a href="#" id="markAllRead" class="text-danger" style="font-size: 11px;">
                                            Mark all read
                                        </a>
                                    @endif
                                </div>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            @forelse(auth()->user()->notifications->take(10) as $notification)
                                <li>
                                    <a class="dropdown-item py-2 {{ is_null($notification->read_at) ? 'bg-light' : '' }}"
                                        href="{{ route('notifications.read', $notification->id) }}">

                                        @if ($notification->data['type'] === 'sale')
                                            <span class="badge bg-primary me-1">Sale</span>
                                        @else
                                            <span class="badge bg-warning me-1">Purchase</span>
                                        @endif

                                        <span class="fw-semibold" style="font-size: 13px;">
                                            {{ $notification->data['invoice_no'] }}
                                        </span>

                                        @if (is_null($notification->read_at))
                                            <span class="float-end">
                                                <i class="fa-solid fa-circle text-danger" style="font-size: 8px;"></i>
                                            </span>
                                        @endif

                                        <br>

                                        <small class="text-muted">
                                            Amount: {{ number_format($notification->data['amount'], 2) }}
                                        </small>
                                        <br>

                                        <small class="text-danger">
                                            <i class="fa-solid fa-clock me-1"></i>
                                            Due: {{ $notification->data['due_date'] }}
                                        </small>

                                    </a>
                                </li>
                            @empty
                                <li>
                                    <p class="text-center text-muted py-3 mb-0" style="font-size: 13px;">
                                        <i class="fa-regular fa-bell-slash me-1"></i>
                                        No notifications
                                    </p>
                                </li>
                            @endforelse

                        </ul>
                    </div>

                    <div class="dropdown">
                        <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false"
                            role="button">
                            @php $user = Auth::user(); @endphp

                            @if ($user->google_avatar)
                                <img src="{{ $user->google_avatar }}" class="rounded-circle"
                                    style="width:50px; height:50px; object-fit:cover;">
                            @elseif ($user->profile_photo_path)
                                <img src="{{ Storage::disk('public')->url($user->profile_photo_path) }}"
                                    class="rounded-circle" style="width:50px; height:50px; object-fit:cover;">
                            @else
                                <div class="rounded-circle bg-dark bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                                    style="width:50px; height:50px; font-size:18px;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                            <div class="d-none d-sm-flex flex-column">
                                <span class="user-name">{{ Auth::user()->name }}</span>
                            </div>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i
                                        class="fa-solid fa-user me-2 text-muted"></i>
                                    Profile</a></li>
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
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/js/data-samples.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/charts.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#clientFilter').select2({
                theme: 'bootstrap-5',
                placeholder: 'Search Client...',
                allowClear: true,
                width: '100%',
            });
            if ($('#salesTable').length) {
                $('#salesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('sales.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'invoice_no',
                            name: 'invoice_no'
                        },
                        {
                            data: 'invoice_date',
                            name: 'invoice_date',
                            className: 'd-none d-md-table-cell'
                        },
                        {
                            data: 'client',
                            name: 'client.name',
                            orderable: false
                        },
                        {
                            data: 'amount',
                            name: 'amount',
                            className: 'd-none d-md-table-cell'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-2 p-3"lf>rtip',
                    language: {
                        search: "",
                        searchPlaceholder: "Search...",
                    }
                });
            }
            if ($('#clientTable').length) {
                $('#clientTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('client.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email',
                        },
                        {
                            data: 'phone',
                            name: 'phone',
                        },
                        {
                            data: 'credit_period',
                            name: 'credit_period',
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-2 p-3"lf>rtip',
                    language: {
                        search: "",
                        searchPlaceholder: "Search...",
                    }
                });
            }

            if ($('#supplierTable').length) {
                $('#supplierTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('supplier.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email',
                        },
                        {
                            data: 'phone',
                            name: 'phone',
                        },
                        {
                            data: 'credit_period',
                            name: 'credit_period',
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-2 p-3"lf>rtip',
                    language: {
                        search: "",
                        searchPlaceholder: "Search...",
                    }
                });
            }
        });
    </script>
    <script>
        $('#markAllRead').on('click', function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route('notifications.markAllRead') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    $('.badge.bg-danger').remove();

                    $('.dropdown-item').removeClass('bg-light');

                    $('.fa-circle.text-danger').closest('span').remove();

                    $('#markAllRead').hide();
                }
            });
        });
    </script>
</body>

</html>
