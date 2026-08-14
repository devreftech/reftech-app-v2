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
                    return d;
                },
            },
            columns: [
                { data: "no_invoice" },
                { data: "tanggal" },
                { data: "company" },
                { data: "harga_total" },
                { data: "company" },
                { data: "name" },
                {
                    data: "bendera"
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
                    targets: 3,
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
                    targets: 4,
                    render: function (data, type, row) {
                        if (type === "display" || type === "filter") {
                            return (
                                '<div class="text-center">'+ '-' +'</div>'
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: -1,
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
                            '<span class="badge rounded-pill ' +
                            label +
                            '">' +
                            title +
                            "</span>"
                        );
                    },
                },
            ],
            order: [],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });

        window.invoiceDataTables = window.invoiceDataTables || {};
        window.invoiceDataTables.escrow = dt_filter;

        dt_table_sales_invoice_escrow.on("draw", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }

    $("#nav-inv-escrow").on("shown.bs.tab", initTable);
});
