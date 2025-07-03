@extends('layouts.admin')

@section('title')
    Project Task Step
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
                    <h1>Project - <strong>{{ $project->kode }}</strong></h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('project.index') }}">Project</a>
                        </li>
                        <li class="breadcrumb-item active"><strong>Task Step</strong></li>
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
                    <div class="card card-primary card-outline card-outline-tabs rounded-partner card-primary">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link rounded" id="tabs_project"
                                        href="{{ route('project.detail', $project->kode) }}" role="tab"
                                        aria-controls="tabs_project" aria-selected="false">Overview</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" id="tabs_task"
                                        href="{{ route('project.task', $project->kode) }}" role="tab"
                                        aria-controls="tabs_task" aria-selected="true">Task Step</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tabs_review"
                                        href="{{ route('project.review', $project->kode) }}" role="tab"
                                        aria-controls="tabs_review" aria-selected="false">Review</a>
                                </li>
                                @if (auth()->user()->role_id != 5)
                                    <li class="nav-item">
                                        <a class="nav-link" id="tabs_finalization" href="{{ route('project.finalization', $project->kode) }}" role="tab"
                                            aria-controls="tabs_finalization" aria-selected="false">Finalization</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-four-tabContent">
                                <div class="tab-pane fade show active" id="project" role="tabpanel"
                                    aria-labelledby="tabs_task">
                                    <h5>Project - <strong>{{ $project->name }}</strong></h5>
                                    <table id="taskTable" class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width: 5%" class="sort">
                                                    No
                                                </th>
                                                <th style="width: 20%">
                                                    Title
                                                </th>
                                                <th style="width: 30%">
                                                    Description
                                                </th>
                                                <th style="width: 10%">
                                                    Attachment
                                                </th>
                                                <th style="width: 5%">
                                                    Status
                                                </th>
                                                <th style="width: 10%">
                                                    Info
                                                </th>
                                                @if (in_array(auth()->user()->id, $access->toArray()))
                                                    <th style="width: 20%">Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody class="text-sm">
                                            @foreach ($tasks as $key => $task)
                                                <tr>
                                                    <td class="text-center">{{ $key + 1 }}</td>
                                                    <td>{{ $task->title }}</td>
                                                    <td>{{ $task->desc }}</td>
                                                    <td class="text-center text-sm">
                                                        @if ($task->attachment == null)
                                                        @else
                                                            <a href="{{ $task->attachment }}"
                                                                target="_blank"><strong><u>Attachment</u></strong></a>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($task->status == 'Done')
                                                            <span class="badge badge-success">Done</span>
                                                        @else
                                                            <span class="badge badge-danger">Undone</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a>{{ $task->by }}</a><br><small
                                                            class="text-muted">{{ $task->updated_at->toFormattedDateString('d/m/y') }}</small>
                                                    </td>
                                                    @if (in_array(auth()->user()->id, $access->toArray()))
                                                        <td class="text-center">
                                                            @if ($task->status == 'Undone')
                                                                <a href="{{ route('task.status', $task->id) }}"
                                                                    class="btn btn-xs btn-success rounded-partner text-sm">
                                                                    Done
                                                                </a>
                                                            @else
                                                                <a href="{{ route('task.status', $task->id) }}"
                                                                    class="btn btn-xs btn-danger rounded-partner text-sm">
                                                                    Undone
                                                                </a>
                                                            @endif
                                                            <button type="button"
                                                                class="btn btn-sm btn-warning rounded-partner"
                                                                data-toggle="modal"
                                                                data-target="#editStepModal{{ $task->id }}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger rounded-partner"
                                                                onclick="deleteTask({{ $task->id }})"><i
                                                                    class="fas fa-trash"></i></button>
                                                            <form id="delete-form-{{ $task->id }}"
                                                                action="{{ route('task.destroy', $task->id) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer rounded-partner">
                            @if (in_array(auth()->user()->id, $access->toArray()))
                                <button type="button" class="btn btn-primary rounded-partner" data-toggle="modal"
                                    data-target="#addStepModal">
                                    Add Step
                                </button>
                            @endif

                            @if (auth()->user()->id == 1 || auth()->user()->id == $project->user_id)
                                <button type="button" class="btn btn-success rounded-partner float-right"
                                    data-toggle="modal" data-target="#finishModal">
                                    <i class="fas fa-check"></i>
                                    Finish Project
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Modal Add Step-->
    <div class="modal fade" id="addStepModal" tabindex="-1" aria-labelledby="addStepModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStepModalLabel">Add New Step</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('task.store', $project->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title" class="mb-0 form-label col-form-label-sm">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                id="title" name="title" placeholder="Enter step title"
                                value="{{ old('title') }}">
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="form-group">
                                <label for="desc" class="mt-3 mb-0 form-label col-form-label-sm">Description</label>
                                <textarea class="form-control @error('desc') is-invalid @enderror" rows="4"
                                    placeholder="Enter step description..." id="desc" name="desc">{{ old('desc') }}</textarea>
                                @error('desc')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="attachment" class="mb-0 form-label col-form-label-sm">Attachment <small
                                    class="text-danger">*Opsional</small></label>
                            <input type="text" class="form-control @error('attachment') is-invalid @enderror"
                                id="attachment" name="attachment" placeholder="Enter step attachment"
                                value="{{ old('attachment') }}">
                            @error('attachment')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">Add Step</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Finish-->
    <div class="modal fade" id="finishModal" aria-labelledby="finishModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="finishModalLabel">Finishing Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('project.done', $project->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="sp2d" class="mb-0 form-label col-form-label-sm">SP2D</label>
                            <input type="text" name="sp2d" class="form-control price" placeholder="Enter SP2D"
                                min="0" step="0.01" value="{{ old('sp2d') }}" required>

                            <label for="review" class="mb-0 form-label col-form-label-sm">Review</label>
                            <textarea class="form-control @error('review') is-invalid @enderror" rows="4"
                                placeholder="Enter Project review..." id="review" name="review" required>{{ old('review') }}</textarea>
                            @error('review')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success rounded-partner">Finish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($tasks as $task)
        <!-- Modal Edit Step-->
        <div class="modal fade" id="editStepModal{{ $task->id }}" tabindex="-1"
            aria-labelledby="editStepModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStepModalLabel">Edit Step</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('task.update', $task->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="title" class="mb-0 form-label col-form-label-sm">Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" placeholder="Enter step title"
                                    value="{{ $task->title }}">
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="form-group">
                                    <label for="desc"
                                        class="mt-3 mb-0 form-label col-form-label-sm">Description</label>
                                    <textarea class="form-control @error('desc') is-invalid @enderror" rows="4"
                                        placeholder="Enter step description..." id="desc" name="desc">{{ $task->desc }}</textarea>
                                    @error('desc')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <label for="attachment" class="mb-0 form-label col-form-label-sm">Attachment</label>
                                <input type="text" class="form-control @error('attachment') is-invalid @enderror"
                                    id="attachment" name="attachment" placeholder="Enter step attachment"
                                    value="{{ $task->attachment }}">
                                @error('attachment')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary rounded-partner">Update Step</button>
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

    <script src="{{ asset('assets/adminLTE/plugins/inputmask/jquery.inputmask.min.js') }}"></script>

    <script type="text/javascript">
        $('.price').inputmask({
            alias: 'numeric',
            prefix: 'Rp',
            digits: 0,
            groupSeparator: '.',
            autoGroup: true,
            removeMaskOnSubmit: true,
            rightAlign: false
        });

        $(document).ready(function() {
            $(function() {
                $('#taskTable').DataTable({
                    "paging": false,
                    'processing': true,
                    "lengthChange": true,
                    "searching": false,
                    "ordering": false,
                    "info": false,
                    "autoWidth": false,
                    "responsive": true,
                });
            });
        });

        function deleteTask(id) {
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
    <script type="text/javascript">
        @if (count($errors) > 0)
            $('#addStepModal').modal('show');
        @endif
        // @if (count($errors) > 0)
        //     $('#finishModal').modal('show');
        // @endif
    </script>
@endpush
