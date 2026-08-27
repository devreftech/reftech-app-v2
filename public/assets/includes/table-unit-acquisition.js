$(function () {
    var $tables = $(".datatable-unit-acquisition");
    if (!$tables.length) return;

    var qcBadges = {
        checking: '<span class="badge bg-label-warning">Dalam Pengecekan</span>',
        ok: '<span class="badge bg-label-success">Unit OK</span>',
        reject: '<span class="badge bg-label-danger">Reject</span>',
    };

    var statusUnitBadges = {
        Service: '<span class="badge bg-label-warning">Service</span>',
        Rental: '<span class="badge bg-label-primary">Rental</span>',
        Breakdown: '<span class="badge bg-label-danger">Breakdown</span>',
        Reserved: '<span class="badge bg-label-info">Reserved</span>',
        Sold: '<span class="badge bg-label-dark">Sold</span>',
    };

    // Badge total "Unit Second" di tab luar (unitAcquisitionTabs) adalah gabungan dari
    // semua kategori (Compressor/Dryer/Filter/Chiller) — dipanggil dari tiap dataSrc callback.
    var totals = {};
    function updateTotalBadge(group, count) {
        totals[group] = count;
        var total = Object.keys(totals).reduce(function (sum, k) { return sum + totals[k]; }, 0);
        $("#unit-second-count-badge").text(total).toggleClass("d-none", total === 0);
    }

    // Label tampilan buat kolom Type di sub-tab Dryer — u.unit dari server masih
    // pakai istilah "REFRIGERANT AIR DRYER"/"DESICANT DRYER", disederhanakan di sini.
    var dryerTypeLabels = {
        "REFRIGERANT AIR DRYER": "Refrigerant Dryer",
        "DESICANT DRYER": "Desiccant Dryer",
    };

    function airCapCol(data) {
        if (!data) return "-";
        return /m.?\/min/i.test(data) ? data : data + " m³/min";
    }

    function dash(data) {
        return data || data === 0 ? data : "-";
    }

    // Kolom spesifikasi per kategori — field dari `unit` (join fixed_asset -> unit),
    // disamakan dengan kolom yang dipakai tab "Unit Baru" (table-unit-inventory.js).
    var specColumnsByGroup = {
        screw: [
            { data: "lubricant", render: dash },
            { data: "power", render: dash },
            { data: "air_cap", render: airCapCol },
        ],
        dryer: [
            { data: "unit_category", render: function (data) { return dryerTypeLabels[data] || data || "-"; } },
            { data: "pdp", render: dash },
            { data: "air_cap", render: airCapCol },
        ],
        filter: [
            { data: "air_cap", render: airCapCol },
            { data: "grade", render: dash },
            { data: "connect", render: dash },
        ],
        chiller: [
            { data: "capacity", render: dash },
            { data: "power", render: dash },
        ],
        tank: [
            { data: "capacity", render: dash },
            { data: "material", render: dash },
            { data: "lubricant", render: dash },
        ],
    };

    function buildColumns(group) {
        var columns = [
            { data: "code" },
            { data: null },
        ];
        var columnDefs = [
            {
                // Code — link ke halaman detail unit acquisition-nya.
                targets: 0,
                render: function (data, type, full) {
                    if (type !== "display") return data || "";
                    if (!data) return "-";
                    var url = route("unit-acquisition.show", full.id);
                    return '<a href="' + url + '">' + data + "</a>";
                },
            },
            {
                // Unit — gabungan brand + model dari unit global-nya.
                targets: 1,
                render: function (data, type, full) {
                    var name = [full.unit_brand, full.unit_model].filter(Boolean).join(" ");
                    return name || "-";
                },
            },
        ];

        var specs = specColumnsByGroup[group] || [];
        var next = 2;
        specs.forEach(function (spec) {
            columns.push({ data: spec.data });
            columnDefs.push({ targets: next, render: spec.render });
            next++;
        });

        columns.push({ data: "serial_number" }, { data: null });
        columnDefs.push(
            { targets: next, render: function (data) { return data || "-"; } },
            {
                // Status — qc_status (proses pengecekan awal) dulu, baru status_unit
                // (ketersediaan) begitu qc-nya lolos OK.
                targets: next + 1,
                orderable: false,
                render: function (data, type, full) {
                    if (type !== "display") {
                        return [full.qc_status, full.qc_status === "ok" ? full.status_unit : null].filter(Boolean).join(" ");
                    }
                    var badge = qcBadges[full.qc_status] || "";
                    if (full.qc_status === "ok" && full.status_unit && statusUnitBadges[full.status_unit]) {
                        badge += " " + statusUnitBadges[full.status_unit];
                    }
                    return badge || "-";
                },
            }
        );

        return { columns: columns, columnDefs: columnDefs };
    }

    $tables.each(function () {
        var $table = $(this);
        var group = $table.data("group");
        if (!group) return;

        var built = buildColumns(group);

        var dt = $table.DataTable({
            ajax: {
                type: "GET",
                url: "/db/unit-acquisition?group=" + group,
                headers: { "Content-Type": "application/json" },
                dataSrc: function (json) {
                    var rows = json.data || [];
                    $("#unit-acquisition-" + group + "-count-badge").text(rows.length).toggleClass("d-none", rows.length === 0);
                    updateTotalBadge(group, rows.length);
                    return rows;
                },
            },
            columns: built.columns,
            columnDefs: built.columnDefs,
            order: [[0, "desc"]],
            orderCellsTop: true,
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { emptyTable: "Belum ada data unit acquisition di kategori ini." },
        });

        // Search per kolom — input/select di baris kedua thead (.column-filters), satu
        // per kolom yang searchable. Klik/pilih jangan sampai kepencet ke sorting
        // header (th-nya dobel-baris).
        $table.find("thead tr.column-filters th").each(function (idx) {
            var $select = $(this).find("select[data-col-search-status]");
            if ($select.length) {
                $select
                    .on("click", function (e) { e.stopPropagation(); })
                    .on("change", function () {
                        // Exact match, bukan substring — "ok" doang bakal ke-match ke
                        // "ok OK"/"ok Service"/dst kalau dibiarin substring biasa.
                        var val = this.value ? "^" + $.fn.dataTable.util.escapeRegex(this.value) + "$" : "";
                        dt.column(idx).search(val, true, false).draw();
                    });
                return;
            }

            var $input = $(this).find("input[data-col-search]");
            if (!$input.length) return;
            $input
                .on("click", function (e) { e.stopPropagation(); })
                .on("keyup change", function () {
                    if (dt.column(idx).search() !== this.value) {
                        dt.column(idx).search(this.value).draw();
                    }
                });
        });

        // Tab yang start hidden bikin DataTables ngitung lebar kolom salah pas init —
        // perbaiki begitu tab sub-kategori-nya beneran ditampilkan.
        $('button[data-bs-target="#subtab-second-' + group + '"]').on("shown.bs.tab", function () {
            dt.columns.adjust().draw(false);
        });
    });
});
