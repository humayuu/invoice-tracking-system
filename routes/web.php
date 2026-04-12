<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('login');
    })->name('welcome');

    Route::get('/register', function () {
        return view('register');
    })->name('register');

    Route::get('google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])
        ->name('google.redirect');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/user/profile-photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::get('google/connect', [GoogleLoginController::class, 'redirectToConnectGoogle'])
        ->name('google.connect');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales-summary/pdf', [ReportController::class, 'salesSummaryPdf'])->name('reports.sales-summary.pdf');
    Route::get('/reports/purchase-summary/pdf', [ReportController::class, 'purchaseSummaryPdf'])->name('reports.purchase-summary.pdf');

    Route::put('sales/status/{sale}', [SalesController::class, 'saleStatus'])->name('sales.status');
    Route::get('sales/export', [SalesController::class, 'export'])->name('sales.export');
    Route::get('sales/invoice/export/{id}', [SalesController::class, 'invoiceExport'])->name('sale.invoice.export');
    Route::get('sales/invoice/pdf/{id}', [SalesController::class, 'invoicePdf'])->name('sale.invoice.pdf');

    Route::put('purchase/status/{purchase}', [PurchaseController::class, 'purchaseStatus'])->name('purchase.status');
    Route::get('purchase/export', [PurchaseController::class, 'export'])->name('purchase.export');
    Route::get('purchase/invoice/export/{id}', [PurchaseController::class, 'invoiceExport'])->name('purchase.invoice.export');
    Route::get('purchase/invoice/pdf/{id}', [PurchaseController::class, 'invoicePdf'])->name('purchase.invoice.pdf');

    Route::get('client/wise/invoices/{id}', [ClientController::class, 'clientWiseInvoices'])->name('client.wise.invoices');
    Route::get('client/wise/invoices/{id}/pdf', [ClientController::class, 'clientWiseInvoicesPdf'])->name('client.wise.invoices.pdf');
    Route::get('client/wise/invoices/{id}/export', [ClientController::class, 'clientWiseInvoicesExport'])->name('client.wise.invoices.export');

    Route::get('supplier/wise/invoices/{id}', [SupplierController::class, 'supplierWiseInvoices'])->name('supplier.wise.invoices');
    Route::get('supplier/wise/invoices/{id}/pdf', [SupplierController::class, 'supplierWiseInvoicesPdf'])->name('supplier.wise.invoices.pdf');
    Route::get('supplier/wise/invoices/{id}/export', [SupplierController::class, 'supplierWiseInvoicesExport'])->name('supplier.wise.invoices.export');

    Route::resource('client', ClientController::class);
    Route::resource('supplier', SupplierController::class);
    Route::resource('sales', SalesController::class);
    Route::resource('purchase', PurchaseController::class);

    Route::post('/notifications/mark-all-read',
        [NotificationController::class, 'markAllRead'])
        ->name('notifications.markAllRead');

    Route::get('/notifications/{id}/read',
        [NotificationController::class, 'markRead'])
        ->name('notifications.read');
});

Route::get('/forgot-password', function () {
    return view('forgot-password');
})->middleware('guest')->name('password.request');

Route::get('google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])
    ->name('google.callback');
