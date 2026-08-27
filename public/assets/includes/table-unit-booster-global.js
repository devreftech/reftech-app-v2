$(function () {
    var $table = $(".datatable-unit-booster");
    if (!$table.length) return;

    var dt = $table.DataTable({
        ajax: {
            type: "GET",
            url: "/db/unit/global/booster",
            headers: { "Content-Type": "application/json" },
            dataSrc: "data",
        },
        columns: [
            { data: "sku" },
            { data: "brand" },
            { data: "model" },
            { data: "inlet_pressure" },
            { data: "outlet_pressure" },
            { data: "inlet_cap" },
            { data: "outlet_cap" },
            { data: "power" },
        ],
        columnDefs: [
            {
                targets: 0,
                className: "text-center",
                render: function (data, type, full) {
                    if (type !== "display") return data;
                    if (!data) return "-";
                    var url = route("unit-global.show", full.id);
                    return '<a href="' + url + '">' + data + "</a>";
                },
            },
            { targets: [1, 2, 3, 4, 5, 6, 7], className: "text-center", render: function (data) { return data || "-"; } },
        ],
        order: [[0, "desc"]],
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        dom:
            '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
            '<"table-responsive"t>' +
            '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { emptyTable: "Belum ada data unit." },
    });

    $("#btn-tab-booster").on("shown.bs.tab", function () {
        dt.columns.adjust().draw(false);
    });
});
