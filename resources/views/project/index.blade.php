@extends('layouts.admin')

@section('title')
    Project
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
                    <h1>Project</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><strong>Project</strong></li>
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
                                    <h3 class="card-title">List Project - <a href="{{ route('project.archive') }}"
                                            class="btn btn-xs btn-outline-secondary rounded-partner">Archive</a></h3>
                                </div>
                                @if (auth()->user()->role_id != 5)
                                    <div class="col-6">
                                        <a href="{{ route('project.create') }}"
                                            class="btn btn-sm btn-primary rounded-partner float-right"><i
                                                class="fas fa-plus"></i> Buat Project</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="projectTable" class="table table-bordered text-nowrap">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 45%">
                                            Nama Project
                                        </th>
                                        <th style="width: 10%">
                                            PIC
                                        </th>
                                        <th style="width: 18%">
                                            Progress
                                        </th>
                                        <th style="width: 5%">
                                            Tanggal Mulai
                                        </th>
                                        <th style="width: 5%">
                                            Tanggal Selesai
                                        </th>
                                        <th style="width: 5%">
                                            Sisa Hari
                                        </th>
                                        <th style="width: 15%; text-align: center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projects as $project)
                                        <tr>
                                            <td class="text-wrap">{{ $project->name }}
                                                @if ($project->deadline->isPast())
                                                    <span class="badge badge-danger">
                                                        Overdue
                                                    </span>
                                                    <br>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $project->pic->name }}
                                            </td>
                                            <td>
                                                @php
                                                    $finished = $project->tasks->where('status', 'Done')->count();
                                                    $total = $project->tasks->count();
                                                    if ($finished == null || $total == null) {
                                                        $progress = 0;
                                                    } else {
                                                        $progress = ($finished / $total) * 100;
                                                    }
                                                @endphp
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-success-2" role="progressbar"
                                                        aria-valuenow="57" aria-valuemin="0" aria-valuemax="100"
                                                        style="width: {{ $progress }}%">
                                                    </div>
                                                </div>
                                                <small>
                                                    @if ($progress != null)
                                                        {{ number_format($progress, 1, ',', '') }}
                                                    @else
                                                        No Progress
                                                    @endif
                                                </small>
                                            </td>
                                            <td>{{ $project->start->toFormattedDateString('d/m/y') }}</td>
                                            @if ($project->deadline->isPast())
                                                <td bgcolor="ea9999">
                                                    {{ $project->deadline->toFormattedDateString('d/m/y') }}
                                                </td>
                                            @elseif ($project->deadline->diffInDays($today) <= '7')
                                                <td bgcolor="ffe599" class="text-black">
                                                    {{ $project->deadline->toFormattedDateString('d/m/y') }}
                                                </td>
                                            @else
                                                <td>{{ $project->deadline->toFormattedDateString('d/m/y') }}</td>
                                            @endif
                                            @if ($project->deadline->isPast())
                                                <td bgcolor="ea9999">
                                                    {{ $project->deadline->diffInDays($today, true) }} days`
                                                </td>
                                            @elseif ($project->deadline->diffInDays($today) <= '7')
                                                <td bgcolor="ffe599" class="text-black">
                                                    {{ $project->deadline->diffInDays($today, true) }} days`
                                                </td>
                                            @else
                                                <td>{{ $project->deadline->diffInDays($today, true) }} days`</td>
                                            @endif
                                            <td class="text-center">
                                                @if (auth()->user()->role_id != 5)
                                                    <a class="btn btn-sm btn-info rounded-partner" title="Detail Project"
                                                        href="{{ route('project.detail', $project->kode) }}">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @if ($project->status != 'Finished')
                                                        <a class="btn btn-sm btn-success rounded-partner"
                                                            title="Edit Project"
                                                            href="{{ route('project.edit', $project->kode) }}">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                    @endif
                                                    {{-- ! Belum diimplement --}}
                                                    <a class="btn btn-sm btn-primary rounded-partner muted"
                                                        title="To-do List"
                                                        href="{{ route('project.task', $project->kode) }}">
                                                        <i class="fas fa-tasks"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-secondary rounded-partner muted"
                                                        title="Dokumen Project" href="#">
                                                        <i class="fas fa-file"></i>
                                                    </a>
                                                @else
                                                    <a class="btn btn-sm btn-info rounded-partner"
                                                        href="{{ route('project.detail', $project->kode) }}">
                                                        <i class="fa-solid fa-eye"></i>
                                                        View Project
                                                    </a>
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

    <script type="text/javascript">
        $(function() {
            $('#projectTable').DataTable({
                paging: true,
                processing: true,
                searching: false,
                info: true,
                scrollX: true,
                headerScroll: true,
                ordering: false,
                language: {
                    processing: '<i class="fa fa-spinner fa-spin"></i><span class="sr-only">Loading...</span>',
                    paginate: {
                        previous: '<i class="fas fa-angle-left"></i>',
                        next: '<i class="fas fa-angle-right"></i>'
                    },
                    emptyTable: 'Tidak ada data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                    infoFiltered: '(filtered from _MAX_ total records)',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    zeroRecords: 'Data tidak ditemukan'
                },
            });
        });
    </script>
@endpush
