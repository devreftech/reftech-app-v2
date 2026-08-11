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

        var dt_filter = dt_table_sales_aging_ap.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },

                // success: function (hasil, Url) {
                //     console.log("Url:", Url);
                //     console.log(hasil);
                // },
                // error: function (error) {
                //     console.log("Url:", Url);
                //     console.error("Error:", error);
                //     console.log("error disini");
                // },
            },
            columns: [
                // { data: "" },
                // { data: "id" },
                // { data: "id" },
                { data: "invoice" },
                { data: "tanggal" },
                { data: "overdue" },
                {
                    data: "supplier",
                    render: (data, type, full) =>
                        data ? data : full.d_supplier,
                },
                { data: "total" },
                { data: "total_qty" },
                // { data: "total_payment_level1" },
                {
                    data: "accept",
                    render: function (data, type, full) {
                        if (type !== "display") return data;

                        return data == 1
                            ? '<span class="badge bg-label-success">Paid</span>'
                            : '<span class="badge bg-label-danger">UnPaid</span>';
                    },
                },
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
                            detailRoute = route("payable.show_aging", id);
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
                    targets: 2,
                    render: function (data, type, row) {
                        if (type !== "display") return data;

                        return row.accept == 1
                            ? '<span class="badge bg-success">PAID</span>'
                            : data + ' Days';
                    },
                },
                {
                    targets: 4,
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
            ],
            order: [],
            // orderCellsTop: true,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });
    }
    dt_table_sales_aging_ap.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
