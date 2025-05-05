<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

use App\Models\TestTag;

class TestTagController extends Controller
{
    public function add(Request $request) {
        if ($request->header('Client-Token') !== env('MOBILE_CLIENT_TOKEN')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'tags' => ['required', 'array'],
            'tags.*' => ['string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->tags;
        $now = Carbon::now();
        $data = array_map(function ($item) use ($now) {
            return [
                'rfid_number' => $item,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $request->input('tags'));

        DB::beginTransaction();

        try {
            foreach (array_chunk($data, 500) as $chunk) {
                TestTag::insert($chunk);
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil menambahkan semua tag.'
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'fail',
                'message' => 'Gagal menambahkan tag.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
