/**
 * Reftech ERP - Finance Expense Vouchers Datatable
 */
$(function () {
    'use strict';

    var dt_table_expense_data = $(".datatable-expense-data");
    var baseApiUrl = "/db/expense/data";

    if (dt_table_expense_data.length) {

        function getFilterParams() {
            var y = $('#filter-expense-year').val() || '';
            var m = $('#filter-expense-month').val() || '';
            var b = $('#filter-expense-bank').val() || '';
            return { year: y, month: m, bank_id: b };
        }

        var dt_filter = dt_table_expense_data.DataTable({
            processing: true,
            ajax: {
                type: "GET",
                url: baseApiUrl,
                data: function (d) {
                    var p = getFilterParams();
                    d.year = p.year;
                    d.month = p.month;
                    d.bank_id = p.bank_id;
                },
                dataSrc: "data"
            },
            columns: [
                { data: "no_expense" },
                { data: "date" },
                { data: "id_bank" },
                { data: "memo" },
                { data: "no_cheque" },
                { data: "amount" },
                { data: "id" }
            ],
            columnDefs: [
                // 0. No Voucher
                {
                    targets: 0,
                    render: function (data, type, full, meta) {
                        var id = full.id;
                        var detailUrl = (typeof route === 'function') ? route("expense.show", id) : ("/expense/" + id);
                        var noExp = data || ('EXP-' + id);
                        return '<div class="d-flex align-items-center">' +
                            '<div class="avatar avatar-xs bg-label-primary rounded p-1 me-2 d-flex align-items-center justify-content-center">' +
                                '<i class="mdi mdi-receipt-text-outline fs-6"></i>' +
                            '</div>' +
                            '<div>' +
                                '<a href="' + detailUrl + '" class="fw-bold text-primary text-decoration-none" title="Buka Detail Jurnal">' + noExp + '</a>' +
                                (full.no_voucher ? '<div class="text-muted small" style="font-size: 11px;">Voucher: ' + full.no_voucher + '</div>' : '') +
                            '</div>' +
                        '</div>';
                    },
                },
                // 1. Tanggal
                {
                    targets: 1,
                    className: "text-nowrap",
                    render: function (data, type, full, meta) {
                        if (!data) return '<span class="text-muted">-</span>';
                        var d = new Date(data);
                        if (isNaN(d.getTime())) return data;
                        var months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
                        var day = String(d.getDate()).padStart(2, '0');
                        var month = months[d.getMonth()];
                        var year = d.getFullYear();
                        return '<span class="d-inline-flex align-items-center text-dark fw-medium small">' +
                            '<i class="mdi mdi-calendar-blank-outline text-muted me-1"></i>' +
                            day + ' ' + month + ' ' + year +
                        '</span>';
                    },
                },
                // 2. Sumber Rekening Bank
                {
                    targets: 2,
                    render: function (data, type, full, meta) {
                        var bank = full.bank;
                        if (!bank) {
                            return '<span class="badge bg-label-secondary">Kas / Umum</span>';
                        }
                        var bankName = bank.bank || 'Bank';
                        var noRek = bank.no_rek || '-';
                        var atasNama = bank.nama_rek || '';
                        return '<div>' +
                            '<span class="badge bg-label-info fw-bold me-1">' +
                                '<i class="mdi mdi-bank-outline me-1"></i>' + bankName +
                            '</span>' +
                            '<span class="small fw-semibold text-dark">' + noRek + '</span>' +
                            (atasNama ? '<div class="text-muted small" style="font-size: 10.5px;">a.n ' + atasNama + '</div>' : '') +
                        '</div>';
                    },
                },
                // 3. Keperluan / Memo
                {
                    targets: 3,
                    render: function (data, type, full, meta) {
                        var memo = data || '-';
                        var payee = full.payee;
                        return '<div style="max-width: 280px;">' +
                            '<div class="fw-semibold text-dark text-truncate" title="' + memo + '">' + memo + '</div>' +
                            (payee ? '<small class="text-muted d-block"><i class="mdi mdi-account-arrow-right-outline me-1"></i>Kepada: <strong class="text-secondary">' + payee + '</strong></small>' : '') +
                        '</div>';
                    },
                },
                // 4. Ref Cheque / No Invoice
                {
                    targets: 4,
                    render: function (data, type, full, meta) {
                        var parts = [];
                        if (full.no_cheque) {
                            parts.push('<span class="badge bg-label-warning text-dark font-monospace" style="font-size: 11px;"><i class="mdi mdi-checkbook me-0.5"></i>' + full.no_cheque + '</span>');
                        }
                        if (full.no_invoice) {
                            parts.push('<span class="badge bg-label-secondary font-monospace" style="font-size: 11px;"><i class="mdi mdi-file-document-outline me-0.5"></i>' + full.no_invoice + '</span>');
                        }
                        return parts.length ? parts.join('<div class="mt-1"></div>') : '<span class="text-muted small">-</span>';
                    },
                },
                // 5. Amount (Nominal IDR)
                {
                    targets: 5,
                    className: "text-end text-nowrap",
                    render: function (data, type, full, meta) {
                        var num = Number(data) || 0;
                        var formatted = num.toLocaleString('id-ID');
                        return '<div class="fw-bold text-danger fs-6">' +
                            'Rp ' + formatted +
                        '</div>';
                    },
                },
                // 6. Action Buttons
                {
                    targets: 6,
                    orderable: false,
                    searchable: false,
                    className: "text-center text-nowrap",
                    render: function (data, type, full, meta) {
                        var id = full.id;
                        var detailUrl = (typeof route === 'function') ? route("expense.show", id) : ("/expense/" + id);
                        var printUrl = (typeof route === 'function') ? route("expense.print", id) : ("/expense-print/" + id);

                        return '<div class="d-inline-flex align-items-center gap-1">' +
                            '<a href="' + detailUrl + '" class="btn btn-sm btn-icon btn-label-primary rounded" title="Lihat Jurnal & Rincian">' +
                                '<i class="mdi mdi-eye-outline"></i>' +
                            '</a>' +
                            '<a href="' + printUrl + '" target="_blank" class="btn btn-sm btn-icon btn-label-info rounded" title="Cetak Voucher">' +
                                '<i class="mdi mdi-printer-outline"></i>' +
                            '</a>' +
                            '<button type="button" class="btn btn-sm btn-icon btn-label-danger rounded delete-expense" data-id="' + id + '" title="Hapus Pengeluaran">' +
                                '<i class="mdi mdi-trash-can-outline"></i>' +
                            '</button>' +
                        '</div>';
                    },
                }
            ],
            order: [[1, "desc"]],
            dom: '<"card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-2.5 px-3 gap-2"<"d-flex align-items-center gap-2"l><"d-flex align-items-center gap-2"f>>t<"card-footer d-flex flex-column flex-md-row justify-content-between align-items-center py-2.5 px-3 gap-2"ip>',
            language: {
                search: "",
                searchPlaceholder: "Cari No. Voucher / Memo / Cheque...",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ s/d _END_ dari total _TOTAL_ voucher",
                infoEmpty: "Tidak ada data voucher pengeluaran",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Tidak ditemukan data voucher yang cocok",
                paginate: {
                    first: '<i class="mdi mdi-chevron-double-left"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    last: '<i class="mdi mdi-chevron-double-right"></i>'
                }
            }
        });

        // Filter event listeners
        $('#filter-expense-year, #filter-expense-month, #filter-expense-bank').on('change', function () {
            dt_filter.ajax.reload();
        });

        $('#btn-reset-filter').on('click', function () {
            $('#filter-expense-year').val('');
            $('#filter-expense-month').val('');
            $('#filter-expense-bank').val('');
            dt_filter.ajax.reload();
        });
    }

    // Delete Expense handler
    $(document).on('click', '.delete-expense', function () {
        var id = $(this).data('id');
        if (!id) return;

        Swal.fire({
            title: "Hapus Voucher Pengeluaran?",
            text: "Data jurnal transaksi dan saldo rekening bank asal akan dikembalikan secara otomatis!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "<i class='mdi mdi-trash-can-outline me-1'></i> Ya, Hapus!",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: "btn btn-danger me-2 waves-effect",
                cancelButton: "btn btn-label-secondary waves-effect",
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: '/expense/' + id,
                    type: 'POST',
                    data: {
                        '_method': 'DELETE',
                        '_token': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                    },
                    success: function (response) {
                        if (response == 1) {
                            Swal.fire({
                                icon: "success",
                                title: "Voucher Berhasil Dihapus!",
                                text: "Saldo rekening bank telah disesuaikan kembali.",
                                timer: 1500,
                                showConfirmButton: false
                            });
                            if (dt_table_expense_data.length) {
                                dt_table_expense_data.DataTable().ajax.reload(null, false);
                            } else {
                                window.location.reload();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menghapus',
                                text: 'Terjadi kendala saat menghapus data expense.'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal menghubungi server.'
                        });
                    }
                });
            }
        });
    });
});
