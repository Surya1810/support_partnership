<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExpenseRequestController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserExtensionController;
use App\Http\Controllers\UserJobController;
use App\Models\ExpenseItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes();
Route::middleware('auth')->group(function () {
    Route::get('/rekap', function () {
        $items = ExpenseItem::select(
            '*',
            FacadesDB::raw('ABS(total_price - actual_amount) AS selisih')
        )
            ->whereNotNull('actual_amount')
            ->whereColumn('total_price', '!=', 'actual_amount')
            ->get();

        return response()->json($items, 200, [], JSON_PRETTY_PRINT);
    });

    Route::get('/share/{id}', function ($id) {
        $file = \App\Models\File::findOrFail($id);
        $path = storage_path('app/public/' . $file->file_path);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download($path);
    })->name('files.share');

    // Check
    Route::get('/check-user-extension/{userId}', [UserController::class, 'checkUserExtension']);

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
    Route::middleware('auth.development')
        ->group(function () {
            Route::resource('finance', FinanceController::class);
            Route::get('/procurement', [FinanceController::class, 'procurement'])->name('procurement.report');
            Route::get('/technology',  [FinanceController::class, 'technology'])->name('technology.report');
            Route::get('/construction',  [FinanceController::class, 'construction'])->name('construction.report');
            Route::post('/pembagian',  [FinanceController::class, 'pembagian'])->name('finance.pembagian');
        });

    // Application
    Route::middleware('auth.development')
        ->group(function () {
            Route::resource('application', ExpenseRequestController::class);
            Route::put('/application/{id}/approve', [ExpenseRequestController::class, 'approve'])->name('application.approve');
            Route::get('/approval/pengajuan', [ExpenseRequestController::class, 'approval'])->name('application.approval');
            Route::put('/application/{id}/reject', [ExpenseRequestController::class, 'reject'])->name('application.reject');
            Route::post('/application/bulk-action', [ExpenseRequestController::class, 'bulkAction'])->name('application.bulkAction');
            Route::put('/application/{id}/process', [ExpenseRequestController::class, 'process'])->name('application.process');
            Route::post('/application/{id}/report', [ExpenseRequestController::class, 'report'])->name('application.report');
            Route::get('/application/{id}/pdf', [ExpenseRequestController::class, 'pdf'])->name('application.pdf');
        });

    // Debt
    Route::resource('izin', IzinController::class);
    Route::get('/approval/izin', [IzinController::class, 'approval'])->name('izin.approval');
    Route::put('/izin/{id}/approve', [IzinController::class, 'approve'])->name('izin.approve');
    Route::put('/izin/{id}/reject', [IzinController::class, 'reject'])->name('izin.reject');
    Route::post('/izin/bulk-action', [IzinController::class, 'bulkAction'])->name('izin.bulkAction');

    // Debt
    Route::resource('debt', DebtController::class);

    // Document
    Route::resource('document', DocumentController::class);
    Route::post('/document/import', [DocumentController::class, 'import'])->name('document.import');

    // Project Management
    Route::middleware('auth.development')
        ->group(function () {
            Route::resource('project', ProjectController::class);
            Route::get('/project/detail/{kode}', [ProjectController::class, 'detail'])->name('project.detail');
            Route::get('/project/task/{kode}', [ProjectController::class, 'task'])->name('project.task');
            Route::get('/project/review/{kode}', [ProjectController::class, 'review'])->name('project.review');
            Route::post('/project/done/{id}', [ProjectController::class, 'done'])->name('project.done');
            Route::get('/projects/arsip', [ProjectController::class, 'archive'])->name('project.archive');
        });

    // Files
    Route::resource('files', FileController::class);

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

    // Scan RFID
    Route::resource('scan', ScanController::class);

    // Client
    Route::resource('client', ClientController::class);

    // Supplier
    Route::resource('supplier', SupplierController::class);

    // Partner
    Route::resource('partner', PartnerController::class);

    /**
     * Date: 21/05/2025
     * UserJobs Menu
     */
    Route::controller(UserJobController::class)
        ->prefix('/jobs')
        ->group(function () {
            Route::get('/', 'index')->name('jobs.index');
            Route::get('/my-tasks', 'myTasks')->name('jobs.my_tasks');
            Route::post('/store', 'store')->name('jobs.store');
            Route::get('/upload/export', 'export')->name('jobs.export');
            Route::get('/upload/export/my-tasks', 'exportMyTasks')->name('jobs.export.my_tasks');
            Route::post('/upload/import', 'import')->name('jobs.import');
            Route::get('/upload/report/download-template', 'downloadTemplateImport')->name('jobs.download_template');
            Route::post('/upload/report/{id}', 'uploadFile')->name('jobs.upload_report');
            Route::get('/{id}', 'show')->name('jobs.show');
            Route::put('/{id}', 'update')->name('jobs.update');
            Route::post('/{id}/mark-complete', 'markComplete')->name('jobs.complete');
        });

    /**
     * Wed, 02 July 2025
     *
     * Project
     */
    Route::controller(ProjectController::class)
        ->middleware('auth.development')
        ->prefix('/projects')
        ->group(function () {
            Route::post('/import/rab', 'importRab')->name('project.import.rab');
        });

    /**
     * Wed, 02 July 2025
     */
    Route::controller(CostCenterController::class)
        ->middleware('auth.development')
        ->prefix('/cost-center')
        ->group(function () {
            Route::get('/', 'index')->name('cost-center.index');
            Route::get('/create/rab-department', 'indexCreateRABDepartment')->name('cost-center.create.rab-department');
            Route::get('/transactions/rab-department/credit', 'indexTransactionRABDepartment')
                ->name('cost-center.transactions.rab-department.credit');
        });
});
