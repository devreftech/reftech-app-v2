$(function () {
    var dt_table_expense_inventory = $(".datatable-expense-inventory");
    var Url = "db/expense/inventory";

    if (dt_table_expense_inventory.length) {
        $('[data-toggle="tooltip"]').tooltip();
        // Setup - add a text input to each footer cell
        $(".datatable-expense-inventory thead tr")
            .clone(true)
            .appendTo(".datatable-expense-inventory thead");
        $(".datatable-expense-inventory thead tr:eq(1) th").each(function (i) {
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

        var dt_filter = dt_table_expense_inventory.DataTable({
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
                { data: "date" },
                { data: "no_invoice" },
                { data: "account" },
                { data: "memo" },
                { data: "replacement" },
                { data: "amount" },
                { data: "id" },
            ],
            columnDefs: [
                // {
                //     targets: 1,
                //     render: function (data, type, full, row) {
                //         if (type === "display") {
                //             var $dataId = full["id"];
                //             var detailRoute = route("expense.show", $dataId);
                //             return (
                //                 '<a class="text-dark" href="' +
                //                 detailRoute +
                //                 '">' +
                //                 data +
                //                 "</a>"
                //             );
                //         }
                //         return data;
                //     },
                // },
                {
                    targets: 5,
                    render: $.fn.dataTable.render.number(".", "", 0, "Rp."),
                },
                {
                    targets: -1,
                    render: function (data, type, full, row) {
                        var id = full["id"];
                        return (
                            '<a href="#" data-id="' +
                            id +
                            '" class="btn btn-sm btn-label-danger delete-inventory m-2"><i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i></a>'
                        );
                    },
                },
            ],
            order: [[0, "desc"]],
            // orderCellsTop: true,
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f><"dt-action-buttons text-end pt-3 pt-md-0"B>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [
                {
                    text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Adjusment</span>',
                    className: "btn btn-primary btn-new",
                    action: function (e, dt, node, config) {
                        window.location = route("expense-inventory.create");
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
    dt_table_expense_inventory.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
