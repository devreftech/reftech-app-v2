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

        var bucketColors = {
            success: "#28a745",
            warning: "#c79100",
            orange: "#fd7e14",
            danger: "#dc3545",
        };

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
                { data: "bucket" },
                {
                    data: "supplier",
                    render: (data, type, full) =>
                        data ? data : full.d_supplier,
                },
                { data: "total" },
                { data: "total_qty" },
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
                    className: "text-center",
                    render: function (data, type, row) {
                        if (type !== "display") return data;

                        return row.accept == 1 ? "-" : data + " hari";
                    },
                },
                {
                    targets: 3,
                    className: "text-center",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        if (full.accept == 1) return "-";

                        var color = bucketColors[full.bucket_class] || "#6c757d";
                        return (
                            '<span class="badge" style="background-color:' +
                            color +
                            '20; color:' +
                            color +
                            ';">' +
                            data +
                            "</span>"
                        );
                    },
                },
                {
                    targets: 5,
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
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });
    }
    dt_table_sales_aging_ap.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
