<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseRequestController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserExtensionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    // Check
    Route::get('/check-user-extension/{userId}', [UserController::class, 'checkUserExtension']);

    // Coming Soon
    Route::get('/coming-soon', function () {
        return view('coming_soon');
    })->name('coming_soon');

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
    Route::get('/procurement', [FinanceController::class, 'procurement'])->name('procurement.report');
    Route::get('/technology',  [FinanceController::class, 'technology'])->name('technology.report');
    Route::get('/construction',  [FinanceController::class, 'construction'])->name('construction.report');
    Route::post('/pembagian',  [FinanceController::class, 'pembagian'])->name('finance.pembagian');

    // Application
    Route::resource('application', ExpenseRequestController::class);
    Route::put('/application/{id}/approve', [ExpenseRequestController::class, 'approve'])->name('application.approve');
    Route::get('/approval', [ExpenseRequestController::class, 'approval'])->name('application.approval');
    Route::post('/application/{id}/reject', [ExpenseRequestController::class, 'reject'])->name('application.reject');
    Route::post('/application/bulk-action', [ExpenseRequestController::class, 'bulkAction'])->name('application.bulkAction');
    Route::put('/application/{id}/process', [ExpenseRequestController::class, 'process'])->name('application.process');
    Route::post('/application/{id}/report', [ExpenseRequestController::class, 'report'])->name('application.report');
    Route::get('/application/{id}/pdf', [ExpenseRequestController::class, 'pdf'])->name('application.pdf');
    // Debt
    Route::resource('debt', DebtController::class);

    // Document
    Route::resource('document', DocumentController::class);
    Route::post('/document/import', [DocumentController::class, 'import'])->name('document.import');

    // Project Management
    Route::resource('project', ProjectController::class);
    Route::get('/project/detail/{kode}', [ProjectController::class, 'detail'])->name('project.detail');
    Route::get('/project/task/{kode}', [ProjectController::class, 'task'])->name('project.task');
    Route::get('/project/review/{kode}', [ProjectController::class, 'review'])->name('project.review');
    Route::post('/project/done/{id}', [ProjectController::class, 'done'])->name('project.done');
    Route::get('/projects/arsip', [ProjectController::class, 'archive'])->name('project.archive');

    //Task Management
    Route::resource('task', TaskController::class)->except([
        'store'
    ]);
    Route::post('/task/{id}', [TaskController::class, 'store'])->name('task.store');
    Route::get('/task/status/{id}', [TaskController::class, 'status'])->name('task.status');

    // Asset RFID
    Route::resource('asset', AssetController::class);
    Route::post('/asset/import', [AssetController::class, 'import'])->name('asset.import');
    Route::post('/asset/maintenance', [AssetController::class, 'maintenance'])->name('asset.maintenance');

    // Tag RFID
    Route::resource('tag', TagController::class);
    Route::post('/tag/import', [TagController::class, 'import'])->name('tag.import');

    // Scan RFID
    Route::resource('scan', ScanController::class);

    // Client
    Route::resource('client', ClientController::class);

    // Supplier
    Route::resource('supplier', SupplierController::class);

    // Partner
    Route::resource('partner', PartnerController::class);
});
