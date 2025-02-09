<?php

use App\Http\Controllers\ScanController;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/scan', [ScanController::class, 'scan']);
Route::get('/assets', function () {
    return response()->json(
        Asset::with('user:id,username')->with('tag:rfid_number,rfid_number')->orderBy('created_at', 'desc')->get()
    );
});
