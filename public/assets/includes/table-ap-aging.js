$(function () {
    var dt_table_sales_aging_ap = $(".datatable-sales-aging-ap");
    var Url = "/db/payable/aging";

    if (dt_table_sales_aging_ap.length) {
        $('[data-toggle="tooltip"]').tooltip();
        // Setup - add a text input to each footer cell
        $(".datatable-sales-aging-ap thead tr")
            .clone(true)
            .appendTo(".datatable-sales-aging-ap thead");
        $(".datatable-sales-aging-ap thead tr:eq(1) th").each(function (i) {
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

        var dt_filter = dt_table_sales_aging_ap.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "invoice" },
                { data: "tanggal" },
                { data: "overdue" },
                {
                    data: "bucket",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var badgeClass = "bg-label-" + (full.bucket_class || "secondary");
                        return '<span class="badge ' + badgeClass + ' rounded-pill px-2 py-1 fw-semibold">' + (data || "-") + '</span>';
                    },
                },
                {
                    data: "supplier",
                    render: (data, type, full) =>
                        data ? data : (full.d_supplier || "-"),
                },
                { data: "total" },
                { data: "total_qty" },
                {
                    data: "accept",
                    render: function (data, type, full) {
                        if (type !== "display") return data;

                        return data == 1
                            ? '<span class="badge bg-label-success rounded-pill px-3 py-1 fw-semibold"><i class="mdi mdi-check-circle-outline me-1"></i>Paid</span>'
                            : '<span class="badge bg-label-danger rounded-pill px-3 py-1 fw-semibold"><i class="mdi mdi-clock-outline me-1"></i>Unpaid</span>';
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
                                ? route("payable.show_aging", id)
                                : "/payable/aging/" + id;
                            return (
                                '<a class="fw-bold text-primary text-decoration-none" href="' +
                                detailRoute +
                                '"><i class="mdi mdi-file-document-outline me-1"></i>' +
                                (data || "-") +
                                "</a>"
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        if (type !== "display") return data;

                        return row.accept == 1
                            ? '<span class="badge bg-label-success rounded-pill px-2 py-1">PAID</span>'
                            : '<span class="fw-semibold text-danger">' + (data || 0) + ' Days</span>';
                    },
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        if (type === "display" || type === "filter") {
                            return (
                                '<div class="text-end fw-bold text-dark">Rp ' +
                                new Intl.NumberFormat("id-ID").format(data || 0) +
                                "</div>"
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 6,
                    className: "text-center",
                    render: function (data, type, row) {
                        if (type === "display") {
                            return '<span class="badge bg-label-secondary px-2 py-1">' + (data || 0) + ' items</span>';
                        }
                        return data;
                    },
                },
                {
                    targets: 7,
                    className: "text-center",
                },
            ],
            order: [],
            dom: '<"card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 bg-transparent border-bottom"<"d-flex align-items-center gap-2"l><"d-flex align-items-center gap-2"f>><"table-responsive"t><"card-footer d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 bg-transparent border-top"<"small text-muted"i><"pagination-wrapper"p>>',
        });
    }
    dt_table_sales_aging_ap.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
