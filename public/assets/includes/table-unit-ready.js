$(function () {

    var BASE_URL = '/db/unit/ready';

    function brandCol(full) {
        return '<a href="javascript:void(0);" class="text-primary fw-bold btn-view-spec" data-id="' + full.id + '">' +
            (full.brand || '-') + '</a>';
    }

    var SPEC_LABELS = [
        { key: 'brand', label: 'Brand' },
        { key: 'model', label: 'Model' },
        { key: 'serial_number', label: 'Serial Number' },
        { key: 'power', label: 'Motor Power' },
        { key: 'air_cap', label: 'Air Capacity', suffix: ' m³/min' },
        { key: 'bar', label: 'Pressure', suffix: ' Bar' },
        { key: 'voltage', label: 'Voltage' },
        { key: 'exhaust', label: 'Connection' },
        { key: 'filtration', label: 'Filtration' },
        { key: 'oil_content', label: 'Oil Content' },
        { key: 'grade', label: 'Grade' },
        { key: 'capacity', label: 'Capacity', suffix: ' Liter' },
        { key: 'type_unit', label: 'Type' },
        { key: 'dimension', label: 'Dimensi' },
        { key: 'weight', label: 'Berat', suffix: ' kg' },
    ];

    function formatRupiah(n) {
        return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function priceCol(full) {
        return full.harga_jual ? formatRupiah(full.harga_jual) : '<span class="text-muted">Belum diset</span>';
    }

    $(document).on('click', '.btn-view-spec', function () {
        var id = $(this).data('id');
        var table = $.fn.dataTable.Api($(this).closest('table'));
        var full = table.row($(this).closest('tr')).data();
        if (!full) return;

        var rows = '';
        SPEC_LABELS.forEach(function (f) {
            if (full[f.key] === null || full[f.key] === undefined || full[f.key] === '') return;
            rows += '<div class="col-5 text-muted py-1">' + f.label + '</div>' +
                '<div class="col-7 py-1 fw-semibold">' + full[f.key] + (f.suffix || '') + '</div>';
        });
        rows += '<div class="col-5 text-muted py-1">Kondisi</div><div class="col-7 py-1">' + kondisiCol(full) + '</div>';
        rows += '<div class="col-5 text-muted py-1">Status</div><div class="col-7 py-1">' + statusCol(full) + '</div>';
        rows += '<div class="col-5 text-muted py-1">Harga Jual</div><div class="col-7 py-1 fw-semibold">' + priceCol(full) + '</div>';

        $('#unitSpecModalTitle').text((full.brand || 'Unit') + (full.model ? ' ' + full.model : ''));
        $('#unitSpecModalBody').html(rows);
        var modal = new bootstrap.Modal(document.getElementById('unitSpecModal'));
        modal.show();
    });

    function kondisiCol(full) {
        return full.kondisi === 'Baru'
            ? '<span class="badge bg-label-info">Unit Baru</span>'
            : '<span class="badge bg-label-secondary">Unit Second</span>';
    }

    function statusCol(full) {
        var map = {
            OK: '<span class="badge bg-label-success">OK</span>',
            Service: '<span class="badge bg-label-warning">Sedang Service</span>',
            Rental: '<span class="badge bg-label-primary">Sedang Rental</span>',
            Sold: '<span class="badge bg-label-dark">Sold</span>',
        };
        return map[full.status_unit] || '<span class="badge bg-label-success">OK</span>';
    }

    function snDef(target) {
        return { targets: target, className: 'text-center', render: function (d) { return d || '-'; } };
    }

    function kondisiDef(target) {
        return { targets: target, className: 'text-center', render: function (d, t, f) { return t !== 'display' ? d : kondisiCol(f); } };
    }

    function statusDef(target) {
        return { targets: target, className: 'text-center', render: function (d, t, f) { return t !== 'display' ? d : statusCol(f); } };
    }

    function priceDef(target) {
        return { targets: target, className: 'text-center', render: function (d, t, f) { return t !== 'display' ? d : priceCol(f); } };
    }

    function makeTable(selector, urlParams, columns, columnDefs, order) {
        var $t = $(selector);
        if (!$t.length) return null;

        var url = BASE_URL + (urlParams ? '?' + $.param(urlParams) : '');

        var dt = $t.DataTable({
            ajax: { type: 'GET', url: url, headers: { 'Content-Type': 'application/json' } },
            columns: columns,
            columnDefs: columnDefs,
            order: order || [[0, 'asc']],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50],
            pageLength: 25,
            language: { emptyTable: 'Belum ada unit yang siap ditawarkan di kategori ini.' },
        });

        return dt;
    }

    // Kolom umum: 0=id (dipakai render Brand), 1=model, sisanya spek, lalu SN/Kondisi/Status di paling kanan.

    var OIL_INJECTED_COLS = [
        { data: 'id' }, { data: 'model' },
        { data: 'air_cap' }, { data: 'bar' }, { data: 'power' },
        { data: 'serial_number' }, { data: 'kondisi' }, { data: 'status_unit' }, { data: 'harga_jual' },
    ];
    var OIL_INJECTED_DEFS = [
        { targets: 0, render: function (d, t, f) { return t !== 'display' ? d : brandCol(f); } },
        { targets: 1, render: function (d) { return d || '-'; } },
        { targets: 2, className: 'text-center', render: function (d) { return d ? d + ' m³/min' : '-'; } },
        { targets: 3, className: 'text-center', render: function (d) { return d ? d + ' Bar' : '-'; } },
        { targets: 4, className: 'text-center', render: function (d) { return d || '-'; } },
        snDef(5), kondisiDef(6), statusDef(7), priceDef(8),
    ];

    var OIL_FREE_COLS = [
        { data: 'id' }, { data: 'model' },
        { data: 'air_cap' }, { data: 'bar' }, { data: 'power' },
        { data: 'serial_number' }, { data: 'kondisi' }, { data: 'status_unit' }, { data: 'harga_jual' },
    ];
    var OIL_FREE_DEFS = [
        { targets: 0, render: function (d, t, f) { return t !== 'display' ? d : brandCol(f); } },
        { targets: 1, render: function (d) { return d || '-'; } },
        { targets: 2, className: 'text-center', render: function (d) { return d ? d + ' m³/min' : '-'; } },
        { targets: 3, className: 'text-center', render: function (d) { return d ? d + ' Bar' : '-'; } },
        { targets: 4, className: 'text-center', render: function (d) { return d || '-'; } },
        snDef(5), kondisiDef(6), statusDef(7), priceDef(8),
    ];

    var REF_DRYER_COLS = [
        { data: 'id' }, { data: 'model' },
        { data: 'air_cap' }, { data: 'voltage' }, { data: 'exhaust' },
        { data: 'serial_number' }, { data: 'kondisi' }, { data: 'status_unit' }, { data: 'harga_jual' },
    ];
    var REF_DRYER_DEFS = [
        { targets: 0, render: function (d, t, f) { return t !== 'display' ? d : brandCol(f); } },
        { targets: 1, render: function (d) { return d || '-'; } },
        { targets: 2, className: 'text-center', render: function (d) { return d ? d + ' m³/min' : '-'; } },
        { targets: [3, 4], className: 'text-center', render: function (d) { return d || '-'; } },
        snDef(5), kondisiDef(6), statusDef(7), priceDef(8),
    ];

    var DESICCANT_COLS = [
        { data: 'id' }, { data: 'model' },
        { data: 'air_cap' }, { data: 'voltage' },
        { data: 'serial_number' }, { data: 'kondisi' }, { data: 'status_unit' }, { data: 'harga_jual' },
    ];
    var DESICCANT_DEFS = [
        { targets: 0, render: function (d, t, f) { return t !== 'display' ? d : brandCol(f); } },
        { targets: 1, render: function (d) { return d || '-'; } },
        { targets: 2, className: 'text-center', render: function (d) { return d ? d + ' m³/min' : '-'; } },
        { targets: 3, className: 'text-center', render: function (d) { return d || '-'; } },
        snDef(4), kondisiDef(5), statusDef(6), priceDef(7),
    ];

    var FILTRATION_COLS = [
        { data: 'id' }, { data: 'model' },
        { data: 'exhaust' }, { data: 'filtration' }, { data: 'oil_content' }, { data: 'grade' },
        { data: 'serial_number' }, { data: 'kondisi' }, { data: 'status_unit' }, { data: 'harga_jual' },
    ];
    var FILTRATION_DEFS = [
        { targets: 0, render: function (d, t, f) { return t !== 'display' ? d : brandCol(f); } },
        { targets: 1, render: function (d) { return d || '-'; } },
        { targets: [2, 3, 4, 5], className: 'text-center', render: function (d) { return d || '-'; } },
        snDef(6), kondisiDef(7), statusDef(8), priceDef(9),
    ];

    var TANK_COLS = [
        { data: 'id' }, { data: 'model' },
        { data: 'capacity' }, { data: 'bar' }, { data: 'type_unit' },
        { data: 'serial_number' }, { data: 'kondisi' }, { data: 'status_unit' }, { data: 'harga_jual' },
    ];
    var TANK_DEFS = [
        { targets: 0, render: function (d, t, f) { return t !== 'display' ? d : brandCol(f); } },
        { targets: 1, render: function (d) { return d || '-'; } },
        { targets: 2, className: 'text-center', render: function (d) { return d ? d + ' Liter' : '-'; } },
        { targets: 3, className: 'text-center', render: function (d) { return d ? d + ' Bar' : '-'; } },
        { targets: 4, className: 'text-center', render: function (d) { return d || '-'; } },
        snDef(5), kondisiDef(6), statusDef(7), priceDef(8),
    ];

    // ── Init Oil-Injected (default tab — loads on page ready) ─────────────
    var dtOilInjected = makeTable(
        '#table-oil-injected',
        { category: 'AIR COMPRESSOR SCREW', type_unit: 'Oil-injected' },
        OIL_INJECTED_COLS, OIL_INJECTED_DEFS, [[1, 'asc']]
    );

    var dtOilFree = null;
    $('button[data-bs-target="#tab-oil-free"]').on('shown.bs.tab', function () {
        if (dtOilFree) return;
        dtOilFree = makeTable(
            '#table-oil-free',
            { category: 'AIR COMPRESSOR SCREW', type_unit: 'Oil-free Compressor' },
            OIL_FREE_COLS, OIL_FREE_DEFS, [[1, 'asc']]
        );
    });

    var dtRefDryer = null;
    $('button[data-bs-target="#tab-main-dryer"]').on('shown.bs.tab', function () {
        if (dtRefDryer) return;
        dtRefDryer = makeTable(
            '#table-ref-dryer',
            { category: 'REFRIGERANT AIR DRYER' },
            REF_DRYER_COLS, REF_DRYER_DEFS, [[1, 'asc']]
        );
    });

    var dtDesiccant = null;
    $('button[data-bs-target="#tab-desiccant"]').on('shown.bs.tab', function () {
        if (dtDesiccant) return;
        dtDesiccant = makeTable(
            '#table-desiccant',
            { category: 'DESICANT DRYER' },
            DESICCANT_COLS, DESICCANT_DEFS, [[1, 'asc']]
        );
    });

    var dtFiltration = null;
    $('button[data-bs-target="#tab-main-filtration"]').on('shown.bs.tab', function () {
        if (dtFiltration) return;
        dtFiltration = makeTable(
            '#table-filtration',
            { category: 'FILTRATION SYSTEM' },
            FILTRATION_COLS, FILTRATION_DEFS, [[1, 'asc']]
        );
    });

    var dtTank = null;
    $('button[data-bs-target="#tab-main-tank"]').on('shown.bs.tab', function () {
        if (dtTank) return;
        dtTank = makeTable(
            '#table-tank',
            { category: 'AIR RECEIVER TANK' },
            TANK_COLS, TANK_DEFS, [[1, 'asc']]
        );
    });

});
