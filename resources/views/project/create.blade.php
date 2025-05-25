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
                                        <label for="name" class="small">Project name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" placeholder="Enter project name"
                                            value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="client" class="small">Client</label>
                                        <select class="form-control client" style="width: 100%;" id="client"
                                            name="client" required>
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
                                        <label for="creative_brief" class="small">Creative Brief</label>
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
                                        <input type="hidden" value="{{ auth()->user()->department_id }}" name="department_id">
                                        <label for="department_select_id" class="mb-0 form-label col-form-label-sm"
                                            class="small">Department</label>
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
                                        <select class="form-control pic" style="width: 100%;" id="pic" name="pic" required>
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
                                        <label for="assisten" class="small">Team Members</label>
                                        <select class="form-control team select2 @error('assisten') is-invalid @enderror"
                                            multiple="multiple" style="width: 100%;" id="assisten" name="assisten[]" required>
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
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="nilai_pekerjaan" class="small">Nilai Pekerjaan</label>
                                        <input type="text"
                                            class="form-control price @error('nilai_pekerjaan') is-invalid @enderror"
                                            placeholder="Rp0" id="nilai_pekerjaan" name="nilai_pekerjaan"
                                            value="{{ old('nilai_pekerjaan') }}" required>
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
                                    <label for="pph" class="small">PPH</label>
                                    <select class="form-control pph" style="width: 100%;" id="pph" name="pph" required>
                                        <option value="0" disabled selected>Pilih PPH</option>
                                        <option value="1.5" {{ old('pph') == 1.5 ? 'selected' : '' }}>
                                            1.5%
                                        </option>
                                        <option value="2" {{ old('pph') == 2 ? 'selected' : '' }}>
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
                                        placeholder="Rp0" id="margin" value="{{ old('margin') }}" readonly>
                                    @error('margin')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                                {{-- ? Biaya Lain-lain --}}
                                <label for="items" class="mb-0 mt-3 ml-2 border-0">Biaya Lain-lain</label>
                                <div class="table-responsive border-0">
                                    <table class="table table-sm border-0" id="items-table-other-cost">
                                        <tbody>
                                            <tr>
                                                <td class="border-0">
                                                    <div class="row g-2 align-items-end">
                                                        <div class="col-md-4">
                                                            <label class="mb-0 form-label form-label-sm small">Nama</label>
                                                            <input type="text" name="othercosts[0][item_name]"
                                                                class="form-control form-control-sm"
                                                                placeholder="Masukkan nama item" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label
                                                                class="mb-0 form-label form-label-sm small">Nominal</label>
                                                            <input type="text" name="othercosts[0][unit_price]"
                                                                class="form-control form-control-sm price"
                                                                placeholder="Rp0" min="0" step="0.01" required>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-item-other-cost mt-4">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" id="add-item-other-cost"
                                    class="btn btn-primary btn-sm rounded-partner ml-3 mt-0">
                                    <i class="fas fa-plus"></i>
                                </button>
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
                                                    <div class="row g-2 align-items-end">
                                                        <div class="col-md-1">
                                                            <label
                                                                class="mb-0 form-label form-label-sm small">Jenis</label>
                                                            <select name="items[0][item_type]"
                                                                class="form-control form-control-sm" required>
                                                                <option value="" disabled selected>Pilih Jenis
                                                                </option>
                                                                @foreach ($costCenterCategories as $category)
                                                                    <option value="{{ $category->id }}">
                                                                        {{ $category->code . ' - ' . $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="mb-0 form-label form-label-sm small">Nama</label>
                                                            <input type="text" name="items[0][item_name]"
                                                                class="form-control form-control-sm"
                                                                placeholder="Masukkan nama item" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label
                                                                class="mb-0 form-label form-label-sm small">Nominal</label>
                                                            <input type="text" name="items[0][unit_price]"
                                                                class="form-control form-control-sm price"
                                                                placeholder="Rp0" min="0" step="0.01" required>
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
                                    class="btn btn-primary btn-sm rounded-partner ml-3 mt-0">
                                    <i class="fas fa-plus"></i>
                                </button>

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
                                            value="{{ old('profit_perusahaan') }}">
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
                                            value="{{ old('profit_penyusutan') }}">
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
                                        value="{{ old('profit_divisi') }}">
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
                                        value="{{ old('profit_bonus') }}">
                                    <small class="text-muted">Nilai: <span id="value_bonus">Rp0</span></small>
                                    @error('profit_bonus')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="start" class="small">Start Date</label>

                                        <input type="date" class="form-control @error('start') is-invalid @enderror"
                                            id="start" name="start" value="{{ old('start') }}" required>

                                        @error('start')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="deadline" class="small">Due Date</label>

                                        <input type="date"
                                            class="form-control @error('deadline') is-invalid @enderror" id="deadline"
                                            name="deadline" value="{{ old('deadline') }}" required>

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

            // Fungsi untuk tombol add item dan remove item rincian RAB
            let itemIndexOtherCost = 1;

            $('#add-item-other-cost').click(function() {
                const newRow = `
                    <tr>
                        <td class="border-0">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="mb-0 form-label form-label-sm small">Nama</label>
                                    <input type="text" name="othercosts[${itemIndexOtherCost}][item_name]"
                                        class="form-control form-control-sm"
                                        placeholder="Masukkan nama item" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="mb-0 form-label form-label-sm small">Nominal</label>
                                    <input type="text" name="othercosts[${itemIndexOtherCost}][unit_price]"
                                        class="form-control form-control-sm price"
                                        placeholder="Rp0" min="0" step="0.01" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button"
                                        class="btn btn-danger btn-sm remove-item-other-cost mt-4">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;

                $('#items-table-other-cost tbody').append(newRow);
                $('#items-table-other-cost .price').last().inputmask({
                    alias: 'numeric',
                    prefix: 'Rp',
                    digits: 0,
                    groupSeparator: '.',
                    autoGroup: true,
                    removeMaskOnSubmit: true,
                    rightAlign: false
                });
                calculateSP2DandMargin();
                itemIndexOtherCost++;
            });

            // Fungsi untuk menghapus baris item
            $('#items-table-other-cost').on('click', '.remove-item-other-cost', function() {
                $(this).closest('tr').remove();
                calculateSP2DandMargin();
            });

            // Fungsi untuk tombol add item dan remove item rincian RAB
            let itemIndex = 1;
            const costCenterCategories = @json($costCenterCategories);

            $('#add-item').click(function() {
                let optionsHtml = '<option value="">Pilih Jenis</option>';
                costCenterCategories.forEach(category => {
                    optionsHtml +=
                        `<option value="${category.id}">${category.code} - ${category.name}</option>`;
                });

                const newRow = `
                    <tr>
                        <td class="border-0">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-1">
                                    <label class="mb-0 form-label form-label-sm small">Jenis</label>
                                    <select name="items[${itemIndex}][item_type]" class="form-control form-control-sm" required>
                                        ${optionsHtml}
                                    </select>
                                </div>
                                <div class="col-md-3
                                ">
                                    <label class="mb-0 form-label form-label-sm small">Nama</label>
                                    <input type="text" name="items[${itemIndex}][item_name] small"
                                        class="form-control form-control-sm"
                                        placeholder="Masukkan nama item" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="mb-0 form-label form-label-sm small">Nominal</label>
                                    <input type="text" name="items[${itemIndex}][unit_price] small"
                                        class="form-control form-control-sm price"
                                        placeholder="Rp0" min="0" step="0.01" required>
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
                `;

                $('#items-table tbody').append(newRow);
                $('#items-table .price').last().inputmask({
                    alias: 'numeric',
                    prefix: 'Rp',
                    digits: 0,
                    groupSeparator: '.',
                    autoGroup: true,
                    removeMaskOnSubmit: true,
                    rightAlign: false
                });
                calculateSP2DandMargin();
                itemIndex++;
            });

            // Fungsi untuk menghapus baris item
            $('#items-table').on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
                calculateSP2DandMargin();
            });

            // Trigger untuk hitung SP2D
            $('#nilai_pekerjaan').on('input', calculateSP2DandMargin);
            $('#ppn, #pph').on('change', calculateSP2DandMargin);

            // Hitung ulang jika ada perubahan di biaya lain-lain
            $(document).on('input', '#items-table-other-cost .price', calculateSP2DandMargin);
            // Hitung ulang jika ada perubahan di RAB
            $(document).on('input', '#items-table .price', calculateSP2DandMargin);

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

        })

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

            // Hitung total biaya lain-lain
            let totalOtherCosts = 0;
            $('#items-table-other-cost .price').each(function() {
                totalOtherCosts += getNumberFromCurrency($(this).val());
            });

            // Hitung total item RAB
            let totalRAB = 0;
            $('#items-table .price').each(function() {
                totalRAB += getNumberFromCurrency($(this).val());
            });

            const margin = sp2d - totalOtherCosts - totalRAB;

            // Tampilkan hasil SP2D dan Margin
            $('#sp2d').val(formatCurrency(sp2d));
            $('#sp2d_numeric').val(sp2d);
            $('#margin').val(formatCurrency(margin));

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

            const ranges = {
                profit_perusahaan: [10, 40],
                profit_penyusutan: [10, 20],
                profit_divisi: [10, 20],
                profit_bonus: [10, 30]
            };

            let hasRangeError = false;

            // Cek dan beri feedback jika ada yang keluar dari batas
            Object.entries(ranges).forEach(([id, [min, max]]) => {
                const raw = $(`#${id}`).val().trim();

                if (raw === '') {
                    $(`#${id}`).removeClass('is-invalid');
                    return;
                }

                const value = parseFloat(raw);

                if (value > max) {
                    hasRangeError = true;
                    $(`#${id}`).addClass('is-invalid');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: `Persentase ${label(id)} harus antara ${min}% - ${max}%`,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    $(`#${id}`).removeClass('is-invalid');
                }
            });

            // Jika ada kesalahan range, jangan lanjut hitung
            if (hasRangeError) return;

            const perusahaanValue = margin * (perusahaanPercent / 100);
            const penyusutanValue = margin * (penyusutanPercent / 100);
            const divisiValue = margin * (divisiPercent / 100);
            const bonusValue = margin * (bonusPercent / 100);
            const total = perusahaanPercent + penyusutanPercent + divisiPercent + bonusPercent;

            if (total > 100) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Total persentase tidak boleh lebih dari 100%',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
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
