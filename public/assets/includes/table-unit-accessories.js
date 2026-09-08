$(function () {
    var $tables = $(".datatable-unit-accessories");
    if (!$tables.length) return;

    var dtInstances = {};
    var totals = {};

    function updateTotalBadge(group, count) {
        totals[group] = count;
        var total = Object.keys(totals).reduce(function (sum, k) { return sum + (totals[k] || 0); }, 0);
        $("#accessories-count-badge").text(total).toggleClass("d-none", total === 0);
    }

    var statusRentalBadges = {
        available: '<span class="badge bg-label-success">Tersedia</span>',
        rental: '<span class="badge bg-label-primary">Sedang Dirental</span>',
        reserved: '<span class="badge bg-label-info">Reserved</span>',
        maintenance: '<span class="badge bg-label-warning">Perawatan</span>',
        broken: '<span class="badge bg-label-danger">Rusak</span>',
    };

    var kondisiBadges = {
        ok: '<span class="badge bg-label-success">Bagus / OK</span>',
        fair: '<span class="badge bg-label-warning">Cukup</span>',
        damaged: '<span class="badge bg-label-danger">Rusak</span>',
    };

    function renderItemCode(data, type, row) {
        var name = row.name || "-";
        var code = row.code ? '<br><small class="text-muted"><i class="mdi mdi-barcode me-1"></i>' + row.code + '</small>' : "";
        var brand = row.brand ? '<span class="badge bg-label-secondary ms-1">' + row.brand + '</span>' : "";
        return '<div><span class="fw-semibold text-heading">' + name + '</span>' + brand + code + '</div>';
    }

    function renderActions(data, type, row) {
        return (
            '<div class="d-flex align-items-center justify-content-center gap-1">' +
            '<button type="button" class="btn btn-sm btn-icon btn-text-primary rounded-pill waves-effect btn-edit-acc" data-id="' + row.id + '" title="Edit Aksesoris">' +
            '<i class="mdi mdi-pencil-outline mdi-20px"></i>' +
            '</button>' +
            '<button type="button" class="btn btn-sm btn-icon btn-text-danger rounded-pill waves-effect btn-delete-acc" data-id="' + row.id + '" data-name="' + (row.name || "") + '" title="Hapus Aksesoris">' +
            '<i class="mdi mdi-delete-outline mdi-20px"></i>' +
            '</button>' +
            '</div>'
        );
    }

    var columnsByGroup = {
        hose: [
            { data: "name", render: renderItemCode },
            { data: "size", defaultContent: "-" },
            { data: "length", defaultContent: "-" },
            { data: "max_pressure", defaultContent: "-" },
            { data: "connection", defaultContent: "-" },
            { data: "condition", defaultContent: "ok", render: function (d) { return kondisiBadges[d] || d || "-"; } },
            { data: "rental_status", defaultContent: "available", render: function (d) { return statusRentalBadges[d] || d || "-"; } },
            { data: "stock", defaultContent: 0, className: "text-center", render: function (d) { return '<span class="badge bg-label-primary">' + (d || 0) + ' unit</span>'; } },
            { data: "id", orderable: false, searchable: false, className: "text-center", render: renderActions },
        ],
        header: [
            { data: "name", render: renderItemCode },
            { data: "size", defaultContent: "-" },
            { data: "extra_spec", defaultContent: "-" },
            { data: "connection", defaultContent: "-" },
            { data: "material", defaultContent: "-" },
            { data: "condition", defaultContent: "ok", render: function (d) { return kondisiBadges[d] || d || "-"; } },
            { data: "rental_status", defaultContent: "available", render: function (d) { return statusRentalBadges[d] || d || "-"; } },
            { data: "stock", defaultContent: 0, className: "text-center", render: function (d) { return '<span class="badge bg-label-primary">' + (d || 0) + ' unit</span>'; } },
            { data: "id", orderable: false, searchable: false, className: "text-center", render: renderActions },
        ],
        reducer: [
            { data: "name", render: renderItemCode },
            { data: "size", defaultContent: "-" },
            { data: "connection", defaultContent: "-" },
            { data: "material", defaultContent: "-" },
            { data: "extra_spec", defaultContent: "-" },
            { data: "condition", defaultContent: "ok", render: function (d) { return kondisiBadges[d] || d || "-"; } },
            { data: "rental_status", defaultContent: "available", render: function (d) { return statusRentalBadges[d] || d || "-"; } },
            { data: "stock", defaultContent: 0, className: "text-center", render: function (d) { return '<span class="badge bg-label-primary">' + (d || 0) + ' unit</span>'; } },
            { data: "id", orderable: false, searchable: false, className: "text-center", render: renderActions },
        ],
        cable: [
            { data: "name", render: renderItemCode },
            { data: "size", defaultContent: "-" },
            { data: "length", defaultContent: "-" },
            { data: "extra_spec", defaultContent: "-" },
            { data: "connection", defaultContent: "-" },
            { data: "condition", defaultContent: "ok", render: function (d) { return kondisiBadges[d] || d || "-"; } },
            { data: "rental_status", defaultContent: "available", render: function (d) { return statusRentalBadges[d] || d || "-"; } },
            { data: "stock", defaultContent: 0, className: "text-center", render: function (d) { return '<span class="badge bg-label-primary">' + (d || 0) + ' unit</span>'; } },
            { data: "id", orderable: false, searchable: false, className: "text-center", render: renderActions },
        ],
        nipple: [
            { data: "name", render: renderItemCode },
            { data: "size", defaultContent: "-" },
            { data: "connection", defaultContent: "-" },
            { data: "material", defaultContent: "-" },
            { data: "max_pressure", defaultContent: "-" },
            { data: "condition", defaultContent: "ok", render: function (d) { return kondisiBadges[d] || d || "-"; } },
            { data: "rental_status", defaultContent: "available", render: function (d) { return statusRentalBadges[d] || d || "-"; } },
            { data: "stock", defaultContent: 0, className: "text-center", render: function (d) { return '<span class="badge bg-label-primary">' + (d || 0) + ' unit</span>'; } },
            { data: "id", orderable: false, searchable: false, className: "text-center", render: renderActions },
        ],
    };

    // Category Spec Dynamic Configurations
    var specConfigByCategory = {
        hose: {
            sizeLabel: "Ukuran (Diameter)",
            sizePlaceholder: "Contoh: 2 Inch, 3 Inch",
            lengthLabel: "Panjang",
            lengthPlaceholder: "Contoh: 5 Meter, 10 Meter",
            maxPressureLabel: "Max. Pressure",
            maxPressurePlaceholder: "Contoh: 10 Bar / 150 PSI",
            connectionLabel: "Koneksi / Fitting",
            connectionPlaceholder: "Contoh: Camlock Type C+E, Flange",
            materialLabel: "Material",
            materialPlaceholder: "Contoh: Rubber, PVC, Stainless Steel",
            extraSpecLabel: "Spesifikasi Lain",
            extraSpecPlaceholder: "Opsional",
        },
        header: {
            sizeLabel: "Ukuran Pipa Utama",
            sizePlaceholder: "Contoh: 4 Inch, 6 Inch",
            lengthLabel: "Panjang Header",
            lengthPlaceholder: "Contoh: 1.5 Meter",
            maxPressureLabel: "Max. Pressure",
            maxPressurePlaceholder: "Contoh: 10 Bar / 16 Bar",
            connectionLabel: "Ukuran Port Outlet",
            connectionPlaceholder: "Contoh: 1 Inch Ball Valve",
            materialLabel: "Material Header",
            materialPlaceholder: "Contoh: Carbon Steel, SS304",
            extraSpecLabel: "Jumlah Port Outlet",
            extraSpecPlaceholder: "Contoh: 4 Port, 6 Port",
        },
        reducer: {
            sizeLabel: "Ukuran Inlet",
            sizePlaceholder: "Contoh: 3 Inch, 4 Inch",
            lengthLabel: "Panjang / Tinggi",
            lengthPlaceholder: "Opsional",
            maxPressureLabel: "Max. Pressure",
            maxPressurePlaceholder: "Contoh: 16 Bar / 20 Bar",
            connectionLabel: "Ukuran Outlet",
            connectionPlaceholder: "Contoh: 2 Inch, 1.5 Inch",
            materialLabel: "Material",
            materialPlaceholder: "Contoh: Carbon Steel, SS304, Kuningan",
            extraSpecLabel: "Tipe Koneksi",
            extraSpecPlaceholder: "Contoh: Flange ANSI 150, Threaded NPT",
        },
        cable: {
            sizeLabel: "Tipe / Ukuran Kabel",
            sizePlaceholder: "Contoh: 4 x 16 mm², 4 x 25 mm²",
            lengthLabel: "Panjang Kabel (Meter)",
            lengthPlaceholder: "Contoh: 25 Meter, 50 Meter",
            maxPressureLabel: "Rating Tegangan",
            maxPressurePlaceholder: "Contoh: 0.6/1 kV, 380V",
            connectionLabel: "Tipe Terminal / Plug",
            connectionPlaceholder: "Contoh: CEE Plug 63A 5P, Skun M10",
            materialLabel: "Jenis Kabel / Isolasi",
            materialPlaceholder: "Contoh: NYYHY, H07RN-F",
            extraSpecLabel: "Kapasitas Arus (Ampere)",
            extraSpecPlaceholder: "Contoh: 100A, 125A",
        },
        nipple: {
            sizeLabel: "Ukuran Ulir / Thread",
            sizePlaceholder: "Contoh: 1 Inch x 1 Inch, 1\" to 3/4\"",
            lengthLabel: "Panjang (Opsional)",
            lengthPlaceholder: "Contoh: 50 mm",
            maxPressureLabel: "Rating Tekanan",
            maxPressurePlaceholder: "Contoh: 3000 PSI / 200 Bar",
            connectionLabel: "Tipe Ulir",
            connectionPlaceholder: "Contoh: NPT Male x Male, BSPT",
            materialLabel: "Material",
            materialPlaceholder: "Contoh: Kuningan / Brass, SS304, Galvanis",
            extraSpecLabel: "Keterangan Model / Hex",
            extraSpecPlaceholder: "Contoh: Hex Nipple",
        },
    };

    function updateFormLabels($form, category) {
        var config = specConfigByCategory[category] || specConfigByCategory.hose;
        $form.find(".label-size").text(config.sizeLabel);
        $form.find('input[name="size"]').attr("placeholder", config.sizePlaceholder);

        $form.find(".label-length").text(config.lengthLabel);
        $form.find('input[name="length"]').attr("placeholder", config.lengthPlaceholder);

        $form.find(".label-max-pressure").text(config.maxPressureLabel);
        $form.find('input[name="max_pressure"]').attr("placeholder", config.maxPressurePlaceholder);

        $form.find(".label-connection").text(config.connectionLabel);
        $form.find('input[name="connection"]').attr("placeholder", config.connectionPlaceholder);

        $form.find(".label-material").text(config.materialLabel);
        $form.find('input[name="material"]').attr("placeholder", config.materialPlaceholder);

        $form.find(".label-extra-spec").text(config.extraSpecLabel);
        $form.find('input[name="extra_spec"]').attr("placeholder", config.extraSpecPlaceholder);
    }

    // Dynamic category change event in modals
    $(document).on("change", ".select-acc-category", function () {
        var $form = $(this).closest("form");
        updateFormLabels($form, $(this).val());
    });

    // Initialize all 5 datatables
    $tables.each(function () {
        var $table = $(this);
        var group = $table.data("group");
        if (!group) return;

        var columns = columnsByGroup[group] || [
            { data: "name", render: renderItemCode },
            { data: "stock", defaultContent: 0, className: "text-center" },
            { data: "id", orderable: false, searchable: false, className: "text-center", render: renderActions },
        ];

        var dt = $table.DataTable({
            ajax: {
                type: "GET",
                url: "/db/accessories?group=" + group,
                headers: { "Content-Type": "application/json" },
                dataSrc: function (json) {
                    var rows = json.data || [];
                    $("#acc-" + group + "-count-badge").text(rows.length).toggleClass("d-none", rows.length === 0);
                    updateTotalBadge(group, rows.length);
                    return rows;
                },
            },
            columns: columns,
            order: [[0, "asc"]],
            orderCellsTop: true,
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { emptyTable: "Belum ada data aksesoris di kategori " + group + "." },
        });

        dtInstances[group] = dt;

        // Search per kolom
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

        // Adjust columns on tab display
        $('button[data-bs-target="#subtab-acc-' + group + '"]').on("shown.bs.tab", function () {
            dt.columns.adjust().draw(false);
        });
        $('button[data-bs-target="#tab-accessories"]').on("shown.bs.tab", function () {
            dt.columns.adjust().draw(false);
        });
    });

    function reloadAllTables() {
        Object.keys(dtInstances).forEach(function (grp) {
            if (dtInstances[grp]) {
                dtInstances[grp].ajax.reload(null, false);
            }
        });
    }

    // When opening #modalAddAccessory, preset category from active subtab
    $("#modalAddAccessory").on("show.bs.modal", function () {
        var $activeSubtab = $("#accessoriesSubtabs .nav-link.active");
        var activeTarget = $activeSubtab.data("bs-target") || "";
        var activeCategory = "hose";
        if (activeTarget.indexOf("header") !== -1) activeCategory = "header";
        else if (activeTarget.indexOf("reducer") !== -1) activeCategory = "reducer";
        else if (activeTarget.indexOf("cable") !== -1) activeCategory = "cable";
        else if (activeTarget.indexOf("nipple") !== -1) activeCategory = "nipple";

        var $form = $("#formAddAccessory");
        $form[0].reset();
        $form.find('select[name="category"]').val(activeCategory);
        updateFormLabels($form, activeCategory);
    });

    // Handle Add Accessory Submit
    $("#formAddAccessory").on("submit", function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find(".btn-submit-acc");
        var $spinner = $btn.find(".spinner-border");

        $btn.prop("disabled", true);
        $spinner.removeClass("d-none");

        $.ajax({
            url: "/rental-accessories",
            type: "POST",
            data: $form.serialize(),
            success: function (res) {
                $("#modalAddAccessory").modal("hide");
                $form[0].reset();
                reloadAllTables();
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: res.message || "Data aksesoris berhasil ditambahkan.",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            },
            error: function (xhr) {
                var msg = "Terjadi kesalahan saat menyimpan data.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                if (typeof Swal !== "undefined") {
                    Swal.fire({ icon: "error", title: "Gagal", text: msg });
                } else {
                    alert(msg);
                }
            },
            complete: function () {
                $btn.prop("disabled", false);
                $spinner.addClass("d-none");
            },
        });
    });

    // Handle Open Edit Modal
    $(document).on("click", ".btn-edit-acc", function () {
        var id = $(this).data("id");
        if (!id) return;

        $.ajax({
            url: "/rental-accessories/" + id,
            type: "GET",
            success: function (res) {
                var d = res.data;
                if (!d) return;

                var $form = $("#formEditAccessory");
                $form[0].reset();

                $("#edit-acc-id").val(d.id);
                $("#edit-acc-category").val(d.category);
                $("#edit-acc-code").val(d.code || "");
                $("#edit-acc-name").val(d.name || "");
                $("#edit-acc-brand").val(d.brand || "");
                $("#edit-acc-size").val(d.size || "");
                $("#edit-acc-length").val(d.length || "");
                $("#edit-acc-max-pressure").val(d.max_pressure || "");
                $("#edit-acc-connection").val(d.connection || "");
                $("#edit-acc-material").val(d.material || "");
                $("#edit-acc-extra-spec").val(d.extra_spec || "");
                $("#edit-acc-stock").val(d.stock || 1);
                $("#edit-acc-condition").val(d.condition || "ok");
                $("#edit-acc-rental-status").val(d.rental_status || "available");
                $("#edit-acc-location").val(d.location || "");
                $("#edit-acc-notes").val(d.notes || "");

                updateFormLabels($form, d.category);
                $("#modalEditAccessory").modal("show");
            },
            error: function () {
                if (typeof Swal !== "undefined") {
                    Swal.fire({ icon: "error", title: "Gagal", text: "Gagal memuat detail data aksesoris." });
                }
            },
        });
    });

    // Handle Edit Accessory Submit
    $("#formEditAccessory").on("submit", function (e) {
        e.preventDefault();
        var $form = $(this);
        var id = $("#edit-acc-id").val();
        var $btn = $form.find(".btn-submit-acc");
        var $spinner = $btn.find(".spinner-border");

        $btn.prop("disabled", true);
        $spinner.removeClass("d-none");

        $.ajax({
            url: "/rental-accessories/" + id,
            type: "POST",
            data: $form.serialize(),
            success: function (res) {
                $("#modalEditAccessory").modal("hide");
                reloadAllTables();
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: res.message || "Data aksesoris berhasil diperbarui.",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            },
            error: function (xhr) {
                var msg = "Terjadi kesalahan saat memperbarui data.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                if (typeof Swal !== "undefined") {
                    Swal.fire({ icon: "error", title: "Gagal", text: msg });
                } else {
                    alert(msg);
                }
            },
            complete: function () {
                $btn.prop("disabled", false);
                $spinner.addClass("d-none");
            },
        });
    });

    // Handle Delete Accessory
    $(document).on("click", ".btn-delete-acc", function () {
        var id = $(this).data("id");
        var name = $(this).data("name") || "aksesoris ini";
        if (!id) return;

        function doDelete() {
            $.ajax({
                url: "/rental-accessories/" + id,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content") || $('input[name="_token"]').val(),
                    _method: "DELETE",
                },
                success: function (res) {
                    reloadAllTables();
                    if (typeof Swal !== "undefined") {
                        Swal.fire({
                            icon: "success",
                            title: "Terhapus!",
                            text: res.message || "Data aksesoris berhasil dihapus.",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                },
                error: function (xhr) {
                    var msg = "Gagal menghapus data aksesoris.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (typeof Swal !== "undefined") {
                        Swal.fire({ icon: "error", title: "Gagal", text: msg });
                    } else {
                        alert(msg);
                    }
                },
            });
        }

        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "Hapus Aksesoris?",
                text: 'Apakah Anda yakin ingin menghapus "' + name + '"?',
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#8592a3",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
            }).then(function (result) {
                if (result.isConfirmed) {
                    doDelete();
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus "' + name + '"?')) {
                doDelete();
            }
        }
    });
});
