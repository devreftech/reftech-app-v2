$(function () {
    function currency(data) {
        if (data === null || data === undefined || data === "") return "-";
        return "Rp " + Number(data).toLocaleString("id-ID");
    }

    function formatDateTime(dtStr) {
        if (!dtStr) return "-";
        try {
            var parts = dtStr.split(/[- :]/);
            if (parts.length >= 5) {
                var d = parts[2];
                var m = parts[1];
                var y = parts[0];
                var h = parts[3];
                var min = parts[4];
                return d + "/" + m + "/" + y + " " + h + ":" + min;
            }
            var dObj = new Date(dtStr);
            if (!isNaN(dObj.getTime())) {
                var day = String(dObj.getDate()).padStart(2, '0');
                var month = String(dObj.getMonth() + 1).padStart(2, '0');
                var year = dObj.getFullYear();
                var hours = String(dObj.getHours()).padStart(2, '0');
                var minutes = String(dObj.getMinutes()).padStart(2, '0');
                return day + "/" + month + "/" + year + " " + hours + ":" + minutes;
            }
        } catch (e) {}
        return dtStr;
    }

    function renderIdrPriceCol(data, type, full) {
        if (type !== "display") return full.price_idr || 0;

        var formattedPrice = (full.price_idr !== null && full.price_idr !== undefined && full.price_idr !== "")
            ? "Rp " + Number(full.price_idr).toLocaleString("id-ID")
            : "-";

        var dateFormatted = formatDateTime(full.price_updated_at);
        var dateHtml = '<div class="small text-muted mt-1" style="font-size:0.75rem;" title="Tanggal update harga terakhir">' +
            '<i class="mdi mdi-calendar-clock-outline me-1"></i>' + dateFormatted +
            '</div>';

        if (window.isCatalogAdmin) {
            var brandModel = [full.brand, full.model].filter(Boolean).join(" ");
            return '<div class="idr-price-cell-admin text-center" ' +
                'data-id="' + full.id + '" ' +
                'data-idr="' + (full.price_idr || 0) + '" ' +
                'data-usd="' + (full.price_usd || 0) + '" ' +
                'data-sku="' + (full.sku || "") + '" ' +
                'data-name="' + brandModel + '" ' +
                'data-updated="' + dateFormatted + '" ' +
                'data-by="' + (full.price_updated_by || "") + '" ' +
                'title="Klik untuk update harga IDR">' +
                '<span class="fw-semibold text-primary">' + formattedPrice + '</span>' +
                '<i class="mdi mdi-pencil-outline btn-edit-icon ms-1 small"></i>' +
                dateHtml +
                '</div>';
        }

        return '<div class="text-center"><span class="fw-semibold">' + formattedPrice + '</span>' + dateHtml + '</div>';
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
            { targets: 4, className: "text-center", render: renderIdrPriceCol },
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
            { targets: 3, className: "text-center", render: renderIdrPriceCol },
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
            { targets: 3, className: "text-center", render: renderIdrPriceCol },
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
            { targets: 3, className: "text-center", render: renderIdrPriceCol },
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
            { targets: 3, className: "text-center", render: renderIdrPriceCol },
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
            { targets: 3, className: "text-center", render: renderIdrPriceCol },
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

    // ── Event Handler Klik IDR Price Cell (Khusus Admin) ──
    $(document).on("click", ".idr-price-cell-admin", function (e) {
        e.preventDefault();
        var $el = $(this);
        var id = $el.data("id");
        var idr = $el.data("idr") || 0;
        var sku = $el.data("sku") || "";
        var name = $el.data("name") || "";
        var updated = $el.data("updated") || "-";
        var by = $el.data("by") || "";

        $("#edit-catalog-id").val(id);
        $("#edit-catalog-unit-name").text((name ? name + " · " : "") + sku);
        $("#edit-catalog-current-price").text("Rp " + Number(idr).toLocaleString("id-ID"));
        $("#edit-catalog-last-updated").html('<i class="mdi mdi-calendar-clock-outline me-1"></i>' + updated);

        if (by) {
            $("#edit-catalog-last-by").text("oleh " + by).removeClass("d-none");
        } else {
            $("#edit-catalog-last-by").text("").addClass("d-none");
        }

        var idrDisplay = idr ? String(parseInt(idr)).replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
        $("#edit-catalog-price-display").val(idrDisplay);
        $("#edit-catalog-price-raw").val(idr);
        $("#edit-catalog-note").val("");

        $("#modalUpdateCatalogPrice").modal("show");
        setTimeout(function () {
            $("#edit-catalog-price-display").focus().select();
        }, 400);
    });

    // ── Event Handler Submit Form Update IDR Price via AJAX ──
    $("#formUpdateCatalogPrice").on("submit", function (e) {
        e.preventDefault();
        var id = $("#edit-catalog-id").val();
        var priceIdr = $("#edit-catalog-price-raw").val();
        var note = $("#edit-catalog-note").val();
        if (!id) return;

        var $btn = $("#btnSubmitUpdatePrice");
        var $spinner = $("#spinnerSubmitUpdatePrice");

        $btn.prop("disabled", true);
        $spinner.removeClass("d-none");

        $.ajax({
            url: "/catalog-unit/" + id,
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content") || $('input[name="_token"]').val(),
                _method: "PATCH",
                price_idr: priceIdr,
                note: note,
            },
            dataType: "json",
            success: function (res) {
                $("#modalUpdateCatalogPrice").modal("hide");
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: res.message || "Harga IDR katalog berhasil diperbarui.",
                        timer: 1800,
                        showConfirmButton: false,
                    });
                } else {
                    alert(res.message || "Harga berhasil diperbarui.");
                }

                allTables.forEach(function ($t) {
                    if ($.fn.DataTable.isDataTable($t)) {
                        $t.DataTable().ajax.reload(null, false);
                    }
                });
            },
            error: function (xhr) {
                var err = (xhr.responseJSON && xhr.responseJSON.message) || "Terjadi kesalahan saat mengupdate harga.";
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: err,
                    });
                } else {
                    alert(err);
                }
            },
            complete: function () {
                $btn.prop("disabled", false);
                $spinner.addClass("d-none");
            },
        });
    });
});
