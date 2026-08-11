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

        var dt_filter = dt_table_sales_invoice_ar.DataTable({
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
                // { data: "" },
                // { data: "id" },
                // { data: "id" },
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
                // {
                //     // For Responsive
                //     className: "control",
                //     orderable: false,
                //     searchable: false,
                //     responsivePriority: 2,
                //     targets: 0,
                //     render: function (data, type, full, meta) {
                //         return "";
                //     },
                // },
                // {
                //     targets: 0,
                //     searchable: true,
                //     visible: false,
                // },
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
                    targets: 7,
                    render: function (data, type, full, row) {
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
            // orderCellsTop: true,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });

        window.invoiceDataTables = window.invoiceDataTables || {};
        window.invoiceDataTables.general = dt_filter;
    }
    dt_table_sales_invoice_ar.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
