@extends('layouts.admin')

@section('title')
    Employee
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
                    <h1>Employee</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><strong>Employee</strong></li>
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
                                    <h3 class="card-title">Employee List</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="employeeTable" class="table table-bordered text-nowrap text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 5%">
                                            ID
                                        </th>
                                        <th>
                                            Name
                                        </th>
                                        <th>
                                            Position
                                        </th>
                                        <th>
                                            Email
                                        </th>
                                        <th>
                                            Department
                                        </th>
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $key => $user)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->role->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                {{ $user->department->name }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info rounded-partner"
                                                    data-toggle="modal" data-target="#infoEmployee{{ $user->id }}">
                                                    <i class="fas fa-regular fa-eye"></i>
                                                </button>
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

    <!-- Modal Employee Info-->
    @foreach ($users as $user)
        <div class="modal fade" id="infoEmployee{{ $user->id }}" tabindex="-1" aria-labelledby="infoEmployeeLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoEmployeeLabel">Employee Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p class="m-0"><strong>Personal Information</strong></p>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label class="mb-0 form-label col-form-label-sm">Name</label>
                                <p>{{ $user->name }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="mb-0 form-label col-form-label-sm">Nickname</label>
                                <p>{{ $user->username }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="mb-0 form-label col-form-label-sm">NIK</label>
                                <p>{{ $user->nik }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="mb-0 form-label col-form-label-sm">NPWP</label>
                                <p>{{ $user->npwp }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="mb-0 form-label col-form-label-sm">Phone</label>
                                <p>{{ $user->phone }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="mb-0 form-label col-form-label-sm">Address</label>
                                <p>{{ $user->address }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="mb-0 form-label col-form-label-sm">Religion</label>
                                <p>{{ $user->religion }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="mb-0 form-label col-form-label-sm">Birth</label>
                                <p>{{ $user->pob }}, {{ $user->dob->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="mb-0 form-label col-form-label-sm">Congenital Disease</label>
                                <p>{{ $user->disease }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="mb-0 form-label col-form-label-sm">Marriage Status</label>
                                <p>{{ $user->marriage }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="mb-0 form-label col-form-label-sm">Gender</label>
                                <p>{{ $user->gender }}</p>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="mb-0 form-label col-form-label-sm">Language Skills</label>
                                <p>{{ $user->language }}</p>
                            </div>
                        </div>
                        <hr>

                        <p class="m-0"><strong>Company Information</strong></p>
                        <div class="col-12 col-md-6">
                            <label class="mb-0 form-label col-form-label-sm">Email</label>
                            <p>{{ $user->email }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="mb-0 form-label col-form-label-sm">Position</label>
                            <p>{{ $user->email }}</p>
                        </div>
                        <hr>

                        <p class="m-0"><strong>Educational Information</strong></p>
                        <div class="col-12 col-md-6">
                            <label class="mb-0 form-label col-form-label-sm">Elementary School</label>
                            <p>{{ $user->elementary }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="mb-0 form-label col-form-label-sm">Junior High School</label>
                            <p>{{ $user->junior }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="mb-0 form-label col-form-label-sm">Senior High School</label>
                            <p>{{ $user->senior }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="mb-0 form-label col-form-label-sm">College</label>
                            <p>{{ $user->college }}</p>
                        </div>
                        <hr>

                        <p class="m-0"><strong>Account Information</strong></p>
                        <div class="col-12 col-md-6">
                            <label class="mb-0 form-label col-form-label-sm">Bank</label>
                            <p>{{ $user->bank }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="mb-0 form-label col-form-label-sm">Account Number</label>
                            <p>{{ $user->number }}</p>
                        </div>
                        <hr>
                    </div>
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
    <script src="{{ asset('assets/adminLTE/plugins/jszip/jszip.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/adminLTE/plugins/pdfmake/pdfmake.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/adminLTE/plugins/pdfmake/vfs_fonts.js') }}"></script> --}}
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            $('#employeeTable').DataTable({
                "paging": true,
                'processing': true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                // "scrollX": true,
                // width: "700px",
                // columnDefs: [{
                //     className: 'dtr-control',
                //     orderable: false,
                //     targets: -8
                // }]
            });
        });
    </script>
@endpush
