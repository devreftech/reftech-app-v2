$(function () {
    var dt_table_expense_umum_data = $(".datatable-expense-umum-data");
    var Url = "db/expense/umum/data";

    if (dt_table_expense_umum_data.length) {
        $('[data-toggle="tooltip"]').tooltip();

        var dt_filter = dt_table_expense_umum_data.DataTable({
            processing: true,
            ajax: {
                type: "GET",
                url: Url,
                data: function(d) {
                    d.year = $("#filter-umum-year").val() || "all";
                    d.month = $("#filter-umum-month").val() || "all";
                },
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "date" },
                { data: "memo" },
                { data: "no_invoice" },
                { data: "no_cheque" },
                { data: "amount" },
                { data: "id" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data) {
                        if (!data) return '<span class="text-muted">-</span>';
                        var d = new Date(data);
                        if (isNaN(d.getTime())) return data;
                        var formatted = d.toLocaleDateString("id-ID", {
                            day: "2-digit",
                            month: "short",
                            year: "numeric",
                        });
                        return '<span class="text-nowrap"><i class="mdi mdi-calendar-blank-outline me-1 text-muted"></i>' + formatted + '</span>';
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, full) {
                        var id = full["id"];
                        var detailRoute = "/expense/" + id;
                        return (
                            '<div class="d-flex flex-column">' +
                            '<a class="fw-semibold text-dark text-decoration-none hover-primary mb-1" href="' + detailRoute + '">' + (data || 'Pengeluaran Kas Umum') + '</a>' +
                            '<small class="text-muted"><i class="mdi mdi-cash me-1"></i>Kas Operasional Langsung</small>' +
                            '</div>'
                        );
                    },
                },
                {
                    targets: 2,
                    render: function (data) {
                        if (!data) return '<span class="text-muted">-</span>';
                        return (
                            '<div class="d-flex align-items-center">' +
                            '<span class="badge bg-label-secondary font-monospace"><i class="mdi mdi-receipt me-1"></i>' + data + '</span>' +
                            '</div>'
                        );
                    },
                },
                {
                    targets: 3,
                    render: function (data) {
                        if (!data) return '<span class="text-muted">-</span>';
                        return '<span class="badge bg-label-info font-monospace">' + data + '</span>';
                    },
                },
                {
                    targets: 4,
                    className: "text-end",
                    render: function (data) {
                        var val = parseFloat(data || 0);
                        return '<span class="fw-bold text-danger">Rp ' + val.toLocaleString("id-ID") + '</span>';
                    },
                },
                {
                    targets: 5,
                    className: "text-center",
                    orderable: false,
                    render: function (data, type, full, row) {
                        var id = full["id"];
                        return (
                            '<div class="d-inline-flex gap-1">' +
                            '<a href="/expense/' + id + '" class="btn btn-icon btn-sm btn-label-info" title="Lihat Detail"><i class="mdi mdi-eye-outline"></i></a>' +
                            '<a href="/expense-print/' + id + '" target="_blank" class="btn btn-icon btn-sm btn-label-secondary" title="Cetak Voucher"><i class="mdi mdi-printer-outline"></i></a>' +
                            '<button type="button" class="btn btn-icon btn-sm btn-label-danger delete-expense" data-id="' + id + '" title="Hapus Voucher"><i class="mdi mdi-delete-outline"></i></button>' +
                            '</div>'
                        );
                    },
                },
            ],
            order: [[0, "desc"]],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                search: "",
                searchPlaceholder: "Cari memo, no bukti...",
                lengthMenu: "_MENU_",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ kas umum",
                paginate: {
                    first: '<i class="mdi mdi-chevron-double-left"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    last: '<i class="mdi mdi-chevron-double-right"></i>'
                }
            }
        });

        // Filter toolbar handlers
        $("#filter-umum-year, #filter-umum-month").on("change", function () {
            dt_filter.ajax.reload();
        });

        $("#btn-reset-umum-filter").on("click", function () {
            $("#filter-umum-year").val("");
            $("#filter-umum-month").val("");
            dt_filter.search("").draw();
            dt_filter.ajax.reload();
        });

        dt_table_expense_umum_data.on("draw", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }
});
