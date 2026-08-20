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

    var $table = $(".datatable-suo-logistic");
    if (!$table.length) return;

    $table.DataTable({
        ajax: {
            type: "GET",
            url: route("suo.data.logistic"),
            headers: { "Content-Type": "application/json" },
        },
        columns: [
            { data: "no_suo" },
            { data: "company" },
            { data: "pic" },
            { data: "status" },
            { data: "created_at" },
            { data: null },
        ],
        columnDefs: [
            { targets: 0, render: function (data) { return data || "-"; } },
            { targets: [1, 2], render: function (data) { return data || "-"; } },
            { targets: 3, className: "text-center", render: function (data, type) {
                return type === "display" ? statusBadge(data) : data;
            } },
            { targets: 4, render: function (data) { return dateCol(data); } },
            {
                targets: 5,
                className: "text-center",
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    if (type !== "display") return "";
                    var url = route("suo.show", full.id);
                    return '<a href="' + url + '" class="btn btn-sm btn-primary">Proses</a>';
                },
            },
        ],
        order: [[4, "desc"]],
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        dom:
            '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
            '<"table-responsive"t>' +
            '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { emptyTable: "Belum ada Sales Urgent Order." },
    });
});
