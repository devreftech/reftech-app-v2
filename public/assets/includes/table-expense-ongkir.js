$(function () {
    var dt_table_expense_ongkir = $(".datatable-expense-ongkir");
    var Url = "db/expense/ongkir";

    if (dt_table_expense_ongkir.length) {
        var dt_filter = dt_table_expense_ongkir.DataTable({
            processing: true,
            ajax: {
                type: "GET",
                url: Url,
                data: function(d) {
                    d.status = $("#filter-ongkir-status").val() || "all";
                    d.kurir = $("#filter-ongkir-kurir").val() || "all";
                },
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "date" },
                { data: "no_pending" },
                { data: "title" },
                { data: "kurir" },
                { data: "no_track" },
                { data: "cost" },
                { data: "status" },
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
                    render: function (data) {
                        if (!data) return '<span class="text-muted">-</span>';
                        return '<span class="badge bg-label-primary font-monospace fw-semibold"><i class="mdi mdi-pound me-1"></i>' + data + '</span>';
                    },
                },
                {
                    targets: 2,
                    render: function (data) {
                        return '<div class="text-truncate fw-medium text-dark" style="max-width: 250px;" title="' + (data || '') + '">' + (data || '-') + '</div>';
                    },
                },
                {
                    targets: 3,
                    render: function (data) {
                        if (!data) return '<span class="text-muted">-</span>';
                        return '<span class="badge bg-label-info fw-semibold"><i class="mdi mdi-truck-fast-outline me-1"></i>' + data + '</span>';
                    },
                },
                {
                    targets: 4,
                    render: function (data) {
                        if (!data) return '<span class="text-muted">-</span>';
                        return '<span class="badge bg-label-secondary font-monospace"><i class="mdi mdi-barcode-scan me-1"></i>' + data + '</span>';
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
                    targets: 6,
                    className: "text-center",
                    render: function (data, type, full, row) {
                        if (full["status"] === "posted") {
                            return '<span class="badge bg-label-success fw-semibold"><i class="mdi mdi-check-circle me-1"></i>Posted ke Finance</span>';
                        }
                        return '<span class="badge bg-label-warning fw-semibold"><i class="mdi mdi-clock-outline me-1"></i>Menunggu Posting</span>';
                    },
                },
                {
                    targets: -1,
                    className: "text-center",
                    orderable: false,
                    render: function (data, type, full, row) {
                        if (full["status"] === "posted") {
                            return '<span class="badge bg-label-secondary"><i class="mdi mdi-check-all me-1"></i>Selesai</span>';
                        }
                        return (
                            '<button type="button" class="btn btn-sm btn-primary btn-post-ongkir shadow-sm" ' +
                            'data-id="' + full["id"] + '" ' +
                            'data-kurir="' + (full["kurir"] || '') + '" ' +
                            'data-cost="' + (full["cost"] || 0) + '" ' +
                            'data-track="' + (full["no_track"] || '') + '" ' +
                            'data-pending="' + (full["no_pending"] || '') + '">' +
                            '<i class="mdi mdi-send me-1"></i> Posting</button>'
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
                searchPlaceholder: "Cari pending PO, resi, kurir...",
                lengthMenu: "_MENU_",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ resi",
                paginate: {
                    first: '<i class="mdi mdi-chevron-double-left"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    last: '<i class="mdi mdi-chevron-double-right"></i>'
                }
            }
        });

        // Filter toolbar handlers
        $("#filter-ongkir-status, #filter-ongkir-kurir").on("change", function () {
            dt_filter.ajax.reload();
        });

        $("#btn-reset-ongkir-filter").on("click", function () {
            $("#filter-ongkir-status").val("");
            $("#filter-ongkir-kurir").val("");
            dt_filter.search("").draw();
            dt_filter.ajax.reload();
        });
    }

    $(document).on("click", ".btn-post-ongkir", function () {
        var id = $(this).data("id");
        var kurir = $(this).data("kurir");
        var cost = $(this).data("cost");
        var pending = $(this).data("pending");
        var track = $(this).data("track");

        var formatted = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0
        }).format(cost);

        $("#ongkir-info-text").html(
            "<strong>PO #" + pending + "</strong> &bull; Resi: <code>" + track + "</code> &bull; Ekspedisi: <strong>" + kurir + "</strong> &bull; Total Biaya: <strong class='text-danger'>" + formatted + "</strong>"
        );
        $("#memo").val("Biaya Ongkir PO #" + pending + " (" + kurir + " - " + track + ")");
        $("#formPostOngkir").attr("action", "/expense-ongkir/" + id);
        var modal = new bootstrap.Modal(document.getElementById("postOngkirModal"));
        modal.show();
    });
});
