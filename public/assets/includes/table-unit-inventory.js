$(function () {
    var $tables = $(".datatable-unit-inventory");
    if (!$tables.length) return;

    function currency(data) {
        if (data === null || data === undefined || data === "") return "-";
        return "Rp " + Number(data).toLocaleString("id-ID");
    }

    // "Rp" rata kiri, angkanya rata kanan — biar rapi kalau angkanya beda-beda panjang.
    function priceCell(data) {
        if (data === null || data === undefined || data === "") return "-";
        return '<div class="d-flex justify-content-between"><span>Rp</span><span>' +
            Number(data).toLocaleString("id-ID") + "</span></div>";
    }

    // Tiap entri items_detail formatnya "serial_number::id_unit_inventory", dipisah
    // pas di-GROUP_CONCAT dari server — lihat /db/unit-inventory. Cuma preview cepat
    // di sini (harga jual sekarang satu per model, udah kelihatan di kolom sendiri),
    // gak ada link per-serial karena detail lengkapnya ada di halaman detail per-model.
    function itemsChildHtml(itemsDetail) {
        if (!itemsDetail) {
            return '<div class="p-2 ps-4 text-muted small">Tidak ada serial number.</div>';
        }
        var rows = itemsDetail.split("||").map(function (it) {
            var parts = it.split("::");
            var serial = parts[0] || it;
            return "<tr><td class=\"ps-4\">" + serial + "</td></tr>";
        }).join("");
        return '<table class="table table-sm mb-0"><tbody>' + rows + "</tbody></table>";
    }

    // Badge total "Unit Baru" di tab luar (unitAcquisitionTabs) adalah gabungan dari
    // semua kategori (Compressor/Dryer/Filter/Chiller) — dipanggil dari tiap dataSrc callback.
    var totals = {};
    function updateTotalBadge(group, count) {
        totals[group] = count;
        var total = Object.keys(totals).reduce(function (sum, k) { return sum + totals[k]; }, 0);
        $("#unit-baru-count-badge").text(total).toggleClass("d-none", total === 0);
    }

    // Label tampilan buat kolom Type di sub-tab Compressor & Dryer — u.unit dari
    // server masih pakai istilah "AIR COMPRESSOR SCREW" dst, disederhanakan di sini.
    var categoryLabels = {
        "AIR COMPRESSOR SCREW": "Screw Compressor",
        "PISTON COMPRESSOR": "Piston Compressor",
        "BOOSTER COMPRESSOR": "Booster Compressor",
        "REFRIGERANT AIR DRYER": "Refrigerant Dryer",
        "DESICANT DRYER": "Desiccant Dryer",
    };

    function unitNameCol(data, type, full) {
        // Sebagian unit (mis. Chiller) belum ada brand/model-nya di master data,
        // fallback ke SKU biar barisnya tetap kebaca, bukan cuma "-".
        var name = [full.unit_brand, full.unit_model].filter(Boolean).join(" ") || full.unit_sku || "-";
        if (type !== "display") return name;
        return '<a href="javascript:;" class="item-expand-toggle text-primary fw-semibold text-decoration-none">' +
            '<i class="mdi mdi-chevron-right item-expand-icon me-1"></i>' + name + "</a>";
    }

    function detailBtnCol(data, type, full) {
        var url = route("unit-inventory.show", full.id_unit);
        return '<a href="' + url + '" class="btn btn-sm btn-outline-primary">Detail</a>';
    }

    // Data master air_cap kadang udah nyimpen satuannya sendiri ("1,15 m³/min"),
    // kadang cuma angka polos ("2,5") — jangan dobel nambahin "m³/min" kalau udah ada.
    function airCapCol(data) {
        if (!data) return "-";
        return /m.?\/min/i.test(data) ? data : data + " m³/min";
    }

    $tables.each(function () {
        var $table = $(this);
        var group = $table.data("group");
        if (!group) return;

        // Sub-tab Compressor & Dryer nampung beberapa jenis unit sekaligus dan
        // butuh kolom spek tambahan buat bedain — sub-tab lain (Filter/Chiller)
        // tetap layout ringkas.
        var stockBadgeCol = function (data, type) {
            if (type !== "display") return data;
            return '<span class="badge bg-label-success">' + data + " unit</span>";
        };
        var hargaJualCol = function (data, type, full) {
            if (type !== "display") return full.harga_jual || 0;
            return priceCell(full.harga_jual);
        };
        var columns, columnDefs, order;

        if (group === "screw") {
            columns = [
                { data: null },
                { data: "unit_category" },
                { data: "lubricant" },
                { data: "power" },
                { data: "air_cap" },
                { data: "stock" },
                { data: null },
                { data: null },
            ];
            columnDefs = [
                { targets: 0, render: unitNameCol },
                { targets: 1, render: function (data) { return categoryLabels[data] || data || "-"; } },
                { targets: 2, render: function (data) { return data || "-"; } },
                { targets: 3, render: function (data) { return data || "-"; } },
                { targets: 4, render: function (data) { return airCapCol(data); } },
                { targets: 5, className: "text-center", render: stockBadgeCol },
                { targets: 6, render: hargaJualCol },
                { targets: 7, orderable: false, searchable: false, render: detailBtnCol },
            ];
            order = [[0, "asc"]];
        } else if (group === "dryer") {
            columns = [
                { data: null },
                { data: "unit_category" },
                { data: "pdp" },
                { data: "air_cap" },
                { data: "stock" },
                { data: null },
                { data: null },
            ];
            columnDefs = [
                { targets: 0, render: unitNameCol },
                { targets: 1, render: function (data) { return categoryLabels[data] || data || "-"; } },
                { targets: 2, render: function (data) { return data || "-"; } },
                { targets: 3, render: function (data) { return airCapCol(data); } },
                { targets: 4, className: "text-center", render: stockBadgeCol },
                { targets: 5, render: hargaJualCol },
                { targets: 6, orderable: false, searchable: false, render: detailBtnCol },
            ];
            order = [[0, "asc"]];
        } else if (group === "filter") {
            columns = [
                { data: null },
                { data: "air_cap" },
                { data: "grade" },
                { data: "connect" },
                { data: "stock" },
                { data: null },
                { data: null },
            ];
            columnDefs = [
                { targets: 0, render: unitNameCol },
                { targets: 1, render: function (data) { return airCapCol(data); } },
                { targets: 2, render: function (data) { return data || "-"; } },
                { targets: 3, render: function (data) { return data || "-"; } },
                { targets: 4, className: "text-center", render: stockBadgeCol },
                { targets: 5, render: hargaJualCol },
                { targets: 6, orderable: false, searchable: false, render: detailBtnCol },
            ];
            order = [[0, "asc"]];
        } else if (group === "chiller") {
            columns = [
                { data: null },
                { data: "capacity" },
                { data: "power" },
                { data: "stock" },
                { data: null },
                { data: null },
            ];
            columnDefs = [
                { targets: 0, render: unitNameCol },
                { targets: 1, render: function (data) { return data || "-"; } },
                { targets: 2, render: function (data) { return data || "-"; } },
                { targets: 3, className: "text-center", render: stockBadgeCol },
                { targets: 4, render: hargaJualCol },
                { targets: 5, orderable: false, searchable: false, render: detailBtnCol },
            ];
            order = [[0, "asc"]];
        } else {
            columns = [
                { data: null },
                { data: "stock" },
                { data: null },
                { data: null },
            ];
            columnDefs = [
                { targets: 0, render: unitNameCol },
                { targets: 1, className: "text-center", render: stockBadgeCol },
                { targets: 2, render: hargaJualCol },
                { targets: 3, orderable: false, searchable: false, render: detailBtnCol },
            ];
            order = [[0, "asc"]];
        }

        var dt = $table.DataTable({
            ajax: {
                type: "GET",
                url: "/db/unit-inventory?group=" + group,
                headers: { "Content-Type": "application/json" },
                dataSrc: function (json) {
                    var rows = json.data || [];
                    var count = rows.reduce(function (sum, r) { return sum + (parseInt(r.stock, 10) || 0); }, 0);
                    $("#unit-inventory-" + group + "-count-badge").text(count).toggleClass("d-none", count === 0);
                    updateTotalBadge(group, count);
                    return rows;
                },
            },
            columns: columns,
            columnDefs: columnDefs,
            order: order,
            orderCellsTop: true,
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { emptyTable: "Belum ada stok unit baru di kategori ini." },
        });

        // Search per kolom — input di baris kedua thead (.column-filters), satu per
        // kolom yang searchable. Klik/ketik di input jangan sampai kepencet ke sorting
        // header (th-nya dobel-baris).
        $table.find("thead tr.column-filters th").each(function (idx) {
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

        $table.on("click", ".item-expand-toggle", function (e) {
            e.preventDefault();
            var $icon = $(this).find(".item-expand-icon");
            var tr = $(this).closest("tr");
            var row = dt.row(tr);
            if (row.child.isShown()) {
                row.child.hide();
                $icon.removeClass("mdi-chevron-down").addClass("mdi-chevron-right");
            } else {
                row.child(itemsChildHtml(row.data().items_detail)).show();
                $icon.removeClass("mdi-chevron-right").addClass("mdi-chevron-down");
            }
        });

        // Tab yang start hidden bikin DataTables ngitung lebar kolom salah pas init —
        // perbaiki begitu tab sub-kategori-nya beneran ditampilkan.
        $('button[data-bs-target="#subtab-' + group + '"]').on("shown.bs.tab", function () {
            dt.columns.adjust().draw(false);
        });
    });
});
