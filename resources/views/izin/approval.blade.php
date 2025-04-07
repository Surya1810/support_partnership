@extends('layouts.admin')

@section('title')
    Approval
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
            <h1>Approval</h1>
            <ol class="breadcrumb text-black-50">
                <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Izin</li>
                <li class="breadcrumb-item active"><strong>Approval</strong></li>
            </ol>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid text-sm">
            <div class="row">
                <!-- Tabel pending manager-->
                <div class="col-12">
                    <div class="card card-outline rounded-partner card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Pending Approval</h3>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="pendingManagerTable" class="table table-bordered">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th style="width: 10%">
                                            User
                                        </th>
                                        <th style="width: 15%">
                                            Jenis
                                        </th>
                                        <th style="width: 30%">
                                            Keterangan
                                        </th>
                                        <th style="width: 10%">
                                            Tanggal Pengajuan
                                        </th>
                                        <th style="width: 10%">
                                            Tanggal Izin
                                        </th>
                                        <th style="width: 5%">
                                            Waktu
                                        </th>
                                        <th style="width: 10%">
                                            Status
                                        </th>
                                        <th style="width: 10%">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pending as $pend)
                                        <tr>
                                            <td>{{ $pend->user->name }}</td>
                                            <td>{{ $pend->jenis }}</td>
                                            <td>{{ $pend->keterangan }}</td>
                                            <td>{{ $pend->created_at->toFormattedDateString('d/m/y') }}</td>
                                            <td>{{ $pend->tanggal->toFormattedDateString('d/m/y') }}</td>
                                            <td>
                                                @isset($pend->lama)
                                                    {{ $pend->lama }} hari
                                                @else
                                                    {{ $pend->jam }}
                                                @endisset
                                            </td>
                                            <td>
                                                @if ($pend->status == 'pending')
                                                    <span class="badge badge-secondary">{{ $pend->status }}</span>
                                                @elseif ($pend->status == 'approved')
                                                    <span class="badge badge-success">{{ $pend->status }}</span>
                                                @elseif ($pend->status == 'rejected')
                                                    <span class="badge badge-danger">{{ $pend->status }}</span>
                                                @endif
                                            </td>
                                            <td>

                                                <button class="btn btn-sm btn-success rounded-partner"
                                                    onclick="approveExpense({{ $pend->id }})">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                                <form id="approve-form-{{ $pend->id }}"
                                                    action="{{ route('izin.approve', $pend->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                                <button class="btn btn-sm btn-danger rounded-partner"
                                                    onclick="rejectExpense({{ $pend->id }})"><i
                                                        class="fa fa-times"></i></button>
                                                <form id="reject-form-{{ $pend->id }}"
                                                    action="{{ route('izin.reject', $pend->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tabel all-->
                <div class="col-12">
                    <div class="card card-outline rounded-partner card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Seluruh Pengajuan Izin</h3>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="allTable" class="table table-bordered">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th style="width: 10%">
                                            User
                                        </th>
                                        <th style="width: 15%">
                                            Jenis
                                        </th>
                                        <th style="width: 35%">
                                            Keterangan
                                        </th>
                                        <th style="width: 10%">
                                            Tanggal Pengajuan
                                        </th>
                                        <th style="width: 10%">
                                            Tanggal Izin
                                        </th>
                                        <th style="width: 10%">
                                            Waktu
                                        </th>
                                        <th style="width: 10%">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($all as $izin)
                                        <tr>
                                            <td>{{ $izin->user->name }}</td>
                                            <td>{{ $izin->jenis }}</td>
                                            <td>{{ $izin->keterangan }}</td>
                                            <td>{{ $izin->created_at->toFormattedDateString('d/m/y') }}</td>
                                            <td>{{ $izin->tanggal->toFormattedDateString('d/m/y') }}</td>
                                            <td>
                                                @isset($izin->lama)
                                                    {{ $izin->lama }} hari
                                                @else
                                                    {{ $izin->jam }}
                                                @endisset
                                            </td>
                                            <td>
                                                @if ($izin->status == 'pending')
                                                    <span class="badge badge-secondary">{{ $izin->status }}</span>
                                                @elseif ($izin->status == 'approved')
                                                    <span class="badge badge-success">{{ $izin->status }}</span>
                                                @elseif ($izin->status == 'rejected')
                                                    <span class="badge badge-danger">{{ $izin->status }}</span>
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

    <script src="{{ asset('assets/adminLTE/plugins/inputmask/jquery.inputmask.min.js') }}"></script>


    <script type="text/javascript">
        $(function() {
            $('#pendingManagerTable').DataTable({
                "paging": true,
                'processing': true,
                "searching": true,
                "info": true,
                "scrollX": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
            });
            $('#allTable').DataTable({
                "paging": true,
                'processing': true,
                "searching": true,
                "info": true,
                "scrollX": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
            });


        });

        function rejectExpense(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Please provide a reason for rejection:',
                icon: 'warning',
                input: 'textarea',
                inputPlaceholder: 'Type your reason here...',
                inputAttributes: {
                    'aria-label': 'Rejection reason'
                },
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to write a reason!'
                    }
                },
                confirmButtonColor: '#d33',
                confirmButtonText: 'Reject'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('reject-form-' + id);
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'reason';
                    input.value = result.value;
                    form.appendChild(input);
                    form.submit();
                }
            });
        }

        function approveExpense(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#5cb85c',
                confirmButtonText: 'Approve'
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('approve-form-' + id).submit();
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
