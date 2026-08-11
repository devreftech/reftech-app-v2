$(function () {
    var dt_table_expense_data = $(".datatable-expense-data");
    var Url = "db/expense/data";

    if (dt_table_expense_data.length) {
        $('[data-toggle="tooltip"]').tooltip();
        // Setup - add a text input to each footer cell
        $(".datatable-expense-data thead tr")
            .clone(true)
            .appendTo(".datatable-expense-data thead");
        $(".datatable-expense-data thead tr:eq(1) th").each(function (i) {
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

        var dt_filter = dt_table_expense_data.DataTable({
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
                { data: "no_expense" },
                { data: "no_invoice" },
                { data: "date" },
                { data: "memo" },
                { data: "no_cheque" },
                { data: "amount" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full, meta) {
                        var detailUrl = route("expense.show", full["id"]);
                        return '<a href="' + detailUrl + '" class="fw-semibold text-primary">' + (data ?? '-') + '</a>';
                    },
                },
                {
                    targets: 2,
                    className: "text-center",
                    render: function (data, type, full, meta) {
                        if (!data) return '-';
                        var d = new Date(data);
                        var day   = String(d.getDate()).padStart(2, '0');
                        var month = String(d.getMonth() + 1).padStart(2, '0');
                        var year  = d.getFullYear();
                        return day + '-' + month + '-' + year;
                    },
                },
                {
                    targets: 5,
                    className: "text-end",
                    render: $.fn.dataTable.render.number(".", "", 0, "Rp "),
                },
            ],
            order: [[2, "desc"]],
            // orderCellsTop: true,
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"B>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [
                {
                    text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Expense</span>',
                    className: "btn btn-primary btn-new",
                    action: function (e, dt, node, config) {
                        window.location = route("expense.create");
                    },
                },
                // {
                //     text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New expense</span>',
                //     className: "btn btn-primary",
                //     attr: {
                //         href: "{{ route('expense.create') }}",
                //     },
                // },
            ],
        });
    }
    dt_table_expense_data.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
