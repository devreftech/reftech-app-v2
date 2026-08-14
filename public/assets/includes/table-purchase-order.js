$(function () {
    var dt_table_purchase_order = $(".datatable-purchase-order");
    var Url = "db/purchase-order";

    if (dt_table_purchase_order.length) {
        $('[data-toggle="tooltip"]').tooltip();

        dt_table_purchase_order.find("thead tr")
            .clone(true)
            .appendTo(dt_table_purchase_order.find("thead"));

        dt_table_purchase_order.find("thead tr:eq(1) th").each(function (i) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
            );
            $("input", this).on("keyup change", function () {
                if (dt_purchase_order.column(i).search() !== this.value) {
                    dt_purchase_order.column(i).search(this.value).draw();
                }
            });
        });

        var dt_purchase_order = dt_table_purchase_order.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "no_po" },
                { data: "company" },
                { data: "attn" },
                { data: "total" },
                { data: "tanggal" },
                { data: "payment" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full, meta) {
                        if (type !== "display") return data;
                        var detailUrl = route("purchase.show", full["id"]);
                        return '<a href="' + detailUrl + '" class="fw-semibold text-primary">' + (data ?? "-") + "</a>";
                    },
                },
                {
                    targets: 3,
                    className: "text-end",
                    render: $.fn.dataTable.render.number(".", "", 0, "Rp "),
                },
                {
                    targets: 4,
                    className: "text-center",
                    render: function (data) { return data || "-"; },
                },
            ],
            orderCellsTop: true,
            order: [[4, "desc"]],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"B>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            displayLength: 10,
            buttons: [
                {
                    text: '<i class="mdi mdi-plus me-1"></i><span class="d-none d-sm-inline-block">Purchase Order</span>',
                    className: "btn btn-sm btn-primary",
                    action: function (e, dt, node, config) {
                        window.location = route("purchase.create");
                    },
                },
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) { return "Detail: " + row.data()["no_po"]; },
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

        dt_table_purchase_order.on("draw.dt", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    }
});
