$(function () {
    "use strict";

    var $table = $(".datatable-gr-uninvoiced");
    if (!$table.length) return;

    var Url = "/db/payable/gr-uninvoiced";

    function rupiah(n) {
        return "Rp " + new Intl.NumberFormat("id-ID").format(Number(n) || 0);
    }

    $table.find("thead tr").clone(true).appendTo($table.find("thead"));
    $table.find("thead tr:eq(1) th").each(function (i) {
        var title = $(this).text().trim();
        if (i >= 6) {
            $(this).html("");
            return;
        }
        $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />');
        $("input", this).on("keyup change", function () {
            if (dt.column(i).search() !== this.value) {
                dt.column(i).search(this.value).draw();
            }
        });
    });

    var dt = $table.DataTable({
        ajax: { type: "GET", url: Url },
        columns: [
            { data: "no_product_in" },
            { data: "tanggal" },
            { data: "supplier", render: function (d, t, full) { return d || full.d_supplier || "-"; } },
            { data: "no_po" },
            { data: "total_qty", className: "text-center" },
            { data: "total", className: "text-end" },
            { data: "age_days", className: "text-center" },
            { data: null, orderable: false, searchable: false },
        ],
        columnDefs: [
            {
                targets: 0,
                render: function (data, type, full) {
                    if (type !== "display") return data || "";
                    return '<span class="fw-bold text-dark"><i class="mdi mdi-package-variant-closed me-1"></i>' + (data || "-") + "</span>";
                },
            },
            {
                targets: 3,
                render: function (data, type, full) {
                    if (type !== "display") return data || "";
                    if (!data) return '<span class="text-muted">-</span>';
                    var term = full.payment_type === "tempo" ? ' <span class="badge bg-label-warning">Tempo</span>' : "";
                    return '<span class="fw-semibold">' + data + "</span>" + term;
                },
            },
            {
                targets: 4,
                render: function (data, type) {
                    return type === "display" ? '<span class="badge bg-label-secondary">' + (data || 0) + " item</span>" : data;
                },
            },
            {
                targets: 5,
                render: function (data, type) {
                    return type === "display" ? '<span class="fw-bold text-dark">' + rupiah(data) + "</span>" : Number(data) || 0;
                },
            },
            {
                targets: 6,
                render: function (data, type) {
                    if (type !== "display") return data || 0;
                    var d = Number(data) || 0;
                    var cls = d > 30 ? "bg-label-danger" : d > 14 ? "bg-label-warning" : "bg-label-success";
                    return '<span class="badge ' + cls + ' rounded-pill">' + d + " hari</span>";
                },
            },
            {
                targets: 7,
                className: "text-center",
                render: function (data, type, full) {
                    if (type !== "display") return "";
                    if (!full.id_po) return '<span class="text-muted small">-</span>';
                    var url = typeof route === "function" ? route("purchase.show", full.id_po) : "/purchase/" + full.id_po;
                    return '<a href="' + url + '" class="btn btn-sm btn-label-primary"><i class="mdi mdi-upload me-1"></i>Input Invoice</a>';
                },
            },
        ],
        orderCellsTop: true,
        order: [[6, "desc"]],
        dom: '<"row mx-1 mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"table-responsive"t><"row mx-1 mt-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        displayLength: 25,
    });
});
