$(function () {
    function currency(data) {
        if (data === null || data === undefined || data === "" || isNaN(data)) return "-";
        return "Rp " + Number(data).toLocaleString("id-ID");
    }

    // Initialize Generic Tables (Tanah, Bangunan, Peralatan Kantor)
    $(".datatable-fixed-generic").each(function () {
        var $table = $(this);
        var type = $table.data("type");
        var badgeId = $table.data("badge");

        $table.DataTable({
            ajax: {
                type: "GET",
                url: "/db/fixed-asset?type=" + encodeURIComponent(type),
                headers: { "Content-Type": "application/json" },
                dataSrc: function (json) {
                    var count = (json.data || []).length;
                    if (badgeId) {
                        $("#" + badgeId).text(count);
                    }
                    return json.data || [];
                },
            },
            columns: [
                { data: "code" },
                { data: "desc" },
                { data: "qty" },
                { data: "total" },
                { data: "tanggal_beli" },
                { data: "tanggal_pakai" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full) {
                        var code = data || "-";
                        return (
                            '<a href="/fixed/' +
                            full.id +
                            '" class="fw-semibold text-primary">' +
                            code +
                            "</a>"
                        );
                    },
                },
                {
                    targets: 1,
                    render: function (data) {
                        return data || "-";
                    },
                },
                {
                    targets: 2,
                    className: "text-center",
                    render: function (data) {
                        return data !== null && data !== undefined ? data : "-";
                    },
                },
                {
                    targets: 3,
                    className: "text-end",
                    render: function (data) {
                        return currency(data);
                    },
                },
                {
                    targets: 4,
                    className: "text-center",
                    render: function (data) {
                        return data || "-";
                    },
                },
                {
                    targets: 5,
                    className: "text-center",
                    render: function (data) {
                        return data || "-";
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
            language: { emptyTable: "Belum ada data fixed asset." },
        });
    });

    // Initialize Kendaraan Table
    var $tableKendaraan = $(".datatable-fixed-kendaraan");
    if ($tableKendaraan.length) {
        var badgeKendaraan = $tableKendaraan.data("badge");
        $tableKendaraan.DataTable({
            ajax: {
                type: "GET",
                url: "/db/fixed-asset?type=Kendaraan",
                headers: { "Content-Type": "application/json" },
                dataSrc: function (json) {
                    var count = (json.data || []).length;
                    if (badgeKendaraan) {
                        $("#" + badgeKendaraan).text(count);
                    }
                    return json.data || [];
                },
            },
            columns: [
                { data: "code" },
                { data: "jenis_kendaraan" },
                { data: "merk_model" },
                { data: "plat_nomor" },
                { data: "atas_nama" },
                { data: "tanggal_beli" },
                { data: "total" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full) {
                        var code = data || "-";
                        return (
                            '<a href="/fixed/' +
                            full.id +
                            '" class="fw-semibold text-primary">' +
                            code +
                            "</a>"
                        );
                    },
                },
                {
                    targets: 1,
                    render: function (data) {
                        return data || "-";
                    },
                },
                {
                    targets: 2,
                    render: function (data) {
                        return data || "-";
                    },
                },
                {
                    targets: 3,
                    render: function (data) {
                        return data
                            ? '<span class="badge bg-label-secondary">' +
                                  data +
                                  "</span>"
                            : "-";
                    },
                },
                {
                    targets: 4,
                    render: function (data) {
                        return data || "-";
                    },
                },
                {
                    targets: 5,
                    className: "text-center",
                    render: function (data) {
                        return data || "-";
                    },
                },
                {
                    targets: 6,
                    className: "text-end",
                    render: function (data) {
                        return currency(data);
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
            language: { emptyTable: "Belum ada data kendaraan." },
        });
    }

    // Initialize Mesin Table
    var $tableMesin = $(".datatable-fixed-mesin");
    if ($tableMesin.length) {
        var badgeMesin = $tableMesin.data("badge");
        $tableMesin.DataTable({
            ajax: {
                type: "GET",
                url: "/db/fixed-asset?type=Mesin",
                headers: { "Content-Type": "application/json" },
                dataSrc: function (json) {
                    var count = (json.data || []).length;
                    if (badgeMesin) {
                        $("#" + badgeMesin).text(count);
                    }
                    return json.data || [];
                },
            },
            columns: [
                { data: "code" },
                { data: "unit_brand" },
                { data: "unit_model" },
                { data: "serial_number" },
                { data: "kondisi" },
                { data: "tanggal_beli" },
                { data: "total" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full) {
                        var code = data || "-";
                        return (
                            '<a href="/fixed/' +
                            full.id +
                            '" class="fw-semibold text-primary">' +
                            code +
                            "</a>"
                        );
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, full) {
                        return full.unit_brand || full.desc || "-";
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, full) {
                        return full.unit_model || "-";
                    },
                },
                {
                    targets: 3,
                    render: function (data) {
                        return data || "-";
                    },
                },
                {
                    targets: 4,
                    render: function (data) {
                        if (data === "Baru")
                            return '<span class="badge bg-label-secondary">Unit Baru</span>';
                        if (data === "Second")
                            return '<span class="badge bg-label-secondary">Unit Second</span>';
                        return data || "-";
                    },
                },
                {
                    targets: 5,
                    className: "text-center",
                    render: function (data) {
                        return data || "-";
                    },
                },
                {
                    targets: 6,
                    className: "text-end",
                    render: function (data) {
                        return currency(data);
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
            language: { emptyTable: "Belum ada data mesin." },
        });
    }

    // Initialize Tools Table
    var $tableTools = $(".datatable-fixed-tools");
    if ($tableTools.length) {
        var badgeTools = $tableTools.data("badge");
        $tableTools.DataTable({
            ajax: {
                type: "GET",
                url: "/db/fixed-asset?type=Tools",
                headers: { "Content-Type": "application/json" },
                dataSrc: function (json) {
                    var count = (json.data || []).length;
                    if (badgeTools) {
                        $("#" + badgeTools).text(count);
                    }
                    return json.data || [];
                },
            },
            columns: [
                { data: "code" },
                { data: "nama_tools" },
                { data: "teknisi_name" },
                { data: "qty" },
                { data: "tanggal_serah_terima_fmt" },
                { data: "status_finance" },
                { data: "total" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full) {
                        var code = data || ("TLS-" + full.id);
                        return (
                            '<a href="/fixed/' +
                            full.id +
                            '" class="fw-semibold text-primary">' +
                            code +
                            "</a>"
                        );
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, full) {
                        return full.nama_tools || full.desc || "-";
                    },
                },
                {
                    targets: 2,
                    render: function (data) {
                        return data || "-";
                    },
                },
                {
                    targets: 3,
                    className: "text-center",
                    render: function (data) {
                        return data !== null && data !== undefined ? data : "-";
                    },
                },
                {
                    targets: 4,
                    className: "text-center",
                    render: function (data) {
                        return data || "-";
                    },
                },
                {
                    targets: 5,
                    render: function (data) {
                        if (data === "Lengkap") {
                            return '<span class="badge bg-label-success">Lengkap</span>';
                        }
                        return '<span class="badge bg-label-warning">Belum Lengkap</span>';
                    },
                },
                {
                    targets: 6,
                    className: "text-end",
                    render: function (data) {
                        return currency(data);
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
            language: { emptyTable: "Belum ada data tools." },
        });
    }

    // Auto adjust columns on tab switch
    $('button[data-bs-toggle="tab"]').on("shown.bs.tab", function () {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
});
