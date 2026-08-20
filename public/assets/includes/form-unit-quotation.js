$(function () {

    var rowIndex = 0;

    // Textarea deskripsi custom-row rows="2" fixed — template PM (mis. Scope of Work
    // PM4) bisa puluhan baris, jadi harus auto-expand supaya gak keliatan "kepotong".
    function autoResizeDescTextarea(el) {
        if (!el) return;
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }

    // ── Spec labels per field ─────────────────────────────────────────────
    var SPEC_LABELS = {
        brand:            'Brand',
        model:            'Model',
        capacity:         'Capacity',
        type_unit:        'Type',
        bar:              'Max Pressure',
        test_pressure:    'Test Pressure',
        air_cap:          'Air Capacity',
        power:            'Motor Power',
        voltage:          'Voltage',
        connect:          'Drive',
        exhaust:          'Connection',
        refrigerant_type: 'Refrigerant Type',
        pdp:              'PDP',
        filtration:       'Filtration',
        oil_content:      'Oil Content',
        material:         'Material',
        inlet_pressure:   'Inlet Pressure',
        outlet_pressure:  'Outlet Pressure',
        inlet_cap:        'Inlet Capacity (LP)',
        outlet_cap:       'Outlet Capacity (HP)',
        grade:            'Grade',
        dimension:        'Dimension',
        weight:           'Weight',
        cooling:          'Cooling Method',
    };

    var SPEC_UNITS = {
        bar:            ' Bar',
        air_cap:        ' m³/min',
        filtration:     ' µm',
        oil_content:    ' ppm',
        test_pressure:  ' Bar',
        inlet_pressure: ' Bar',
        outlet_pressure:' Bar',
        inlet_cap:      ' m³/min',
        outlet_cap:     ' m³/min',
        weight:         ' Kg',
        capacity:       ' Liter',
    };

    // ── Format Rupiah ─────────────────────────────────────────────────────
    function formatRupiah(n) {
        n = String(n).replace(/\D/g, '');
        return n.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function parseRupiah(str) {
        return parseFloat(String(str).replace(/\./g, '')) || 0;
    }

    // ── Update amount per row ─────────────────────────────────────────────
    function updateRowAmount($row) {
        var qty   = parseFloat($row.find('.field-qty').val()) || 0;
        var price = parseRupiah($row.find('.field-price').val());
        var disc  = parseFloat($row.find('.field-disc').val()) || 0;
        var amount = qty * price * (1 - disc / 100);
        $row.find('.field-amount').text('Rp ' + formatRupiah(Math.round(amount)));
        $row.find('.field-price-raw').val(price);
        recalcSummary();
    }

    // ── Recalc subtotal / tax / total ─────────────────────────────────────
    function recalcSummary() {
        var subtotal = 0;
        $('.unit-row').each(function () {
            var qty   = parseFloat($(this).find('.field-qty').val()) || 0;
            var price = parseRupiah($(this).find('.field-price').val());
            var disc  = parseFloat($(this).find('.field-disc').val()) || 0;
            subtotal += qty * price * (1 - disc / 100);
        });

        var diskonType  = $('#select-diskon-type').val() || 'percent';
        var diskon      = diskonType === 'amount'
            ? parseRupiah($('#input-diskon').val())
            : (parseFloat($('#input-diskon').val()) || 0);
        var afterDiskon = diskonType === 'amount' ? (subtotal - diskon) : (subtotal - (subtotal * diskon / 100));
        var tax         = $('#toggle-tax').is(':checked');
        var taxAmount   = tax ? Math.round(afterDiskon * 0.11) : 0;
        var shipping    = parseRupiah($('#input-shipping').val());
        var total       = afterDiskon + taxAmount + shipping;

        $('#display-subtotal').text('Rp ' + formatRupiah(Math.round(subtotal)));
        $('#display-tax').text('Rp ' + formatRupiah(taxAmount));
        $('#display-total').text('Rp ' + formatRupiah(Math.round(total)));

        toggleEmptyState();
    }

    function toggleEmptyState() {
        var count = $('.unit-row').length;
        $('#empty-state').toggle(count === 0);
        $('#items-count-badge').text(count + (count === 1 ? ' Item' : ' Items'));
    }

    // ── Category-specific label overrides ────────────────────────────────
    var SPEC_LABELS_OVERRIDE = {
        'AIR RECEIVER TANK': {
            bar:     'Max. Pressure',
            grade:   'T Plate',
            cooling: 'Certification',
        },
        'FILTRATION SYSTEM': {
            air_cap:  'Flowrate',
            material: 'Element',
            connect:  'Drain',
        },
    };

    // ── Category-specific field order ─────────────────────────────────────
    var SPEC_ORDER = {
        'FILTRATION SYSTEM': ['brand', 'model', 'grade', 'air_cap', 'bar', 'filtration', 'oil_content', 'material', 'exhaust', 'connect'],
    };

    // ── Build spec preview rows (all specs) ───────────────────────────────
    function buildSpecPreview($row, unit) {
        var $specRows = $row.find('.spec-rows').empty();
        $row.find('.spec-preview').show();
        var visible = [];
        var catOverrides = SPEC_LABELS_OVERRIDE[unit.unit] || {};
        var fieldList    = SPEC_ORDER[unit.unit] || Object.keys(SPEC_LABELS);

        $.each(fieldList, function (i, field) {
            var val = unit[field];
            if (!val || val === '' || val === '0' || val === 0) return;

            var displayLabel = catOverrides[field] || SPEC_LABELS[field] || field;
            var displayVal = val + (SPEC_UNITS[field] || '');
            var $line = $('<div class="d-flex align-items-center spec-line py-0" data-field="' + field + '" ' +
                'style="font-size:12px; padding:2px 0;">' +
                '<span class="text-muted" style="min-width:110px; font-size:11px;">' + displayLabel + '</span>' +
                '<span style="font-size:11px;">: ' + displayVal + '</span>' +
                '<button type="button" class="btn btn-xs btn-icon ms-auto btn-remove-spec text-danger" ' +
                    'style="line-height:1; padding:0 4px; font-size:11px;" title="Sembunyikan">' +
                    '&times;</button>' +
                '</div>');
            $specRows.append($line);
            visible.push(field);
        });

        $row.find('.field-spec-visible').val(JSON.stringify(visible));
    }

    // ── Build spec preview (only from saved visible array) ───────────────
    function buildSpecPreviewFiltered($row, unit, visibleFields) {
        if (!unit || !visibleFields || visibleFields.length === 0) return;
        var $specRows = $row.find('.spec-rows').empty();
        $row.find('.spec-preview').show();
        var catOverrides = SPEC_LABELS_OVERRIDE[unit.unit] || {};

        $.each(visibleFields, function (i, field) {
            var val = unit[field];
            if (!val) return;
            var label      = catOverrides[field] || SPEC_LABELS[field] || field;
            var displayVal = val + (SPEC_UNITS[field] || '');
            var $line = $('<div class="d-flex align-items-center spec-line py-0" data-field="' + field + '" ' +
                'style="font-size:12px; padding:2px 0;">' +
                '<span class="text-muted" style="min-width:110px; font-size:11px;">' + label + '</span>' +
                '<span style="font-size:11px;">: ' + displayVal + '</span>' +
                '<button type="button" class="btn btn-xs btn-icon ms-auto btn-remove-spec text-danger" ' +
                    'style="line-height:1; padding:0 4px; font-size:11px;" title="Sembunyikan">' +
                    '&times;</button>' +
                '</div>');
            $specRows.append($line);
        });

        $row.find('.field-spec-visible').val(JSON.stringify(visibleFields));
    }

    // ── Add unit row ──────────────────────────────────────────────────────
    function addUnitRow() {
        var idx  = rowIndex++;
        var html = $('#tmpl-unit-row').html().replace(/__IDX__/g, idx);
        var $row = $(html);
        $('#line-items-container').append($row);
        toggleEmptyState();
        initUnitRowSelect2($row);
        initFixedAssetRowSelect2($row);
        initEquivalentRowSelect2($row);
        bindUnitSourceToggle($row);
        bindRowEvents($row);

        $row.find('.unit-source-radio:checked').trigger('change');
    }

    // ── Add unit row pre-populated from edit data ─────────────────────────
    function addUnitRowFromData(item) {
        var idx  = rowIndex++;
        var html = $('#tmpl-unit-row').html().replace(/__IDX__/g, idx);
        var $row = $(html);
        $('#line-items-container').append($row);
        toggleEmptyState();

        var $sel = initUnitRowSelect2($row);
        var $selFixedAsset = initFixedAssetRowSelect2($row);
        var $selEquivalent = initEquivalentRowSelect2($row);
        bindUnitSourceToggle($row);

        if (item.id_equivalent && item.equivalent) {
            $row.find('.unit-source-radio[value="sparepart"]').prop('checked', true);
            $row.find('.unit-source-catalog').hide();
            $row.find('.unit-source-fixed-asset').hide();
            $row.find('.unit-source-equivalent').show();

            var eq = item.equivalent;
            var eqText = equivalentBrandPn(eq);
            var eqOption = new Option(eqText, item.id_equivalent, true, true);
            $selEquivalent.empty().append(eqOption).trigger('change.select2');

            $row.find('.field-id-equivalent').val(item.id_equivalent);
        } else if (item.id_fixed_asset && item.fixed_asset) {
            // Sumbernya Unit Second (Fixed Asset) atau Rental — tampilkan blok pencarian itu
            var isRental = (item.info_qty === 'Days' || item.info_qty === 'Hari');
            var sourceVal = isRental ? 'rental' : 'fixed_asset';
            $row.find('.unit-source-radio[value="' + sourceVal + '"]').prop('checked', true);
            $row.find('.unit-source-catalog').hide();
            $row.find('.unit-source-fixed-asset').show();

            var fa = item.fixed_asset;
            var sn = fa.serial_number ? (' — SN: ' + fa.serial_number) : '';
            var faText = (item.unit ? (item.unit.brand || '') + ' — ' + (item.unit.model || item.unit.sku || '') : '') +
                sn + ' (' + fa.code + ')';
            var faOption = new Option(faText, item.id_fixed_asset, true, true);
            $selFixedAsset.empty().append(faOption).trigger('change.select2');

            $row.find('.field-id-unit').val(item.id_unit);
            $row.find('.field-id-fixed-asset').val(item.id_fixed_asset);
            if (item.unit) buildSpecPreviewFiltered($row, item.unit, item.spec_visible);
        } else if (item.id_unit && item.unit) {
            // Pre-select unit in Select2 (Catalog Unit)
            var unitText = (item.unit.brand || '') + ' — ' + (item.unit.model || item.unit.sku || '');
            var option   = new Option(unitText, item.id_unit, true, true);
            $sel.empty().append(option).trigger('change.select2');

            $row.find('.field-id-unit').val(item.id_unit);
            buildSpecPreviewFiltered($row, item.unit, item.spec_visible);
        }

        $row.find('.field-label').val(item.label || '');
        $row.find('.field-qty').val(item.qty || 1);
        $row.find('.field-info-qty').val(item.info_qty || 'Unit');
        $row.find('.field-price').val(formatRupiah(Math.round(item.price || 0)));
        $row.find('.field-disc').val(item.disc || 0);
        updateRowAmount($row);

        bindRowEvents($row);
    }

    // ── Add custom row ────────────────────────────────────────────────────
    function addCustomRow() {
        var idx  = rowIndex++;
        var html = $('#tmpl-custom-row').html().replace(/__IDX__/g, idx);
        var $row = $(html);
        $('#line-items-container').append($row);
        toggleEmptyState();
        bindRowEvents($row);
    }

    // ── Add custom row pre-populated from edit data ───────────────────────
    function addCustomRowFromData(item) {
        var idx  = rowIndex++;
        var html = $('#tmpl-custom-row').html().replace(/__IDX__/g, idx);
        var $row = $(html);
        $('#line-items-container').append($row);
        toggleEmptyState();

        $row.find('input[name*="[label]"]').val(item.label || '');
        $row.find('textarea[name*="[description]"], input[name*="[description]"]').val(item.description || '');
        autoResizeDescTextarea($row.find('.field-description')[0]);
        $row.find('.field-qty').val(item.qty || 1);

        var $infoQty = $row.find('select[name*="[info_qty]"]');
        if (item.info_qty) {
            $infoQty.val(item.info_qty);
            if ($infoQty.val() === null) {
                $infoQty.append(new Option(item.info_qty, item.info_qty, true, true));
            }
        }

        $row.find('.field-price').val(formatRupiah(Math.round(item.price || 0)));
        $row.find('.field-disc').val(item.disc || 0);
        updateRowAmount($row);

        bindRowEvents($row);
    }

    // ── Add header row ────────────────────────────────────────────────────
    function getNextHeaderPrefix() {
        var headerCount = $('#line-items-container .unit-row[data-type="header"]').length;
        var letter = String.fromCharCode(65 + (headerCount % 26));
        return letter + '. ';
    }

    function addHeaderRow() {
        var idx    = rowIndex++;
        var prefix = getNextHeaderPrefix();
        var html   = $('#tmpl-header-row').html().replace(/__IDX__/g, idx);
        var $row   = $(html);
        $row.find('.field-label').val(prefix);
        $('#line-items-container').append($row);
        toggleEmptyState();
        bindRowEvents($row);
    }

    function addHeaderRowFromData(item) {
        var idx  = rowIndex++;
        var html = $('#tmpl-header-row').html().replace(/__IDX__/g, idx);
        var $row = $(html);
        $('#line-items-container').append($row);
        toggleEmptyState();
        $row.find('.field-label').val(item.label || '');
        bindRowEvents($row);
    }

    // ── Add transport row — pakai layout Custom Item, ditambah dropdown Tipe + Kota (master Transportation) ──
    var TRANSPORT_PRICES = window.TRANSPORT_PRICES || [];

    // Tipe kunjungan menentukan teks judul yang tampil di quotation; harga tetap ambil dari master per Kota.
    var TRANSPORT_TYPES = [
        { label: 'General Check',      text: 'Inspection & Transportation Cost' },
        { label: 'Service',            text: 'Service & Transportation Cost' },
        { label: 'Pengecekan Piping',  text: 'Piping Inspection & Transportation Cost' },
        { label: 'Survey Project',     text: 'Site Survey & Transportation Cost' },
    ];

    function transportTypeOptionsHtml(selectedText) {
        var html = '<option value="">-- Pilih Tipe Transportation --</option>';
        $.each(TRANSPORT_TYPES, function (i, t) {
            var sel = (selectedText && selectedText === t.text) ? ' selected' : '';
            html += '<option value="' + t.text + '"' + sel + '>' + t.label + '</option>';
        });
        return html;
    }

    function transportCityOptionsHtml(selectedCity) {
        var html = '<option value="">-- Pilih Kota Transportasi --</option>';
        $.each(TRANSPORT_PRICES, function (i, tp) {
            var sel = (selectedCity && selectedCity === tp.city) ? ' selected' : '';
            html += '<option value="' + tp.city + '" data-price="' + tp.price + '"' + sel + '>' + tp.city + '</option>';
        });
        return html;
    }

    function bindTransportRowEvents($row) {
        $row.find('.field-transport-type').on('change', function () {
            $row.find('.field-label').val($(this).val() || '');
            updateRowAmount($row);
        });
        $row.find('.field-transport-city').on('change', function () {
            var price = $(this).find('option:selected').data('price') || 0;
            $row.find('.field-price').val(formatRupiah(Math.round(price)));
            updateRowAmount($row);
        });
    }

    function buildTransportRow(idx, selectedTypeText, selectedCity) {
        var html = $('#tmpl-custom-row').html().replace(/__IDX__/g, idx);
        var $row = $(html);
        $row.attr('data-type', 'transport');
        $row.find('input[name*="[type]"]').val('transport');

        var selectsHtml =
            '<div class="mb-2"><select class="form-select form-select-sm field-transport-type">' + transportTypeOptionsHtml(selectedTypeText) + '</select></div>' +
            '<div class="mb-2"><select class="form-select form-select-sm field-transport-city">' + transportCityOptionsHtml(selectedCity) + '</select></div>';
        $row.find('.field-label').before(selectsHtml);
        $row.find('.field-label').prop('readonly', true).addClass('bg-light').val(selectedTypeText || '');
        $row.find('.field-description').closest('div').hide();

        return $row;
    }

    function addTransportRow() {
        var idx  = rowIndex++;
        var $row = buildTransportRow(idx, null, null);
        $('#line-items-container').append($row);
        toggleEmptyState();
        bindRowEvents($row);
        bindTransportRowEvents($row);
    }

    function addTransportRowFromData(item) {
        var idx = rowIndex++;
        var matchedType = null;
        $.each(TRANSPORT_TYPES, function (i, t) {
            if (t.text === item.label) { matchedType = t.text; return false; }
        });
        var $row = buildTransportRow(idx, matchedType, null);
        $('#line-items-container').append($row);
        toggleEmptyState();

        $row.find('.field-label').val(item.label || '');
        $row.find('.field-qty').val(item.qty || 1);
        $row.find('.field-price').val(formatRupiah(Math.round(item.price || 0)));
        $row.find('.field-disc').val(item.disc || 0);
        updateRowAmount($row);

        bindRowEvents($row);
        bindTransportRowEvents($row);
    }

    // ── Init Select2 AJAX for unit row ────────────────────────────────────
    function initUnitRowSelect2($row) {
        var $sel = $row.find('.select2-unit-search');
        $sel.select2({
            placeholder: 'Search unit (SKU / Brand / Model)...',
            minimumInputLength: 1,
            templateResult: function (item) {
                if (!item.id) return item.text;
                var u     = item.unit;
                var price = u && u.price && parseFloat(u.price) > 0
                    ? ' <span style="color:#696cff;font-size:10px;">Rp ' + formatRupiah(Math.round(u.price)) + '</span>'
                    : '';
                return $('<span>' + (item.label || item.text) + price + '</span>');
            },
            templateSelection: function (item) {
                return item.text;
            },
            ajax: {
                url: '/db/catalog/search',
                dataType: 'json',
                delay: 300,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (u) {
                            return {
                                id:    u.id,
                                text:  (u.brand || '') + ' — ' + (u.model || u.sku || ''),
                                label: (u.brand || '') + ' — ' + (u.model || u.sku || ''),
                                unit:  u,
                            };
                        })
                    };
                },
            },
        });

        $sel.on('select2:select', function (e) {
            var unit = e.params.data.unit;
            $row.find('.field-id-unit').val(unit.id);
            $row.find('.field-id-fixed-asset').val('');

            // Auto-fill title: brand + model + short desc from unit global
            var desc = (unit.desc && unit.desc !== '-') ? ' ' + unit.desc : '';
            var prefix = (unit.unit === 'AIR RECEIVER TANK') ? 'AIR RECEIVER TANK ' : '';
            $row.find('.field-label').val(prefix + (unit.brand || '') + ' ' + (unit.model || unit.sku || '') + desc);

            // Auto-fill price from catalog (latest price_idr)
            if (unit.price && parseFloat(unit.price) > 0) {
                $row.find('.field-price').val(formatRupiah(Math.round(unit.price)));
            }

            buildSpecPreview($row, unit);
            updateRowAmount($row);
        });

        return $sel;
    }

    // ── Init Select2 AJAX for unit row — sumber Fixed Asset (Unit Second) ──
    function initFixedAssetRowSelect2($row) {
        var $sel = $row.find('.select2-fixed-asset-search');
        $sel.select2({
            placeholder: 'Search Unit Second (SKU / Brand / Serial Number)...',
            minimumInputLength: 1,
            templateResult: function (item) {
                if (!item.id) return item.text;
                var u     = item.unit;
                var price = u && u.price && parseFloat(u.price) > 0
                    ? ' <span style="color:#696cff;font-size:10px;">Rp ' + formatRupiah(Math.round(u.price)) + '</span>'
                    : '';
                return $('<span>' + (item.label || item.text) + price + '</span>');
            },
            templateSelection: function (item) {
                return item.text;
            },
            ajax: {
                url: '/db/fixed-asset/search',
                dataType: 'json',
                delay: 300,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (u) {
                            var sn = u.serial_number ? (' — SN: ' + u.serial_number) : '';
                            var text = (u.brand || '') + ' — ' + (u.model || u.sku || '') + sn + ' (' + u.code + ')';
                            return {
                                id:    u.id_fixed_asset,
                                text:  text,
                                label: text,
                                unit:  u,
                            };
                        })
                    };
                },
            },
        });

        $sel.on('select2:select', function (e) {
            var unit   = e.params.data.unit;
            var source = $row.find('.unit-source-radio:checked').val();
            $row.find('.field-id-unit').val(unit.id);
            $row.find('.field-id-fixed-asset').val(unit.id_fixed_asset);

            var brand = unit.brand || '';
            var model = unit.model || unit.sku || '';
            var label = source === 'rental'
                ? 'RENTAL SCREW AIR COMPRESSOR ' + brand + ' ' + model
                : 'SCREW AIR COMPRESSOR ' + brand + ' ' + model + ' SECOND';
            $row.find('.field-label').val(label);

            // Auto-fill price from catalog kalau spek-nya sudah punya harga (bisa diedit manual)
            if (unit.price && parseFloat(unit.price) > 0) {
                $row.find('.field-price').val(formatRupiah(Math.round(unit.price)));
            }

            buildSpecPreview($row, unit);
            updateRowAmount($row);
        });

        return $sel;
    }

    // ── Helpers: badge + text builder buat hasil Spare Part / Equivalent ──
    function equivalentBrandPn(eq) {
        var brand = eq.brand || '';
        var pn    = eq.pn || eq.fxp_parts || '';
        if (!brand && !pn) return eq.product_name || eq.product_desc || 'Spare Part';
        return brand + (brand && pn ? ' — ' : '') + pn;
    }

    function equivalentBadgeHtml(eq) {
        if (eq.genuine_status === 'Genuine') {
            return '<span class="badge bg-label-success" style="font-size:10px;">Genuine</span> ';
        }
        if (eq.genuine_status === 'Replacement') {
            return '<span class="badge bg-label-warning" style="font-size:10px;">Replacement</span> ';
        }
        return '';
    }

    // ── Init Select2 AJAX for unit row — sumber Spare Part (Equivalent) ────
    function initEquivalentRowSelect2($row) {
        var $sel = $row.find('.select2-equivalent-search');
        $sel.select2({
            placeholder: 'Search Spare Part / Equivalent (PN / Brand / Name)...',
            minimumInputLength: 1,
            width: '100%',
            templateResult: function (item) {
                if (!item.id) return item.text;
                var eq    = item.equivalent;
                var badge = eq ? equivalentBadgeHtml(eq) : '';
                var price = eq && eq.price && parseFloat(eq.price) > 0
                    ? ' <span style="color:#696cff;font-size:10px;">Rp ' + formatRupiah(Math.round(eq.price)) + '</span>'
                    : '';
                return $('<span>' + badge + (item.label || item.text) + price + '</span>');
            },
            templateSelection: function (item) {
                return item.text;
            },
            ajax: {
                url: '/db/equivalent/search',
                dataType: 'json',
                delay: 300,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) {
                    var items = Array.isArray(data) ? data : (data.data || []);
                    return {
                        results: $.map(items, function (eq) {
                            var text = equivalentBrandPn(eq);
                            return {
                                id:         eq.id_equivalent || eq.id,
                                text:       text,
                                label:      text,
                                equivalent: eq,
                            };
                        })
                    };
                },
            },
        });

        $sel.on('select2:select', function (e) {
            var eq = e.params.data.equivalent;
            $row.find('.field-id-unit').val('');
            $row.find('.field-id-fixed-asset').val('');
            $row.find('.field-id-equivalent').val(eq.id_equivalent);

            $row.find('.field-label').val(eq.product_desc || equivalentBrandPn(eq));
            $row.find('.field-info-qty').val(eq.product_unit || 'Pcs');

            if (eq.price && parseFloat(eq.price) > 0) {
                $row.find('.field-price').val(formatRupiah(Math.round(eq.price)));
            }

            $row.find('.stock-bdg').text(eq.stock || 0);
            $row.find('.stock-bks').text(eq.warehouse_stock || 0);
            $row.find('.stock-pending').text(eq.pending_stock || 0);
            $row.find('.equivalent-stock-preview').show();

            $row.find('.spec-preview').hide();
            updateRowAmount($row);
        });

        return $sel;
    }

    // ── Toggle sumber unit (Catalog Unit / Unit Second / Rental / Spare Part) ─
    function bindUnitSourceToggle($row) {
        $row.find('.unit-source-radio').on('change', function () {
            var source = $row.find('.unit-source-radio:checked').val();
            $row.find('.unit-source-catalog').hide();
            $row.find('.unit-source-fixed-asset').hide();
            $row.find('.unit-source-equivalent').hide();
            $row.find('.equivalent-stock-preview').hide();

            if (source === 'fixed_asset' || source === 'rental') {
                $row.find('.unit-source-fixed-asset').show();
            } else if (source === 'sparepart') {
                $row.find('.unit-source-equivalent').show();
            } else {
                $row.find('.unit-source-catalog').show();
            }

            // Reset pilihan supaya tidak ada data campur dari sumber sebelumnya
            $row.find('.field-id-unit').val('');
            $row.find('.field-id-fixed-asset').val('');
            $row.find('.field-id-equivalent').val('');
            $row.find('.field-label').val('');

            // Sync type select berdasarkan source yang dipilih
            if (source === 'rental') {
                $('#select-type').val('Rental');
            } else if (source === 'sparepart') {
                $('#select-type').val('Parts');
            } else {
                $('#select-type').val('Unit');
            }

            // Penyesuaian satuan Qty — untuk Spare Part, satuan menyesuaikan
            // produk yang dipilih (diisi saat select2:select), jadi dikosongkan dulu
            if (source === 'rental') {
                $row.find('.field-info-qty').val('Days');
            } else if (source === 'sparepart') {
                $row.find('.field-info-qty').val('');
            } else {
                $row.find('.field-info-qty').val('Unit');
            }
        });
    }

    // ── Bind events per row ───────────────────────────────────────────────
    function bindRowEvents($row) {
        $row.find('.field-qty, .field-disc').on('input', function () {
            updateRowAmount($row);
        });

        $row.find('.field-description').on('input', function () {
            autoResizeDescTextarea(this);
        });
        autoResizeDescTextarea($row.find('.field-description')[0]);

        $row.find('.rupiah-input').on('input', function () {
            var raw = $(this).val().replace(/\./g, '');
            $(this).val(formatRupiah(raw));
            updateRowAmount($row);
        });

        $row.on('click', '.btn-remove-row', function () {
            $row.remove();
            reindexRows();
            recalcHeaderPrefixes();
            recalcSummary();
        });

        $row.on('click', '.btn-remove-spec', function () {
            var $line  = $(this).closest('.spec-line');
            var field  = $line.data('field');
            $line.remove();

            var visible = JSON.parse($row.find('.field-spec-visible').val() || '[]');
            visible = visible.filter(function (f) { return f !== field; });
            $row.find('.field-spec-visible').val(JSON.stringify(visible));
        });
    }

    // ── Init Select2 for client ───────────────────────────────────────────
    function clientBadge(role) {
        if (!role) return '';
        var map = {
            'Leads':     '<span class="badge rounded-pill bg-label-warning me-1">Leads</span>',
            'Customers': '<span class="badge rounded-pill bg-label-success me-1">Customer</span>',
        };
        return map[role] || '';
    }

    function initClientSelect2() {
        var $clientSelect = $('#client-select');
        if ($clientSelect.data('select2')) {
            $clientSelect.select2('destroy');
        }
        $clientSelect.select2({
            placeholder: '-- Select Client --',
            allowClear: true,
            templateResult: function (option) {
                if (!option.id) return option.text;
                var role = $(option.element).data('role');
                return $('<span>' + clientBadge(role) + option.text + '</span>');
            },
            templateSelection: function (option) {
                if (!option.id) return option.text;
                var role = $(option.element).data('role');
                return $('<span>' + clientBadge(role) + option.text + '</span>');
            },
        });
    }
    initClientSelect2();

    // ── Sales filter (Sales Manager / Admin only) — reload Client list by selected sales ──
    var $salesSelect = $('#sales-select');
    if ($salesSelect.length) {
        $salesSelect.select2({ placeholder: '-- Semua Sales (Optional) --', allowClear: true });

        $salesSelect.on('change', function () {
            var salesId = $(this).val();
            var $clientSelect = $('#client-select');
            var currentClientId = $clientSelect.val();

            $clientSelect.empty().append('<option value="">-- Select Client --</option>');

            if (!salesId) {
                initClientSelect2();
                return;
            }

            $.get('/smart-quote/clients-by-sales/' + salesId, function (res) {
                (res.clients || []).forEach(function (c) {
                    var selected = (currentClientId && String(c.id) === String(currentClientId)) ? ' selected' : '';
                    $clientSelect.append('<option value="' + c.id + '" data-role="' + (c.role || '') + '"' + selected + '>' + c.company + '</option>');
                });
                initClientSelect2();
            });
        });
    }

    // ── PIC & Address dropdowns — load by client ─────────────────────────
    $('#client-select').on('change', function () {
        var clientId    = $(this).val();
        var $pic        = $('#pic-select');
        var $addrSelect = $('#address-select');
        var editPicId   = window.EDIT_PIC_ID || null;
        var editPlantId = window.EDIT_PLANT_ID || null;
        var editAddress = window.EDIT_ADDRESS || null;

        $pic.empty().append('<option value="">-- Select PIC --</option>').prop('disabled', true);
        $addrSelect.empty().append('<option value="">-- Select Address --</option>').prop('disabled', true);
        $('#manual-address-wrapper').hide();

        if (!clientId) return;

        $.get('/smart-quote/pics/' + clientId, function (data) {
            var pics     = Array.isArray(data) ? data : (data.pics || []);
            var mainAddr = !Array.isArray(data) ? (data.address || '') : '';
            var subAddr  = !Array.isArray(data) ? (data.subAddress || '') : '';
            var plants   = !Array.isArray(data) ? (data.plants || []) : [];

            // Populate PIC
            $.each(pics, function (i, p) {
                var label    = p.name_pic + (p.position ? ' (' + p.position + ')' : '');
                var selected = (editPicId && p.id == editPicId) ? ' selected' : '';
                $pic.append('<option value="' + p.id + '"' + selected + '>' + label + '</option>');
            });
            $pic.prop('disabled', pics.length === 0);

            // Populate Address
            $addrSelect.empty().append('<option value="">-- Select Address / Plant --</option>');

            if (mainAddr) {
                $addrSelect.append($('<option>', {
                    value: 'main',
                    'data-type': 'main',
                    'data-address': mainAddr,
                    text: 'Office / Factory: ' + mainAddr
                }));
            }

            if (subAddr) {
                $addrSelect.append($('<option>', {
                    value: 'sub',
                    'data-type': 'sub',
                    'data-address': subAddr,
                    text: 'Sub Address: ' + subAddr
                }));
            }

            if (plants && plants.length > 0) {
                $.each(plants, function (i, plant) {
                    $addrSelect.append($('<option>', {
                        value: 'plant_' + plant.id,
                        'data-type': 'plant',
                        'data-plant-id': plant.id,
                        'data-address': plant.address,
                        text: 'Plant: ' + plant.name + (plant.address ? ' (' + plant.address + ')' : '')
                    }));
                });
            }

            $addrSelect.append($('<option>', {
                value: 'manual',
                'data-type': 'manual',
                text: '-- Custom Address (Manual) --'
            }));

            $addrSelect.prop('disabled', false);

            // Pre-select address in Edit mode
            var selectedVal = '';
            if (editPlantId) {
                selectedVal = 'plant_' + editPlantId;
            } else if (editAddress) {
                if (mainAddr && editAddress.trim() === mainAddr.trim()) {
                    selectedVal = 'main';
                } else if (subAddr && editAddress.trim() === subAddr.trim()) {
                    selectedVal = 'sub';
                } else {
                    var matchPlant = plants.find(function(p) { return p.address && p.address.trim() === editAddress.trim(); });
                    if (matchPlant) {
                        selectedVal = 'plant_' + matchPlant.id;
                    } else {
                        selectedVal = 'manual';
                        $('#input-address-manual').val(editAddress);
                    }
                }
            }

            if (selectedVal) {
                $addrSelect.val(selectedVal).trigger('change');
            }

            // Auto-set Payment Term from Sales Payment Template (Client specific or Default)
            var targetPayment = data.clientPayment || data.defaultPayment || null;
            if (targetPayment && !window.EDIT_PAYMENT) {
                var $paymentSelect = $('#payment-select');
                if ($paymentSelect.length) {
                    var hasMatchingOption = false;
                    $paymentSelect.find('option').each(function () {
                        if ($(this).val() === targetPayment) {
                            hasMatchingOption = true;
                            return false;
                        }
                    });

                    if (hasMatchingOption) {
                        $paymentSelect.val(targetPayment).trigger('change');
                    } else {
                        $paymentSelect.val('manual').trigger('change');
                        $('#input-payment-manual').val(targetPayment);
                        $('#input-payment-hidden').val(targetPayment);
                    }
                }
            }

            // Clear edit variables so subsequent client changes don't re-select old data
            window.EDIT_PIC_ID = null;
            window.EDIT_PLANT_ID = null;
            window.EDIT_ADDRESS = null;
        });
    });

    $('#address-select').on('change', function () {
        var $opt     = $(this).find('option:selected');
        var type     = $opt.data('type');
        var plantId  = $opt.data('plant-id') || '';
        var addrText = $opt.data('address') || '';

        if (type === 'plant') {
            $('#input-id-plant').val(plantId);
            $('#input-address-hidden').val(addrText);
            $('#manual-address-wrapper').hide();
        } else if (type === 'main' || type === 'sub') {
            $('#input-id-plant').val('');
            $('#input-address-hidden').val(addrText);
            $('#manual-address-wrapper').hide();
        } else if (type === 'manual') {
            $('#input-id-plant').val('');
            $('#manual-address-wrapper').show();
            $('#input-address-hidden').val($('#input-address-manual').val());
        } else {
            $('#input-id-plant').val('');
            $('#input-address-hidden').val('');
            $('#manual-address-wrapper').hide();
        }
    });

    $('#input-address-manual').on('input', function () {
        $('#input-address-hidden').val($(this).val());
    });

    // ── Payment dropdown with custom "isi sendiri" option ─────────────────
    var $paymentSelect = $('#payment-select');
    if ($paymentSelect.length) {
        var editPayment = window.EDIT_PAYMENT || null;
        var presetValues = $paymentSelect.find('option').map(function () { return $(this).val(); }).get();

        if (editPayment && presetValues.indexOf(editPayment) === -1) {
            $paymentSelect.val('manual');
            $('#manual-payment-wrapper').show();
            $('#input-payment-manual').val(editPayment);
            $('#input-payment-hidden').val(editPayment);
        } else if (editPayment) {
            $paymentSelect.val(editPayment);
            $('#input-payment-hidden').val(editPayment);
        } else {
            // Initial sync on page load (Create mode or when EDIT_PAYMENT is empty)
            var initialVal = $paymentSelect.val();
            if (initialVal === 'manual') {
                $('#manual-payment-wrapper').show();
                $('#input-payment-hidden').val($('#input-payment-manual').val());
            } else if (initialVal) {
                $('#input-payment-hidden').val(initialVal);
            }
        }
        window.EDIT_PAYMENT = null;
    }

    $('#payment-select').on('change', function () {
        var val = $(this).val();
        if (val === 'manual') {
            $('#manual-payment-wrapper').show();
            $('#input-payment-hidden').val($('#input-payment-manual').val());
        } else {
            $('#manual-payment-wrapper').hide();
            $('#input-payment-hidden').val(val);
        }
    });

    $('#input-payment-manual').on('input', function () {
        $('#input-payment-hidden').val($(this).val());
    });

    // Safety sync right before form submission
    $('#form-unit-quotation').on('submit', function () {
        var $paymentSelect = $('#payment-select');
        if ($paymentSelect.length) {
            var val = $paymentSelect.val();
            if (val === 'manual') {
                $('#input-payment-hidden').val($('#input-payment-manual').val());
            } else if (val) {
                $('#input-payment-hidden').val(val);
            }
        }
    });

    // ── Auto-calculate week from date ─────────────────────────────────────
    function syncWeekFromDate(dateStr) {
        if (!dateStr) return;
        var day = parseInt(dateStr.split('-')[2], 10);
        var week = Math.ceil(day / 7);
        $('#select-week').val(week);
    }

    $('#input-date').on('change', function () {
        syncWeekFromDate($(this).val());
    });

    // Set week on page load based on current date value
    syncWeekFromDate($('#input-date').val());

    // ── Button handlers ───────────────────────────────────────────────────
    $('#btn-add-unit').on('click', addUnitRow);
    $('#btn-add-custom').on('click', addCustomRow);
    $('#btn-add-header').on('click', addHeaderRow);
    $('#btn-add-transport').on('click', addTransportRow);

    $('#input-diskon').on('input', function () {
        if ($('#select-diskon-type').val() === 'amount') {
            var raw = $(this).val().replace(/\D/g, '');
            $(this).val(formatRupiah(raw));
        }
        recalcSummary();
    });
    $('#select-diskon-type').on('change', function () {
        // Format ulang tampilan diskon sesuai tipe baru; nilai di-reset
        // supaya angka % tidak salah dibaca sebagai nominal Rp (atau sebaliknya).
        $('#input-diskon').val('0');
        recalcSummary();
    });
    $('#input-shipping').on('input', function () {
        var raw = $(this).val().replace(/\D/g, '');
        $(this).val(formatRupiah(raw));
        recalcSummary();
    });
    $('#toggle-tax').on('change', recalcSummary);

    // ── Pre-submit: convert rupiah field to raw number for server & show loader ──
    $('#form-unit-quotation').on('submit', function () {
        reindexRows();
        recalcHeaderPrefixes();

        $('.rupiah-input').each(function () {
            $(this).val(parseRupiah($(this).val()));
        });

        var $diskon = $('#input-diskon');
        $diskon.val($('#select-diskon-type').val() === 'amount'
            ? parseRupiah($diskon.val())
            : (parseFloat($diskon.val()) || 0));

        var $shipping = $('#input-shipping');
        if ($shipping.length) {
            $shipping.val(parseRupiah($shipping.val()));
        }

        // Show smooth saving loader
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Saving Quotation...',
                text: 'Please wait while we process your document.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: function () {
                    Swal.showLoading();
                }
            });
        }
    });

    // ── Hydrate existing items for edit mode ──────────────────────────────
    if (window.EDIT_ITEMS && window.EDIT_ITEMS.length > 0) {
        $.each(window.EDIT_ITEMS, function (i, item) {
            if (item.type === 'unit') {
                addUnitRowFromData(item);
            } else if (item.type === 'header' || item.type === 'heading') {
                addHeaderRowFromData(item);
            } else if (item.type === 'transport') {
                addTransportRowFromData(item);
            } else {
                addCustomRowFromData(item);
            }
        });

        // Trigger client change to load PICs (with pre-selected PIC)
        if (window.EDIT_CLIENT_ID) {
            $('#client-select').trigger('change');
        }
    }

    // ── Load Template PM (Unit Global) — dipanggil dari modal, bukan sessionStorage lagi ──
    var $pmLoadModal = $('#modal-load-pm-template');
    if ($pmLoadModal.length) {
        var pmLoadSelectedUnitId = null;
        var pmLoadSelectedLevel = null;
        var pmLoadPreviewData = null;

        $('#pm-load-unit-select').select2({
            dropdownParent: $pmLoadModal,
            placeholder: 'Cari unit (SKU / Brand / Model)...',
            minimumInputLength: 1,
            ajax: {
                url: '/unit-global/search',
                dataType: 'json',
                delay: 300,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (u) {
                            return {
                                id: u.id,
                                text: (u.brand || '') + ' — ' + (u.model || u.sku || '') + ' (' + (u.sku || '') + ')'
                            };
                        })
                    };
                }
            }
        });

        // Ubah note master (bebas format: plain line, "- " dash, dst) jadi list ber-bullet "• "
        // biar konsisten sama auto-bullet textarea Note di form ini.
        function pmFormatNoteBullets(text) {
            var BULLET = '• ';
            return String(text).split(/\r?\n/).map(function (line, idx) {
                var trimmed = line.trim().replace(/^[-*•]\s*/, '');
                if (!trimmed) return '';
                if (/^Scope of Work\s*:?/i.test(trimmed) || (trimmed.endsWith(':') && idx === 0)) {
                    return trimmed;
                }
                return BULLET + trimmed;
            }).filter(function (line) { return line !== ''; }).join('\n');
        }

        function pmLoadTryFetch() {
            $('#pm-load-preview').hide();
            $('#btn-pm-load-confirm').prop('disabled', true);
            pmLoadPreviewData = null;

            if (!pmLoadSelectedUnitId || !pmLoadSelectedLevel) return;

            $.ajax({
                url: '/unit-global/' + pmLoadSelectedUnitId + '/pm-template',
                method: 'GET',
                data: { level: pmLoadSelectedLevel },
                success: function (data) {
                    pmLoadPreviewData = data;
                    var count = data.items.length;
                    $('#pm-load-preview').text(count === 0
                        ? 'Template level ini masih kosong — susun dulu di halaman Unit Global.'
                        : count + ' item tersimpan di template ini.'
                    ).show();
                    $('#btn-pm-load-confirm').prop('disabled', count === 0);
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Gagal memuat template', text: 'Unit ini belum punya template PM untuk level tersebut.' });
                }
            });
        }

        $('#pm-load-unit-select').on('select2:select', function (e) {
            pmLoadSelectedUnitId = e.params.data.id;
            pmLoadTryFetch();
        });

        $(document).on('click', '.pm-load-level-btn', function () {
            $('.pm-load-level-btn').removeClass('active');
            $(this).addClass('active');
            pmLoadSelectedLevel = $(this).data('level');
            pmLoadTryFetch();
        });

        $('#btn-pm-load-confirm').on('click', function () {
            if (!pmLoadPreviewData) return;

            // Auto-set Type Quote to "Service" when loading PM template
            $('#select-type, select[name="type"]').val('Service').trigger('change');

            // Auto-set Title / Description to "Level PM | Nama Unit" (e.g. PM1 | KAESER BSD 71)
            var select2Data = $('#pm-load-unit-select').select2('data');
            var unitText = '';
            if (select2Data && select2Data.length > 0 && select2Data[0].text) {
                unitText = select2Data[0].text.replace(/\s*\([^)]*\)\s*$/, '').replace(/\s*—\s*/, ' ').trim();
            }
            if (pmLoadSelectedLevel && unitText) {
                var generatedTitle = pmLoadSelectedLevel + ' | ' + unitText;
                $('input[name="title"]').val(generatedTitle).trigger('change');
            }

            // Delivery Process: PM3/PM4 punya Non-Consumable Part, jadi dipecah 2 baris.
            // Level lain (PM1/PM2) biarkan default "Ready stock".
            if (pmLoadSelectedLevel === 'PM3' || pmLoadSelectedLevel === 'PM4') {
                $('#delivery').val('Consumable Part ( Ready Stock )\nNon-Consumable Part ( Ready Stock )').trigger('change');
            }

            // Warranty: PM1, PM2, PM3 disembunyikan & dikosongkan. PM4 ditampilkan & diisi "3 Month".
            if (pmLoadSelectedLevel === 'PM1' || pmLoadSelectedLevel === 'PM2' || pmLoadSelectedLevel === 'PM3') {
                $('#warranty-wrapper').hide();
                $('#warranty').val('');
            } else if (pmLoadSelectedLevel === 'PM4') {
                $('#warranty-wrapper').show();
                $('#warranty').val('3 Month');
            }

            // Replay urutan item template apa adanya — head title, part, custom (termasuk jasa
            // service, yang sudah jadi item biasa di template, bukan auto-generate lagi).
            // Item type "part" (ada id_equivalent) dipasang sebagai baris Spare Part beneran
            // (bukan custom text) supaya id_equivalent kebawa & terkoneksi ke logic baca stok
            // saat quotation ini nanti dikonversi jadi PO.
            // "Preventive Maintenance PM1" -> "Preventive Maintenance 1" (begitu juga PM2/PM3/PM4),
            // dipakai baik utk head title maupun item jasa service biasa di bawahnya.
            function pmSimplifyLevelLabel(label) {
                return (label || '').replace(/\bPM([1-4])\b/i, '$1');
            }

            // Header "... SERVICE COST" ditandai jadi "... SERVICE COST & TRANSPORTATION" —
            // section ini yang menaungi baris Transportation yang ikut ditambahkan otomatis.
            function pmMaybeAppendTransportToHeader(label) {
                if (/SERVICE COST/i.test(label) && !/TRANSPORTATION/i.test(label)) {
                    return label + ' & TRANSPORTATION';
                }
                return label;
            }

            $.each(pmLoadPreviewData.items, function (i, item) {
                if (item.type === 'header') {
                    addHeaderRowFromData({ label: pmMaybeAppendTransportToHeader(pmSimplifyLevelLabel(item.label)) });
                } else if (item.type === 'part' && item.id_equivalent && item.equivalent) {
                    // Judul "Brand - PN" sudah otomatis direkonstruksi halaman detail quotation
                    // dari relasi equivalent, jadi field label di sini diisi DESKRIPSI produk
                    // (konvensi yang sama dipakai saat pilih Spare Part manual) — bukan "Brand PN"
                    // lagi, supaya gak dobel nongol jadi "judul" pas ditampilkan.
                    addUnitRowFromData({
                        id_equivalent: item.id_equivalent,
                        equivalent: item.equivalent,
                        label: item.description || item.equivalent.product_desc || item.label || 'Item',
                        qty: item.qty,
                        info_qty: item.info_qty || 'Pcs',
                        price: item.price,
                        disc: 0
                    });
                } else {
                    addCustomRowFromData({
                        label: pmSimplifyLevelLabel(item.label) || 'Item',
                        description: pmFormatNoteBullets(item.description || ''),
                        qty: item.qty,
                        info_qty: item.info_qty || 'Pcs',
                        price: item.price,
                        disc: 0
                    });
                }
            });

            // Selalu tambahkan 1 baris Transportation — Tipe & Kota sengaja dikosongkan, dipilih manual oleh sales.
            addTransportRow();

            // Note master per level (dari power_service_prices.note_pmX) — selalu timpa isi
            // textarea Note yang ada saat ini, diformat ber-bullet biar selaras sama gaya
            // auto-bullet textarea Note (lihat BULLET di script inline halaman ini).
            if (pmLoadPreviewData.note) {
                $('#note').val(pmFormatNoteBullets(pmLoadPreviewData.note)).trigger('input');
            }

            $pmLoadModal.modal('hide');
            $('#pm-load-unit-select').val(null).trigger('change');
            $('.pm-load-level-btn').removeClass('active');
            $('#pm-load-preview').hide();
            pmLoadSelectedUnitId = null;
            pmLoadSelectedLevel = null;
            pmLoadPreviewData = null;
        });
    }

    // Format nilai diskon awal (mode edit) kalau tipenya nominal Rupiah.
    // Nilai dari server berbentuk decimal string ("300000.00") — parse dulu
    // sebagai float sebelum diformat, supaya titik desimalnya tidak ikut
    // dianggap ribuan oleh formatRupiah.
    // ── Dynamic Quotation Numbering by Type ──────────────────────────────
    var TYPE_PREFIXES = {
        'Unit':      'U',
        'Rental':    'R',
        'Project':   'PR',
        'Parts':     'P',
        'Service':   'S',
        'Piping':    'PIP',
        'Air Audit': 'AA'
    };

    $('#select-type').on('change', function () {
        var type = $(this).val();
        var prefix = TYPE_PREFIXES[type] || 'PU';
        var $noQuote = $('input[name="no_quote"]');
        var currentNo = $noQuote.val();

        if (currentNo) {
            var updatedNo = currentNo.replace(/^(\d+)-([A-Z0-9]+)(\/.*)$/i, function (match, seq, oldPrefix, rest) {
                return seq + '-' + prefix + rest;
            });
            $noQuote.val(updatedNo);
        }
    });

    if ($('#select-diskon-type').val() === 'amount') {
        var initialDiskon = parseFloat($('#input-diskon').val()) || 0;
        $('#input-diskon').val(formatRupiah(Math.round(initialDiskon)));
    }

    // ── Reorder Items Logic (Drag & Drop + Up/Down Buttons) ──────────────
    function reindexRows() {
        $('#line-items-container .unit-row').each(function (newIdx) {
            var $row = $(this);
            $row.find('input, select, textarea').each(function () {
                var name = $(this).attr('name');
                if (name) {
                    var updatedName = name.replace(/^items\[\d+\]/, 'items[' + newIdx + ']')
                                          .replace(/^unit_source_\d+$/, 'unit_source_' + newIdx);
                    $(this).attr('name', updatedName);
                }
            });
        });
    }

    function recalcHeaderPrefixes() {
        var headerCount = 0;
        $('#line-items-container .unit-row[data-type="header"]').each(function () {
            var $input = $(this).find('.field-label');
            var val = $input.val() || '';
            var prefix = String.fromCharCode(65 + (headerCount % 26)) + '. ';
            var cleanVal = val.replace(/^[A-Z]\.\s*/i, '');
            $input.val(prefix + cleanVal);
            headerCount++;
        });
    }

    var lineItemsContainer = document.getElementById('line-items-container');
    if (lineItemsContainer && typeof Sortable !== 'undefined') {
        Sortable.create(lineItemsContainer, {
            handle: '.btn-drag-handle',
            animation: 150,
            ghostClass: 'bg-light-primary',
            onEnd: function () {
                reindexRows();
                recalcHeaderPrefixes();
                recalcSummary();
            }
        });
    }

    $(document).on('click', '.btn-move-up', function () {
        var $row = $(this).closest('.unit-row');
        var $prev = $row.prev('.unit-row');
        if ($prev.length) {
            $row.insertBefore($prev);
            reindexRows();
            recalcHeaderPrefixes();
            recalcSummary();
        }
    });

    $(document).on('click', '.btn-move-down', function () {
        var $row = $(this).closest('.unit-row');
        var $next = $row.next('.unit-row');
        if ($next.length) {
            $row.insertAfter($next);
            reindexRows();
            recalcHeaderPrefixes();
            recalcSummary();
        }
    });

    toggleEmptyState();
    recalcSummary();
});