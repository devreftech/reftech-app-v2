$(function () {
    var dt_table_sales_invoice_escrow = $(".datatable-sales-invoice-escrow");
    var Url = "/db/sales/invoice/escrow";
    var initialized = false;

    function initTable() {
        if (initialized || !dt_table_sales_invoice_escrow.length) return;
        initialized = true;

        $('[data-toggle="tooltip"]').tooltip();
        // Setup - add a text input to each footer cell
        $(".datatable-sales-invoice-escrow thead tr")
            .clone(true)
            .appendTo(".datatable-sales-invoice-escrow thead");
        $(".datatable-sales-invoice-escrow thead tr:eq(1) th").each(function (i) {
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

        var dt_filter = dt_table_sales_invoice_escrow.DataTable({
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
                { data: "company" },
                { data: "harga_total" },
                { data: "fee" },
                { data: "name" },
                { data: "bendera" },
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
                    targets: 3,
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
                    targets: 4,
                    render: function (data, type, row) {
                        if (type === "display" || type === "filter") {
                            if (data && data > 0) {
                                return (
                                    '<div class="d-flex justify-content-between"><span class="me-1">Rp</span><span>' +
                                    new Intl.NumberFormat("id-ID").format(data) +
                                    "</span></div>"
                                );
                            }
                            return '<div class="text-center">-</div>';
                        }
                        return data;
                    },
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        return '<div class="text-center">' + (data || "-") + '</div>';
                    },
                },
                {
                    targets: 6,
                    render: function (data, type, full, row) {
                        var title, label;
                        if (data == "Reftech") {
                            title = "RJO";
                            label = "bg-label-primary";
                        } else {
                            title = "KII";
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
        window.invoiceDataTables.escrow = dt_filter;

        dt_table_sales_invoice_escrow.on("draw", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }

    $("#nav-inv-escrow").on("shown.bs.tab", initTable);
});
