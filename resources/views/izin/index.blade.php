@extends('layouts.admin')

@section('title')
    Izin
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
            <h1>Izin</h1>
            <ol class="breadcrumb text-black-50">
                <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Pengajuan</li>
                <li class="breadcrumb-item active"><strong>Izin</strong></li>
            </ol>
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
                                    <h3 class="card-title">Pengajuan Izin Saya</h3>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-sm btn-primary rounded-partner float-right"
                                        data-toggle="modal" data-target="#addIzin"><i class="fas fa-plus"></i> Buat
                                        Izin</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="myizinTable" class="table table-bordered">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th style="width: 10%">
                                            Jenis
                                        </th>
                                        <th style="width: 50%">
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
                                    @foreach ($my_izin as $izin)
                                        <tr>
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

    <!-- Modal Add Izin-->
    <div class="modal fade" id="addIzin" tabindex="-1" aria-labelledby="addIzinLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addIzinLabel">Buat Pengajuan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <label for="jenis" class="mb-0 form-label col-form-label-sm">Jenis izin</label>
                                <select class="form-control jenis" style="width: 100%;" id="jenis" name="jenis"
                                    required>
                                    <option></option>
                                    <option value="Cuti Tahunan" {{ old('jenis') == 'Cuti Tahunan' ? 'selected' : '' }}>
                                        Cuti tahunan (Sisa: {{ $sisaCuti }})
                                    </option>
                                    <option value="Lembur / tukar off"
                                        {{ old('jenis') == 'Lembur / tukar off' ? 'selected' : '' }}>
                                        Lembur / tukar off
                                    </option>
                                    <option value="Izin tidak masuk"
                                        {{ old('jenis') == 'Izin tidak masuk' ? 'selected' : '' }}>
                                        Izin tidak masuk
                                    </option>
                                    <option value="Izin terlambat / pulang awal"
                                        {{ old('jenis') == 'Izin terlambat / pulang awal' ? 'selected' : '' }}>
                                        Izin terlambat / pulang awal
                                    </option>
                                    <option value="Izin tugas luar"
                                        {{ old('jenis') == 'Izin tugas luar' ? 'selected' : '' }}>
                                        Izin tugas luar
                                    </option>
                                </select>
                                @error('jenis')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="tanggal" class="mb-0 form-label col-form-label-sm">Tanggal Izin</label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                    id="tanggal" name="tanggal" value="{{ old('tanggal') }}" required>
                                @error('tanggal')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4" id="lama-container">
                                <label for="lama" class="mb-0 form-label col-form-label-sm">Lama Izin <small
                                        class="text-danger">*hari</small></label>
                                <input type="number" class="form-control @error('lama') is-invalid @enderror"
                                    id="lama" name="lama" value="{{ old('lama') }}" min=1
                                    placeholder="Jumlah hari">
                                @error('lama')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4" id="jam-container">
                                <label for="jam" class="mb-0 form-label col-form-label-sm">Jam</label>
                                <input type="time" class="form-control @error('jam') is-invalid @enderror"
                                    id="jam" name="jam" value="{{ old('jam') }}">
                                @error('jam')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="keterangan" class="mb-0 form-label col-form-label-sm">Keterangan</label>
                                <textarea class="form-control" name="keterangan" id="keterangan" cols="30"
                                    placeholder="Tulis keterangan detail"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">Submit</button>
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

    <script src="{{ asset('assets/adminLTE/plugins/inputmask/jquery.inputmask.min.js') }}"></script>


    <script type="text/javascript">
        $(function() {
            $('.jenis').select2({
                placeholder: "Pilih Jenis Izin",
                allowClear: true,
            })
        })

        $(function() {
            $('#myizinTable').DataTable({
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

        function toggleFields() {
            const jenis = document.getElementById('jenis').value;
            const lamaContainer = document.getElementById('lama-container');
            const jamContainer = document.getElementById('jam-container');

            if (jenis === 'Izin terlambat / pulang awal') {
                lamaContainer.style.display = 'none';
                jamContainer.style.display = 'block';
            } else {
                lamaContainer.style.display = 'block';
                jamContainer.style.display = 'none';
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Inisialisasi select2
            $('.jenis').select2({
                placeholder: "Pilih Jenis Izin",
                allowClear: true,
            })

            // Trigger toggleFields saat select2 berubah
            $('.jenis').on('change', function() {
                toggleFields();
            });

            // Jalankan sekali saat halaman dimuat
            toggleFields();
        });
    </script>
@endpush
