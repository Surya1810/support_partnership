@extends('layouts.admin')

@section('title')
    Asset
@endsection

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/adminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/adminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/adminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/adminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Asset</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><strong>Asset</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline rounded-partner card-primary">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h3 class="card-title">Asset List</h3>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="float-right btn btn-sm btn-primary rounded-partner ml-2"
                                        data-toggle="modal" data-target="#addAsset">
                                        <i class="fa-solid fa-plus"></i> Add
                                    </button>
                                    <button type="button" class="float-right btn btn-sm btn-primary rounded-partner ml-2"
                                        data-toggle="modal" data-target="#importAsset">
                                        <i class="fa-solid fa-file-import"></i> Import
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="assetTable" class="table table-bordered text-nowrap text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th rowspan="2" class="align-middle">
                                            RFID
                                        </th>
                                        <th rowspan="2" class="align-middle">
                                            Code
                                        </th>
                                        <th rowspan="2" class="align-middle">
                                            Name
                                        </th>
                                        <th rowspan="2" class="align-middle">
                                            Type
                                        </th>
                                        <th rowspan="2" class="align-middle">
                                            Condition
                                        </th>
                                        <th rowspan="2" class="align-middle">
                                            Maintenance
                                        </th>
                                        <th rowspan="2" class="align-middle">
                                            PIC
                                        </th>
                                        <th rowspan="2" class="align-middle">
                                            Value
                                        </th>
                                        <th colspan="3" class="align-middle">
                                            Location
                                        </th>
                                        <th rowspan="2" style="width: 5%" class="align-middle">
                                            Action
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Building</th>
                                        <th>Floor</th>
                                        <th>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($assets as $key => $asset)
                                        <tr>
                                            <td>{{ $asset->tag->rfid_number }}</td>
                                            <td>{{ $asset->code }}</td>
                                            <td>{{ $asset->name }}</td>
                                            <td>{{ $asset->type }}</td>
                                            <td>{{ $asset->condition }}</td>
                                            <td>{{ $asset->tgl_perawatan }}</td>
                                            <td>{{ $asset->user->username }}</td>
                                            <td>{{ $asset->tahun_perolehan }} -
                                                {{ formatRupiah($asset->harga_perolehan) }}</td>
                                            <td>{{ $asset->gedung }}</td>
                                            <td>{{ $asset->lantai }}</td>
                                            <td>{{ $asset->ruangan }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning rounded-partner"
                                                    data-toggle="modal" data-target="#editAsset{{ $asset->id }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>

                                                <button class="btn btn-sm btn-danger rounded-partner"
                                                    onclick="deleteAsset({{ $asset->id }})"><i
                                                        class="fas fa-trash"></i></button>
                                                <form id="delete-form-{{ $asset->id }}"
                                                    action="{{ route('asset.destroy', $asset->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
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

    <!-- Modal Add Asset-->
    <div class="modal fade" id="addAsset" aria-labelledby="addAssetLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAssetLabel">Add Asset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('asset.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <label for="tag" class="mb-0 form-label col-form-label-sm">RFID Number</label>
                                <select class="form-control tag" style="width: 100%;" id="tag" name="tag"
                                    required>
                                    <option></option>
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->rfid_number }}"
                                            {{ old('tag') == $tag->rfid_number ? 'selected' : '' }}>
                                            {{ $tag->rfid_number }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tag')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="name" class="mb-0 form-label col-form-label-sm">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}"
                                    placeholder="Enter asset name" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="code" class="mb-0 form-label col-form-label-sm">Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    id="name" name="code" value="{{ old('code') }}"
                                    placeholder="Enter asset code" required>
                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import Asset-->
    <div class="modal fade" id="importAsset" tabindex="-1" aria-labelledby="importAssetLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importAssetLabel">Import Asset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('asset.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="pic" class="mb-0 form-label col-form-label-sm">PIC</label>
                            <select class="form-control pic" style="width: 100%;" id="pic" name="pic"
                                required>
                                <option></option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('pic') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pic')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="file" class="mb-0 form-label col-form-label-sm">Upload File</label>
                            <input class="form-control @error('file') is-invalid @enderror" id="file" name="file"
                                placeholder="Choose file" value="{{ old('file') }}" required type="file"
                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            @error('file')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('assets/adminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            $('#assetTable').DataTable({
                "paging": true,
                'processing': true,
                "searching": true,
                "info": true,
                "scrollX": true,
                "ordering": false,
            });
        });

        $(function() {

            //Initialize Select2 Elements
            $('.tag').select2({
                placeholder: "Select RFID Number",
                allowClear: true,
            })
            $('.pic').select2({
                placeholder: "Select PIC",
            })
        });

        function deleteAsset(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('delete-form-' + id).submit();
                } else if (
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swal(
                        'Cancelled',
                        'Your data is safe !',
                        'error'
                    )
                }
            })
        }
    </script>
@endpush
