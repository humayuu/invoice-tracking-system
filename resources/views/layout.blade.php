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

    <style>
        #app-toast-container .toast {
            --bs-toast-max-width: 420px;
            backdrop-filter: blur(8px);
        }

        #app-toast-container .toast.showing,
        #app-toast-container .toast.show {
            animation: appToastIn 0.35s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        @keyframes appToastIn {
            from {
                opacity: 0;
                transform: translateX(1.25rem);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>

</head>

<body>

    <div class="d-flex w-100 vh-100 overflow-hidden">
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <aside class="sidebar" id="sidebar" aria-label="Application navigation">
            <div class="sidebar-header d-flex align-items-center px-4 fw-bold fs-5">
                <i class="fa-solid fa-chart-line sidebar-brand-icon" aria-hidden="true"></i>
                <a href="{{ auth()->user()->firstAccessibleUrl() }}"><span class="sidebar-brand-text">InvoiceTracker</span></a>
            </div>
            <nav class="sidebar-menu flex-grow-1 overflow-auto py-3" aria-label="Main">
                <ul class="list-unstyled mb-0">
                    @if (auth()->user()->canAccessModule('dashboard'))
                        <li><a href="{{ route('dashboard') }}" class="sidebar-link"><i
                                    class="fa-solid fa-house fa-fw" aria-hidden="true"></i><span>Dashboard</span></a></li>
                    @endif
                    @if (auth()->user()->canAccessModule('sales'))
                        <li>
                            <a href="{{ route('sales.index') }}" class="sidebar-link" aria-expanded="false">
                                <i class="fa-solid fa-cart-shopping fa-fw" aria-hidden="true"></i><span>Sales</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->canAccessModule('purchase'))
                        <li>
                            <a href="{{ route('purchase.index') }}" class="sidebar-link" aria-expanded="false">
                                <i class="fa-solid fa-bag-shopping fa-fw" aria-hidden="true"></i><span>Purchase</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->canAccessModule('clients'))
                        <li><a href="{{ route('client.index') }}" class="sidebar-link"><i
                                    class="fa-solid fa-users fa-fw" aria-hidden="true"></i><span>Clients</span></a></li>
                    @endif
                    @if (auth()->user()->canAccessModule('suppliers'))
                        <li><a href="{{ route('supplier.index') }}" class="sidebar-link"><i
                                    class="fa-solid fa-truck-field fa-fw" aria-hidden="true"></i><span>Suppliers</span></a></li>
                    @endif
                    @if (auth()->user()->canAccessModule('reports'))
                        <li><a href="{{ route('reports.index') }}" class="sidebar-link"><i
                                    class="fa-solid fa-file-invoice fa-fw" aria-hidden="true"></i><span>Reports</span></a></li>
                    @endif
                    @if (auth()->user()->is_admin)
                        <li><a href="{{ route('admin.users.index') }}" class="sidebar-link"><i
                                    class="fa-solid fa-user-gear fa-fw" aria-hidden="true"></i><span>Users</span></a></li>
                    @endif
                </ul>
            </nav>
        </aside>

        <main class="main-content d-flex flex-column flex-grow-1 min-vh-0" id="main-content">
            <header class="topbar d-flex align-items-center justify-content-between px-4 bg-body shadow-sm z-2">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebar-toggle" type="button"
                        aria-label="Open or close navigation menu" aria-controls="sidebar" aria-expanded="true"><i
                            class="fa-solid fa-bars" aria-hidden="true"></i></button>
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

                            @if ($user->profile_photo_path)
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

            <div id="app-toast-container" class="toast-container position-fixed end-0 p-3"
                style="top: 4.5rem; z-index: 1090;" aria-live="polite" aria-atomic="true"></div>

            @yield('main')

        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    @php
        $__successRaw = session('success');
        $__allowedFlashActions = ['created', 'updated', 'deleted', 'delete_blocked'];
        $fa = session('flash_action');
        $__flashAction = in_array($fa, $__allowedFlashActions, true) ? $fa : null;
        $__appFlash = collect([
            'success' => $__successRaw,
            'error' => session('error'),
            'warning' => session('warning'),
            'info' => session('info'),
        ])
            ->map(fn ($v) => is_string($v) ? $v : (is_scalar($v) ? (string) $v : null))
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->all();
    @endphp
    @if (! empty($__appFlash))
        <script>
            window.__appFlash = @json($__appFlash);
            window.__appFlashAction = @json($__flashAction);
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof bootstrap === 'undefined' || !window.__appFlash) {
                return;
            }
            const container = document.getElementById('app-toast-container');
            if (!container) {
                return;
            }

            const headerClass = {
                success: 'bg-success text-white border-0',
                error: 'bg-danger text-white border-0',
                warning: 'bg-warning text-dark border-0',
                info: 'bg-primary text-white border-0',
            };
            const icons = {
                success: 'fa-circle-check',
                error: 'fa-circle-xmark',
                warning: 'fa-triangle-exclamation',
                info: 'fa-circle-info',
            };
            const titles = {
                success: 'Success',
                error: 'Something went wrong',
                warning: 'Warning',
                info: 'Notice',
            };

            Object.entries(window.__appFlash).forEach(function(entry) {
                const type = entry[0];
                const message = entry[1];
                if (!message) {
                    return;
                }

                let visualType = type;
                if (type === 'success') {
                    const action = window.__appFlashAction;
                    if (action === 'updated') {
                        visualType = 'info';
                    } else if (action === 'deleted') {
                        visualType = 'error';
                    }
                }

                const toastEl = document.createElement('div');
                toastEl.className = 'toast border-0 shadow mb-2 overflow-hidden';
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', visualType === 'error' ? 'assertive' : 'polite');

                const delay = 3000;
                toastEl.setAttribute('data-bs-autohide', 'false');

                const hClass = headerClass[visualType] || 'bg-secondary text-white border-0';
                const closeWhite = visualType !== 'warning';

                const header = document.createElement('div');
                header.className = 'toast-header ' + hClass + ' d-flex align-items-center gap-2 py-2';
                const icon = document.createElement('i');
                icon.className = 'fa-solid ' + (icons[visualType] || 'fa-bell');
                const strong = document.createElement('strong');
                strong.className = 'me-auto small text-uppercase';
                strong.style.letterSpacing = '0.04em';
                let titleText = titles[visualType] || 'Notice';
                if (type === 'success' && window.__appFlashAction === 'created') {
                    titleText = 'Created';
                } else if (type === 'success' && window.__appFlashAction === 'updated') {
                    titleText = 'Updated';
                } else if (type === 'success' && window.__appFlashAction === 'deleted') {
                    titleText = 'Deleted';
                } else if (type === 'error' && window.__appFlashAction === 'delete_blocked') {
                    titleText = 'Cannot delete';
                }
                strong.appendChild(document.createTextNode(titleText));
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn-close' + (closeWhite ? ' btn-close-white' : '');
                btn.setAttribute('data-bs-dismiss', 'toast');
                btn.setAttribute('aria-label', 'Close');
                header.appendChild(icon);
                header.appendChild(strong);
                header.appendChild(btn);

                const body = document.createElement('div');
                body.className = 'toast-body bg-white text-dark small py-3';
                body.appendChild(document.createTextNode(message));

                toastEl.appendChild(header);
                toastEl.appendChild(body);
                container.appendChild(toastEl);

                const t = new bootstrap.Toast(toastEl, {
                    autohide: false,
                    animation: true,
                });

                const hideTimer = window.setTimeout(function() {
                    t.hide();
                }, delay);

                toastEl.addEventListener('hidden.bs.toast', function onHidden() {
                    window.clearTimeout(hideTimer);
                    toastEl.removeEventListener('hidden.bs.toast', onHidden);
                    toastEl.remove();
                });

                t.show();
            });

            delete window.__appFlash;
            delete window.__appFlashAction;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/js/data-samples.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/charts.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            if ($('#clientFilter').length) {
                $('#clientFilter').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Search Client...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('body'),
                });
            }
            if ($('#supplierFilter').length) {
                $('#supplierFilter').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Search supplier...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('body'),
                });
            }
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
            if ($('#purchaseTable').length) {
                $('#purchaseTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('purchase.index') }}",
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
                            data: 'supplier',
                            name: 'supplier.name',
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

    {{-- Global delete confirmation (same style as invoice show pages) --}}
    <div class="modal fade" id="globalDeleteModal" tabindex="-1" role="dialog"
        aria-modal="true" aria-labelledby="globalDeleteTitle" aria-describedby="globalDeleteMessage" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>
                <div class="modal-body text-center px-4 pb-0">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10"
                            style="width: 70px; height: 70px;" aria-hidden="true">
                            <i class="fa-solid fa-trash-can text-danger" style="font-size: 28px;" aria-hidden="true"></i>
                        </div>
                    </div>
                    <h2 class="h5 fw-bold mb-2" id="globalDeleteTitle">Delete?</h2>
                    <p class="text-muted mb-1" id="globalDeleteMessage">Are you sure?</p>
                    <p class="text-danger small mb-0">
                        <i class="fa-solid fa-circle-exclamation me-1" aria-hidden="true"></i>
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pt-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <form id="globalDeleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4" id="globalDeleteConfirmBtn">
                            <i class="fa-solid fa-trash me-1" aria-hidden="true"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('globalDeleteModal')?.addEventListener('show.bs.modal', function(event) {
            const trigger = event.relatedTarget;
            if (!trigger || !trigger.getAttribute('data-delete-url')) {
                return;
            }
            const url = trigger.getAttribute('data-delete-url');
            const title = trigger.getAttribute('data-delete-title') || 'Delete?';
            const message = trigger.getAttribute('data-delete-message') ||
                'Are you sure you want to delete this item?';
            document.getElementById('globalDeleteForm').setAttribute('action', url);
            document.getElementById('globalDeleteTitle').textContent = title;
            document.getElementById('globalDeleteMessage').textContent = message;
        });
    </script>
</body>

</html>
