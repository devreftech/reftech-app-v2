$(function () {
    // Halaman /unit (role Sales) — 1 card, 2 tab: Unit Bekas & Unit Baru.
    // Datanya sinkron dengan yang dilihat admin: sumbernya persis sama,
    //   Unit Bekas -> /db/unit-acquisition  (fixed_asset type "Mesin", join unit)
    //   Unit Baru  -> /db/unit-inventory    (unit_inventory status "available")
    // Baris cuma buka modal spesifikasi (#unitSpecModal), tanpa navigasi/aksi.

    function dash(d) {
        return d || d === 0 ? d : "-";
    }

    // air_cap kadang udah simpan satuannya sendiri ("1,15 m³/min"), kadang angka polos.
    function airCapCol(d) {
        if (!d) return "-";
        return /m.?\/min/i.test(d) ? d : d + " m³/min";
    }

    function currency(d) {
        if (d === null || d === undefined || d === "") return '<span class="text-muted">Belum diset</span>';
        return "Rp " + Number(d).toLocaleString("id-ID");
    }

    var categoryLabels = {
        "AIR COMPRESSOR SCREW": "Screw Compressor",
        "PISTON COMPRESSOR": "Piston Compressor",
        "BOOSTER COMPRESSOR": "Booster Compressor",
        "REFRIGERANT AIR DRYER": "Refrigerant Dryer",
        "DESICANT DRYER": "Desiccant Dryer",
    };
    function categoryCol(d) {
        return categoryLabels[d] || d || "-";
    }

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

    function statusCol(data, type, full) {
        if (type !== "display") {
            return [full.qc_status, full.qc_status === "ok" ? full.status_unit : null].filter(Boolean).join(" ");
        }
        var badge = qcBadges[full.qc_status] || "";
        if (full.qc_status === "ok" && full.status_unit && statusUnitBadges[full.status_unit]) {
            badge += " " + statusUnitBadges[full.status_unit];
        }
        return badge || "-";
    }

    function stockBadgeCol(data, type) {
        if (type !== "display") return data;
        return '<span class="badge bg-label-success">' + (data || 0) + " unit</span>";
    }

    function unitName(full) {
        return [full.unit_brand, full.unit_model].filter(Boolean).join(" ") || full.unit_sku || "-";
    }

    function nameCol(data, type, full) {
        var name = unitName(full);
        if (type !== "display") return name;
        return '<a href="javascript:void(0);" class="text-primary fw-semibold btn-view-spec">' + name + "</a>";
    }

    // ── Modal spesifikasi ────────────────────────────────────────────────
    var SPEC_LABELS = [
        { key: "unit_brand", label: "Brand" },
        { key: "unit_model", label: "Model" },
        { key: "unit_sku", label: "SKU" },
        { key: "code", label: "Code" },
        { key: "serial_number", label: "Serial Number" },
        { key: "unit_category", label: "Kategori", render: categoryCol },
        { key: "lubricant", label: "Type / Lubricant" },
        { key: "power", label: "Motor Power" },
        { key: "air_cap", label: "Air Capacity", render: airCapCol },
        { key: "pdp", label: "PDP" },
        { key: "grade", label: "Grade" },
        { key: "connect", label: "Connection" },
        { key: "capacity", label: "Capacity" },
        { key: "material", label: "Material" },
        { key: "supplier_name", label: "Supplier" },
    ];

    function showSpecModal(full) {
        var rows = "";
        SPEC_LABELS.forEach(function (f) {
            var v = full[f.key];
            if (v === null || v === undefined || v === "") return;
            rows +=
                '<div class="col-5 text-muted py-1">' + f.label + "</div>" +
                '<div class="col-7 py-1 fw-semibold">' + (f.render ? f.render(v) : v) + "</div>";
        });
        if (full.stock !== undefined && full.stock !== null) {
            rows += '<div class="col-5 text-muted py-1">Stok</div><div class="col-7 py-1 fw-semibold">' +
                full.stock + " unit</div>";
        }
        if (full.harga_jual !== undefined) {
            rows += '<div class="col-5 text-muted py-1">Harga Jual</div><div class="col-7 py-1 fw-semibold">' +
                currency(full.harga_jual) + "</div>";
        }
        $("#unitSpecModalTitle").text(unitName(full) === "-" ? "Spesifikasi Unit" : unitName(full));
        $("#unitSpecModalBody").html(rows || '<div class="col-12 text-muted py-2">Tidak ada data spesifikasi.</div>');
        new bootstrap.Modal(document.getElementById("unitSpecModal")).show();
    }

    $(document).on("click", ".btn-view-spec", function () {
        var $table = $(this).closest("table");
        if (!$.fn.dataTable.isDataTable($table)) return;
        var dt = $table.DataTable();
        var full = dt.row($(this).closest("tr")).data();
        if (full) showSpecModal(full);
    });

    // ── Tab Unit Bekas — /db/unit-acquisition ───────────────────────────
    var bekasSpecs = {
        screw: [
            { data: "lubricant", render: dash },
            { data: "power", render: dash },
            { data: "air_cap", render: airCapCol },
        ],
        dryer: [
            { data: "unit_category", render: categoryCol },
            { data: "pdp", render: dash },
            { data: "air_cap", render: airCapCol },
        ],
        filter: [
            { data: "air_cap", render: airCapCol },
            { data: "grade", render: dash },
            { data: "connect", render: dash },
        ],
        tank: [
            { data: "capacity", render: dash },
            { data: "material", render: dash },
            { data: "lubricant", render: dash },
        ],
    };

    var bekasTotals = {};
    function updateBekasTotal(group, count) {
        bekasTotals[group] = count;
        var total = Object.keys(bekasTotals).reduce(function (s, k) { return s + bekasTotals[k]; }, 0);
        $("#unit-second-count-badge").text(total).toggleClass("d-none", total === 0);
    }

    // ── Tab Unit Baru — /db/unit-inventory ──────────────────────────────
    var baruSpecs = {
        screw: [
            { data: "unit_category", render: categoryCol },
            { data: "lubricant", render: dash },
            { data: "power", render: dash },
            { data: "air_cap", render: airCapCol },
        ],
        dryer: [
            { data: "unit_category", render: categoryCol },
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
    };

    var baruTotals = {};
    function updateBaruTotal(group, count) {
        baruTotals[group] = count;
        var total = Object.keys(baruTotals).reduce(function (s, k) { return s + baruTotals[k]; }, 0);
        $("#unit-baru-count-badge").text(total).toggleClass("d-none", total === 0);
    }

    var DOM =
        '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
        '<"table-responsive"t>' +
        '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>';

    // Unit Bekas tables
    $(".datatable-unit-sales-bekas").each(function () {
        var $table = $(this);
        var group = $table.data("group");
        if (!group) return;

        var specs = bekasSpecs[group] || [];
        var columns = [{ data: "code", render: dash }, { data: null, render: nameCol }];
        var columnDefs = [];
        var idx = 2;
        specs.forEach(function (spec) {
            columns.push({ data: spec.data, render: spec.render });
            idx++;
        });
        columns.push({ data: "serial_number", render: dash });
        columns.push({ data: null, orderable: false, render: statusCol });

        var dt = $table.DataTable({
            ajax: {
                type: "GET",
                url: "/db/unit-acquisition?group=" + group,
                headers: { "Content-Type": "application/json" },
                dataSrc: function (json) {
                    var data = json.data || [];
                    $("#unit-acquisition-" + group + "-count-badge")
                        .text(data.length).toggleClass("d-none", data.length === 0);
                    updateBekasTotal(group, data.length);
                    return data;
                },
            },
            columns: columns,
            columnDefs: columnDefs,
            order: [[0, "desc"]],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom: DOM,
            language: { emptyTable: "Belum ada unit bekas di kategori ini." },
        });

        $('button[data-bs-target="#pill-bekas-' + group + '"]').on("shown.bs.tab", function () {
            dt.columns.adjust().draw(false);
        });
    });

    // Unit Baru tables
    $(".datatable-unit-sales-baru").each(function () {
        var $table = $(this);
        var group = $table.data("group");
        if (!group) return;

        var specs = baruSpecs[group] || [];
        var columns = [{ data: null, render: nameCol }];
        specs.forEach(function (spec) {
            columns.push({ data: spec.data, render: spec.render });
        });
        columns.push({ data: "stock", className: "text-center", render: stockBadgeCol });
        columns.push({ data: "harga_jual", render: function (d, t) { return t !== "display" ? (d || 0) : currency(d); } });

        var dt = $table.DataTable({
            ajax: {
                type: "GET",
                url: "/db/unit-inventory?group=" + group,
                headers: { "Content-Type": "application/json" },
                dataSrc: function (json) {
                    var data = json.data || [];
                    var count = data.reduce(function (s, r) { return s + (parseInt(r.stock, 10) || 0); }, 0);
                    $("#unit-inventory-" + group + "-count-badge")
                        .text(count).toggleClass("d-none", count === 0);
                    updateBaruTotal(group, count);
                    return data;
                },
            },
            columns: columns,
            order: [[0, "asc"]],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom: DOM,
            language: { emptyTable: "Belum ada stok unit baru di kategori ini." },
        });

        $('button[data-bs-target="#pill-baru-' + group + '"]').on("shown.bs.tab", function () {
            dt.columns.adjust().draw(false);
        });
    });

    // DataTables salah ngukur lebar kolom saat tab-nya masih hidden — koreksi
    // begitu tab utama "Unit Baru" pertama kali dibuka.
    $('button[data-bs-target="#tab-unit-baru"]').one("shown.bs.tab", function () {
        $(".datatable-unit-sales-baru").each(function () {
            if ($.fn.dataTable.isDataTable(this)) $(this).DataTable().columns.adjust().draw(false);
        });
    });
});
