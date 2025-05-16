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
                                        data-id="{{ isset($costCentersProcurement[0]->department_id) ? $costCentersProcurement[0]->department_id : '' }}"
                                        onclick="modalImport(this)">
                                        <i class="fas fa-upload"></i>
                                        Import
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary mr-2 float-right"
                                        data-id="{{ isset($costCentersProcurement[0]->department_id) ? $costCentersProcurement[0]->department_id : '' }}"
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
                                        data-id="{{ isset($costCentersConstruction[0]->department_id) ? $costCentersConstruction[0]->department_id : '' }}"
                                        onclick="modalImport(this)">
                                        <i class="fas fa-upload"></i>
                                        Import
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary mr-2 float-right"
                                        data-id="{{ isset($costCentersConstruction[0]->department_id) ? $costCentersConstruction[0]->department_id : '' }}"
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
                                        data-id="{{ isset($costCentersTechnology[0]->department_id) ? $costCentersTechnology[0]->department_id : '' }}"
                                        onclick="modalImport(this)">
                                        <i class="fas fa-upload"></i>
                                        Import
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary mr-2 float-right"
                                        data-id="{{ isset($costCentersTechnology[0]->department_id) ? $costCentersTechnology[0]->department_id : '' }}"
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

    {{-- Modal Add --}}
    <div class="modal fade" id="modalAddCostCenter" tabindex="-1" role="dialog" aria-labelledby="modalAddLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formAddCostCenter" method="POST" action="{{ route('cost.center.store') }}">
                @csrf
                <input type="hidden" name="department_id" id="department_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAddLabel">Tambah Cost Center</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cost_center_name">Nama Cost Center</label>
                            <input type="text" class="form-control" id="cost_center_name" name="name"
                                placeholder="Masukkan nama cost center" required>
                        </div>
                        <div class="form-group">
                            <label for="cost_center_amount">Amount</label>
                            <input type="text" class="form-control price @error('amount') is-invalid @enderror"
                                id="amount" name="amount" value="{{ old('amount') }}"
                                placeholder="Masukkan nilai anggaran" min="0" step="0.01" required>
                            @error('amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="modalEditCostCenter" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formEditCostCenter">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Edit Cost Center</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="form-group">
                            <label for="edit-code">Kode</label>
                            <input type="text" class="form-control" id="edit-code" name="code" disabled>
                        </div>
                        <div class="form-group">
                            <label for="edit-name">Nama Cost Center</label>
                            <input type="text" class="form-control" id="edit-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-amount">Amount</label>
                            <input type="text" class="form-control price @error('amount') is-invalid @enderror"
                                id="edit-amount" name="amount" value="{{ old('amount') }}"
                                placeholder="Masukkan nilai anggaran" min="0" step="0.01" required>
                            @error('amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Import Cost Center --}}
    <div class="modal fade" id="modalImportCostCenter" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formImportCostCenter" enctype="multipart/form-data" method="POST"
                action="{{ route('cost.center.import') }}">
                @csrf
                <input type="hidden" name="department_id" id="import-department-id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Cost Center dari Excel</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="import_file">Pilih File Excel</label>
                            <input type="file" class="form-control-file" name="import_file" id="import_file" required
                                accept=".xlsx, .xls">
                        </div>
                        <small class="form-text text-muted">
                            Format kolom header: <strong>Nama</strong> dan <strong>Amount</strong>
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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

            // format ke rupiah
            $('.price').inputmask({
                alias: 'numeric',
                prefix: 'Rp',
                digits: 0,
                groupSeparator: '.',
                autoGroup: true,
                removeMaskOnSubmit: true,
                rightAlign: false
            });
        });

        function modalAdd(button) {
            const id = $(button).data('id');
            $('#formAddCostCenter')[0].reset();
            $('#department_id').val(id);
            $('#modalAddCostCenter').modal('show');
        }

        function confirmDelete(button) {
            const id = $(button).data('id');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Cost center ini beserta turunannya akan terhapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('cost.center.delete', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Cost center telah dihapus.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            console.log(xhr);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat menghapus data.'
                            });
                        }
                    });
                }
            });
        }

        function modalEdit(button) {
            const id = $(button).data('id');

            $.ajax({
                url: "{{ route('cost.center.show', ':id') }}".replace(':id', id),
                type: 'GET',
                success: function(response) {
                    $('#edit-id').val(response.id);
                    $('#edit-code').val(response.code);
                    $('#edit-name').val(response.name);
                    $('#edit-amount').val(response.amount);

                    $('#modalEditCostCenter').modal('show');
                },
                error: function(xhr) {
                    if (xhr.status === 404) {
                        return Swal.fire('Error', 'Cost center tidak ditemukan', 'error');
                    }
                    return Swal.fire('Error', 'Gagal mengambil data cost center', 'error');
                }
            });
        }

        // Submit form edit
        $('#formEditCostCenter').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit-id').val();

            $.ajax({
                url: "{{ route('cost.center.update', ':id') }}".replace(':id', id),
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: $('#edit-name').val(),
                    amount: $('#edit-amount').inputmask('unmaskedvalue')
                },
                success: function(response) {
                    $('#modalEditCostCenter').modal('hide');
                    Swal.fire('Berhasil', 'Cost center berhasil diperbarui', 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 404) {
                        return Swal.fire('Gagal', 'Cost center tidak ditemukan', 'error');
                    }
                    return Swal.fire('Gagal', 'Terjadi kesalahan saat memperbarui cost center',
                        'error');
                }
            });
        });

        function modalImport(button) {
            const departmentId = $(button).data('id');
            $('#import-department-id').val(departmentId);
            $('#modalImportCostCenter').modal('show');
        }
    </script>
@endpush
