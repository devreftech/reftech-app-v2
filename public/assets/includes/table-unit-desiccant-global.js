$(function () {
    var $table = $(".datatable-unit-desiccant");
    if (!$table.length) return;

    var dt = $table.DataTable({
        ajax: {
            type: "GET",
            url: "/db/unit/global/desiccant",
            headers: { "Content-Type": "application/json" },
            dataSrc: "data",
        },
        columns: [
            { data: "sku" },
            { data: "brand" },
            { data: "air_cap" },
            { data: "refrigerant_type" },
            { data: "pdp" },
            { data: "voltage" },
            { data: "connect" },
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
            { targets: [1, 2, 3, 4, 5, 6], className: "text-center", render: function (data) { return data || "-"; } },
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

    $("#btn-tab-desiccant").on("shown.bs.tab", function () {
        dt.columns.adjust().draw(false);
    });
});
