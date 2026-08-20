$(function () {
    function dateCol(data) {
        if (!data) return "-";
        var m = moment(data);
        return m.isValid() ? m.format("DD-MM-YYYY") : data;
    }

    function statusBadge(status) {
        switch (status) {
            case "submitted":
                return '<span class="badge bg-label-warning">Menunggu Konfirmasi Gudang</span>';
            case "confirmed":
                return '<span class="badge bg-label-info">Stok Dikonfirmasi</span>';
            case "goods_out":
                return '<span class="badge bg-label-primary">Barang Keluar</span>';
            case "converted":
                return '<span class="badge bg-label-success">Sudah Dikonversi ke Invoice</span>';
            default:
                return '<span class="badge bg-label-secondary">' + (status || "-") + "</span>";
        }
    }

    var $table = $(".datatable-suo-sales");
    if (!$table.length) return;

    // Admin/Sales Manager lihat SUO seluruh tim, jadi tabelnya dapat kolom
    // tambahan "Sales" biar kelihatan itu punya siapa (lihat SuoController::dataSales()).
    var isManager = $table.data("is-manager") == 1 || $table.data("is-manager") === "1";

    var columns = [{ data: "no_suo" }];
    if (isManager) columns.push({ data: "sales_name" });
    columns.push(
        { data: "company" },
        { data: "pic" },
        { data: "status" },
        { data: "no_invoice_booking" },
        { data: "created_at" }
    );

    var statusTarget = isManager ? 4 : 3;
    var dateTarget = isManager ? 6 : 5;

    var columnDefs = [
        {
            targets: 0,
            render: function (data, type, full) {
                if (type !== "display") return data || "-";
                var url = route("suo.show", full.id);
                return '<a href="' + url + '" class="fw-semibold text-primary">' + (data || "-") + "</a>";
            },
        },
        { targets: statusTarget, className: "text-center", render: function (data, type) {
            return type === "display" ? statusBadge(data) : data;
        } },
        { targets: dateTarget, render: function (data) { return dateCol(data); } },
    ];

    $table.DataTable({
        ajax: {
            type: "GET",
            url: route("suo.data.sales"),
            headers: { "Content-Type": "application/json" },
        },
        columns: columns,
        columnDefs: columnDefs,
        order: [[dateTarget, "desc"]],
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        dom:
            '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
            '<"table-responsive"t>' +
            '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { emptyTable: "Belum ada Sales Urgent Order." },
    });
});
