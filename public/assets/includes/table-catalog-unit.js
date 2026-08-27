$(function () {
    function currency(data) {
        if (data === null || data === undefined || data === "") return "-";
        return "Rp " + Number(data).toLocaleString("id-ID");
    }

    function skuCol(data, type, full) {
        if (type !== "display") return data;
        if (!data) return "-";
        var url = route("catalog-unit.show", full.id);
        return '<a href="' + url + '">' + data + "</a>";
    }

    function dash(data) {
        return data || data === 0 ? data : "-";
    }

    var allTables = [];

    function initTable(selector, url, columns, columnDefs, extraAjax) {
        var $table = $(selector);
        if (!$table.length) return null;

        var ajax = Object.assign({
            type: "GET",
            url: url,
            headers: { "Content-Type": "application/json" },
            dataSrc: "data",
        }, extraAjax || {});

        allTables.push($table);
        return $table.DataTable({
            ajax: ajax,
            columns: columns,
            columnDefs: columnDefs,
            order: [[1, "asc"]],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { emptyTable: "Belum ada data katalog." },
        });
    }

    // ── Oil-Injected (bisa difilter per generation lewat tombol) ──
    var currentGen = "";
    var dtOilInjected = initTable(
        "#table-oil-injected",
        "/db/catalog-unit?type_unit=Oil-injected",
        [
            { data: "sku" },
            { data: "brand" },
            { data: "model" },
            { data: "generation" },
            { data: "price_idr" },
            { data: "air_cap" },
            { data: "bar" },
            { data: "power" },
        ],
        [
            { targets: 0, render: skuCol },
            { targets: 3, className: "text-center", render: function (data) { return data === "old" ? "Old Model" : data === "new" ? "New Model" : "-"; } },
            { targets: 4, className: "text-center", render: function (data) { return currency(data); } },
            { targets: [5, 6, 7], className: "text-center", render: dash },
        ]
    );

    $(".btn-gen-filter").on("click", function () {
        if (!dtOilInjected) return;
        $(".btn-gen-filter").removeClass("active");
        $(this).addClass("active");
        currentGen = $(this).data("gen") || "";
        var url = "/db/catalog-unit?type_unit=Oil-injected" + (currentGen ? "&generation=" + currentGen : "");
        dtOilInjected.ajax.url(url).load();
    });

    // ── Oil-Free ──
    initTable(
        "#table-oil-free",
        "/db/catalog-unit?type_unit=" + encodeURIComponent("Oil-free Compressor"),
        [
            { data: "sku" },
            { data: "brand" },
            { data: "model" },
            { data: "price_idr" },
            { data: "air_cap" },
            { data: "bar" },
            { data: "power" },
        ],
        [
            { targets: 0, render: skuCol },
            { targets: 3, className: "text-center", render: function (data) { return currency(data); } },
            { targets: [4, 5, 6], className: "text-center", render: dash },
        ]
    );

    // ── Dryer: Refrigerant ──
    initTable(
        "#table-ref-dryer",
        "/db/catalog-unit?category=" + encodeURIComponent("REFRIGERANT AIR DRYER"),
        [
            { data: "sku" },
            { data: "brand" },
            { data: "model" },
            { data: "price_idr" },
            { data: "air_cap" },
            { data: "voltage" },
            { data: "connect" },
        ],
        [
            { targets: 0, render: skuCol },
            { targets: 3, className: "text-center", render: function (data) { return currency(data); } },
            { targets: [4, 5, 6], className: "text-center", render: dash },
        ]
    );

    // ── Dryer: Desiccant ──
    initTable(
        "#table-desiccant",
        "/db/catalog-unit?category=" + encodeURIComponent("DESICANT DRYER"),
        [
            { data: "sku" },
            { data: "brand" },
            { data: "model" },
            { data: "price_idr" },
            { data: "air_cap" },
            { data: "voltage" },
        ],
        [
            { targets: 0, render: skuCol },
            { targets: 3, className: "text-center", render: function (data) { return currency(data); } },
            { targets: [4, 5], className: "text-center", render: dash },
        ]
    );

    // ── Filtration System ──
    initTable(
        "#table-filtration",
        "/db/catalog-unit?category=" + encodeURIComponent("FILTRATION SYSTEM"),
        [
            { data: "sku" },
            { data: "brand" },
            { data: "model" },
            { data: "price_idr" },
            { data: "connect" },
            { data: "filtration" },
            { data: "oil_content" },
            { data: "grade" },
        ],
        [
            { targets: 0, render: skuCol },
            { targets: 3, className: "text-center", render: function (data) { return currency(data); } },
            { targets: [4, 5, 6, 7], className: "text-center", render: dash },
        ]
    );

    // ── Air Receiver Tank ──
    initTable(
        "#table-tank",
        "/db/catalog-unit?category=" + encodeURIComponent("AIR RECEIVER TANK"),
        [
            { data: "sku" },
            { data: "brand" },
            { data: "model" },
            { data: "price_idr" },
            { data: "capacity" },
            { data: "bar" },
            { data: "type_unit" },
        ],
        [
            { targets: 0, render: skuCol },
            { targets: 3, className: "text-center", render: function (data) { return currency(data); } },
            { targets: [4, 5, 6], className: "text-center", render: dash },
        ]
    );

    // Tab/sub-tab yang start hidden bikin DataTables ngitung lebar kolom salah pas
    // init — perbaiki begitu tab/pill-nya beneran ditampilkan.
    $('[data-bs-toggle="tab"], [data-bs-toggle="pill"]').on("shown.bs.tab", function () {
        allTables.forEach(function ($table) {
            if ($table.is(":visible")) {
                $table.DataTable().columns.adjust().draw(false);
            }
        });
    });
});
