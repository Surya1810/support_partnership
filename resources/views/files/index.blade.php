@extends('layouts.admin')

@section('title')
    Files Management
@endsection

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/adminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/adminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/adminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/adminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <style>
        .pagination svg {
            width: 1rem !important;
            height: 1rem !important;
            vertical-align: middle;
        }

        .pagination .relative {
            display: inline-flex;
            align-items: center;
        }

        .pagination nav>div:first-child {
            display: none;
        }
    </style>
@endpush

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Files</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><strong>Files</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid text-sm">
            <div class="row">
                <!-- Tabel on going application-->
                <div class="col-12">
                    <div class="card card-outline rounded-partner card-primary">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h3 class="card-title">Seluruh File</h3>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('files.create') }}"
                                        class="btn btn-sm btn-primary rounded-partner float-right"><i
                                            class="fas fa-plus"></i> Upload File</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            {{-- Filter dan Search --}}
                            <form method="GET" action="{{ route('files.index') }}" class="mb-4">
                                <div class="row">
                                    <div class="col-md-2">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Cari nama file..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="category" class="form-control">
                                            <option value="">-- Semua Kategori --</option>
                                            <option value="Data Administrasi & Umum"
                                                {{ request('category') == 'Data Administrasi & Umum' ? 'selected' : '' }}>
                                                Data Administrasi & Umum</option>
                                            <option value="Data Keuangan"
                                                {{ request('category') == 'Data Keuangan' ? 'selected' : '' }}>Data Keuangan
                                            </option>
                                            <option value="Data Proyek / Operasional"
                                                {{ request('category') == 'Data Proyek / Operasional' ? 'selected' : '' }}>
                                                Data Proyek / Operasional</option>
                                            <option value="Data Aset & Inventaris"
                                                {{ request('category') == 'Data Aset & Inventaris' ? 'selected' : '' }}>
                                                Data Aset & Inventaris</option>
                                            <option value="Data Penjualan & Pemasaran"
                                                {{ request('category') == 'Data Penjualan & Pemasaran' ? 'selected' : '' }}>
                                                Data Penjualan & Pemasaran</option>
                                            <option value="Dokumen Legal"
                                                {{ request('category') == 'Dokumen Legal' ? 'selected' : '' }}>Dokumen
                                                Legal</option>
                                            <option value="Dokumentasi"
                                                {{ request('category') == 'Dokumentasi' ? 'selected' : '' }}>Dokumentasi
                                            </option>
                                            <option value="Template"
                                                {{ request('category') == 'Template' ? 'selected' : '' }}>Template
                                            </option>
                                            <option value="Lainnya"
                                                {{ request('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="year" class="form-control">
                                            <option value="">-- Semua Tahun --</option>
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}"
                                                    {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary" type="submit">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <div class="row">
                                @forelse ($files as $file)
                                    <div class="col-md-3 mb-4">
                                        <div class="card position-relative h-100">
                                            <div class="card-body text-center d-flex flex-column justify-content-between">

                                                {{-- Icon file berdasarkan tipe --}}
                                                <div class="mb-2">
                                                    @php
                                                        $ext = pathinfo($file->file_path, PATHINFO_EXTENSION);
                                                        $icon = match ($ext) {
                                                            'pdf' => 'far fa-file-pdf text-danger',
                                                            'doc', 'docx' => 'far fa-file-word text-primary',
                                                            'xls', 'xlsx' => 'far fa-file-excel text-success',
                                                            default => 'far fa-file text-muted',
                                                        };
                                                    @endphp
                                                    <i class="{{ $icon }} fa-4x"></i>
                                                </div>

                                                <div class="text-truncate" title="{{ $file->name }}">
                                                    {{ $file->name }}
                                                </div>

                                                <small class="text-muted">Diunggah:
                                                    {{ $file->created_at->format('d M Y') }}</small>
                                                <small class="text-muted">Oleh:
                                                    {{ $file->user->name }}</small>

                                                {{-- Dropdown aksi 3 titik --}}
                                                <div class="dropdown position-absolute" style="top: 10px; right: 10px;">
                                                    <button class="btn btn-sm btn-light" data-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        {{-- Preview Modal Trigger --}}
                                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                                            data-target="#previewModal{{ $file->id }}">
                                                            <i class="fas fa-eye mr-2"></i> Preview
                                                        </a>

                                                        {{-- Edit --}}
                                                        <a class="dropdown-item"
                                                            href="{{ route('files.edit', $file->id) }}">
                                                            <i class="fas fa-edit mr-2"></i> Edit
                                                        </a>

                                                        {{-- Download --}}
                                                        <a class="dropdown-item"
                                                            href="{{ asset('storage/' . $file->file_path) }}" download>
                                                            <i class="fas fa-download mr-2"></i> Download
                                                        </a>

                                                        {{-- Share --}}
                                                        <a class="dropdown-item" href="#"
                                                            onclick="copyShareLink('{{ route('files.share', $file->id) }}')">
                                                            <i class="fas fa-link mr-2"></i>Share
                                                        </a>

                                                        {{-- Hapus --}}
                                                        <button class="dropdown-item text-danger"
                                                            onclick="deleteFile({{ $file->id }})"><i
                                                                class="fas fa-trash mr-2"></i> Hapus
                                                        </button>
                                                        <form id="delete-form-{{ $file->id }}"
                                                            action="{{ route('files.destroy', $file->id) }}" method="POST"
                                                            style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </div>
                                                </div>

                                                {{-- Modal Preview --}}
                                                @if (Str::endsWith($file->file_path, ['.pdf', '.doc', '.docx', '.xls', '.xlsx']))
                                                    <div class="modal fade" id="previewModal{{ $file->id }}"
                                                        tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-xl" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">{{ $file->name }}</h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Tutup">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body p-0">
                                                                    @php
                                                                        $url = Storage::url($file->file_path);
                                                                    @endphp

                                                                    @if (Str::endsWith($file->file_path, '.pdf'))
                                                                        <iframe
                                                                            src="{{ asset('storage/' . $file->file_path) }}"
                                                                            width="100%" height="600px"
                                                                            frameborder="0"></iframe>
                                                                    @elseif(Str::endsWith($file->file_path, ['.doc', '.docx', '.xls', '.xlsx']))
                                                                        <iframe
                                                                            src="https://docs.google.com/gview?url={{ urlencode(asset('storage/' . $file->file_path)) }}&embedded=true"
                                                                            width="100%" height="600px"
                                                                            frameborder="0"></iframe>
                                                                    @else
                                                                        <div class="text-center p-4">Preview tidak tersedia
                                                                            untuk file ini.</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center">
                                        <p>Tidak ada file ditemukan.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="mt-4">
                                {{ $files->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

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

    <script>
        function deleteFile(id) {
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

    <script>
        function copyShareLink(link) {
            navigator.clipboard.writeText(link).then(function() {
                alert('Link berhasil disalin ke clipboard!');
            }, function(err) {
                alert('Gagal menyalin link');
            });
        }
    </script>
@endpush
