@extends('layouts.admin')

@section('title')
    Upload Files
@endsection

@push('css')
@endpush

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Files</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('files.index') }}">Project</a>
                        </li>
                        <li class="breadcrumb-item active"><strong>Upload</strong></li>
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
                        <h3 class="card-title">Upload Files</h3>
                    </div>
                    <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data"
                        autocomplete="off">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal" class="mb-0 form-label col-form-label-sm">Nama File</label>
                                        <input type="text" name="name" placeholder="Tulis Nama File"
                                            class="form-control @error('name') is-invalid @enderror" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="kategori" class="mb-0 form-label col-form-label-sm">Kategori</label>
                                        <select name="category" class="form-control select2" required>
                                            <option></option>
                                            <option value="Surat Penawaran">Surat Penawaran</option>
                                            <option value="Invoice">Invoice</option>
                                            <option value="Dokumen Lain">Dokumen Lain</option>
                                        </select>
                                        @error('kategori')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="file" class="mb-0 form-label col-form-label-sm">Upload File <small
                                                class="text-danger">*10MB</small></label>
                                        <input type="file" name="file" class="form-control-file" required>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer rounded-partner">
                            <button type="submit" class="btn btn-primary rounded-partner float-right">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.select2').select2({
                placeholder: "Pilih Kategori",
                allowClear: true,
            })
        })
    </script>
@endpush
