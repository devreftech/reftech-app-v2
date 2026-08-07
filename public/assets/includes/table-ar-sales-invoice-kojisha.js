$(function () {
    var dt_table_sales_invoice_kojisha = $(".datatable-sales-invoice-kojisha");
    var Url = "/db/sales/invoice/kojisha";
    var initialized = false;

    function initTable() {
        if (initialized || !dt_table_sales_invoice_kojisha.length) return;
        initialized = true;

        $('[data-toggle="tooltip"]').tooltip();
        // Setup - add a text input to each footer cell
        $(".datatable-sales-invoice-kojisha thead tr")
            .clone(true)
            .appendTo(".datatable-sales-invoice-kojisha thead");
        $(".datatable-sales-invoice-kojisha thead tr:eq(1) th").each(function (
            i
        ) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control" placeholder="Search ' +
                    title +
                    '" />'
            );

            $("input", this).on("keyup change", function () {
                if (dt_filter.column(i).search() !== this.value) {
                    dt_filter.column(i).search(this.value).draw();
                }
            });
        });

        var dt_filter = dt_table_sales_invoice_kojisha.DataTable({
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
                { data: "name" },
            ],
            columnDefs: [
                {
                    responsivePriority: 1,
                    targets: 0,
                },
                {
                    targets: 0,
                    render: function (data, type, full, row) {
                        var full_no = full["no_invoice"] || data || "-";
                        if (type === "display") {
                            var id = full["id"];
                            var detailRoute = route("payment_detail.invoice", id);
                            var short = full_no.length > 8 ? full_no.substring(0, 8) + "…" : full_no;
                            return (
                                '<a class="fw-bold text-primary" href="' +
                                detailRoute +
                                '" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-quote-no" title="' +
                                full_no +
                                '">' +
                                short +
                                "</a>"
                            );
                        }
                        return full_no;
                    },
                },
                {
                    targets: [4, 5, 6],
                    render: function (data, type, row) {
                        if (type === "display" || type === "filter") {
                            return (
                                '<div class="d-flex justify-content-between"><span class="me-1">Rp</span><span>' +
                                new Intl.NumberFormat("id-ID").format(data || 0) +
                                "</span></div>"
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 8,
                    render: function (data, type, row) {
                        if (type === "display" || type === "filter") {
                            return (
                                '<div class="text-center">'+ data +'</div>'
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, full, row) {
                        var type = full["last_payment_type"];
                        var overdue = full["last_overdue"] ?? "No";
                        var paytot = full["total_payment"];
                        var title, label;
                        if (type == "CBD" || type == "COD" || type == "BP") {
                            title = "Full Paid";
                            label = "bg-label-success";
                        } else if (type == "DP") {
                            title = "Partial";
                            label = "bg-label-warning";
                        } else if (type == "Tempo") {
                            title = "Credit " + overdue + " Days";
                            label = "bg-label-primary";
                        } else {
                            title = "Unpaid";
                            label = "bg-label-danger";
                        }
                        return (
                            '<div class="text-center"><span class="badge rounded-pill ' +
                            label +
                            '">' +
                            title +
                            "</span></div>"
                        );
                    },
                },
            ],
            order: [],
            dom: '<"row me-2"<"col-md-2"<"me-3"l>><"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-label-secondary dropdown-toggle mx-3',
                    text: '<i class="mdi mdi-export-variant me-1"></i>Export',
                    buttons: [
                        { extend: 'csv', className: 'dropdown-item', text: '<i class="mdi mdi-file-document-outline me-1"></i>CSV' },
                        { extend: 'excel', className: 'dropdown-item', text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel' }
                    ]
                }
            ],
        });

        window.invoiceDataTables = window.invoiceDataTables || {};
        window.invoiceDataTables.kojisha = dt_filter;

        dt_table_sales_invoice_kojisha.on("draw", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }

    $("#nav-inv-kojisha").on("shown.bs.tab", initTable);
});
