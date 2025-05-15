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
            <div class="row text-sm">
                {{-- Table Cost Center Per Divisi --}}
                <div class="col-12 col-md-4">
                    <div class="card card-outline rounded-partner card-primary p-3">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h2 class="card-title"><b>Divisi Procurement</b></h2>
                            </div>
                            <div class="col-6">
                                @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 3)
                                    <button type="button" class="btn btn-sm btn-warning float-right"
                                        data-id="{{ isset($costCentersProcurement[0]->id) ? $costCentersProcurement[0]->id : '' }}"
                                        onclick="modalImport(this)">
                                        <i class="fas fa-upload"></i>
                                        Import
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary mr-2 float-right"
                                        data-id="{{ isset($costCentersProcurement[0]->id) ? $costCentersProcurement[0]->id : '' }}"
                                        onclick="modalAdd(this)">
                                        <i class="fas fa-plus"></i>
                                        Tambah Cost Center
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="tableCostCenterProcurement" class="table table-bordered text-nowrap w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            No.
                                        </th>
                                        <th>
                                            Nama
                                        </th>
                                        <th>
                                            Kode
                                        </th>
                                        <th>
                                            Amount
                                        </th>
                                        <th>
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($costCentersProcurement as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}.</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->code }}</td>
                                            <td>{{ formatRupiah($item->amount) }}</td>
                                            <td>
                                            @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 3)
                                                    <button type="button" class="badge badge-primary border-0 p-1"
                                                        data-toggle="modal" onclick="modalEdit(this)"
                                                        data-id="{{ $item->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="badge badge-danger border-0 p-1"
                                                        data-toggle="modal" onclick="confirmDelete(this)"
                                                        data-id="{{ $item->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                                <a href="#{{ $item->id }}" class="badge badge-info btn-sm p-1"
                                                    data-toggle="modal">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Divisi Construction --}}
                <div class="col-12 col-md-4">
                    <div class="card card-outline rounded-partner card-primary p-3">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h2 class="card-title"><b>Divisi Konstruksi</b></h2>
                            </div>
                            <div class="col-6">
                                @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 3)
                                    <button type="button" class="btn btn-sm btn-warning float-right"
                                        data-id="{{ isset($costCentersConstruction[0]->id) ? $costCentersConstruction[0]->id : '' }}"
                                        onclick="modalImport(this)">
                                        <i class="fas fa-upload"></i>
                                        Import
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary mr-2 float-right"
                                        data-id="{{ isset($costCentersConstruction[0]->id) ? $costCentersConstruction[0]->id : '' }}"
                                        onclick="modalAdd(this)">
                                        <i class="fas fa-plus"></i>
                                        Tambah Cost Center
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="tableCostCenterConstruction" class="table table-bordered text-nowrap w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            No.
                                        </th>
                                        <th>
                                            Nama
                                        </th>
                                        <th>
                                            Kode
                                        </th>
                                        <th>
                                            Amount
                                        </th>
                                        <th>
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($costCentersConstruction as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}.</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->code }}</td>
                                            <td>{{ formatRupiah($item->amount) }}</td>
                                            <td>
                                            @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 3)
                                                    <button type="button" class="badge badge-primary border-0 p-1"
                                                        data-toggle="modal" onclick="modalEdit(this)"
                                                        data-id="{{ $item->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="badge badge-danger border-0 p-1"
                                                        data-toggle="modal" onclick="confirmDelete(this)"
                                                        data-id="{{ $item->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                                <a href="#{{ $item->id }}" class="badge badge-info p-1"
                                                    data-toggle="modal">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Divisi Technology --}}
                <div class="col-12 col-md-4">
                    <div class="card card-outline rounded-partner card-primary p-3">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h2 class="card-title"><b>Divisi Teknologi</b></h2>
                            </div>
                            <div class="col-6">
                                @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 3)
                                    <button type="button" class="btn btn-sm btn-warning float-right"
                                        data-id="{{ isset($costCentersTechnology[0]->id) ? $costCentersTechnology[0]->id : '' }}"
                                        onclick="modalImport(this)">
                                        <i class="fas fa-upload"></i>
                                        Import
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary mr-2 float-right"
                                        data-id="{{ isset($costCentersTechnology[0]->id) ? $costCentersTechnology[0]->id : '' }}"
                                        onclick="modalAdd(this)">
                                        <i class="fas fa-plus"></i>
                                        Tambah Cost Center
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="tableCostCenterTechnology" class="table table-bordered text-nowrap w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            No.
                                        </th>
                                        <th>
                                            Nama
                                        </th>
                                        <th>
                                            Kode
                                        </th>
                                        <th>
                                            Amount
                                        </th>
                                        <th>
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($costCentersTechnology as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}.</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->code }}</td>
                                            <td>{{ formatRupiah($item->amount) }}</td>
                                            <td>
                                            @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 3)
                                                    <button type="button" class="badge badge-primary border-0 p-1"
                                                        data-toggle="modal" onclick="modalEdit(this)"
                                                        data-id="{{ $item->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="badge badge-danger border-0 p-1"
                                                        data-toggle="modal" onclick="confirmDelete(this)"
                                                        data-id="{{ $item->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                                <a href="#{{ $item->id }}" class="badge badge-info p-1"
                                                    data-toggle="modal">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/adminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $('#tableCostCenterProcurement, #tableCostCenterConstruction, #tableCostCenterTechnology').DataTable({
                "lengthChange": false,
                "searching": false,
                "scrollX": true
            });

            // merapihkan pagination button datatable
            $('.dataTables_paginate.paging_simple_numbers').addClass('float-right');
        });
    </script>
@endpush
