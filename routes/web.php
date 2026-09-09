<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExportTransactionController;
use App\Http\Controllers\SettingController;

// Direct access dashboard (No Login required for simple demo)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Settings & Email Routes
Route::post('/settings/admin-email', [SettingController::class, 'updateAdminEmail'])->name('settings.update-email');
Route::post('/suppliers/send-test-email', [SupplierController::class, 'sendTestEmail'])->name('suppliers.send-test-email');
Route::get('/emails/preview', [SupplierController::class, 'previewEmail'])->name('emails.preview');

Route::resource('stocks', StockController::class);
Route::resource('suppliers', SupplierController::class);
Route::resource('investors', InvestorController::class);
Route::resource('transfers', TransferController::class);
Route::resource('pricings', PricingController::class);
Route::resource('tax-rates', TaxRateController::class);
Route::resource('customers', CustomerController::class);
Route::resource('export-transactions', ExportTransactionController::class);
