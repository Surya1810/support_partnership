@extends('layouts.admin')

@section('title')
    Cost Center
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cost Center</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><strong>Cost Center</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                {{-- Procurement --}}
                <div class="col-12 col-md-4">
                    <div class="card rounded-partner bg-orange">
                        <div class="card-body">
                            <p><strong>Construction</strong></p>
                            <h3>{{ formatRupiah($construction_saldo) }}</h3>
                            <small>Saldo</small>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <small><strong>Pengeluaran Rumah Tangga</strong></small><br>
                                    <small>{{ formatRupiah($construction_household_expense) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Modal Project</strong></small><br>
                                    <small>{{ formatRupiah($construction_project_expense) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Pendapatan Kas</strong></small><br>
                                    <small>{{ formatRupiah($construction_income) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Penyusutan</strong></small><br>
                                    <small>{{ formatRupiah($construction_debts->whereIn('category', ['development', 'debt'])->sum('amount') - $construction_debts->where('category', 'payment')->sum('amount')) }}</small>
                                </div>
                            </div>
                        </div>
                        <a class="text-white" href="{{ route('construction.report') }}">
                            <div class="card-footer rounded-partner">
                                View Recap
                            </div>
                        </a>
                    </div>
                </div>
                {{-- Technology --}}
                <div class="col-12 col-md-4">
                    <div class="card rounded-partner bg-indigo">
                        <div class="card-body">
                            <p><strong>Technology</strong></p>
                            <h3>{{ formatRupiah($technology_saldo) }}</h3>
                            <small>Saldo</small>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <small><strong>Pengeluaran Rumah Tangga</strong></small><br>
                                    <small>{{ formatRupiah($technology_household_expense) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Modal Project</strong></small><br>
                                    <small>{{ formatRupiah($technology_project_expense) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Pendapatan Kas</strong></small><br>
                                    <small>{{ formatRupiah($technology_income) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Penyusutan</strong></small><br>
                                    <small>{{ formatRupiah($technology_debts->whereIn('category', ['development', 'debt'])->sum('amount') - $technology_debts->where('category', 'payment')->sum('amount')) }}</small>
                                </div>
                            </div>
                        </div>
                        <a class="text-white" href="{{ route('technology.report') }}">
                            <div class="card-footer rounded-partner">
                                View Recap
                            </div>
                        </a>
                    </div>
                </div>
                {{-- Construction --}}
                <div class="col-12 col-md-4">
                    <div class="card rounded-partner bg-orange">
                        <div class="card-body">
                            <p><strong>Construction</strong></p>
                            <h3>{{ formatRupiah($construction_saldo) }}</h3>
                            <small>Saldo</small>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <small><strong>Pengeluaran Rumah Tangga</strong></small><br>
                                    <small>{{ formatRupiah($construction_household_expense) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Modal Project</strong></small><br>
                                    <small>{{ formatRupiah($construction_project_expense) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Pendapatan Kas</strong></small><br>
                                    <small>{{ formatRupiah($construction_income) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Penyusutan</strong></small><br>
                                    <small>{{ formatRupiah($construction_debts->whereIn('category', ['development', 'debt'])->sum('amount') - $construction_debts->where('category', 'payment')->sum('amount')) }}</small>
                                </div>
                            </div>
                        </div>
                        <a class="text-white" href="{{ route('construction.report') }}">
                            <div class="card-footer rounded-partner">
                                View Recap
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
