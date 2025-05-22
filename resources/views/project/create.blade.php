@extends('layouts.admin')

@section('title')
    Create Project
@endsection

@push('css')
    <script src="https://cdn.tiny.cloud/1/4ce77u0y45a0kxjxqgmq8hyqdgrqd8pdetaervdmri41d1qa/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea#creative_brief',
            plugins: 'table lists',
            toolbar: 'undo redo | blocks| bold italic | bullist numlist checklist | code | table | alignleft aligncenter alignright alignjustify | outdent indent'
        });
    </script>
@endpush

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Project</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('project.index') }}">Project</a>
                        </li>
                        <li class="breadcrumb-item active"><strong>Create</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="card rounded-partner card-outline card-primary w-100">
                    <div class="card-header">
                        <h3 class="card-title">Project Create</h3>
                    </div>
                    <form action="{{ route('project.store') }}" method="POST" enctype="multipart/form-data"
                        autocomplete="off">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">Project name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" placeholder="Enter project name"
                                            value="{{ old('name') }}">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="client">Client</label>
                                        <select class="form-control client" style="width: 100%;" id="client"
                                            name="client">
                                            <option></option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}"
                                                    {{ old('client') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('client')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="creative_brief">Creative Brief</label>
                                        <textarea class="form-control @error('creative_brief') is-invalid @enderror" rows="4"
                                            placeholder="Enter creative brief..." id="creative_brief" name="creative_brief">{{ old('creative_brief') }}</textarea>
                                        @error('creative_brief')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="department_id"
                                            class="mb-0 form-label col-form-label-sm">Department</label>
                                        <select class="form-control department muted" style="width: 100%;" id="department_id"
                                            name="department_id" readonly required>
                                            <option></option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}"
                                                    {{ auth()->user()->department_id == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="pic">PIC</label>
                                        <select class="form-control pic" style="width: 100%;" id="pic" name="pic">
                                            <option></option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ old('pic') == $user->id ? 'selected' : '' }}>{{ $user->username }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('pic')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="assisten">Team Members</label>
                                        <select class="form-control team select2 @error('assisten') is-invalid @enderror"
                                            multiple="multiple" style="width: 100%;" id="assisten" name="assisten[]">
                                            @foreach ($users as $user)
                                                @if (old('assisten'))
                                                    <option value="{{ $user->id }}"
                                                        {{ in_array($user->id, old('assisten')) ? 'selected' : '' }}>
                                                        {{ $user->username }}</option>
                                                @else
                                                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('assisten')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="nilai_pekerjaan">Nilai Pekerjaan</label>
                                        <input type="text"
                                            class="form-control price @error('nilai_pekerjaan') is-invalid @enderror" placeholder="Rp0"
                                            id="nilai_pekerjaan" name="nilai_pekerjaan" value="{{ old('nilai_pekerjaan') }}">
                                        @error('nilai_pekerjaan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="ppn">PPN</label>
                                        <select class="form-control ppn" style="width: 100%;" id="ppn"
                                            name="ppn">
                                            <option value="" disabled selected>Pilih PPN</option>
                                            <option value="11" {{ old('ppn') == 11 ? 'selected' : '' }}>
                                                11%
                                            </option>
                                            <option value="12" {{ old('ppn') == 12 ? 'selected' : '' }}>
                                                12%
                                            </option>
                                        </select>
                                        @error('ppn')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <label for="pph">PPH</label>
                                    <input type="text" class="form-control @error('pph') is-invalid @enderror"
                                        id="pph" name="pph" placeholder="Masukkan nilai PPH"
                                        value="{{ old('pph') }}">
                                    @error('pph')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="sp2d">SP2D</label>
                                        <input type="text"
                                            class="form-control price muted @error('sp2d') is-invalid @enderror" id="sp2d"
                                            name="sp2d" placeholder="Rp0"
                                            value="{{ old('sp2d') }}" readonly>
                                        @error('sp2d')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="biaya_lain_lain">Biaya Lain-lain</label>
                                        <input type="text"
                                            class="form-control price @error('biaya_lain_lain') is-invalid @enderror"
                                            id="biaya_lain_lain" name="biaya_lain_lain" placeholder="Rp0"
                                            value="{{ old('biaya_lain_lain') }}">
                                        @error('biaya_lain_lain')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <label for="margin">Margin</label>
                                    <input type="text"
                                        class="form-control muted price @error('margin') is-invalid @enderror"
                                        placeholder="Rp0"
                                        value="{{ old('margin') }}" readonly>
                                    @error('margin')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                                {{-- ? Input RAB --}}
                                <label for="items" class="mb-0 mt-3 ml-2 border-0">Rincian Item RAB</label>
                                <div class="table-responsive border-0">
                                    <table class="table table-sm border-0" id="items-table">
                                        <tbody>
                                            <tr>
                                                <td class="border-0">
                                                    <div class="row col-6 g-2 align-items-end">
                                                        <div class="col-md-4">
                                                            <label class="mb-0 form-label form-label-sm">Nama</label>
                                                            <input type="text" name="items[0][item_name]"
                                                                class="form-control form-control-sm"
                                                                placeholder="Masukkan nama item">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="mb-0 form-label form-label-sm">Nominal</label>
                                                            <input type="text" name="items[0][unit_price]"
                                                                class="form-control form-control-sm price"
                                                                placeholder="Rp0" min="0" step="0.01">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-item mt-4">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" id="add-item"
                                    class="btn btn-primary btn-sm rounded-partner ml-3 mt-0"><i
                                        class="fas fa-plus"></i></button>

                                {{-- ? Net Profit --}}
                                <div class="col-12">
                                    <hr>
                                    <label>
                                        Net Profit <small>*optional</small>
                                    </label>
                                </div>
                                <div class="col-lg-1">
                                    <div class="form-group">
                                        <label for="profit_perusahaan">Perusahaan (%)</label>
                                        <input type="text"
                                            class="form-control percent @error('profit_perusahaan') is-invalid @enderror"
                                            id="profit_perusahaan" name="profit_perusahaan" placeholder="0%"
                                            value="{{ old('profit_perusahaan') }}">
                                        @error('profit_perusahaan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <div class="form-group">
                                        <label for="profit_penyusutan">Penyusutan (%)</label>
                                        <input type="text"
                                            class="form-control percent @error('profit_penyusutan') is-invalid @enderror"
                                            id="profit_penyusutan" name="profit_penyusutan" placeholder="0%"
                                            value="{{ old('profit_penyusutan') }}">
                                        @error('profit_penyusutan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <label for="profit_divisi">Kas Divisi (%)</label>
                                    <input type="text"
                                        class="form-control percent @error('profit_divisi') is-invalid @enderror"
                                        id="profit_divisi" name="profit_divisi" placeholder="0%"
                                        value="{{ old('profit_divisi') }}">
                                    @error('profit_divisi')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-1">
                                    <label for="profit_bonus">Bonus Tim (%)</label>
                                    <input type="text"
                                        class="form-control percent @error('profit_bonus') is-invalid @enderror"
                                        id="profit_bonus" name="profit_bonus" placeholder="0%"
                                        value="{{ old('profit_bonus') }}">
                                    @error('profit_bonus')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control status @error('status') is-invalid @enderror"
                                            style="width: 100%;" id="status" name="status">
                                            <option></option>
                                            <option value="Discussion"
                                                {{ old('status') == 'Discussion' ? 'selected' : '' }}>
                                                Discussion</option>
                                            <option value="Planning" {{ old('status') == 'Planning' ? 'selected' : '' }}>
                                                Planning</option>
                                            <option value="On Going" {{ old('status') == 'On Going' ? 'selected' : '' }}>
                                                On
                                                Going</option>
                                            {{-- <option value="Finished" {{ old('status') == 'Finished' ? 'selected' : '' }}>
                                                Finished</option> --}}
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="urgency">Urgency</label>
                                        <select class="form-control urgency @error('urgency') is-invalid @enderror"
                                            style="width: 100%;" id="urgency" name="urgency">
                                            <option></option>
                                            <option value="High" {{ old('urgency') == 'High' ? 'selected' : '' }}>High
                                            </option>
                                            <option value="Medium" {{ old('urgency') == 'Medium' ? 'selected' : '' }}>
                                                Medium</option>
                                            <option value="Low" {{ old('urgency') == 'Low' ? 'selected' : '' }}>Low
                                            </option>
                                        </select>
                                        @error('urgency')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="start">Start Date</label>

                                        <input type="date" class="form-control @error('start') is-invalid @enderror"
                                            id="start" name="start" value="{{ old('start') }}">

                                        @error('start')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="deadline">Due Date</label>

                                        <input type="date"
                                            class="form-control @error('deadline') is-invalid @enderror" id="deadline"
                                            name="deadline" value="{{ old('deadline') }}">

                                        @error('deadline')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer rounded-partner">
                            <button type="submit" class="btn btn-primary rounded-partner float-right">
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/adminLTE/plugins/inputmask/jquery.inputmask.min.js') }}"></script>

    <script>
        $(function() {
            $('.pic').select2({
                placeholder: "Select PIC",
                allowClear: true,
            })
            $('.client').select2({
                placeholder: "Select Client",
                allowClear: true,
            })
            $('.team').select2({
                placeholder: "Select team member",
                allowClear: true,
            })
            $('.status').select2({
                placeholder: "Select status",
                minimumResultsForSearch: -1,
                allowClear: true,
            })
            $('.urgency').select2({
                placeholder: "Select urgency",
                minimumResultsForSearch: -1,
                allowClear: true,
            })

            $('.percent').inputmask('percentage', {
                suffix: '%',
                digits: 2,
                digitsOptional: true,
                placeholder: '0',
                autoUnmask: true,
                removeMaskOnSubmit: true
            });

            $('.price').inputmask({
                alias: 'numeric',
                prefix: 'Rp',
                digits: 0,
                groupSeparator: '.',
                autoGroup: true,
                removeMaskOnSubmit: true,
                rightAlign: false
            });
        })
    </script>
@endpush
