$(function () {
    var dt_table = $(".datatable-sales-reports-year");
    if (!dt_table.length) return;

    var year = dt_table.data("year");
    var Url = "/db/reports/year/" + year;

    function fmtNum(data) {
        var n = parseInt(data, 10) || 0;
        return n === 0 ? '<span class="text-muted">-</span>' : n.toLocaleString("id-ID");
    }

    var dt_sales_reports_year = dt_table.DataTable({
        ajax: {
            type: "GET",
            url: Url,
            headers: { "Content-Type": "application/json" },
        },
        columns: [
            { data: "" },
            { data: "commodity" },
            { data: "GO" },
            { data: "pIn1" },
            { data: "pOut1" },
            { data: "pIn2" },
            { data: "pOut2" },
            { data: "totalIn" },
            { data: "totalOut" },
            { data: "AllStock" },
            // Kolom tersembunyi buat default sort — item yang paling aktif
            // (total in+out kedua semester) tampil duluan, bukan urut alfabet doang.
            { data: "total_movement" },
        ],
        columnDefs: [
            {
                // For Responsive
                className: "control",
                orderable: false,
                searchable: false,
                responsivePriority: 2,
                targets: 0,
                render: function () {
                    return "";
                },
            },
            {
                responsivePriority: 1,
                targets: 1,
                render: function (data, type, full) {
                    if (type !== "display") return data || "";
                    var desc = full.description || "";
                    var truncated = data && data.length > 30 ? data.substring(0, 27) + "…" : data || "-";
                    var detailUrl = route("product.show", full.id);
                    return '<a href="' + detailUrl + '" data-bs-toggle="tooltip" data-bs-placement="top" title="' +
                        (desc ? desc.replace(/"/g, "&quot;") : "") + '">' +
                        '<span class="fw-bold text-primary">' + truncated + "</span></a>";
                },
            },
            {
                targets: 2,
                className: "text-center",
                render: function (data, type) {
                    if (type !== "display") return data;
                    if (data === "Genuine") {
                        return '<span class="badge bg-label-success">Genuine</span>';
                    }
                    if (data) {
                        return '<span class="badge bg-label-warning">' + data + "</span>";
                    }
                    return '<span class="text-muted">-</span>';
                },
            },
            {
                targets: [3, 4, 5, 6, 7, 8, 9],
                className: "text-center",
                render: function (data, type) {
                    if (type !== "display") return data || 0;
                    return fmtNum(data);
                },
            },
            {
                // Total Product In / Total Product Out ditebalkan biar gampang dibedakan
                // dari breakdown per-semester di sebelah kirinya.
                targets: [7, 8],
                className: "text-center fw-semibold",
            },
            {
                targets: 10,
                visible: false,
                searchable: false,
            },
        ],
        order: [[10, "desc"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        displayLength: 15,
        lengthMenu: [15, 25, 50, 75, 100],
        language: { emptyTable: "Tidak ada data product in/out untuk tahun ini." },
    });

    dt_table.on("draw", function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
});
