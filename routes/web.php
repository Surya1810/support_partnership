<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseRequestController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserExtensionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware('auth')->group(function () {
    // Check
    Route::get('/check-user-extension/{userId}', [UserController::class, 'checkUserExtension']);

    // Coming Soon
    Route::get('/coming-soon', function () {
        return view('coming_soon');
    })->name('coming_soon');

    // Dashboard
    Route::get('/', function () {
        return redirect('dashboard');
    });
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Profile Section
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password/{id}', [ProfileController::class, 'password'])->name('profile.password');
    Route::delete('/profile/delete/{id}', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User | Employee  
    Route::resource('employee', UserController::class);
    Route::resource('user-data', UserExtensionController::class);

    // Finance
    Route::resource('finance', FinanceController::class);
    // Application
    Route::resource('application', ExpenseRequestController::class);
    Route::put('/application/{id}/approve', [ExpenseRequestController::class, 'approve'])->name('application.approve');
    Route::put('/application/{id}/reject', [ExpenseRequestController::class, 'reject'])->name('application.reject');
    Route::put('/application/{id}/process', [ExpenseRequestController::class, 'process'])->name('application.process');
    Route::post('/application/{id}/report', [ExpenseRequestController::class, 'report'])->name('application.report');
    Route::get('/application/{id}/pdf', [ExpenseRequestController::class, 'pdf'])->name('application.pdf');
    // Expense
    Route::resource('expense', ExpenseController::class);

    // Document
    Route::resource('document', DocumentController::class);

    // Project Management
    Route::resource('project', ProjectController::class);
    // Route::get('/project/archive', [ProjectController::class, 'archive'])->name('project.archive');
    Route::get('/project/detail/{kode}', [ProjectController::class, 'detail'])->name('project.detail');
    Route::get('/project/task/{kode}', [ProjectController::class, 'task'])->name('project.task');
    Route::get('/project/review/{kode}', [ProjectController::class, 'review'])->name('project.review');
    Route::post('/project/done/{id}', [ProjectController::class, 'done'])->name('project.done');

    //Task Management
    Route::resource('task', TaskController::class)->except([
        'store'
    ]);
    Route::post('/task/{id}', [TaskController::class, 'store'])->name('task.store');
    Route::get('/task/status/{id}', [TaskController::class, 'status'])->name('task.status');

    // Client
    Route::resource('client', ClientController::class);

    // Supplier
    Route::resource('supplier', SupplierController::class);

    // Partner
    Route::resource('partner', PartnerController::class);
});
