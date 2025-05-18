@extends('layouts.admin')

@section('title')
    Report
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
                    <h1>Report</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Finance</li>
                        <li class="breadcrumb-item active"><strong>Report</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Export File --}}
    <div class="modal fade" id="modal-export" tabindex="-1" role="dialog" aria-labelledby="modalExportLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form action="#" method="GET">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalExportLabel">Export Filter</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="filter-by">Filter Berdasarkan</label>
                            <select class="form-control" id="filter-by" name="filter_by">
                                <option value="">-- Pilih Filter --</option>
                                <option value="user">User</option>
                                <option value="cost_center">Cost Center</option>
                                <option value="division">Divisi</option>
                                <option value="period">Periode Waktu</option>
                                <option value="project">Project</option>
                            </select>
                        </div>

                        <div id="filter-fields"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="export-submit" disabled>Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <h4 class="mb-0">Laporan Umum</h4>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-success" type="button" onclick="modalExport()">
                        Export
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card card-outline rounded-partner card-primary">
                        <div class="card-body">
                            <p><strong>Total Kas</strong></p>
                            <h3>{{ formatRupiah($total_cash_balance) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-outline rounded-partner card-primary">
                        <div class="card-body">
                            <p><strong>Total Pengeluaran</strong></p>
                            <h3>{{ formatRupiah($household_expense + $project_expense) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-outline rounded-partner card-primary">
                        <div class="card-body">
                            <p><strong>Total Saldo</strong></p>
                            <h3>{{ formatRupiah($total_saldo) }}</h3>
                        </div>
                    </div>
                </div>
                <hr>
            </div>

            <hr>
            <h4>Laporan Divisi</h4>
            <!-- Department -->
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card rounded-partner bg-primary">
                        <div class="card-body">
                            <p><strong>Procurement</strong></p>
                            <h3>{{ formatRupiah($procurement_saldo) }}</h3>
                            <small>Saldo</small>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <small><strong>Pengeluaran Rumah Tangga</strong></small><br>
                                    <small>{{ formatRupiah($procurement_household_expense) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Modal Project</strong></small><br>
                                    <small>{{ formatRupiah($procurement_project_expense) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Pendapatan Kas</strong></small><br>
                                    <small>{{ formatRupiah($procurement_income) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Penyusutan</strong></small><br>
                                    <small>{{ formatRupiah($procurement_debts->whereIn('category', ['development', 'debt'])->sum('amount') - $procurement_debts->where('category', 'payment')->sum('amount')) }}</small>
                                </div>
                            </div>
                        </div>
                        <a class="text-white" href="{{ route('procurement.report') }}">
                            <div class="card-footer rounded-partner">
                                View Recap
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card rounded-partner bg-indigo">
                        <div class="card-body">
                            <p><strong>Technology</strong></p>
                            <h3>{{ formatRupiah($technology_saldo) }}</h3>
                            <small>Saldo</small>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <small><strong>Pengeluaran Rumah Tangga</strong></small><br>
                                    <small>{{ formatRupiah($technology_household_expense) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Modal Project</strong></small><br>
                                    <small>{{ formatRupiah($technology_project_expense) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Pendapatan Kas</strong></small><br>
                                    <small>{{ formatRupiah($technology_income) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Penyusutan</strong></small><br>
                                    <small>{{ formatRupiah($technology_debts->whereIn('category', ['development', 'debt'])->sum('amount') - $technology_debts->where('category', 'payment')->sum('amount')) }}</small>
                                </div>
                            </div>
                        </div>
                        <a class="text-white" href="{{ route('technology.report') }}">
                            <div class="card-footer rounded-partner">
                                View Recap
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card rounded-partner bg-orange">
                        <div class="card-body">
                            <p><strong>Construction</strong></p>
                            <h3>{{ formatRupiah($construction_saldo) }}</h3>
                            <small>Saldo</small>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <small><strong>Pengeluaran Rumah Tangga</strong></small><br>
                                    <small>{{ formatRupiah($construction_household_expense) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Modal Project</strong></small><br>
                                    <small>{{ formatRupiah($construction_project_expense) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Pendapatan Kas</strong></small><br>
                                    <small>{{ formatRupiah($construction_income) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Penyusutan</strong></small><br>
                                    <small>{{ formatRupiah($construction_debts->whereIn('category', ['development', 'debt'])->sum('amount') - $construction_debts->where('category', 'payment')->sum('amount')) }}</small>
                                </div>
                            </div>
                        </div>
                        <a class="text-white" href="{{ route('construction.report') }}">
                            <div class="card-footer rounded-partner">
                                View Recap
                            </div>
                        </a>
                    </div>
                </div>
                <hr>
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

    <!-- ChartJS -->
    <script src="{{ asset('assets/adminLTE/plugins/chart.js/Chart.min.js') }}"></script>

    <script>
        $(function() {
            //- PIE CHART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
            var pieData = {
                labels: [
                    'Technology',
                    'Procurement',
                    'Construction',
                    'Office',
                    'etc',
                ],
                datasets: [{
                    data: [700, 500, 400, 600, 300],
                    backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc'],
                }]
            }
            var pieOptions = {
                maintainAspectRatio: false,
                responsive: true,
            }
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            new Chart(pieChartCanvas, {
                type: 'pie',
                data: pieData,
                options: pieOptions
            })
            //-------------
            //- BAR CHART -
            //-------------
            var areaChartData = {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [{
                        label: 'Digital Goods',
                        backgroundColor: 'rgba(60,141,188,0.9)',
                        borderColor: 'rgba(60,141,188,0.8)',
                        pointRadius: false,
                        pointColor: '#3b8bba',
                        pointStrokeColor: 'rgba(60,141,188,1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data: [28, 48, 40, 19, 86, 27, 90]
                    },
                    {
                        label: 'Electronics',
                        backgroundColor: 'rgba(210, 214, 222, 1)',
                        borderColor: 'rgba(210, 214, 222, 1)',
                        pointRadius: false,
                        pointColor: 'rgba(210, 214, 222, 1)',
                        pointStrokeColor: '#c1c7d1',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data: [65, 59, 80, 81, 56, 55, 40]
                    },
                ]
            }
            var barChartCanvas = $('#barChart').get(0).getContext('2d')
            var areaChartData = {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [{
                        label: 'Digital Goods',
                        backgroundColor: 'rgba(60,141,188,0.9)',
                        borderColor: 'rgba(60,141,188,0.8)',
                        pointRadius: false,
                        pointColor: '#3b8bba',
                        pointStrokeColor: 'rgba(60,141,188,1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data: [28, 48, 40, 19, 86, 27, 90]
                    },
                    {
                        label: 'Electronics',
                        backgroundColor: 'rgba(210, 214, 222, 1)',
                        borderColor: 'rgba(210, 214, 222, 1)',
                        pointRadius: false,
                        pointColor: 'rgba(210, 214, 222, 1)',
                        pointStrokeColor: '#c1c7d1',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data: [65, 59, 80, 81, 56, 55, 40]
                    },
                ]
            }
            var barChartData = $.extend(true, {}, areaChartData)
            var temp0 = areaChartData.datasets[0]
            var temp1 = areaChartData.datasets[1]
            barChartData.datasets[0] = temp1
            barChartData.datasets[1] = temp0

            var barChartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                datasetFill: false
            }

            new Chart(barChartCanvas, {
                type: 'bar',
                data: barChartData,
                options: barChartOptions
            })

            //---------------------
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

        $(function() {
            $('#ledgerTable').DataTable({
                "paging": true,
                'processing': true,
                "lengthChange": true,
                "searching": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
                // "scrollX": true,
                // width: "700px",
                // columnDefs: [{
                //     className: 'dtr-control',
                //     orderable: false,
                //     targets: -8
                // }]
            });
        });

        function modalExport() {
            $('#modal-export').modal('show');
            $('#filter-fields').html('');
            $('#export-submit').prop('disabled', true);
        }

        $('#filter-by').on('change', function() {
            const value = $(this).val();
            let html = '';
            $('#export-submit').prop('disabled', false); // enable tombol by default

            switch (value) {
                case 'user':
                    html = `
                    <div class="form-group">
                        <label for="user">Pilih User</label>
                        <select class="form-control" name="user_id" required>
                            @if ($users->count())
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            @else
                                <option disabled selected>User tidak tersedia</option>
                            @endif
                        </select>
                    </div>`;
                    break;

                case 'cost_center':
                    html = `
                    <div class="form-group">
                        <label for="cost_center">Pilih Cost Center</label>
                        <select class="form-control" name="cost_center_id" required>
                            @if ($costCenters->count())
                                @foreach ($costCenters as $cc)
                                    <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                                @endforeach
                            @else
                                <option disabled selected>Cost center tidak tersedia</option>
                            @endif
                        </select>
                    </div>`;
                    break;

                case 'division':
                    html = `
                    <div class="form-group">
                        <label for="division">Pilih Divisi</label>
                        <select class="form-control" name="division" required>
                            <option value="Procurement">Procurement</option>
                            <option value="Technology">Technology</option>
                            <option value="Construction">Construction</option>
                        </select>
                    </div>`;
                    break;

                case 'period':
                    html = `
                    <div class="form-group">
                        <label>Periode Waktu</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="start_date" id="start_date" required>
                            <input type="date" class="form-control" name="end_date" id="end_date" required>
                        </div>
                        <small class="text-danger d-none" id="date-error">Tanggal akhir tidak boleh kurang dari tanggal awal.</small>
                    </div>`;
                    break;

                case 'project':
                    html = `
                    <div class="form-group">
                        <label for="project">Pilih Project</label>
                        <select class="form-control" name="project_id" required>
                            @if ($projects->count())
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            @else
                                <option disabled selected>Project tidak tersedia</option>
                            @endif
                        </select>
                    </div>`;
                    break;

                default:
                    $('#export-submit').prop('disabled', true);
            }


            $('#filter-fields').html(html);
        });

        // Validasi periode waktu
        $(document).on('change', '#start_date, #end_date', function() {
            const start = new Date($('#start_date').val());
            const end = new Date($('#end_date').val());

            if (end < start) {
                $('#export-submit').prop('disabled', true);
                $('#date-error').removeClass('d-none');
            } else {
                $('#export-submit').prop('disabled', false);
                $('#date-error').addClass('d-none');
            }
        });
    </script>
@endpush
