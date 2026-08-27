$(function () {
    var unitId = $("#unit-inventory-detail-root").data("unit-id");
    if (!unitId) return;

    function currency(data) {
        if (data === null || data === undefined || data === "") return "-";
        return "Rp " + Number(data).toLocaleString("id-ID");
    }

    var statusBadges = {
        available: '<span class="badge bg-label-success">Available</span>',
        sold: '<span class="badge bg-label-dark">Sold</span>',
    };

    var $tableIn = $(".datatable-unit-inventory-in");
    if ($tableIn.length) {
        $tableIn.DataTable({
            ajax: {
                type: "GET",
                url: "/db/unit-inventory/" + unitId + "/in",
                headers: { "Content-Type": "application/json" },
                dataSrc: "data",
            },
            columns: [
                { data: "tanggal_masuk" },
                { data: "no_transaksi" },
                { data: "serial_number" },
                { data: "supplier_name" },
                { data: "harga_modal" },
                { data: "biaya_rebranding" },
                { data: "total_modal" },
                { data: "status" },
            ],
            columnDefs: [
                { targets: 0, render: function (data) { return data || "-"; } },
                { targets: 1, render: function (data) { return data || "-"; } },
                { targets: 2, render: function (data) { return data || "-"; } },
                { targets: 3, render: function (data) { return data || "-"; } },
                { targets: 4, render: function (data) { return currency(data); } },
                { targets: 5, render: function (data) { return currency(data); } },
                { targets: 6, render: function (data) { return currency(data); } },
                {
                    targets: 7,
                    orderable: false,
                    render: function (data) { return statusBadges[data] || data || "-"; },
                },
            ],
            order: [[0, "desc"]],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { emptyTable: "Belum ada riwayat barang masuk untuk model unit ini." },
        });
    }

    var $tableOut = $(".datatable-unit-inventory-out");
    if ($tableOut.length) {
        var dtOut = $tableOut.DataTable({
            ajax: {
                type: "GET",
                url: "/db/unit-inventory/" + unitId + "/out",
                headers: { "Content-Type": "application/json" },
                dataSrc: "data",
            },
            columns: [
                { data: "tanggal_keluar" },
                { data: "no_transaksi" },
                { data: "serial_number" },
                { data: "customer" },
                { data: "harga_jual" },
                { data: "nilai_pokok" },
                { data: "selisih" },
            ],
            columnDefs: [
                { targets: 0, render: function (data) { return data || "-"; } },
                { targets: 1, render: function (data) { return data || "-"; } },
                { targets: 2, render: function (data) { return data || "-"; } },
                { targets: 3, render: function (data) { return data || "-"; } },
                { targets: 4, render: function (data) { return currency(data); } },
                { targets: 5, render: function (data) { return currency(data); } },
                {
                    targets: 6,
                    render: function (data) {
                        if (data === null || data === undefined || data === "") return "-";
                        var cls = Number(data) < 0 ? "text-danger" : "text-success";
                        return '<span class="' + cls + '">' + currency(data) + "</span>";
                    },
                },
            ],
            order: [[0, "desc"]],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { emptyTable: "Belum ada riwayat barang keluar untuk model unit ini." },
        });

        // Tab-nya start hidden (display:none), jadi DataTables ngitung lebar kolom
        // salah pas init — perbaiki begitu tab-nya beneran ditampilkan.
        $('button[data-bs-target="#tab-riwayat-keluar"]').on("shown.bs.tab", function () {
            dtOut.columns.adjust().draw(false);
        });
    }
});
