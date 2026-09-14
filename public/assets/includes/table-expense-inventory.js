$(function () {
    var dt_table_expense_inventory = $(".datatable-expense-inventory");
    var Url = "db/expense/inventory";

    if (dt_table_expense_inventory.length) {
        $('[data-toggle="tooltip"]').tooltip();

        var dt_filter = dt_table_expense_inventory.DataTable({
            processing: true,
            ajax: {
                type: "GET",
                url: Url,
                data: function(d) {
                    d.year = $("#filter-inventory-year").val() || "all";
                    d.month = $("#filter-inventory-month").val() || "all";
                },
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "date" },
                { data: "no_invoice" },
                { data: "account" },
                { data: "memo" },
                { data: "replacement" },
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
                    render: function (data, type, row) {
                        return (
                            '<div class="d-flex align-items-center">' +
                            '<div class="avatar avatar-xs bg-label-info rounded-circle me-2 d-flex align-items-center justify-content-center">' +
                            '<i class="mdi mdi-file-document-outline" style="font-size:14px;"></i>' +
                            '</div>' +
                            '<span class="fw-semibold text-dark">' + (data || '-') + '</span>' +
                            '</div>'
                        );
                    },
                },
                {
                    targets: 2,
                    render: function (data) {
                        if (!data) return '<span class="text-muted">-</span>';
                        return '<span class="badge bg-label-primary fw-semibold"><i class="mdi mdi-format-list-bulleted me-1"></i>' + data + '</span>';
                    },
                },
                {
                    targets: 3,
                    render: function (data) {
                        return '<div class="text-truncate" style="max-width: 250px;" title="' + (data || '') + '">' + (data || '<span class="text-muted">-</span>') + '</div>';
                    },
                },
                {
                    targets: 4,
                    render: function (data) {
                        if (!data) return '<span class="text-muted">Tidak ada item</span>';
                        var items = data.split(' - ');
                        var html = '';
                        items.forEach(function(item) {
                            html += '<span class="badge bg-label-secondary me-1 mb-1">' + item + '</span>';
                        });
                        return html;
                    },
                },
                {
                    targets: 5,
                    className: "text-end",
                    render: function (data) {
                        var val = parseFloat(data || 0);
                        return '<span class="fw-bold text-danger">Rp ' + val.toLocaleString("id-ID") + '</span>';
                    },
                },
                {
                    targets: -1,
                    className: "text-center",
                    orderable: false,
                    render: function (data, type, full, row) {
                        var id = full["id"];
                        return (
                            '<div class="d-inline-flex gap-1">' +
                            '<a href="/expense/' + id + '" class="btn btn-icon btn-sm btn-label-info" title="Lihat Rincian"><i class="mdi mdi-eye-outline"></i></a>' +
                            '<button type="button" class="btn btn-icon btn-sm btn-label-danger delete-inventory" data-id="' + id + '" title="Batalkan Penyesuaian"><i class="mdi mdi-delete-outline"></i></button>' +
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
                searchPlaceholder: "Cari dokumen, akun, memo...",
                lengthMenu: "_MENU_",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ penyesuaian",
                paginate: {
                    first: '<i class="mdi mdi-chevron-double-left"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    last: '<i class="mdi mdi-chevron-double-right"></i>'
                }
            }
        });

        // Filter toolbar handlers
        $("#filter-inventory-year, #filter-inventory-month").on("change", function () {
            dt_filter.ajax.reload();
        });

        $("#btn-reset-inventory-filter").on("click", function () {
            $("#filter-inventory-year").val("");
            $("#filter-inventory-month").val("");
            dt_filter.search("").draw();
            dt_filter.ajax.reload();
        });

        dt_table_expense_inventory.on("draw", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }
});
