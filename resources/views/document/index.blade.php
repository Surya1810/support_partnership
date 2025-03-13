@extends('layouts.admin')

@section('title')
    Document
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
                    <h1>Document</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><strong>Document</strong></li>
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
                                    <h3 class="card-title">Document List</h3>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="float-right btn btn-sm btn-primary rounded-partner ml-2"
                                        data-toggle="modal" data-target="#importDocument">
                                        <i class="fa-solid fa-file-import"></i> Import
                                    </button>
                                    <button type="button" class="float-right btn btn-sm btn-primary rounded-partner"
                                        data-toggle="modal" data-target="#addDocument">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="documentTable" class="table table-bordered text-nowrap text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 10%">
                                            Number
                                        </th>
                                        <th style="width: 10%">
                                            Type
                                        </th>
                                        <th style="width: 10%">
                                            Date
                                        </th>
                                        <th style="width: 35%">
                                            Purpose
                                        </th>
                                        <th style="width: 15%">
                                            Company
                                        </th>
                                        <th style="width: 15%">
                                            Description
                                        </th>
                                        <th style="width: 5%">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents as $key => $document)
                                        <tr>
                                            <td>{{ $document->number }}</td>
                                            @php
                                                $type = explode(',', $document->type);
                                            @endphp
                                            <td>
                                                @foreach ($type as $jenis)
                                                    <span class="badge badge-primary">{{ $jenis }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $document->date }}</td>
                                            <td>{{ $document->purpose }}</td>
                                            <td>{{ $document->company }}</td>
                                            <td>{{ $document->desc }}</td>
                                            <td>
                                                {{-- <button type="button" class="btn btn-sm btn-warning rounded-partner"
                                                    data-toggle="modal" data-target="#editPartner{{ $partner->id }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button> --}}

                                                <button class="btn btn-sm btn-danger rounded-partner"
                                                    onclick="deleteDocument({{ $document->id }})"><i
                                                        class="fas fa-trash"></i></button>
                                                <form id="delete-form-{{ $document->id }}"
                                                    action="{{ route('document.destroy', $document->id) }}" method="POST"
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

    <!-- Modal Add Document-->
    <div class="modal fade" id="addDocument" tabindex="-1" aria-labelledby="addDocumentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDocumentLabel">Add New Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('document.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="number" class="mb-0 form-label col-form-label-sm">Document Number</label>
                            <input type="text" class="form-control @error('number') is-invalid @enderror" id="number"
                                name="number" placeholder="Enter document number" value="{{ old('number') }}" required>
                            @error('number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="type" class="mb-0 form-label col-form-label-sm">Type</label>
                            <select id="type" name="type[]" class="form-control" multiple="multiple" required
                                style="width: 100%;">
                                <option></option>
                                <option value="Invoice">Invoice</option>
                                <option value="Kuitansi">Kuitansi</option>
                                <option value="Penawaran">Penawaran</option>
                                <option value="Perjanjian">Perjanjian</option>
                                <option value="Permohonan PKL">Permohonan PKL</option>
                            </select>

                            <label for="date" class="mb-0 form-label col-form-label-sm">Date</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                                name="date" placeholder="Enter document date" value="{{ old('date') }}" required>
                            @error('date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="purpose" class="mb-0 form-label col-form-label-sm">Purpose</label>
                            <input type="text" class="form-control @error('purpose') is-invalid @enderror"
                                id="purpose" name="purpose" placeholder="Enter purpose of the letter"
                                value="{{ old('purpose') }}" required>
                            @error('purpose')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <label for="company" class="mb-0 form-label col-form-label-sm">Company</label>
                            <select id="company" name="company" class="form-control" required style="width: 100%;">
                                <option></option>
                                <option value="Partnership Procurement">Partnership Procurement</option>
                                <option value="Partnership Technology">Partnership Technology</option>
                                <option value="Partnership Construction">Partnership Construction</option>
                                <option value="Rakindo Karya Putra">Rakindo Karya Putra</option>
                                <option value="Sanrian">Sanrian</option>
                                <option value="Shuriken">Shuriken</option>
                            </select>

                            <label for="desc" class="mb-0 form-label col-form-label-sm">Description <small
                                    class="text-danger">*Optional</small></label>
                            <input type="text" class="form-control @error('desc') is-invalid @enderror" id="desc"
                                name="desc" placeholder="Enter description" value="{{ old('desc') }}">
                            @error('desc')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import Document-->
    <div class="modal fade" id="importDocument" tabindex="-1" aria-labelledby="importDocumentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importDocumentLabel">Import Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('document.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
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

    <!-- Modal Edit Partner-->
    @foreach ($documents as $data)
        <div class="modal fade" id="editPartner{{ $data->id }}" tabindex="-1" aria-labelledby="editPartnerLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPartnerLabel">Edit Partner</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('partner.update', $data->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name" class="mb-0 form-label col-form-label-sm">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Enter partner name"
                                    value="{{ $data->name }}">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <label for="contact" class="mb-0 form-label col-form-label-sm">Contact</label>
                                <input type="text" class="form-control @error('contact') is-invalid @enderror"
                                    id="contact" name="contact" placeholder="Enter contact name"
                                    value="{{ $data->contact }}">
                                @error('contact')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <label for="number" class="mb-0 form-label col-form-label-sm">Number</label>
                                <input type="number" class="form-control @error('number') is-invalid @enderror"
                                    id="number" name="number" placeholder="Enter contact number"
                                    value="{{ $data->number }}">
                                @error('number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <label for="desc" class="mb-0 form-label col-form-label-sm">Description</label>
                                <input type="text" class="form-control @error('desc') is-invalid @enderror"
                                    id="desc" name="desc" placeholder="Enter partner description"
                                    value="{{ $data->desc }}">
                                @error('desc')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning rounded-partner">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
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
            $('#documentTable').DataTable({
                "paging": true,
                'processing': true,
                "searching": false,
                "info": true,
                "scrollX": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
            });
        });

        // Inisialisasi Select2
        $('#type').select2({
            tags: true, // Aktifkan fitur input manual
            placeholder: 'Choose type',
            allowClear: true,
        });

        $('#company').select2({
            placeholder: 'Choose company',
        });

        function deleteDocument(id) {
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
