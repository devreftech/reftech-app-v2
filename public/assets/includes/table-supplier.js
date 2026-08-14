$(function () {
    var dt_table_supplier = $(".datatable-supplier");
    var Url = "db/supplier";

    if (dt_table_supplier.length) {
        $('[data-toggle="tooltip"]').tooltip();

        var dt_supplier = dt_table_supplier.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "" },
                { data: "id" },
                { data: "supplier" },
                { data: "phone" },
                { data: "email" },
                { data: "area" },
                { data: "info" },
            ],
            columnDefs: [
                {
                    // For Responsive
                    className: "control",
                    orderable: false,
                    searchable: false,
                    responsivePriority: 3,
                    targets: 0,
                    render: function () { return ""; },
                },
                {
                    targets: 1,
                    visible: false,
                    orderable: false,
                    searchable: false,
                },
                {
                    responsivePriority: 1,
                    targets: 2,
                    render: function (data, type, full, meta) {
                        if (type !== "display") return data;
                        var detailUrl = route("supplier.detail", full["id"]);
                        return '<a href="' + detailUrl + '" class="fw-semibold text-primary">' + (data ?? "-") + "</a>";
                    },
                },
                {
                    targets: [3, 4, 5, 6],
                    render: function (data) { return data || "-"; },
                },
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            displayLength: 10,
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) { return "Detail: " + row.data()["supplier"]; },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col) {
                            return col.title !== ""
                                ? "<tr><td>" + col.title + ":</td><td>" + col.data + "</td></tr>"
                                : "";
                        }).join("");
                        return data ? $('<table class="table"/>').append("<tbody>" + data + "</tbody>") : false;
                    },
                },
            },
        });

        dt_table_supplier.on("draw.dt", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    }
});
