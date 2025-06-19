@extends('layouts.admin')

@section('title')
    Cost Center
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cost Center</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><strong>Cost Center</strong></li>
                        <script src="https://cdn.tailwindcss.com"></script>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <h2 class="text-xl font-bold mb-4 text-gray-700">Laporan General</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
           @php
            $generalCards = [
            [
            'label' => 'Total Debit',
            'value' => formatRupiah($totalDebitGeneral),
            'bgFrom' => 'blue-400',
            'bgTo' => 'blue-600',
            'text' => 'white'
            ],
            [
            'label' => 'Total Kredit',
            'value' => formatRupiah($totalKreditGeneral),
            'bgFrom' => 'green-400',
            'bgTo' => 'green-600',
            'text' => 'white'
            ],
            [
            'label' => 'Total Saldo',
            'value' => formatRupiah($totalSaldoGeneral),
            'bgFrom' => 'purple-400',
            'bgTo' => 'purple-600',
            'text' => 'white'
            ],
            [
            'label' => 'Total Pendapatan Tahun Berjalan',
            'value' => formatRupiah($totalPendapatanGeneral),
            'bgFrom' => 'orange-400',
            'bgTo' => 'orange-600',
            'text' => 'white'
            ],
            ];
            @endphp

            @foreach ($generalCards as $card)
            <div
                class="bg-gradient-to-br from-{{ $card['bgFrom'] }} to-{{ $card['bgTo'] }} rounded-lg shadow p-4 text-center">
                <p class="text-sm font-semibold text-{{ $card['text'] }}">{{ $card['label'] }}</p>
                <p class="text-2xl font-extrabold text-{{ $card['text'] }}">{{ $card['value'] }}</p>
            </div>
            @endforeach
        </div>
    </section>
    <br><br>
    <section>
        <h2 class="text-xl font-bold mb-4 text-gray-700">Laporan Divisi</h2>
        <div class="flex flex-wrap -mx-2">
            @foreach ($divisions as $division)
            @php
            $color = $division['color'] ?? 'gray';
            $departmentId = $division['department_id'] ?? null;
            @endphp
            <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4">
                <div class="bg-white border-l-4 border-{{ $color }}-500 rounded-lg shadow p-4">
                    <div class="grid grid-cols-2 gap-3 text-sm text-gray-700">
                        <div class="bg-blue-100 rounded p-3 text-center">
                            <p class="font-semibold text-blue-800">Total Debit</p>
                            <p class="text-lg font-bold text-blue-900">{{ formatRupiah($division['debit']) }}</p>
                        </div>
                        <div class="bg-green-100 rounded p-3 text-center">
                            <p class="font-semibold text-green-800">Total Kredit</p>
                            <p class="text-lg font-bold text-green-900">{{ formatRupiah($division['kredit']) }}</p>
                        </div>
                        <div class="bg-yellow-100 rounded p-3 text-center">
                            <p class="font-semibold text-yellow-800">Total Saldo</p>
                            <p class="text-lg font-bold text-yellow-900">{{ formatRupiah($division['saldo']) }}</p>
                        </div>
                        <div class="bg-purple-100 rounded p-3 text-center">
                            <p class="font-semibold text-purple-800">Total Pendapatan Tahun Berjalan</p>
                            <p class="text-lg font-bold text-purple-900">{{ formatRupiah($division['pendapatan']) }}</p>
                        </div>

                        @if ($departmentId)
                        <div class="col-span-2 mt-4 text-center">
                            <a href="{{ route('cost-center.transaction', ['departmentId' => $departmentId]) }}"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-semibold inline-flex items-center justify-center">
                                <i class="fas fa-receipt mr-1"></i> Lihat Transaksi
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
@endsection

