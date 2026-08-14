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
                        if (type === "display") {
                            var id = full["id"];
                            detailRoute = route("payment_detail.invoice", id);
                            return (
                                '<a class="text-black" href="' +
                                detailRoute +
                                '">' +
                                data +
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
                                '<div class="text-end">Rp ' +
                                new Intl.NumberFormat("id-ID").format(data) +
                                "</div>"
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
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });

        window.invoiceDataTables = window.invoiceDataTables || {};
        window.invoiceDataTables.kojisha = dt_filter;

        dt_table_sales_invoice_kojisha.on("draw", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }

    $("#nav-inv-kojisha").on("shown.bs.tab", initTable);
});
