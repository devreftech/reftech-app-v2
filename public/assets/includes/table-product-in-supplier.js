$(function () {
    var dt_table_product_in_supplier = $(".datatable-product-in-supplier");
    var path = window.location.pathname;
    var id = path.substring(path.lastIndexOf("/") + 1);
    var Url = "/db/productIn-supplier/" + id;

    if (dt_table_product_in_supplier.length) {
        $('[data-toggle="tooltip"]').tooltip();

        var dt_product_in_supplier = dt_table_product_in_supplier.DataTable({
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
                { data: "product" },
                { data: "qty" },
                { data: "subtotal" },
                { data: "tax" },
                { data: "date" },
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
                    render: function (data) { return data || "-"; },
                },
                {
                    targets: 3,
                    render: function (data) { return data || "-"; },
                },
                {
                    targets: [5, 6],
                    className: "text-end",
                    render: function (data, type, full, meta) {
                        if (type !== "display") return data;
                        if (data === null || data === undefined) return "-";
                        return "Rp " + parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    targets: 7,
                    className: "text-center",
                    render: function (data, type, full, meta) {
                        if (type !== "display") return data;
                        if (!data) return "-";
                        var d = new Date(data);
                        var day = String(d.getDate()).padStart(2, "0");
                        var month = String(d.getMonth() + 1).padStart(2, "0");
                        var year = d.getFullYear();
                        return day + "-" + month + "-" + year;
                    },
                },
            ],
            order: [[7, "desc"]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            displayLength: 10,
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) { return "Detail: " + row.data()["invoice"]; },
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

        dt_table_product_in_supplier.on("draw.dt", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    }
});
