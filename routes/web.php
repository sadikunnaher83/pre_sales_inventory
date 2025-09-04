<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Middleware\SessionAuthenticate;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\TokenVerificationMiddleware;

// Route::get('/', function () {
//     // return view('welcome');
//     return "home page";
// });

Route::get('/', [HomeController::class, 'index'])->name('HomePage');
Route::get('/test', [HomeController::class, 'HomePage'])->name('Home');

//user all route
Route::post('/user-registration', [UserController::class, 'UserRegistration'])->name('UserRegistration');
Route::post('/user-login', [UserController::class, 'UserLogin'])->name('user.login');
Route::get('/user-logout', [UserController::class, 'UserLogout'])->name('user.logout');

Route::get('/DashboardPage', [DashboardController::class, 'DashboardPage'])->middleware([SessionAuthenticate::class])->name('dashboard.page');

Route::post('/send-otp', [UserController::class, 'SendOTPcode'])->name('SendOTPcode');
Route::post('/verify-otp', [UserController::class, 'VerifyOTP'])->name('VerifyOTP');

Route::post('/reset-password', [UserController::class, 'ResetPassword'])->middleware([SessionAuthenticate::class]);

Route::middleware(SessionAuthenticate::class)->group(function () {
    //category all routes
    Route::controller(CategoryController::class)->group(function () {
        Route::post('/category-create', 'CategoryCreate')->name('category.create');
        Route::get('/category-list', 'CategoryList')->name('category.list');
        Route::post('/category-by-id', 'CategoryById')->name('category.by.id');
        Route::post('/update-category', 'CategoryUpdate')->name('category.update');
        Route::get('/delete-category/{id}', 'CategoryDelete')->name('category.delete');
        Route::get('/CategoryPage', 'CategoryPage')->name('category.page');
        Route::get('/CategorySavePage', 'CategorySavePage')->name('CategorySavePage');
    });

    //product all routes
    Route::controller(ProductController::class)->group(function () {
        Route::post('/create-product', 'ProductCreate')->name('product.create');
        Route::get('/list-product', 'ProductList')->name('product.list');
        Route::post('/product-by-id', 'ProductById')->name('product.by.id');
        Route::post('/update-product', 'ProductUpdate')->name('product.update');
        Route::get('/delete-product/{id}', 'ProductDelete')->name('product.delete');
        Route::get('/ProductPage', 'ProductPage')->name('ProductPage');
    });
    //customer all routes
    Route::controller(CustomerController::class)->group(function () {
        Route::post('/create-customer', 'CustomerCreate')->name('customer.create');
        Route::get('/list-customer', 'CustomerList')->name('customer.list');
        Route::post('/update-customer', 'CustomerUpdate')->name('customer.update');
        Route::get('/delete-customer/{id}', 'CustomerDelete')->name('customer.delete');
        Route::get('/CustomerPage', 'CustomerPage')->name('CustomerPage');
        Route::get('/CustomerSavePage', 'CustomerSavePage')->name('CustomerSavePage');
    });
    //Invoice all routes
    Route::controller(InvoiceController::class)->group(function () {
        Route::post('/invoice-create', 'InvoiceCreate')->name('invoice.create');
        Route::get('/invoice-list', 'InvoiceList')->name('invoice.list');
        Route::post('/invoice-details', 'InvoiceDetails')->name('invoice.details');
        Route::get('/invoice-delete/{id}', 'InvoiceDelete')->name('invoice.delete');
        Route::get('/InvoiceListPage', 'InvoiceListPage')->name('InvoiceListPage');
    });

    // Dashboard summery route
    Route::get('/dashboard-summary', [DashboardController::class, 'DashboardSummary'])->name('dashboard.summary');

    //user update route
    Route::post('/user-update', [UserController::class, 'UserUpdate'])->name('user.update');

    //sale all routes
    Route::get('/create-sale', [SaleController::class, 'SalePage'])->name('sale.page');


});


//frontend all routes
Route::get('/login', [UserController::class, 'LoginPage'])->name('login.page');
Route::get('/registration', [UserController::class, 'RegistrationPage'])->name('RegistrationPage');
Route::get('/send-otp', [UserController::class, 'SendOTPPage'])->name('SendOTPcode');
Route::get('/verify-otp', [UserController::class, 'VerifyOTPPage'])->name('VerifyOTPPage');
Route::get('/reset-password', [UserController::class, 'ResetPasswordPage'])->middleware([SessionAuthenticate::class]);




