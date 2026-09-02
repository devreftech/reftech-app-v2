$(function () {
    var dt_table_sales_invoice_ar = $(".datatable-sales-invoice-ar");
    var Url = "/db/sales/invoice/ar";

    if (dt_table_sales_invoice_ar.length) {
        $('[data-toggle="tooltip"]').tooltip();
        // Setup - add a text input to each footer cell
        $(".datatable-sales-invoice-ar thead tr")
            .clone(true)
            .appendTo(".datatable-sales-invoice-ar thead");
        $(".datatable-sales-invoice-ar thead tr:eq(1) th").each(function (i) {
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

        var dt_filter = dt_table_sales_invoice_ar.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
                data: function (d) {
                    d.year = window.invoiceYearFilter || "all";
                    d.sales_id = window.invoiceSalesFilter || "all";
                    return d;
                },
            },
            columns: [
                { data: "short_invoice" },
                { data: "tanggal" },
                { data: "short_po" },
                { data: "company" },
                { data: "harga_total" },
                { data: "total_payment_level1" },
                { data: "outstanding" },
                { data: "last_payment_type" },
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
                                ? route("payment_detail.invoice", id) 
                                : "/payment-detail/invoice/" + id;
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
                    targets: [4, 5, 6],
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
                    targets: 7,
                    className: "text-center",
                    render: function (data, type, full, row) {
                        if (type !== "display") return data;
                        var payType  = full["last_payment_type"];
                        var payLevel = full["last_payment_level"];
                        var payCount = full["payment_count"];
                        var overdue  = full["last_overdue"] ?? "No";
                        var title, label;
                        if (!payType || payCount == 0) {
                            title = "Unpaid";
                            label = "bg-label-danger";
                        } else if (payLevel == 0) {
                            title = "Belum Dikonfirmasi";
                            label = "bg-label-secondary";
                        } else if (payType == "CBD" || payType == "COD" || payType == "BP") {
                            title = "Full Paid";
                            label = "bg-label-success";
                        } else if (payType == "DP") {
                            title = "Partial";
                            label = "bg-label-warning";
                        } else if (payType == "Tempo") {
                            title = "Credit " + overdue + " Days";
                            label = "bg-label-primary";
                        } else {
                            title = "Unpaid";
                            label = "bg-label-danger";
                        }
                        return (
                            '<span class="badge rounded-pill px-3 py-1 fw-semibold ' +
                            label +
                            '">' +
                            title +
                            "</span>"
                        );
                    },
                },
            ],
            order: [],
            dom: '<"card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 bg-transparent border-bottom"<"d-flex align-items-center gap-2"l><"d-flex align-items-center gap-2"f>><"table-responsive"t><"card-footer d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 bg-transparent border-top"<"small text-muted"i><"pagination-wrapper"p>>',
        });

        window.invoiceDataTables = window.invoiceDataTables || {};
        window.invoiceDataTables.general = dt_filter;
    }
    dt_table_sales_invoice_ar.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
