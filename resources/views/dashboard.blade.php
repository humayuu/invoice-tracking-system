@extends('layout')
@section('main')
@section('title')
    Dashboard
@endsection
<!-- Page Content -->
<div class="page-content p-4 flex-grow-1 overflow-auto fade-up">
    <h4 class="mb-4 fw-bold">Dashboard Overview</h4>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4 stagger-children">
        <div class="col-sm-6 col-xl-3">
            <div class="card kpi-card kpi-primary h-100 mb-0">
                <div class="d-flex align-items-center p-4">
                    <div class="kpi-icon primary"><i class="fa-solid fa-sack-dollar"></i></div>
                    <div class="kpi-info m-0">
                        <p>Total Sales</p>
                        <h3 id="kpi-total-sales">$0</h3>
                        <span class="kpi-trend up"><i class="fa-solid fa-arrow-trend-up"></i>
                            +12.5%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card kpi-card kpi-warning h-100 mb-0">
                <div class="d-flex align-items-center p-4">
                    <div class="kpi-icon warning"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <div class="kpi-info m-0">
                        <p>Total Purchases</p>
                        <h3 id="kpi-total-purchases">$0</h3>
                        <span class="kpi-trend down"><i class="fa-solid fa-arrow-trend-down"></i>
                            -3.2%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card kpi-card kpi-success h-100 mb-0">
                <div class="d-flex align-items-center p-4">
                    <div class="kpi-icon success"><i class="fa-solid fa-users"></i></div>
                    <div class="kpi-info m-0">
                        <p>Active Clients</p>
                        <h3 id="kpi-active-clients">0</h3>
                        <span class="kpi-trend up"><i class="fa-solid fa-arrow-trend-up"></i> +8.1%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card kpi-card kpi-danger h-100 mb-0">
                <div class="d-flex align-items-center p-4">
                    <div class="kpi-icon danger"><i class="fa-solid fa-truck-field"></i></div>
                    <div class="kpi-info m-0">
                        <p>Active Suppliers</p>
                        <h3 id="kpi-active-suppliers">0</h3>
                        <span class="kpi-trend up"><i class="fa-solid fa-arrow-trend-up"></i> +5.7%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm rounded-4 border-light">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Sales Over Time</h5>
                    <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-download"></i></button>
                </div>
                <div class="card-body">
                    <div class="position-relative w-100" style="height: 300px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm rounded-4 border-light">
                <div class="card-header bg-transparent border-bottom px-4 py-3">
                    <h5 class="card-title">Purchases vs Sales</h5>
                </div>
                <div class="card-body">
                    <div class="position-relative w-100" style="height: 300px;">
                        <canvas id="ratioChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Timeline / Feed -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm rounded-4 border-light">
                <div class="card-header bg-transparent border-bottom px-4 py-3">
                    <h5 class="card-title">Recent Transactions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-thead">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Date</th>
                                    <th>Client/Supplier</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th class="pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody id="recent-transactions-tbody">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
