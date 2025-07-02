@extends('layouts.admin')

@section('title')
    Edit Projects
@endsection

@push('css')
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
                        <li class="breadcrumb-item active"><strong>Edit</strong></li>
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
                        <h3 class="card-title">Edit Project</h3>
                    </div>
                    <form action="{{ route('project.update', $project->id) }}" method="POST" enctype="multipart/form-data"
                        autocomplete="off" id="createProjectForm">
                        @method('PUT')
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="name" class="small">Nama Project</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" placeholder="Enter project name"
                                            value="{{ $project->name }}" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="client" class="small">Pemberi Pekerjaan/Client</label>
                                        <select class="form-control client" style="width: 100%;" id="client"
                                            name="client" required>
                                            <option></option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}"
                                                    {{ $project->client_id == $client->id ? 'selected' : '' }}>
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
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="hidden" value="{{ auth()->user()->department_id }}"
                                            name="department_id">
                                        <label for="department_select_id" class="mb-0 form-label col-form-label-sm"
                                            class="small">Divisi</label>
                                        <select class="form-control department muted" style="width: 100%;"
                                            id="department_select_id" name="department_id" readonly required disabled>
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
                                        <label for="pic" class="small">PIC</label>
                                        <select class="form-control pic is-invalid" style="width: 100%;" id="pic"
                                            name="pic">
                                            @foreach ($users as $user)
                                                <option {{ $project->pic->id == $user->id ? 'selected' : '' }}
                                                    value="{{ $user->id }}">{{ $user->username }}</option>
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
                                        <label for="assisten" class="small">Anggota Team</label>
                                        <select class="form-control team select2 @error('assisten') is-invalid @enderror"
                                            multiple="multiple" style="width: 100%;" id="assisten" name="assisten[]">
                                            @php
                                                $assisten = explode(',', $project->assisten_id);
                                            @endphp
                                            @foreach ($users as $user)
                                                @if (count($assisten) > 1)
                                                    <option
                                                        @foreach ($assisten as $team)
                                                    {{ $team == $user->id ? 'selected' : '' }} @endforeach
                                                        value="{{ $user->id }}">{{ $user->username }}</option>
                                                @else
                                                    <option {{ $project->assisten_id == $user->id ? 'selected' : '' }}
                                                        value="{{ $user->id }}">{{ $user->username }}</option>
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
                                <div class="col-12 mb-3">
                                    <hr>
                                    <small class="text-danger">
                                        *Pastikan perubahan Nilai Pekerjaan, PPN, PPH, dan pada bagian Net Profit telah
                                        didiskusikan dan dikonfirmasi oleh direktur secara langsung.
                                    </small>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="nilai_pekerjaan" class="small">Nilai Pekerjaan</label>
                                        <input type="text"
                                            class="form-control price @error('nilai_pekerjaan') is-invalid @enderror"
                                            placeholder="Rp0" id="nilai_pekerjaan" name="nilai_pekerjaan"
                                            value="{{ $project->financial->job_value }}" required>
                                        @error('nilai_pekerjaan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="ppn" class="small">PPN</label>
                                        <select class="form-control ppn" style="width: 100%;" id="ppn"
                                            name="ppn" required>
                                            <option value="0" disabled selected>Pilih PPN</option>
                                            <option value="11"
                                                {{ $project->financial->vat_percent == '11' ? 'selected' : '' }}>
                                                11%
                                            </option>
                                            <option value="12"
                                                {{ $project->financial->vat_percent == '12' ? 'selected' : '' }}>
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
                                    <label for="pph" class="small">PPH</label>
                                    <select class="form-control pph" style="width: 100%;" id="pph" name="pph"
                                        required>
                                        <option value="0" disabled selected>Pilih PPH</option>
                                        <option value="1.5"
                                            {{ $project->financial->tax_percent == '1.5' ? 'selected' : '' }}>
                                            1.5%
                                        </option>
                                        <option value="2"
                                            {{ $project->financial->tax_percent == '2' ? 'selected' : '' }}>
                                            2%
                                        </option>
                                    </select>
                                    @error('pph')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="sp2d" class="small">SP2D</label>
                                        <input type="text"
                                            class="form-control muted @error('sp2d') is-invalid @enderror" id="sp2d"
                                            placeholder="Rp0" value="{{ old('sp2d') }}" readonly>
                                        <input type="hidden" name="sp2d" id="sp2d_numeric">
                                        @error('sp2d')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label for="margin" class="small">Margin</label>
                                    <input type="text" class="form-control muted @error('margin') is-invalid @enderror"
                                        placeholder="Rp0" name="margin" id="margin" value="{{ old('margin') }}"
                                        readonly>
                                    <input type="hidden" name="margin" id="margin_numeric">
                                    @error('margin')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                {{-- ? Net Profit --}}
                                <div class="col-12 mt-3">
                                    <hr>
                                    <label>
                                        Net Profit
                                    </label>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="profit_perusahaan" class="small">Perusahaan (%)</label>
                                        <input type="text"
                                            class="form-control form-control-sm percent @error('profit_perusahaan') is-invalid @enderror"
                                            id="profit_perusahaan" name="profit_perusahaan" placeholder="0%"
                                            value="{{ (int) $project->profit->percent_company }}">
                                        <small class="text-muted">Nilai: <span id="value_perusahaan">Rp0</span></small>
                                        @error('profit_perusahaan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="profit_penyusutan" class="small">Penyusutan (%)</label>
                                        <input type="text"
                                            class="form-control form-control-sm percent @error('profit_penyusutan') is-invalid @enderror"
                                            id="profit_penyusutan" name="profit_penyusutan" placeholder="0%"
                                            value="{{ (int) $project->profit->percent_depreciation }}">
                                        <small class="text-muted">Nilai: <span id="value_penyusutan">Rp0</span></small>
                                        @error('profit_penyusutan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <label for="profit_divisi" class="small">Kas Divisi (%)</label>
                                    <input type="text"
                                        class="form-control form-control-sm percent @error('profit_divisi') is-invalid @enderror"
                                        id="profit_divisi" name="profit_divisi" placeholder="0%"
                                        value="{{ (int) $project->profit->percent_cash_department }}">
                                    <small class="text-muted">Nilai: <span id="value_divisi">Rp0</span></small>
                                    @error('profit_divisi')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2">
                                    <label for="profit_bonus" class="small">Bonus Tim (%)</label>
                                    <input type="text"
                                        class="form-control form-control-sm percent @error('profit_bonus') is-invalid @enderror"
                                        id="profit_bonus" name="profit_bonus" placeholder="0%"
                                        value="{{ (int) $project->profit->percent_team_bonus }}">
                                    <small class="text-muted">Nilai: <span id="value_bonus">Rp0</span></small>
                                    @error('profit_bonus')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12 mt-3">
                                    <hr>
                                    <label>
                                        Waktu Pengerjaan
                                    </label>
                                </div>
                                <div class="col-4 col-md-2">
                                    <div class="form-group">
                                        <label for="start" class="small">Tanggal Mulai</label>

                                        <input type="date" class="form-control @error('start') is-invalid @enderror"
                                            id="start" name="start"
                                            value="{{ \Carbon\Carbon::parse($project->start)->format('Y-m-d') }}"
                                            required>

                                        @error('start')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-4 col-md-2">
                                    <div class="form-group">
                                        <label for="deadline" class="small">Tanggal Selesai</label>

                                        <input type="date"
                                            class="form-control @error('deadline') is-invalid @enderror" id="deadline"
                                            name="deadline"
                                            value="{{ \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') }}"
                                            required>

                                        @error('deadline')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-4 col-md-2">
                                    <div class="form-group">
                                        <label for="total_days" class="small">Total Hari</label>

                                        <input type="numeric"
                                            class="form-control @error('total_days') is-invalid @enderror"
                                            id="total_days" name="total_days" value="{{ old('total_days') }}" readonly
                                            required>

                                        @error('total_days')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="col-12">
                                    <h6><b>Rencana Anggaran Biaya (RAB)</b></h6>
                                    <small class="text-danger">
                                        *Perubahan RAB hanya bisa dilakukan pada halaman Cost Center Project dan minimal
                                        oleh
                                        manager dari divisi terkait.
                                    </small>
                                </div>
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped mt-3" id="costCenterSubsTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th style="width: 5%">No.</th>
                                                    <th>Nama Item</th>
                                                    <th>Debet</th>
                                                    <th>Limit</th>
                                                    <th>Kode Ref.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($project->costCenters as $data)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $data->name }}</td>
                                                        <td>
                                                            {{ $data->amount_debit != 0 ? formatRupiah($data->amount_debit) : '-' }}
                                                        </td>
                                                        <td>
                                                            {{ $data->amount_credit != 0 ? formatRupiah($data->amount_credit) : '-' }}
                                                        </td>
                                                        <td>{{ $data->code_ref }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer rounded-partner">
                            <button type="submit" class="btn btn-primary rounded-partner float-right"
                                id=buttonSubmitProject>
                                Simpan Perubahan
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
    <script src="{{ asset('js/loading-overlay.js') }}"></script>
    <script type="text/javascript">
        const totalDebetProject = Number(@json($totalDebetProject));

        $(function() {
            $('.pic').select2({
                placeholder: "Select PIC",
                allowClear: true,
            });

            $('.client').select2({
                placeholder: "Select Client",
                allowClear: true,
            });

            $('.team').select2({
                placeholder: "Select team member",
                allowClear: true,
            });

            $('.percent').inputmask('percentage', {
                suffix: '%',
                digits: 3,
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

            // Trigger untuk hitung SP2D
            $('#nilai_pekerjaan').on('input', calculateSP2DandMargin);
            $('#ppn, #pph').on('change', calculateSP2DandMargin);

            // Trigger on input
            $('.percent').on('input', function() {
                calculateSP2DandMargin();
                calculateProfitShares();
            });

            // Trigger juga setiap kali margin dihitung ulang
            const originalCalculateSP2DandMargin = calculateSP2DandMargin;
            window.calculateSP2DandMargin = function() {
                originalCalculateSP2DandMargin();
                calculateProfitShares();
            };

            // Inisialisasi awal
            $('#profit_perusahaan, #profit_penyusutan, #profit_divisi, #profit_bonus').on('blur', function() {
                calculateProfitShares();
            });

            // Show loading
            $('#createProjectForm').on('submit', function() {
                $.LoadingOverlay("show");
            });

            $('#start, #deadline').on('change', calculateDays);

            calculateSP2DandMargin(true, Number(totalDebetProject));
            calculateDays();
        });

        function calculateDays() {
            const startDate = $('#start').val();
            const endDate = $('#deadline').val();

            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);

                // Periksa apakah end < start
                if (end < start) {
                    $('#total_days').val('');
                    Swal.fire({
                        'icon': 'error',
                        'toast': true,
                        'position': 'top-right',
                        'showConfirmButton': false,
                        'timer': 5000,
                        'timerProgressBar': true,
                        'text': 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai'
                    });
                } else {
                    // Hitung total hari (termasuk tanggal mulai dan selesai)
                    const timeDiff = end - start;
                    const totalDays = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) + 1;
                    $('#total_days').val(totalDays);
                }
            }
        }

        function formatCurrency(num) {
            return num.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            });
        }

        function getNumberFromCurrency(value) {
            if (!value) return 0;
            return parseFloat(value.replace(/[^0-9,-]+/g, '').replace(',', '.')) || 0;
        }

        function calculateSP2DandMargin() {
            const pekerjaan = getNumberFromCurrency($('#nilai_pekerjaan').val());
            const ppnPercent = parseFloat($('#ppn').val()) || 0;
            const pphPercent = parseFloat($('#pph').val()) || 0;

            const ppn = pekerjaan * (ppnPercent / 100);
            const pph = pekerjaan * (pphPercent / 100);
            const sp2d = pekerjaan - ppn - pph;

            const margin = sp2d - totalDebetProject;

            // Tampilkan hasil SP2D dan Margin
            $('#sp2d').val(formatCurrency(sp2d));
            $('#sp2d_numeric').val(sp2d);

            $('#margin').val(formatCurrency(margin));
            $('#margin_numeric').val(margin);

            // Lanjut hitung distribusi profit jika ada
            calculateProfitShares();
        }

        function calculateProfitShares() {
            const marginRaw = $('#margin').val();
            const margin = getNumberFromCurrency(marginRaw);

            const perusahaanPercent = parseFloat($('#profit_perusahaan').val()) || 0;
            const penyusutanPercent = parseFloat($('#profit_penyusutan').val()) || 0;
            const divisiPercent = parseFloat($('#profit_divisi').val()) || 0;
            const bonusPercent = parseFloat($('#profit_bonus').val()) || 0;

            const perusahaanValue = margin * (perusahaanPercent / 100);
            const penyusutanValue = margin * (penyusutanPercent / 100);
            const divisiValue = margin * (divisiPercent / 100);
            const bonusValue = margin * (bonusPercent / 100);
            const total = perusahaanPercent + penyusutanPercent + divisiPercent + bonusPercent;

            if (total > 100) {
                $('#buttonSubmitProject').attr('disabled', true);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Total persentase tidak boleh lebih dari 100%',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            } else {
                $('#buttonSubmitProject').attr('disabled', false);
            }

            $('#value_perusahaan').text(formatCurrency(perusahaanValue));
            $('#value_penyusutan').text(formatCurrency(penyusutanValue));
            $('#value_divisi').text(formatCurrency(divisiValue));
            $('#value_bonus').text(formatCurrency(bonusValue));
        }

        function label(id) {
            const labels = {
                profit_perusahaan: 'Perusahaan',
                profit_penyusutan: 'Penyusutan',
                profit_divisi: 'Kas Divisi',
                profit_bonus: 'Bonus Tim'
            };
            return labels[id] || id;
        }
    </script>
@endpush
