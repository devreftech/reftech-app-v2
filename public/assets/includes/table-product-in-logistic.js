$(function () {
    var dt_table_product = $(".datatable-product-in-logistic");
    var Url = "db/productIn";

    if (dt_table_product.length) {
        $('[data-toggle="tooltip"]').tooltip();
        var dt_product = dt_table_product.DataTable({
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
                { data: "invoice" },
                {
                    data: "supplier",
                    render: function (data, type, row) {
                        return data ? data : row.supplier_name;
                    },
                },
                { data: "info" },
                { data: "qty" },
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
                    render: function (data, type, full, meta) {
                        return "";
                    },
                },
                {
                    targets: 1,
                    searchable: true,
                    visible: false,
                },
                {
                    responsivePriority: 1,
                    targets: 2,
                },
                {
                    targets: 2,
                    render: function (data, type, full, meta) {
                        var $tip = full["tip"];
                        var $invoice = full["invoice"];
                        var $detailQUrl = route("product-in.show", full["id"]);
                        return (
                            '<a href="' + $detailQUrl + '" data-toggle="tooltip" data-container="body" data-bs-placement="top" data-bs-custom-class="tooltip-primary"' +
                            ' title=" ' +
                            $tip +
                            '">' +
                            ($invoice ?? "-") +
                            "</a>"
                        );
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
            ],
            order: [[1, "desc"]],
            dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            buttons: [
                {
                    extend: "collection",
                    className: "btn btn-label-primary dropdown-toggle me-2",
                    text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                    buttons: [
                        {
                            extend: "print",
                            text: '<i class="mdi mdi-printer-outline me-1" ></i>Print',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6] },
                        },
                        {
                            extend: "csv",
                            text: '<i class="mdi mdi-file-document-outline me-1" ></i>Csv',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6] },
                        },
                        {
                            extend: "excel",
                            text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6] },
                        },
                        {
                            extend: "pdf",
                            text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6] },
                        },
                        {
                            extend: "copy",
                            text: '<i class="mdi mdi-content-copy me-1" ></i>Copy',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6] },
                        },
                    ],
                },
            ],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return "Details of " + data["company"];
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.title !== ""
                                ? '<tr data-dt-row="' +
                                      col.rowIndex +
                                      '" data-dt-column="' +
                                      col.columnIndex +
                                      '">' +
                                      "<td>" +
                                      col.title +
                                      ":" +
                                      "</td> " +
                                      "<td>" +
                                      col.data +
                                      "</td>" +
                                      "</tr>"
                                : "";
                        }).join("");

                        return data
                            ? $('<table class="table"/><tbody />').append(data)
                            : false;
                    },
                },
            },
        });
        $("div.head-label").html(
            '<h5 class="card-title mb-0">Good Receipt History</h5>'
        );
    }
    dt_table_product.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
