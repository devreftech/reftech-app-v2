$(function () {
    var dt_table_part_inquiry = $(".datatable-part-inquiry");
    var Url = "db/part-inquiry";

    if (dt_table_part_inquiry.length) {
        $('[data-toggle="tooltip"]').tooltip();

        var dt_part_inquiry = dt_table_part_inquiry.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "sku" },
                { data: "brand" },
                { data: "pn" },
                { data: "selling_price" },
                { data: "cheapest_vendor" },
                { data: "min_price_idr" },
                { data: "last_inquiry" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full, meta) {
                        if (type !== "display") return data;
                        var detailUrl = route("part-inquiry.show", full["serial_id"]);
                        return '<a href="' + detailUrl + '" class="fw-semibold text-primary">' + (data ?? "-") + "</a>";
                    },
                },
                {
                    targets: 2,
                    render: function (data) { return data || "-"; },
                },
                {
                    targets: 3,
                    className: "text-end",
                    render: $.fn.dataTable.render.number(".", "", 0, "Rp "),
                },
                {
                    targets: 4,
                    render: function (data) { return data || "-"; },
                },
                {
                    targets: 5,
                    className: "text-end",
                    render: function (data, type, full, meta) {
                        if (type !== "display") return data;
                        if (data === null || data === undefined) return "-";
                        return "Rp " + parseInt(data).toLocaleString("id-ID");
                    },
                },
                {
                    targets: 6,
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
            order: [[6, "desc"]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            displayLength: 10,
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) { return "Detail: " + row.data()["sku"]; },
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

        dt_table_part_inquiry.on("draw.dt", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    }
});
