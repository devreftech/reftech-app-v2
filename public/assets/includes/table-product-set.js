$(function () {
    var dt_table_product_set = $(".datatable-product-set");
    var Url = "/db/product/set";

    if (dt_table_product_set.length) {
        $('[data-toggle="tooltip"]').tooltip();

        var dt_product_set = dt_table_product_set.DataTable({
            processing: true,
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
                dataSrc: "data",
                error: function (xhr, error, thrown) {
                    console.error("DataTables load error:", error, thrown);
                }
            },
            columns: [
                { data: "" },
                { data: "id" },
                { data: "commodity" },
                { data: "category" },
                { data: "description" },
                { data: "item_count" },
                { data: "total_stock" },
            ],
            columnDefs: [
                {
                    // Control for responsive
                    className: "control",
                    orderable: false,
                    searchable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function () {
                        return "";
                    },
                },
                {
                    targets: 1,
                    searchable: true,
                    visible: false,
                },
                {
                    responsivePriority: 1,
                    targets: 2,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var id = full["id"];
                            var detailRoute = "/product-set/" + id;
                            var shortDesc = full["detail_desc"] && full["detail_desc"] !== "-" 
                                ? '<div class="text-muted small" style="font-size: 11.5px;">' + full["detail_desc"] + '</div>' 
                                : '';
                            return (
                                '<div class="d-flex align-items-center gap-2">' +
                                    '<div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">' +
                                        '<i class="mdi mdi-package-variant-closed fs-5"></i>' +
                                    '</div>' +
                                    '<div>' +
                                        '<a class="fw-bold text-primary text-decoration-none" href="' + detailRoute + '">' +
                                            (data || "-") +
                                        '</a>' +
                                        shortDesc +
                                    '</div>' +
                                '</div>'
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var cat = data || "Non Bearing Kit";
                            if (cat === "Bearing Kit Airend") {
                                return (
                                    '<span class="badge bg-label-primary px-2 py-1 fw-bold">' +
                                        '<i class="mdi mdi-fan me-1"></i>Bearing Kit Airend' +
                                    '</span>'
                                );
                            } else if (cat === "Bearing Kit Main Motor") {
                                return (
                                    '<span class="badge px-2 py-1 fw-bold" style="background-color: rgba(105, 108, 255, 0.12) !important; color: #696cff !important; border: 1px solid rgba(105, 108, 255, 0.2);">' +
                                        '<i class="mdi mdi-engine me-1"></i>Bearing Kit Main Motor' +
                                    '</span>'
                                );
                            } else if (cat === "Bearing Kit Fan Motor") {
                                return (
                                    '<span class="badge px-2 py-1 fw-bold" style="background-color: rgba(3, 195, 236, 0.12) !important; color: #03c3ec !important; border: 1px solid rgba(3, 195, 236, 0.2);">' +
                                        '<i class="mdi mdi-fan-speed-3 me-1"></i>Bearing Kit Fan Motor' +
                                    '</span>'
                                );
                            } else if (cat.indexOf("Bearing") !== -1) {
                                return (
                                    '<span class="badge bg-label-primary px-2 py-1 fw-bold">' +
                                        '<i class="mdi mdi-cog-sync-outline me-1"></i>' + cat +
                                    '</span>'
                                );
                            } else if (cat === "Non Bearing Kit") {
                                return (
                                    '<span class="badge bg-label-secondary px-2 py-1 fw-semibold">' +
                                        '<i class="mdi mdi-package-variant-closed me-1"></i>Non Bearing Kit' +
                                    '</span>'
                                );
                            } else {
                                return (
                                    '<span class="badge bg-label-dark px-2 py-1 fw-semibold">' +
                                        '<i class="mdi mdi-tag-outline me-1"></i>' + cat +
                                    '</span>'
                                );
                            }
                        }
                        return data;
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var desc = data && data !== "-" ? data : "Tidak ada deskripsi";
                            var truncated = desc.length > 50 ? desc.substr(0, 47) + "..." : desc;
                            return '<span class="text-muted" title="' + desc.replace(/"/g, "&quot;") + '">' + truncated + '</span>';
                        }
                        return data;
                    },
                },
                {
                    targets: 5,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var count = parseInt(data) || 0;
                            if (count > 0) {
                                return (
                                    '<button type="button" class="btn btn-xs btn-label-primary py-1 px-2.5 fw-semibold btn-view-components shadow-none d-inline-flex align-items-center gap-1" ' +
                                    'title="Klik untuk melihat detail komponen & stok">' +
                                        '<i class="mdi mdi-layers-outline fs-6"></i>' +
                                        '<span>' + count + ' Komponen</span>' +
                                        '<i class="mdi mdi-open-in-new" style="font-size: 11px;"></i>' +
                                    '</button>'
                                );
                            } else {
                                return '<span class="badge bg-label-secondary py-1 px-2">0 Komponen</span>';
                            }
                        }
                        return data;
                    },
                },
                {
                    targets: 6,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var total = parseInt(data) || 0;
                            var unit = full["unit"] || "Set";
                            var office = full["stock"] || 0;
                            var wh = full["warehouse_stock"] || 0;
                            var badgeClass = total > 0 ? "bg-label-success" : "bg-label-danger";
                            var tooltip = "Office: " + office + " " + unit + " | Warehouse: " + wh + " " + unit;

                            return (
                                '<span class="badge ' + badgeClass + ' py-1 px-2 fw-bold" data-bs-toggle="tooltip" data-bs-placement="top" title="' + tooltip + '">' +
                                    total + ' ' + unit +
                                '</span>'
                            );
                        }
                        return data;
                    },
                },
            ],
            order: [[1, "desc"]],
            dom:
                '<"card-header flex-column flex-md-row"<"head-label text-start"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            buttons: [
                {
                    extend: "collection",
                    className: "btn btn-label-primary dropdown-toggle me-2",
                    text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                    buttons: [
                        {
                            extend: "print",
                            text: '<i class="mdi mdi-printer-outline me-1" ></i>Print',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6],
                            },
                        },
                        {
                            extend: "csv",
                            text: '<i class="mdi mdi-file-document-outline me-1" ></i>Csv',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6],
                            },
                        },
                        {
                            extend: "excel",
                            text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6],
                            },
                        },
                        {
                            extend: "pdf",
                            text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6],
                            },
                        },
                        {
                            extend: "copy",
                            text: '<i class="mdi mdi-content-copy me-1" ></i>Copy',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6],
                            },
                        },
                    ],
                },
                {
                    text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Product Set</span>',
                    className: "btn btn-primary",
                    attr: {
                        "data-bs-target": "#createProduct",
                        "data-bs-toggle": "modal",
                    },
                },
            ],
            language: {
                search: "",
                searchPlaceholder: "Cari Product Set...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                emptyTable: "Belum ada data Product Set",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya",
                },
            },
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
        });

        $("div.head-label").html(
            '<h5 class="card-title mb-0 fw-bold text-dark"><i class="mdi mdi-package-variant-closed me-2 text-primary"></i>Daftar Product Set / Bundle</h5>'
        );

        // Click handler to open component list modal
        $(document).on("click", ".btn-view-components", function () {
            var tr = $(this).closest("tr");
            var data = dt_product_set.row(tr).data();
            if (!data) return;

            var commodity = data.commodity || "Product Set";
            var count = parseInt(data.item_count) || 0;
            var unit = data.unit || "Set";
            var totalStock = parseInt(data.total_stock) || 0;
            var components = data.components || [];

            $("#mvc_bundle_title").text("Komponen - " + commodity);
            $("#mvc_bundle_subtitle").text("Kategori: " + (data.category || "Bundle") + " | Satuan: " + unit);
            $("#mvc_comp_count").text(count + " Komponen");
            
            var stockBadgeHtml = totalStock > 0 
                ? '<span class="badge bg-label-success fs-6 fw-bold px-2.5 py-1"><i class="mdi mdi-check-circle-outline me-1"></i>' + totalStock + ' ' + unit + ' (Ready)</span>'
                : '<span class="badge bg-label-danger fs-6 fw-bold px-2.5 py-1"><i class="mdi mdi-alert-circle-outline me-1"></i>0 ' + unit + ' (Kosong)</span>';
            $("#mvc_total_stock_badge").html(stockBadgeHtml);
            $("#mvc_detail_btn").attr("href", "/product-set/" + data.id);

            var $tbody = $("#mvc_table_body");
            $tbody.empty();

            if (components.length > 0) {
                // Find min stock among components
                var minVal = null;
                components.forEach(function(c) {
                    var tot = (parseInt(c.stock) || 0) + (parseInt(c.warehouse_stock) || 0);
                    if (minVal === null || tot < minVal) {
                        minVal = tot;
                    }
                });

                components.forEach(function (c, idx) {
                    var off = parseInt(c.stock) || 0;
                    var wh = parseInt(c.warehouse_stock) || 0;
                    var tot = off + wh;
                    var cUnit = c.unit || "Pcs";
                    var isLimiter = (minVal !== null && tot === minVal && components.length > 1);

                    var serialsHtml = "";
                    if (c.serials && c.serials.length > 0) {
                        serialsHtml = '<div class="d-flex flex-wrap gap-1 mt-1">';
                        c.serials.forEach(function (s) {
                            serialsHtml += '<span class="badge" style="font-size: 10.5px; background: #f0f2f8; color: #384556; border: 1px solid #d4d8e3;">' +
                                '<strong class="text-primary">' + (s.brand || 'Brand') + ':</strong> ' + (s.pn || '-') +
                            '</span>';
                        });
                        serialsHtml += '</div>';
                    }

                    var nameHtml = c.product_id 
                        ? '<a href="/product/' + c.product_id + '" target="_blank" class="fw-bold text-primary text-decoration-none d-inline-flex align-items-center gap-1">' +
                              '<span>' + c.name + '</span>' +
                              '<i class="mdi mdi-open-in-new" style="font-size: 12px;"></i>' +
                          '</a>'
                        : '<span class="fw-bold text-dark">' + c.name + '</span>';

                    if (isLimiter) {
                        nameHtml += ' <span class="badge bg-label-warning py-0 px-1 ms-1" style="font-size: 10px;" title="Stok pembatas bundle">' +
                            '<i class="mdi mdi-arrow-down-bold-outline me-0.5"></i>Limiter' +
                        '</span>';
                    }

                    var totalBadge = tot > 0 
                        ? '<span class="badge bg-label-success fw-bold px-2 py-1">' + tot + ' ' + cUnit + '</span>'
                        : '<span class="badge bg-label-danger fw-bold px-2 py-1">0 ' + cUnit + '</span>';

                    var rowHtml = 
                        '<tr>' +
                            '<td class="text-center fw-semibold text-muted align-top py-2.5">' + (idx + 1) + '</td>' +
                            '<td class="align-top py-2.5">' +
                                nameHtml +
                                serialsHtml +
                            '</td>' +
                            '<td class="text-center align-top py-2.5 fw-semibold text-dark">' + off + ' ' + cUnit + '</td>' +
                            '<td class="text-center align-top py-2.5 fw-semibold text-dark">' + wh + ' ' + cUnit + '</td>' +
                            '<td class="text-center align-top py-2.5">' + totalBadge + '</td>' +
                        '</tr>';
                    $tbody.append(rowHtml);
                });
            } else {
                $tbody.append('<tr><td colspan="5" class="text-center py-4 text-muted fst-italic">Belum ada komponen penyusun bundle.</td></tr>');
            }

            $("#modalViewComponents").modal("show");
        });
    }
});
