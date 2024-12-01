<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('dashboard');
});

Auth::routes();

Route::get('/dashboard', function () {
    return view('home.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // User | Employee
    Route::resource('employee', UserController::class);
    // Department
    Route::resource('department', DepartmentController::class);

    // Profile Section
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password/{id}', [ProfileController::class, 'password'])->name('profile.password');
    Route::delete('/profile/delete/{id}', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
