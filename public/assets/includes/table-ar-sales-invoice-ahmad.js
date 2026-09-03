$(function () {
    var dt_table_sales_invoice_ahmad = $(".datatable-sales-invoice-ahmad");
    var Url = "/db/sales/invoice/ahmad";
    var initialized = false;

    function initTable() {
        if (initialized || !dt_table_sales_invoice_ahmad.length) return;
        initialized = true;

        $('[data-toggle="tooltip"]').tooltip();
        // Setup - add a text input to each footer cell
        $(".datatable-sales-invoice-ahmad thead tr")
            .clone(true)
            .appendTo(".datatable-sales-invoice-ahmad thead");
        $(".datatable-sales-invoice-ahmad thead tr:eq(1) th").each(function (i) {
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

        var dt_filter = dt_table_sales_invoice_ahmad.DataTable({
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
                { data: "tax" },
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
                        if (type === "display") {
                            var id = full["id"];
                            var detailRoute = typeof route === "function" ? route("payment_detail.invoice", id) : "/payment/invoice/" + id;
                            return (
                                '<a class="text-black fw-semibold" href="' +
                                detailRoute +
                                '">' +
                                (data || full["no_invoice"] || "-") +
                                "</a>"
                            );
                        }
                        return data || full["no_invoice"] || "";
                    },
                },
                {
                    targets: [4, 5, 6],
                    render: function (data, type, row) {
                        if (type === "display" || type === "filter") {
                            return (
                                '<div class="text-end">Rp ' +
                                new Intl.NumberFormat("id-ID").format(data || 0) +
                                '</div>'
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, full, row) {
                        var ptype = full["last_payment_type"];
                        var overdue = full["last_overdue"] ?? "No";
                        var title, label;
                        if (ptype == "CBD" || ptype == "COD" || ptype == "BP") {
                            title = "Full Paid";
                            label = "bg-label-success";
                        } else if (ptype == "DP") {
                            title = "Partial";
                            label = "bg-label-warning";
                        } else if (ptype == "Tempo") {
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
                {
                    targets: 8,
                    render: function (data, type, full, row) {
                        var tax = full["tax"];
                        if (tax == "11" || tax == "1" || tax == "PPN") {
                            return '<div class="text-center"><span class="badge rounded-pill bg-label-info">PPN</span></div>';
                        }
                        return '<div class="text-center"><span class="badge rounded-pill bg-label-secondary">Non PPN</span></div>';
                    },
                },
            ],
            order: [],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });

        window.invoiceDataTables = window.invoiceDataTables || {};
        window.invoiceDataTables.ahmad = dt_filter;

        dt_table_sales_invoice_ahmad.on("draw", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }

    $("#nav-inv-ahmad").on("shown.bs.tab", initTable);
});
