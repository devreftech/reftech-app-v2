$(function () {
    var dt_table_account_data = $(".datatable-account-data");
    var Url = "db/account/data";

    if (dt_table_account_data.length) {
        $('[data-toggle="tooltip"]').tooltip();

        var dt_filter = dt_table_account_data.DataTable({
            processing: true,
            ajax: {
                type: "GET",
                url: Url,
                data: function(d) {
                    d.category = $("#filter-account-category").val() || "";
                    d.level = $("#filter-account-level").val() || "";
                },
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "code" },
                { data: "name" },
                { data: "category" },
                { data: "currency" },
                { data: "saldo" },
                { data: "id" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, row) {
                        var isHeader = (row.level == 1);
                        var badgeClass = isHeader ? 'bg-label-primary fw-bold' : 'bg-label-secondary';
                        return '<span class="badge ' + badgeClass + ' code-pill"><i class="mdi ' + (isHeader ? 'mdi-folder-outline' : 'mdi-file-document-outline') + ' me-1"></i>' + data + '</span>';
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, row) {
                        var isHeader = (row.level == 1);
                        if (isHeader) {
                            return '<div class="fw-bold text-dark fs-6">' + data + ' <span class="badge bg-label-info ms-1" style="font-size:10px;">HEADER</span></div>';
                        }
                        return '<div class="ms-3 text-secondary"><i class="mdi mdi-subdirectory-arrow-right text-muted me-1"></i>' + data + '</div>';
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        if (!data) return '<span class="text-muted">-</span>';
                        var badgeColor = 'bg-label-info';
                        var cat = data.toLowerCase();
                        if (cat.indexOf('expense') !== -1 || cat.indexOf('cost') !== -1) {
                            badgeColor = 'bg-label-danger';
                        } else if (cat.indexOf('revenue') !== -1 || cat.indexOf('income') !== -1) {
                            badgeColor = 'bg-label-success';
                        } else if (cat.indexOf('asset') !== -1 || cat.indexOf('cash') !== -1) {
                            badgeColor = 'bg-label-primary';
                        } else if (cat.indexOf('liabilit') !== -1 || cat.indexOf('payable') !== -1) {
                            badgeColor = 'bg-label-warning';
                        } else if (cat.indexOf('equity') !== -1) {
                            badgeColor = 'bg-label-secondary';
                        }
                        return '<span class="badge ' + badgeColor + ' fw-semibold">' + data + '</span>';
                    },
                },
                {
                    targets: 3,
                    render: function (data) {
                        return '<span class="badge bg-label-secondary fw-semibold">' + (data || 'IDR') + '</span>';
                    },
                },
                {
                    targets: 4,
                    render: function (data) {
                        if (data === 'Debit' || data === 'D') {
                            return '<span class="badge bg-label-primary fw-semibold">Debit (D)</span>';
                        } else if (data === 'Kredit' || data === 'K') {
                            return '<span class="badge bg-label-danger fw-semibold">Kredit (K)</span>';
                        }
                        return '<span class="text-muted">' + (data || '-') + '</span>';
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
                            '<button type="button" class="btn btn-icon btn-sm btn-label-warning editAccount" data-id="' + id + '" data-bs-toggle="modal" data-bs-target="#editAccount" title="Edit Akun"><i class="mdi mdi-pencil-outline"></i></button>' +
                            '<button type="button" class="btn btn-icon btn-sm btn-label-danger delete-account" data-id="' + id + '" title="Hapus Akun"><i class="mdi mdi-delete-outline"></i></button>' +
                            '</div>'
                        );
                    },
                },
            ],
            order: [[0, "asc"]],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                search: "",
                searchPlaceholder: "Cari kode atau nama akun COA...",
                lengthMenu: "_MENU_",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ akun",
                paginate: {
                    first: '<i class="mdi mdi-chevron-double-left"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    last: '<i class="mdi mdi-chevron-double-right"></i>'
                }
            }
        });

        // Filter toolbar handlers
        $("#filter-account-category, #filter-account-level").on("change", function () {
            dt_filter.ajax.reload();
        });

        $("#btn-reset-account-filter").on("click", function () {
            $("#filter-account-category").val("");
            $("#filter-account-level").val("");
            dt_filter.search("").draw();
            dt_filter.ajax.reload();
        });

        dt_table_account_data.on("draw", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    }
});
