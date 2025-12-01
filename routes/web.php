<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::name('web.')->group(function () {
    //Customers
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('get/customer', [CustomerController::class, 'index'])->name('find.customer');
    });

    //Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('all/products', [ProductController::class, 'index'])->name('all.products');
    });

    // Billings
    Route::prefix('billings')->name('billing.')->group(function () {
        Route::get('create', [BillingController::class, 'create'])->name('create');
        Route::post('create', [BillingController::class, 'store'])->name('store');
    });

    Route::prefix('invoice')->name('invoice.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('list');
        Route::get('generate/{invoiceId}', [InvoiceController::class, 'generateInvoice'])->name('generate');
    });


    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/high-variety-customers', [AnalyticsController::class, 'highVarietyCustomers']);
        Route::get('/stock-forecast', [AnalyticsController::class, 'stockForecast']);
        Route::get('/repeat-customers', [AnalyticsController::class, 'repeatCustomers']);
        Route::get('/high-demand-orders', [AnalyticsController::class, 'highDemandOrders']);
    });

});
