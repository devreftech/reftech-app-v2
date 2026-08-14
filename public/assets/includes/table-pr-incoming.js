$(function () {
    function dateCol(data) {
        if (!data) return "-";
        var m = moment(data);
        return m.isValid() ? m.format("DD-MM-YYYY") : data;
    }

    var $table = $(".datatable-pr-incoming");
    if (!$table.length) return;

    $table.DataTable({
        ajax: {
            type: "GET",
            url: "/db/purchase-request/delivery",
            headers: { "Content-Type": "application/json" },
        },
        columns: [
            { data: "no_pr" },
            { data: "no_po" },
            { data: "no_pending" },
            { data: "company" },
            { data: "item" },
            { data: "purchase_type" },
            { data: "cargo" },
            { data: "purchase_date" },
            { data: null },
        ],
        columnDefs: [
            { targets: [0, 1, 2, 3, 4, 5, 6], render: function (data) { return data || "-"; } },
            { targets: 7, render: function (data) { return dateCol(data); } },
            {
                targets: 8,
                className: "text-center",
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    var url = route("purchase-request.goods-receipt", full.id);
                    return '<a href="' + url + '" class="btn btn-sm btn-primary text-white waves-effect waves-light">' +
                        '<i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>Verifikasi</a>';
                },
            },
        ],
        order: [[7, "asc"]],
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        dom:
            '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
            '<"table-responsive"t>' +
            '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { emptyTable: "Tidak ada PR yang sedang dikirim." },
    });
});
