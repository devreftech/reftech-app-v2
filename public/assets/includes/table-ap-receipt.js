$(function () {
    var dt_table_sales_receipt_ap = $(".datatable-sales-receipt-ap");
    var Url = "/db/payable/receipt";

    if (dt_table_sales_receipt_ap.length) {
        $('[data-toggle="tooltip"]').tooltip();
        // Setup - add a text input to each footer cell
        $(".datatable-sales-receipt-ap thead tr")
            .clone(true)
            .appendTo(".datatable-sales-receipt-ap thead");
        $(".datatable-sales-receipt-ap thead tr:eq(1) th").each(function (i) {
            var title = $(this).text().trim();
            $(this).html(
                '<input type="text" class="form-control form-control-sm bg-white" placeholder="Filter ' +
                    title +
                    '" />'
            );

            $("input", this).on("keyup change", function () {
                if (dt_filter.column(i).search() !== this.value) {
                    dt_filter.column(i).search(this.value).draw();
                }
            });
        });

        var dt_filter = dt_table_sales_receipt_ap.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "no_receipt" },
                { data: "tanggal" },
                { data: "invoice" },
                {
                    data: "supplier",
                    render: (data, type, full) =>
                        data ? data : (full.d_supplier || "-"),
                },
                { data: "total" },
                {
                    data: "accept",
                    render: function (data, type, full) {
                        if (type !== "display") return data;

                        if (data == 1) {
                            return '<span class="badge bg-label-success rounded-pill px-3 py-1 fw-semibold"><i class="mdi mdi-check-circle-outline me-1"></i>Paid</span>';
                        } else if (data == 2) {
                            return '<span class="badge bg-label-warning rounded-pill px-3 py-1 fw-semibold"><i class="mdi mdi-progress-clock me-1"></i>Partial</span>';
                        } else {
                            return '<span class="badge bg-label-danger rounded-pill px-3 py-1 fw-semibold"><i class="mdi mdi-clock-outline me-1"></i>Unpaid</span>';
                        }
                    },
                },
                {
                    data: "info",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        if (!data) return '<span class="badge bg-label-secondary">-</span>';

                        var isImport = String(data).toLowerCase() === "import";
                        var badgeClass = isImport ? "bg-label-info" : "bg-label-primary";
                        var iconClass = isImport ? "mdi-airplane me-1" : "mdi-map-marker-radius-outline me-1";
                        return '<span class="badge ' + badgeClass + ' rounded-pill px-2 py-1"><i class="mdi ' + iconClass + '"></i>' + data + '</span>';
                    },
                },
            ],
            columnDefs: [
                {
                    responsivePriority: 1,
                    targets: 0,
                },
                {
                    targets: 0,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var id = full["id"];
                            var detailRoute = typeof route === "function" 
                                ? route("payable.show_receipt", id) 
                                : "/payable/receipt/" + id;
                            return (
                                '<a class="fw-bold text-primary text-decoration-none" href="' +
                                detailRoute +
                                '"><i class="mdi mdi-receipt-text-outline me-1"></i>' +
                                (data || "-") +
                                "</a>"
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            return '<span class="text-dark fw-medium">' + (data || "-") + '</span>';
                        }
                        return data;
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        if (type === "display" || type === "filter") {
                            var formatted = '<div class="text-end fw-bold text-dark">Rp ' +
                                new Intl.NumberFormat("id-ID").format(data || 0) +
                                '</div>';
                            if (row.accept == 2 && row.remaining > 0) {
                                formatted += '<div class="text-end text-danger fw-semibold" style="font-size: 11px;">Sisa: Rp ' +
                                    new Intl.NumberFormat("id-ID").format(row.remaining || 0) + '</div>';
                            }
                            return formatted;
                        }
                        return data;
                    },
                },
                {
                    targets: [5, 6],
                    className: "text-center",
                },
            ],
            order: [],
            dom: '<"card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 bg-transparent border-bottom"<"d-flex align-items-center gap-2"l><"d-flex align-items-center gap-2"f>><"table-responsive"t><"card-footer d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 bg-transparent border-top"<"small text-muted"i><"pagination-wrapper"p>>',
        });
    }
    dt_table_sales_receipt_ap.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});

