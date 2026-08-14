$(function () {
    var dt_table_product_out_detail = $(".datatable-product-out-detail");
    var Url = "/db/product/out/detail/";
    var path = window.location.pathname;
    var id = path.substring(path.lastIndexOf('/') + 1);

    if (dt_table_product_out_detail.length) {
        $('[data-toggle="tooltip"]').tooltip();
        var dt_product_out_detail = dt_table_product_out_detail.DataTable({
            ajax: {
                type: "GET",
                url: Url + id,
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
                { data: "" },
                { data: "id" },
                { data: "invoice" },
                { data: "detail_client" },
                { data: "replacement" },
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
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $dataId = full["id"];
                            var detailRoute = route("product-out.show", $dataId);
                            return (
                                '<a class="text-dark" href="' + detailRoute + '">' + data + "</a>"
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, full, row) {
                        return data ? data : "-";
                    },
                },
            ],
            order: [[1, "desc"]],
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
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
                            return col.title !== "" // ? Do not show row in modal popup if title is blank (for check box)
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
    }
    dt_table_product_out_detail.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