@push('scripts')
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
        let dataDetailCostCenter = null;

        $(function() {
            $('#tableCostCenterProcurement, #tableCostCenterConstruction, #tableCostCenterTechnology').DataTable({
                "lengthChange": false,
                "searching": false,
                "scrollX": true
            });

            // merapihkan pagination button datatable
            $('.dataTables_paginate.paging_simple_numbers').addClass('float-right');

            // format ke rupiah
            $('.price').inputmask({
                alias: 'numeric',
                prefix: 'Rp',
                digits: 0,
                groupSeparator: '.',
                autoGroup: true,
                removeMaskOnSubmit: true,
                rightAlign: false
            });
        });

        function modalAdd(button) {
            const id = $(button).data('id');
            $('#formAddCostCenter')[0].reset();
            $('#department_id').val(id);
            $('#modalAddCostCenter').modal('show');
        }

        function confirmDelete(button) {
            const id = $(button).data('id');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Cost center ini beserta turunannya akan terhapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('cost.center.delete', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Cost center telah dihapus.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            console.log(xhr);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat menghapus data.'
                            });
                        }
                    });
                }
            });
        }

        function modalEdit(button) {
            const id = $(button).data('id');

            $.ajax({
                url: "{{ route('cost.center.show', ':id') }}".replace(':id', id),
                type: 'GET',
                success: function(response) {
                    $('#edit-id').val(response.id);
                    $('#edit-code').val(response.code);
                    $('#edit-name').val(response.name);
                    $('#edit-amount').val(response.amount);

                    $('#modalEditCostCenter').modal('show');
                },
                error: function(xhr) {
                    if (xhr.status === 404) {
                        return Swal.fire('Error', 'Cost center tidak ditemukan', 'error');
                    }
                    return Swal.fire('Error', 'Gagal mengambil data cost center', 'error');
                }
            });
        }

        // Submit form edit
        $('#formEditCostCenter').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit-id').val();

            $.ajax({
                url: "{{ route('cost.center.update', ':id') }}".replace(':id', id),
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: $('#edit-name').val(),
                    amount: $('#edit-amount').inputmask('unmaskedvalue')
                },
                success: function(response) {
                    $('#modalEditCostCenter').modal('hide');
                    Swal.fire('Berhasil', 'Cost center berhasil diperbarui', 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 404) {
                        return Swal.fire('Gagal', 'Cost center tidak ditemukan', 'error');
                    }
                    return Swal.fire('Gagal', 'Terjadi kesalahan saat memperbarui cost center',
                        'error');
                }
            });
        });

        function modalImport(button) {
            const departmentId = $(button).data('id');
            $('#import-department-id').val(departmentId);
            $('#modalImportCostCenter').modal('show');
        }

        function modalDetail(button) {
            let id;

            // Cek apakah button adalah elemen HTML atau objek JS biasa
            if (button instanceof HTMLElement) {
                id = $(button).data('id');
            } else if (button.dataset && button.dataset.id) {
                id = button.dataset.id;
            } else if (button.id) {
                id = button.id;
            }

            if (!id) {
                console.error("ID Cost Center tidak ditemukan.");
                return;
            }

            $('#detail-cost-center-id').val(id);
            $('#sub-cost-center-table-body').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');

            $.get("{{ route('cost.center.show', ':id') }}".replace(':id', id), function(data) {
                $('#detail-cost-center-kode').text(data.code);
                $('#detail-cost-center-nama').text(data.name);
                $('#detail-cost-center-amount').text(formatRupiah(data.amount));
                $('#modal-detail-cost-center').modal('show');

                dataDetailCostCenter = data;
                let rows = '';

                if (data.subs.length > 0) {
                    data.subs.forEach(sub => {
                        rows += `<tr>
                        <td>${sub.name}</td>
                        <td>Rp${formatRupiah(sub.amount)}</td>
                            <td>
                                <button class="badge badge-primary border-0" onclick="editSubCostCenter(${sub.id})"><i class="fas fa-edit"></i></button>
                                <button class="badge badge-danger border-0" onclick="deleteSubCostCenter(${sub.id}, '${data.id}')"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="4" class="text-center">Belum ada sub cost center</td></tr>';
                }

                $('#sub-cost-center-table-body').html(rows);
            });
        }

        function addSubCostCenter() {
            const parentId = $('#detail-cost-center-id').val();
            const name = $('#sub-cost-center-name').val();
            const amount = $('#sub-cost-center-amount').inputmask('unmaskedvalue');

            if (!name || isNaN(amount)) {
                return alert('Nama dan Amount harus diisi');
            }

            // Cek total amount
            $.get('/cost-centers/show/' + parentId + '/json', function(data) {
                const totalSubAmount = data.subs.reduce((sum, item) => sum + parseFloat(item.amount), 0);
                const sisaAmount = data.amount - totalSubAmount;

                // Cegah penambahan jika sisa amount sudah habis atau amount melebihi sisa
                if (sisaAmount <= 0 || amount > sisaAmount) {
                    return Swal.fire(
                        'Error',
                        `Tidak dapat menambahkan sub cost center.
                        Sisa anggaran dari cost center induk adalah Rp${formatRupiah(sisaAmount)}.`,
                        'error'
                    );
                }

                // Kirim POST
                $.post('/cost-centers/sub-cost-center', {
                    _token: '{{ csrf_token() }}',
                    parent_id: parentId,
                    name: name,
                    amount: amount
                }, function() {
                    modalDetail({
                        dataset: {
                            id: parentId
                        }
                    }); // Reload modal
                    $('#sub-cost-center-name').val('');
                    $('#sub-cost-center-amount').val('');
                });
            });
        }

        function editSubCostCenter(id) {
            $.get('/cost-centers/sub-cost-center/' + id, function(data) {
                $('#edit-sub-id').val(data.id);
                $('#edit-sub-name').val(data.name);
                $('#edit-sub-amount').val(data.amount);
                $('#modal-edit-sub').modal('show');
            });
        }

        $('#form-edit-sub').submit(function(e) {
            e.preventDefault();

            const id = $('#edit-sub-id').val();
            const name = $('#edit-sub-name').val();
            const amount = parseFloat($('#edit-sub-amount').inputmask('unmaskedvalue'));
            const parentAmount = parseFloat(dataDetailCostCenter.amount);

            // Hitung total sub amount lain (selain yang sedang diedit)
            const totalOtherSubs = dataDetailCostCenter.subs
                .filter(sub => sub.id != id)
                .reduce((sum, sub) => sum + parseFloat(sub.amount), 0);

            const sisaAmount = parentAmount - totalOtherSubs;

            if (sisaAmount <= 0 || amount > sisaAmount) {
                return Swal.fire(
                    'Error',
                    `Sub cost center yang diedit (Rp${formatRupiah(amount)}) melebihi sisa anggaran (Rp${formatRupiah(sisaAmount)}) dari cost center induk!`,
                    'error'
                );
            }

            $.ajax({
                url: '/cost-centers/sub-cost-center/' + id,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    amount: amount
                },
                success: function() {
                    $('#modal-edit-sub').modal('hide');
                    modalDetail({
                        dataset: {
                            id: $('#detail-cost-center-id').val()
                        }
                    });
                }
            });
        });

        function deleteSubCostCenter(id, parentId) {
            Swal.fire({
                title: 'Hapus Sub Cost Center?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/cost-centers/sub-cost-center/' + id,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.fire('Terhapus!', 'Sub Cost Center berhasil dihapus.', 'success');
                            modalDetail({
                                dataset: {
                                    id: parentId
                                }
                            });
                        }
                    });
                }
            });
        }

        function formatRupiah(angka) {
            if (!angka || isNaN(angka)) {
                return '0';
            }
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function editSubCostCenter(id) {
            $.get("{{ route('cost.center.sub.show', ':id') }}".replace(':id', id), function(data) {
                $('#edit-sub-id').val(data.id);
                $('#edit-sub-name').val(data.name);
                $('#edit-sub-amount').val(data.amount);
                $('#modal-edit-sub').modal('show');
            });
        }
    </script>
@endpush
