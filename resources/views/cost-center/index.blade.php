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

                                                @if (strtolower($item->name) == 'project')
                                                    <button onclick="modalDetail(this)"
                                                        class="badge badge-info btn-sm p-1 border-0"
                                                        data-id="{{ $item->id }}" data-toggle="modal">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endif
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

                                                @if (strtolower($item->name) == 'project')
                                                    <button onclick="modalDetail(this)" data-id="{{ $item->id }}"
                                                        class="badge badge-info p-1 border-0" data-toggle="modal">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endif
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

                                                @if (strtolower($item->name) == 'project')
                                                    <button onclick="modalDetail(this)"
                                                        class="badge badge-info p-1 border-0" data-toggle="modal"
                                                        data-id="{{ $item->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endif
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

    {{-- Modal Detail Cost Center --}}
    <div class="modal fade" id="modal-detail-cost-center" tabindex="-1" role="dialog"
        aria-labelledby="modalDetailTitle" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Cost Center</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="detail-cost-center-id">
                    <p><strong>Kode:</strong> <span id="detail-cost-center-kode"></span></p>
                    <p><strong>Nama:</strong> <span id="detail-cost-center-nama"></span></p>
                    <p><strong>Amount:</strong> Rp<span id="detail-cost-center-amount"></span></p>
                    <hr>
                    <h6>Sub Cost Center</h6>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Amount</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sub-cost-center-table-body">
                            <tr>
                                <td colspan="4" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group mt-3">
                        <label>Tambah Sub Cost Center</label>
                        <input type="text" class="form-control mb-2" placeholder="Nama Sub Cost Center"
                            id="sub-cost-center-name" required>
                        <input type="text" class="form-control price mb-2" placeholder="Amount"
                            id="sub-cost-center-amount" min="0" step="0.01" required>
                        <button type="button" class="btn btn-sm btn-success"
                            onclick="addSubCostCenter()">Tambah</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Sub Cost Center --}}
    <div class="modal fade" id="modal-edit-sub" tabindex="-1" role="dialog" aria-labelledby="modalEditSubTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <form id="form-edit-sub">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Sub Cost Center</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-sub-id">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control" id="edit-sub-name">
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" class="form-control price" id="edit-sub-amount" min="0"
                                step="0.01">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-success">Simpan</button>
                    </div>
                </form>
            </div>
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
        let dataDetailCostCenter = null;

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

        function modalDetail(button) {
            let id;

            // Cek apakah button adalah elemen HTML atau objek JS biasa
            if (button instanceof HTMLElement) {
                id = $(button).data('id');
            } else if (button.dataset && button.dataset.id) {
                id = button.dataset.id;
            } else if (button.id) {
                id = button.id;
            }

            if (!id) {
                console.error("ID Cost Center tidak ditemukan.");
                return;
            }

            $('#detail-cost-center-id').val(id);
            $('#sub-cost-center-table-body').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');

            $.get("{{ route('cost.center.show', ':id') }}".replace(':id', id), function(data) {
                $('#detail-cost-center-kode').text(data.code);
                $('#detail-cost-center-nama').text(data.name);
                $('#detail-cost-center-amount').text(formatRupiah(data.amount));
                $('#modal-detail-cost-center').modal('show');

                dataDetailCostCenter = data;
                let rows = '';

                if (data.subs.length > 0) {
                    data.subs.forEach(sub => {
                        rows += `<tr>
                        <td>${sub.name}</td>
                        <td>Rp${formatRupiah(sub.amount)}</td>
                            <td>
                                <button class="badge badge-primary border-0" onclick="editSubCostCenter(${sub.id})"><i class="fas fa-edit"></i></button>
                                <button class="badge badge-danger border-0" onclick="deleteSubCostCenter(${sub.id}, '${data.id}')"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="4" class="text-center">Belum ada sub cost center</td></tr>';
                }

                $('#sub-cost-center-table-body').html(rows);
            });
        }

        function addSubCostCenter() {
            const parentId = $('#detail-cost-center-id').val();
            const name = $('#sub-cost-center-name').val();
            const amount = $('#sub-cost-center-amount').inputmask('unmaskedvalue');

            if (!name || isNaN(amount)) {
                return alert('Nama dan Amount harus diisi');
            }

            // Cek total amount
            $.get('/cost-centers/show/' + parentId + '/json', function(data) {
                const totalSubAmount = data.subs.reduce((sum, item) => sum + parseFloat(item.amount), 0);
                const sisaAmount = data.amount - totalSubAmount;

                // Cegah penambahan jika sisa amount sudah habis atau amount melebihi sisa
                if (sisaAmount <= 0 || amount > sisaAmount) {
                    return Swal.fire(
                        'Error',
                        `Tidak dapat menambahkan sub cost center.
                        Sisa anggaran dari cost center induk adalah Rp${formatRupiah(sisaAmount)}.`,
                        'error'
                    );
                }

                // Kirim POST
                $.post('/cost-centers/sub-cost-center', {
                    _token: '{{ csrf_token() }}',
                    parent_id: parentId,
                    name: name,
                    amount: amount
                }, function() {
                    modalDetail({
                        dataset: {
                            id: parentId
                        }
                    }); // Reload modal
                    $('#sub-cost-center-name').val('');
                    $('#sub-cost-center-amount').val('');
                });
            });
        }

        function editSubCostCenter(id) {
            $.get('/cost-centers/sub-cost-center/' + id, function(data) {
                $('#edit-sub-id').val(data.id);
                $('#edit-sub-name').val(data.name);
                $('#edit-sub-amount').val(data.amount);
                $('#modal-edit-sub').modal('show');
            });
        }

        $('#form-edit-sub').submit(function(e) {
            e.preventDefault();

            const id = $('#edit-sub-id').val();
            const name = $('#edit-sub-name').val();
            const amount = parseFloat($('#edit-sub-amount').inputmask('unmaskedvalue'));
            const parentAmount = parseFloat(dataDetailCostCenter.amount);

            // Hitung total sub amount lain (selain yang sedang diedit)
            const totalOtherSubs = dataDetailCostCenter.subs
                .filter(sub => sub.id != id)
                .reduce((sum, sub) => sum + parseFloat(sub.amount), 0);

            const sisaAmount = parentAmount - totalOtherSubs;

            if (sisaAmount <= 0 || amount > sisaAmount) {
                return Swal.fire(
                    'Error',
                    `Sub cost center yang diedit (Rp${formatRupiah(amount)}) melebihi sisa anggaran (Rp${formatRupiah(sisaAmount)}) dari cost center induk!`,
                    'error'
                );
            }

            $.ajax({
                url: '/cost-centers/sub-cost-center/' + id,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    amount: amount
                },
                success: function() {
                    $('#modal-edit-sub').modal('hide');
                    modalDetail({
                        dataset: {
                            id: $('#detail-cost-center-id').val()
                        }
                    });
                }
            });
        });

        function deleteSubCostCenter(id, parentId) {
            Swal.fire({
                title: 'Hapus Sub Cost Center?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/cost-centers/sub-cost-center/' + id,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.fire('Terhapus!', 'Sub Cost Center berhasil dihapus.', 'success');
                            modalDetail({
                                dataset: {
                                    id: parentId
                                }
                            });
                        }
                    });
                }
            });
        }

        function formatRupiah(angka) {
            if (!angka || isNaN(angka)) {
                return '0';
            }
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function editSubCostCenter(id) {
            $.get("{{ route('cost.center.sub.show', ':id') }}".replace(':id', id), function(data) {
                $('#edit-sub-id').val(data.id);
                $('#edit-sub-name').val(data.name);
                $('#edit-sub-amount').val(data.amount);
                $('#modal-edit-sub').modal('show');
            });
        }
    </script>
@endpush
