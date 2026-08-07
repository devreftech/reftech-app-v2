$(function () {
    var dt_table_product = $(".datatable-product-in-req-logistic");
    var Url = "db/product/in/logistik";

    if (dt_table_product.length) {
        $('[data-toggle="tooltip"]').tooltip();
        var dt_product = dt_table_product.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
            },
            columns: [
                { data: "" },
                { data: "id" },
                { data: "no_product_in" },
                {
                    data: "supplier",
                    render: function (data, type, row) {
                        return data ? data : row.supplier_name;
                    },
                },
                { data: "info" },
                { data: "total_qty" },
                { data: "date" },
            ],
            columnDefs: [
                {
                    // For Responsive
                    className: "control",
                    orderable: false,
                    searchable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function () { return ""; },
                },
                {
                    targets: 1,
                    searchable: true,
                    visible: false,
                },
                {
                    // Category, Qty, Date — rata tengah
                    targets: [4, 5, 6],
                    className: "text-center",
                },
                {
                    // No. Product In — link ke detail
                    responsivePriority: 1,
                    targets: 2,
                    render: function (data, type, full, meta) {
                        var $detailUrl = route("product-in.preview", full["id"]);
                        return '<a href="' + $detailUrl + '">#' + (data ?? "-") + "</a>";
                    },
                },
                {
                    // Category badge
                    targets: 4,
                    render: function (data, type, full, meta) {
                        if (!data) return "-";
                        var color = data === "Import" ? "warning" : "info";
                        return '<span class="badge bg-label-' + color + '">' + data + '</span>';
                    },
                },
                {
                    targets: 6,
                    render: function (data, type, full, meta) {
                        if (!data) return "-";
                        var d = new Date(data);
                        var day = String(d.getDate()).padStart(2, "0");
                        var month = String(d.getMonth() + 1).padStart(2, "0");
                        var year = d.getFullYear();
                        return day + "-" + month + "-" + year;
                    },
                },
            ],
            order: [[1, "desc"]],
            dom: '<"card-header flex-column flex-md-row"<"head-label-delay text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            buttons: [
                {
                    text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">New Invoice Product</span>',
                    className: "btn btn-primary btn-new",
                    action: function (e, dt, node, config) {
                        window.location = route("product-in.create");
                    },
                },
            ],
            initComplete: function () {
                var api = this.api();
                // Search input untuk: No. Product In(2), Supplier(3), Category(4), Qty(5), Date(6)
                var searchableCols = [2, 3, 4, 5, 6];
                api.columns().every(function () {
                    var col = this;
                    var idx = col.index();
                    var $footer = $(col.footer());
                    if (!searchableCols.includes(idx)) {
                        $footer.empty();
                        return;
                    }
                    $('<input type="text" class="form-control form-control-sm" placeholder="Cari...">')
                        .appendTo($footer.empty())
                        .on("keyup change clear", function () {
                            if (col.search() !== this.value) {
                                col.search(this.value).draw();
                            }
                        });
                });
            },
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            return "Details of " + row.data()["no_product_in"];
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col) {
                            return col.title !== ""
                                ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                                  "<td>" + col.title + ":</td><td>" + col.data + "</td></tr>"
                                : "";
                        }).join("");
                        return data ? $('<table class="table"/><tbody />').append(data) : false;
                    },
                },
            },
        });
        $("div.head-label-delay").html(
            '<h5 class="card-title mb-0">Pending Good Receipt</h5>'
        );
    }
    dt_table_product.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
