<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('login');
    })->name('login');

    Route::get('/register', function () {
        return view('register');
    })->name('register');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/user/profile-photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::put('sales/status/{id}', [SalesController::class, 'saleStatus'])->name('sales.status');
    Route::get('sales/export', [SalesController::class, 'export'])->name('sales.export');
    Route::get('sales/invoice/export/{id}', [SalesController::class, 'invoiceExport'])->name('sale.invoice.export');
    Route::get('sales/invoice/pdf/{id}', [SalesController::class, 'invoicePdf'])->name('sale.invoice.pdf');

    Route::resource('client', ClientController::class);
    Route::resource('supplier', SupplierController::class);
    Route::resource('sales', SalesController::class);
    Route::resource('purchase', PurchaseController::class);
});

Route::get('/forgot-password', function () {
    return view('forgot-password');
})->middleware('guest')->name('password.request');

// Google Login Routes
Route::controller(GoogleLoginController::class)->group(function () {
    Route::get('google/redirect', 'redirectToGoogle')->name('google.redirect');
    Route::get('google/callback', 'handleGoogleCallback')->name('google.callback');
});
