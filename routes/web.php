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

    // Application
    Route::middleware('auth.development')
        ->group(function () {
            Route::resource('application', ExpenseRequestController::class);

            Route::middleware('auth.module.finance')
                ->group(function () {
                    Route::put('/application/{id}/approve', [ExpenseRequestController::class, 'approve'])
                        ->name('application.approve');
                    Route::get('/approval/pengajuan', [ExpenseRequestController::class, 'approval'])
                        ->name('application.approval');
                    Route::put('/application/{id}/reject', [ExpenseRequestController::class, 'reject'])
                        ->name('application.reject');
                    Route::post('/application/bulk-action', [ExpenseRequestController::class, 'bulkAction'])
                        ->name('application.bulkAction');
                    Route::put('/application/{id}/process', [ExpenseRequestController::class, 'process'])
                        ->name('application.process');
                    Route::put('/application/{id}/checking', [ExpenseRequestController::class, 'checking'])
                        ->name('application.checking');
                });

            Route::post('/application/{id}/report', [ExpenseRequestController::class, 'report'])
                ->name('application.report');
            Route::get('/application/{id}/pdf', [ExpenseRequestController::class, 'pdf'])
                ->name('application.pdf');
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
            Route::controller(ProjectController::class)
                ->prefix('projects')
                ->group(function () {
                    Route::get('/create/download-template-rab', 'downloadTemplateImport')
                        ->name('project.create.download-template-rab');
                    Route::get('/detail/{kode}', 'detail')->name('project.detail');
                    Route::get('/task/{kode}', 'task')->name('project.task');
                    Route::get('/review/{kode}', 'review')->name('project.review');
                    Route::get('/finalization/{kode}', 'finalization')->name('project.finalization');
                    Route::post('/finalization/{id}', 'storeFinalization')->name('project.store.finalization');
                    Route::post('/done/{id}', 'done')->name('project.done');
                    Route::get('/arsip', 'archive')->name('project.archive');
                });
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
        ->middleware(['auth.development', 'auth.module.finance'])
        ->prefix('/cost-center')
        ->group(function () {
            Route::get('/', 'index')->name('cost-center.index');
            Route::get('/create/rab-general', 'indexCreateRABGeneral')
                ->name('cost-center.create.rab-general');

            Route::middleware('auth.admin')
                ->group(function () {
                    Route::post('/create/rab-general', 'storeRABGeneral')
                        ->name('cost-center.store.rab-general');
                    Route::get('/edit/rab-general/{id}/list', 'getRABGeneralJSON')
                        ->name('cost-center.edit.rab-general.list');
                    Route::put('/edit/rab-general/{id}/update', 'updateRABGeneral')
                        ->name('cost-center.edit.rab-general.update');
                });

            Route::get('/transactions/rab-general/credit', 'indexTransactionCreditRABGeneral')
                ->name('cost-center.transactions.rab-general.credit');

            // per divisi
            Route::prefix('/departments')
                ->group(function () {
                    Route::get('/{id}', 'indexDepartment')
                        ->name('cost-center.departments.index');
                    Route::get('/{id}/projects', 'indexDepartmentProjects')
                        ->name('cost-center.departments.projects');
                    Route::get('/projects/{projectId}/budget-plan', 'indexDepartmentProjectBudgetPlan')
                        ->name('cost-center.departments.projects.budget-plan');
                    Route::get('/projects/{projectId}/budget-plan/json', 'getCostCentersProjectJSON')
                        ->name('cost-center.departments.projects.budget-plan.json')
                        ->withoutMiddleware('auth.module.finance');
                    Route::post('/projects/{projectId}/budget-plan', 'storeRABProject')
                        ->name('cost-center.departments.projects.budget-plan.store');
                    Route::put('/projects/{projectId}/budget-plan', 'updateRABProject')
                        ->name('cost-center.departments.projects.budget-plan.update');
                    Route::get('/projects/{projectId}/profit', 'getProjectProfitTable')
                        ->name('cost-center.departments.projects.profit');
                    Route::get('/{id}/requests', 'indexDepartmentRequests')
                        ->name('cost-center.departments.requests');
                });
        });
});
