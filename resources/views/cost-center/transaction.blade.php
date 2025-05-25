@extends('layouts.admin')

@section('title')
Cost Center
@endsection

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="text-2xl font-semibold text-gray-800">Cost Center</h1>
                <ol class="breadcrumb text-gray-500">
                    <li class="breadcrumb-item"><a class="text-blue-600" href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active"><strong>Cost Center</strong></li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-center text-2xl font-bold text-gray-800 mb-8">Detail Transaksi: {{ $departmentName }}</h1>

        {{-- Ringkasan Saldo --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-10">
            <div class="bg-green-100 border border-green-300 rounded-xl p-5 text-center shadow-sm">
                <p class="text-sm font-medium text-green-800">Total Debit</p>
                <p class="text-2xl font-extrabold text-green-900">Rp{{ number_format($totalDebit, 0, ',', '.') }}</p>
            </div>
            <div class="bg-red-100 border border-red-300 rounded-xl p-5 text-center shadow-sm">
                <p class="text-sm font-medium text-red-800">Total Kredit</p>
                <p class="text-2xl font-extrabold text-red-900">Rp{{ number_format($totalKredit, 0, ',', '.') }}</p>
            </div>
            <div class="bg-yellow-100 border border-yellow-300 rounded-xl p-5 text-center shadow-sm">
                <p class="text-sm font-medium text-yellow-800">Total Saldo</p>
                <p class="text-2xl font-extrabold text-yellow-900">Rp{{ number_format($totalSaldo, 0, ',', '.') }}</p>
            </div>
            <div class="bg-blue-100 border border-blue-300 rounded-xl p-5 text-center shadow-sm">
                <p class="text-sm font-medium text-blue-800">Pendapatan Tahun Berjalan</p>
                <p class="text-2xl font-extrabold text-blue-900">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Daftar Cost Center --}}
        <div class="w-full max-w-6xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-16">
                @foreach ($categoryData as $code => $data)
                <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-md hover:shadow-lg transition">
                    <p class="text-sm font-semibold text-gray-700">({{ $code }}) {{ $data['label'] }}</p>
                    <p class="text-xl font-bold mt-3 text-gray-900">Rp{{ number_format($data['amount'], 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
        </div>        

        {{-- Tombol Aksi --}}
        <div class="flex justify-center gap-6">
            <a href="{{ route('cost-center.project') }}" class="bg-blue-400 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition shadow-md">
                Lihat List Project
            </a>
            <a href="{{ route('cost-center.saldo') }}" class="bg-blue-400 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition shadow-md">
                Lihat Data Saldo
            </a>
        </div>
    </div>
</section>
@endsection