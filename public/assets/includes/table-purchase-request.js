$(function () {
    // Pastikan tidak ada filter global DataTables dari script demo lain yang menyaring data
    if ($.fn.dataTableExt && $.fn.dataTableExt.afnFiltering) {
        $.fn.dataTableExt.afnFiltering.length = 0;
    }

    function dateCol(data) {
        if (!data) return "-";
        var m = moment(data);
        return m.isValid() ? m.format("DD-MM-YYYY") : data;
    }

    function shortCode(data) {
        var full = data || "-";
        return { full: full, short: full };
    }

    function signCol(data, type, full) {
        var name = (full && full.user_name) ? full.user_name : "-";
        if (type !== "display") return name;
        var initials = name
            .split(" ")
            .map(function (w) {
                return w.charAt(0);
            })
            .slice(0, 2)
            .join("")
            .toUpperCase();
        var colors = ["bg-label-primary", "bg-label-success", "bg-label-warning", "bg-label-danger", "bg-label-info", "bg-label-secondary"];
        var colorClass = colors[name.charCodeAt(0) % colors.length];
        var av = (full && full.user_image)
            ? '<img src="/' + full.user_image + '" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;" alt="' + name + '">'
            : '<div class="avatar-initial rounded-circle ' + colorClass + '" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:11px;font-weight:700;">' + initials + "</div>";
        return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + name + '">' + av + "</span>";
    }

    function itemCol(data, type, full) {
        if (type !== "display") return (full && full.item_count) ? parseInt(full.item_count) : 1;
        var count = (full && full.item_count) ? parseInt(full.item_count) : 1;
        return '<button type="button" class="btn-item-preview d-inline-flex align-items-center gap-1 btn-pr-drawer" data-bs-toggle="tooltip" title="Klik untuk membuka slide-over rincian PR &amp; item">' +
            '<i class="mdi mdi-package-variant-closed font-14 text-primary"></i>' +
            '<span class="fw-semibold">' + count + ' item</span>' +
            '</button>';
    }

    function openPurchaseRequestDrawer(full, badgeKey) {
        if (!full) return;

        $('#prDrawerNoPr').text(full.no_pr || 'PR-0000');
        $('#prDrawerCompany').text(full.company || '-');
        $('#prDrawerNoPending').text(full.no_pending || '-');
        $('#prDrawerNoPo').text(full.no_po || '-');
        $('#prDrawerDate').text(dateCol(full.date));

        // Sales Person
        var salesName = full.user_name || '-';
        $('#prDrawerUserName').text(salesName);
        if (full.user_image) {
            $('#prDrawerSalesBox').html('<img src="/' + full.user_image + '" class="rounded-circle me-1" style="width:24px;height:24px;object-fit:cover;" alt="' + salesName + '"><span class="fw-semibold text-heading">' + salesName + '</span>');
        } else {
            var initials = salesName.split(" ").map(function(w){return w.charAt(0);}).slice(0,2).join("").toUpperCase();
            $('#prDrawerSalesBox').html('<span class="avatar-initial rounded-circle bg-label-primary font-10 fw-bold me-1" style="width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;">' + initials + '</span><span class="fw-semibold text-heading">' + salesName + '</span>');
        }

        // Status Badge
        var statusBadgeHtml = '';
        if (badgeKey === 'new') {
            statusBadgeHtml = '<span class="badge bg-label-primary rounded-pill px-3 py-1 font-11 fw-semibold"><i class="mdi mdi-clock-outline me-1"></i>Menunggu ACC</span>';
        } else if (badgeKey === 'acc') {
            statusBadgeHtml = '<span class="badge bg-label-warning rounded-pill px-3 py-1 font-11 fw-semibold"><i class="mdi mdi-check me-1"></i>Telah Disetujui</span>';
        } else if (badgeKey === 'delivery') {
            statusBadgeHtml = '<span class="badge bg-label-info rounded-pill px-3 py-1 font-11 fw-semibold"><i class="mdi mdi-truck-fast-outline me-1"></i>Dalam Pengiriman</span>';
        } else if (badgeKey === 'done') {
            statusBadgeHtml = '<span class="badge bg-label-success rounded-pill px-3 py-1 font-11 fw-semibold"><i class="mdi mdi-check-all me-1"></i>Selesai / Good Receipt</span>';
        } else {
            statusBadgeHtml = '<span class="badge bg-label-secondary rounded-pill px-3 py-1 font-11 fw-semibold"><i class="mdi mdi-file-document-outline me-1"></i>Purchase Request</span>';
        }
        $('#prDrawerStatusBadge').html(statusBadgeHtml);

        // Payment status
        if (full.payment_status) {
            $('#prDrawerPaymentSlot').html('<span class="font-11 text-muted d-block mb-1">Status Bayar Customer:</span>' + paymentStatusCol(full.payment_status, 'display')).show();
        } else {
            $('#prDrawerPaymentSlot').empty().hide();
        }

        // Delivery info box
        if (full.cargo || full.no_resi || full.purchase_date || full.purchase_type) {
            $('#prDrawerCargo').text(full.cargo || '-');
            $('#prDrawerResi').text(full.no_resi || '-');
            $('#prDrawerPurchaseDate').text(full.purchase_date ? dateCol(full.purchase_date) : '-');
            $('#prDrawerPurchaseType').text(full.purchase_type || '-');
            $('#prDrawerDeliveryBox').show();
        } else {
            $('#prDrawerDeliveryBox').hide();
        }

        // Delivery Manual GR & Dev Action options in Drawer
        if (badgeKey === 'delivery') {
            var prId = full ? full.id : "";
            var noPr = (full && full.no_pr) ? full.no_pr : ("PR #" + prId);
            var devBtnHtml = '';

            if (window.canManualGr) {
                devBtnHtml += '<button type="button" class="btn btn-sm btn-success d-flex align-items-center gap-1 btn-manual-gr" data-id="' + prId + '" data-no-pr="' + noPr + '">' +
                    '<i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> Selesaikan dengan GR Manual' +
                    '</button>';
            }
            if (window.isDeveloper) {
                devBtnHtml += '<button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 btn-dev-action" data-id="' + prId + '" data-no-pr="' + noPr + '" data-action="force_done" title="Langsung tandai selesai">' +
                    '<i class="mdi mdi-flash me-1"></i> Force Selesai' +
                    '</button>' +
                    '<button type="button" class="btn btn-sm btn-outline-warning d-flex align-items-center gap-1 btn-dev-action" data-id="' + prId + '" data-no-pr="' + noPr + '" data-action="rollback_approved" title="Kembalikan status ke Approved">' +
                    '<i class="mdi mdi-undo-variant me-1"></i> Rollback Approved' +
                    '</button>';
            }

            $('#prDrawerDeliveryActionButtons').html(devBtnHtml);
            $('#prDrawerDeliveryActionsBox').show();
        } else {
            $('#prDrawerDeliveryActionsBox').hide();
        }

        // Good Receipt info box
        if (full.no_gr) {
            $('#prDrawerNoGr').text(full.no_gr);
            if (full.id_gr) {
                $('#prDrawerGrBtnSlot').html('<a href="/product-in/' + full.id_gr + '" class="btn btn-sm btn-outline-success"><i class="mdi mdi-open-in-new me-1"></i> Buka Barang Masuk</a>');
            } else {
                $('#prDrawerGrBtnSlot').empty();
            }
            $('#prDrawerGrBox').show();
        } else {
            $('#prDrawerGrBox').hide();
        }

        // Items list rendering
        var tbodyHtml = '';
        var itemsDetail = full.items_detail || '';
        var rawItems = itemsDetail ? itemsDetail.split("||") : [];

        if (rawItems.length > 0) {
            rawItems.forEach(function(itemStr, idx) {
                var clean = itemStr.trim();
                var match = clean.match(/^(.*?)\s*\((.*?)\)\s*x(\d+(?:\.\d+)?)\s*(.*)$/);
                if (match) {
                    var partInfo = match[1].trim();
                    var goType = match[2].trim();
                    var qtyVal = match[3].trim();
                    var unitVal = match[4].trim() || 'pcs';
                    var isGenuine = (goType.toLowerCase() === 'genuine');
                    var typeBadge = isGenuine 
                        ? '<span class="badge bg-label-primary font-10">Genuine (G)</span>' 
                        : '<span class="badge bg-label-info font-10">Replacement (R)</span>';

                    tbodyHtml += '<tr>' +
                        '<td class="text-muted font-11 text-center align-middle">' + (idx + 1) + '</td>' +
                        '<td>' +
                            '<div class="fw-semibold text-heading font-12">' + partInfo + '</div>' +
                            typeBadge +
                        '</td>' +
                        '<td class="text-end align-middle">' +
                            '<span class="fw-bold font-13 text-primary">' + qtyVal + '</span> <span class="font-11 text-muted">' + unitVal + '</span>' +
                        '</td>' +
                    '</tr>';
                } else {
                    tbodyHtml += '<tr>' +
                        '<td class="text-muted font-11 text-center align-middle">' + (idx + 1) + '</td>' +
                        '<td><span class="fw-semibold text-heading font-12">' + clean + '</span></td>' +
                        '<td class="text-end align-middle"><span class="badge bg-label-secondary font-11">-</span></td>' +
                    '</tr>';
                }
            });
            $('#prDrawerItemCount').text(rawItems.length);
            $('#prDrawerTotalPcs').text(full.qty_full || (rawItems.length + ' item'));
        } else if (full.item) {
            tbodyHtml = '<tr>' +
                '<td class="text-muted font-11 text-center align-middle">1</td>' +
                '<td><div class="fw-semibold text-heading font-12">' + full.item + '</div></td>' +
                '<td class="text-end align-middle"><span class="fw-bold font-13 text-primary">' + (full.qty_full || '1 item') + '</span></td>' +
            '</tr>';
            $('#prDrawerItemCount').text('1');
            $('#prDrawerTotalPcs').text(full.qty_full || '1 item');
        } else {
            tbodyHtml = '<tr><td colspan="3" class="text-center py-4 text-muted font-12">Tidak ada rincian item.</td></tr>';
            $('#prDrawerItemCount').text('0');
            $('#prDrawerTotalPcs').text('0 item');
        }

        $('#prDrawerItemsTbody').html(tbodyHtml);

        // Action links in footer
        if (full.id) {
            $('#prDrawerDetailBtn').attr('href', '/purchase-request/' + full.id).show();
        } else {
            $('#prDrawerDetailBtn').hide();
        }

        var extraHtml = '';
        if (full.id_po) {
            extraHtml += '<a href="/purchase/' + full.id_po + '" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-xs"><i class="mdi mdi-file-document-outline me-1"></i> Buka PO</a>';
        }
        if (full.id) {
            extraHtml += '<a href="/pending-po/' + full.id + '" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-xs"><i class="mdi mdi-cart-outline me-1"></i> Buka SO</a>';
        }
        $('#prDrawerAdditionalLinks').html(extraHtml);

        // Show Offcanvas
        var offcanvasEl = document.getElementById('purchaseRequestOffcanvas');
        if (offcanvasEl) {
            var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            bsOffcanvas.show();
        }
    }

    function itemsChildHtml(itemsDetail) {
        if (!itemsDetail) {
            return '<div class="p-2 ps-4 text-muted small">Tidak ada rincian item.</div>';
        }
        var rows = itemsDetail.split("||").map(function (it) {
            return "<li>" + it + "</li>";
        }).join("");
        return '<div class="p-2 ps-4"><ul class="mb-0 ps-3 small">' + rows + "</ul></div>";
    }

    function bindItemExpand($table, dt) {
        $table.on("click", ".item-expand-toggle", function (e) {
            e.preventDefault();
            var $icon = $(this).find(".item-expand-icon");
            var tr = $(this).closest("tr");
            var row = dt.row(tr);
            if (row.child.isShown()) {
                row.child.hide();
                $icon.removeClass("mdi-chevron-down").addClass("mdi-chevron-right");
            } else {
                row.child(itemsChildHtml(row.data() ? row.data().items_detail : "")).show();
                $icon.removeClass("mdi-chevron-right").addClass("mdi-chevron-down");
            }
        });
    }

    function paymentStatusCol(data, type) {
        if (type !== "display") return data || "";
        if (!data) return '<span class="text-muted">-</span>';
        if (data.indexOf("tempo") === 0) {
            var raw = data.substring(6).trim();
            var label = "Credit";
            if (raw && raw.toLowerCase() !== "credit" && raw.toLowerCase() !== "tempo") {
                if (/^\d+$/.test(raw)) {
                    label = "Credit (" + raw + " Days)";
                } else {
                    var match = raw.match(/(\d+)\s*(days?|hari)/i);
                    if (match) {
                        label = "Credit (" + match[1] + " Days)";
                    } else {
                        label = "Credit (" + raw + ")";
                    }
                }
            }
            return '<span class="badge bg-label-info fw-semibold"><i class="mdi mdi-calendar-clock me-1"></i>' + label + '</span>';
        }
        if (data === "confirmed") {
            return '<span class="badge bg-label-success fw-semibold"><i class="mdi mdi-check-circle-outline me-1"></i>Sudah Dikonfirmasi</span>';
        }
        if (data === "unconfirmed") {
            return '<span class="badge bg-label-warning fw-semibold"><i class="mdi mdi-clock-outline me-1"></i>Belum Dikonfirmasi</span>';
        }
        return '<span class="text-muted">-</span>';
    }

    function initPurchaseRequestTable(selector, ajaxUrl, withDelivery, hideNoPo, showPayment, showGr, badgeKey) {
        var $table = $(selector);
        if (!$table.length) return;
        $table.data('badge-key', badgeKey);

        var offset = showGr ? 1 : 0;
        var columns = [];
        if (showGr) {
            columns.push({ data: "no_gr", defaultContent: "-" });
        }
        columns.push(
            { data: "no_pr", defaultContent: "-" },
            { data: "no_po", defaultContent: "-" },
            { data: "no_pending", defaultContent: "-" },
            { data: "company", defaultContent: "-" },
            { data: "item_count", defaultContent: "1" },
            { data: "date", defaultContent: "-" }
        );
        if (showPayment) {
            columns.push({ data: "payment_status", defaultContent: "" });
        }
        columns.push({ data: "user_name", defaultContent: "-" });

        if (withDelivery) {
            columns.push({ data: "id", defaultContent: "" });
        }

        var signTarget = withDelivery ? columns.length - 2 : columns.length - 1;
        var actionTarget = withDelivery ? columns.length - 1 : null;

        var columnDefs = [
            {
                targets: offset + 0,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    var code = shortCode(data);
                    if (!full || !full.id) return '<span class="text-nowrap" data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</span>";
                    return '<a href="/purchase-request/' + full.id + '" class="fw-semibold text-primary text-nowrap" data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</a>";
                },
            },
            {
                targets: offset + 1,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    if (!data) return "-";
                    if (full && full.id_po) {
                        var code = shortCode(data);
                        return '<a href="/purchase/' + full.id_po + '" class="fw-semibold text-primary text-nowrap" data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</a>";
                    }
                    return '<span class="text-nowrap">' + data + '</span>';
                },
            },
            {
                targets: offset + 2,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    var code = shortCode(data);
                    if (!full || !full.id) return '<span class="text-nowrap" data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</span>";
                    return '<a href="/purchase-request/' + full.id + '" class="text-nowrap" data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</a>";
                },
            },
            { targets: offset + 3, render: function (data) { return data || "-"; } },
            { targets: offset + 4, className: "text-center", render: itemCol },
            { targets: signTarget, className: "text-center", orderable: false, searchable: false, render: signCol },
        ];

        if (showGr) {
            columnDefs.push({
                targets: 0,
                render: function (data, type, full) {
                    if (!showGr || type !== "display") return data || "-";
                    if (!data) return "-";
                    if (full && full.id_gr) {
                        return '<a href="/product-in/' + full.id_gr + '" class="fw-semibold text-primary" data-bs-toggle="tooltip" title="Lihat detail Barang Masuk">' + data + "</a>";
                    }
                    return '<span class="fw-semibold text-dark">' + data + "</span>";
                },
            });
        }

        if (hideNoPo) {
            columnDefs.push({ targets: offset + 1, visible: false });
        }

        if (showPayment) {
            columnDefs.push({ targets: signTarget - 1, className: "text-center", render: paymentStatusCol });
        }

        if (withDelivery) {
            columnDefs.push({
                targets: offset + 5,
                render: function (data, type, full) {
                    if (type !== "display") return (full && full.purchase_date) ? full.purchase_date : "";
                    var cargo = (full && full.cargo) ? full.cargo : "-";
                    var resi = (full && full.no_resi) ? full.no_resi : "Belum ada resi";
                    var tglBeli = (full && full.purchase_date) ? dateCol(full.purchase_date) : "-";
                    return '<div class="small">' + cargo + '<div class="text-muted">' + resi + '</div>' +
                        '<div class="text-muted">Tgl Beli: ' + tglBeli + "</div></div>";
                },
            });

            if (actionTarget !== null) {
                columnDefs.push({
                    targets: actionTarget,
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full) {
                        if (type !== "display") return "";
                        var prId = full ? full.id : "";
                        var noPr = (full && full.no_pr) ? full.no_pr : ("PR #" + prId);

                        var html = '<div class="d-inline-flex align-items-center gap-1">';

                        if (window.canManualGr) {
                            html += '<button type="button" class="btn btn-xs btn-outline-success btn-manual-gr" data-id="' + prId + '" data-no-pr="' + noPr + '" data-bs-toggle="tooltip" title="Selesaikan dengan GR Manual">' +
                                '<i class="mdi mdi-checkbox-marked-circle-outline"></i> Selesai GR' +
                                '</button>';
                        }

                        if (window.isDeveloper) {
                            html += '<div class="dropdown d-inline-block">' +
                                '<button type="button" class="btn btn-xs btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false" title="Dev Quick Action">' +
                                '<i class="mdi mdi-dots-vertical"></i>' +
                                '</button>' +
                                '<ul class="dropdown-menu dropdown-menu-end py-1 shadow-sm">' +
                                '<li><h6 class="dropdown-header font-10 text-uppercase py-1 text-primary"><i class="mdi mdi-code-tags me-1"></i>Dev Actions</h6></li>' +
                                '<li><button type="button" class="dropdown-item py-1 font-12 text-success btn-dev-action" data-id="' + prId + '" data-no-pr="' + noPr + '" data-action="force_done"><i class="mdi mdi-flash me-2"></i>Force Selesai (Done)</button></li>' +
                                '<li><button type="button" class="dropdown-item py-1 font-12 text-warning btn-dev-action" data-id="' + prId + '" data-no-pr="' + noPr + '" data-action="rollback_approved"><i class="mdi mdi-undo-variant me-2"></i>Rollback Approved</button></li>' +
                                '</ul>' +
                                '</div>';
                        }

                        html += '</div>';
                        return html;
                    }
                });
            }
        } else {
            columnDefs.push({ targets: offset + 5, render: function (data) { return dateCol(data); } });
        }

        var dt = $table.DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                type: "GET",
                url: ajaxUrl,
                dataSrc: "data",
                headers: { "Content-Type": "application/json" },
            },
            columns: columns,
            columnDefs: columnDefs,
            order: [[offset + 5, "desc"]],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { emptyTable: "Tidak ada data Purchase Request." },
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
                if (badgeKey) {
                    var api = this.api ? this.api() : null;
                    var total = (api && api.page && api.page.info()) ? (api.page.info().recordsTotal || 0) : 0;
                    $('.stat-count-' + badgeKey).text(total);
                    $('.stat-badge-' + badgeKey).text(total);
                    var $tabBadge = $('.tab-badge-' + badgeKey);
                    if (total >= 1) {
                        $tabBadge.text(total).show();
                    } else {
                        $tabBadge.hide();
                    }
                }
            },
        });

        bindItemExpand($table, dt);
    }

    initPurchaseRequestTable(".datatable-purchase-request-new", "/db/purchase-request/new", false, true, true, false, "new");
    initPurchaseRequestTable(".datatable-purchase-request-acc", "/db/purchase-request/acc", false, true, true, false, "acc");
    initPurchaseRequestTable(".datatable-purchase-request-delivery", "/db/purchase-request/delivery", true, false, false, false, "delivery");
    initPurchaseRequestTable(".datatable-purchase-request-done", "/db/purchase-request/done", false, false, false, true, "done");

    function initPurchaseOrderTable(selector, ajaxUrl, badgeKey) {
        var $table = $(selector);
        if (!$table.length) return;
        $table.data('badge-key', badgeKey);

        var dt = $table.DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                type: "GET",
                url: ajaxUrl,
                dataSrc: "data",
                headers: { "Content-Type": "application/json" },
            },
            columns: [
                { data: "no_po", defaultContent: "-" },
                { data: "no_pr", defaultContent: "-" },
                { data: "no_pending", defaultContent: "-" },
                { data: "company", defaultContent: "-" },
                { data: "item_count", defaultContent: "0" },
                { data: "date", defaultContent: "-" },
                { data: "receipt_status", defaultContent: "-" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "-";
                        var code = shortCode(data);
                        if (!full || !full.id) return '<span class="text-nowrap" data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</span>";
                        return '<a href="/purchase/' + full.id + '" class="fw-semibold text-primary text-nowrap" data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</a>";
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "-";
                        var code = shortCode(data);
                        if (!full || !full.id_pending) {
                            return '<span class="text-nowrap" data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' + code.short + "</span>";
                        }
                        return '<a href="/purchase-request/' + full.id_pending + '" class="text-nowrap" data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' + code.short + "</a>";
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "-";
                        var code = shortCode(data);
                        if (!full || !full.id_pending) {
                            return '<span class="text-nowrap" data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' + code.short + "</span>";
                        }
                        return '<a href="/purchase-request/' + full.id_pending + '" class="text-nowrap" data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' + code.short + "</a>";
                    },
                },
                { targets: 3, render: function (data) { return data || "-"; } },
                {
                    targets: 4,
                    className: "text-center",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var count = parseInt(data || 0);
                        return '<button type="button" class="btn-item-preview d-inline-flex align-items-center gap-1 btn-pr-drawer text-start" data-bs-toggle="tooltip" title="Klik untuk melihat rincian item &amp; PO">' +
                            '<i class="mdi mdi-package-variant-closed font-13 text-primary"></i>' +
                            '<span>' + count + ' item</span>' +
                            '</button>';
                    },
                },
                { targets: 5, render: function (data) { return dateCol(data); } },
                {
                    targets: 6,
                    className: "text-center",
                    render: function (data, type) {
                        if (type !== "display") return data || "-";
                        if (data === "Received") {
                            return '<span class="badge bg-label-success fw-semibold"><i class="mdi mdi-check me-1"></i>Diterima</span>';
                        }
                        return '<span class="badge bg-label-warning fw-semibold"><i class="mdi mdi-truck-fast-outline me-1"></i>Menunggu Pengiriman</span>';
                    },
                },
            ],
            order: [[5, "desc"]],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { emptyTable: "Belum ada Purchase Order." },
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
                if (badgeKey) {
                    var api = this.api ? this.api() : null;
                    var total = (api && api.page && api.page.info()) ? (api.page.info().recordsTotal || 0) : 0;
                    $('.stat-count-' + badgeKey).text(total);
                    $('.stat-badge-' + badgeKey).text(total);
                    var $tabBadge = $('.tab-badge-' + badgeKey);
                    if (total >= 1) {
                        $tabBadge.text(total).show();
                    } else {
                        $tabBadge.hide();
                    }
                }
            },
        });

        bindItemExpand($table, dt);
    }

    initPurchaseOrderTable(".datatable-purchase-order", "/db/purchase-request/po", "po");

    // Delegated click on .btn-pr-drawer to open slide-over
    $(document).on('click', '.btn-pr-drawer', function (e) {
        e.preventDefault();
        var $tr = $(this).closest('tr');
        var $table = $tr.closest('table');
        if (!$table.length) return;
        var dt = $table.DataTable();
        var full = dt.row($tr).data();
        if (!full) return;

        var badgeKey = $table.data('badge-key') || '';
        openPurchaseRequestDrawer(full, badgeKey);
    });

    // Adjust columns when switching tabs
    $('#purchaseRequestTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

    function reloadPurchaseRequestTables() {
        if ($.fn.DataTable.isDataTable('.datatable-purchase-request-delivery')) {
            $('.datatable-purchase-request-delivery').DataTable().ajax.reload(null, false);
        }
        if ($.fn.DataTable.isDataTable('.datatable-purchase-request-done')) {
            $('.datatable-purchase-request-done').DataTable().ajax.reload(null, false);
        }
        if ($.fn.DataTable.isDataTable('.datatable-purchase-request-acc')) {
            $('.datatable-purchase-request-acc').DataTable().ajax.reload(null, false);
        }
        if ($.fn.DataTable.isDataTable('.datatable-purchase-order')) {
            $('.datatable-purchase-order').DataTable().ajax.reload(null, false);
        }
    }

    function closePurchaseRequestDrawer() {
        var offcanvasEl = document.getElementById('purchaseRequestOffcanvas');
        if (offcanvasEl) {
            var bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (bsOffcanvas) {
                bsOffcanvas.hide();
            }
        }
    }

    // Inisialisasi Select2 untuk Modal Manual GR saat modal dibuka
    var select2ProductInInitialized = false;

    function initManualGrSelect2() {
        var $select = $('#manualGrSelectProductIn');
        if (!$select.length || !$.fn.select2) return;

        if (select2ProductInInitialized) {
            $select.val(null).trigger('change');
            return;
        }

        $select.select2({
            dropdownParent: $('#modalManualGr'),
            placeholder: '-- Cari No. Barang Masuk / DO / Supplier --',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '/db/product-in/unlinked',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term || '' };
                },
                processResults: function (response) {
                    var items = (response && response.data) ? response.data : [];
                    return {
                        results: $.map(items, function (it) {
                            return {
                                id: it.id,
                                text: it.text,
                                no_product_in: it.no_product_in,
                                date: it.date
                            };
                        })
                    };
                }
            }
        });

        $select.on('select2:select', function (e) {
            var data = e.params.data;
            if (data) {
                if (data.no_product_in) {
                    $('#manualGrNoGr').val(data.no_product_in);
                }
                if (data.date) {
                    $('#manualGrDate').val(data.date);
                }
            }
        });

        $select.on('select2:clear', function () {
            $('#manualGrNoGr').val('');
        });

        select2ProductInInitialized = true;
    }

    // Modal Manual GR Event Listener: Buka Modal
    $(document).on('click', '.btn-manual-gr', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var prId = $(this).data('id');
        var noPr = $(this).data('no-pr') || ('PR #' + prId);
        var today = moment().format('YYYY-MM-DD');

        $('#manualGrPrId').val(prId);
        $('#modalManualGrNoPr').text(noPr);
        $('#manualGrDate').val(today);
        $('#manualGrNoGr').val('');
        $('#manualGrNote').val('');
        $('#manualGrSubmitBtn').prop('disabled', false).html('<i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> Selesaikan Sekarang');

        var modalEl = document.getElementById('modalManualGr');
        if (modalEl) {
            var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
        }
    });

    $('#modalManualGr').on('shown.bs.modal', function () {
        initManualGrSelect2();
    });

    // Form Submit Handler untuk Modal Manual GR
    $('#formManualGr').on('submit', function (e) {
        e.preventDefault();

        var prId = $('#manualGrPrId').val();
        var idProductIn = $('#manualGrSelectProductIn').val();
        var noGr = $.trim($('#manualGrNoGr').val());
        var grDate = $('#manualGrDate').val();
        var note = $.trim($('#manualGrNote').val());

        if (!noGr) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Belum Lengkap',
                text: 'Harap masukkan Nomor Goods Receipt (GR) atau Nomor Surat Jalan terlebih dahulu.',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
            $('#manualGrNoGr').focus();
            return;
        }

        var $btn = $('#manualGrSubmitBtn');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');

        $.ajax({
            url: '/purchase-request/' + prId + '/manual-gr',
            type: 'POST',
            data: {
                _token: window.csrfToken,
                id_product_in: idProductIn,
                no_gr: noGr,
                gr_date: grDate,
                note: note
            }
        }).done(function (response) {
            var modalEl = document.getElementById('modalManualGr');
            if (modalEl) {
                var bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) {
                    bsModal.hide();
                }
            }

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: (response && response.message) ? response.message : 'Purchase Request berhasil diselesaikan.',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });

            reloadPurchaseRequestTables();
            closePurchaseRequestDrawer();
        }).fail(function (error) {
            var msg = 'Terjadi kesalahan saat memproses permintaan.';
            if (error.responseJSON && error.responseJSON.message) {
                msg = error.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyelesaikan',
                text: msg,
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> Selesaikan Sekarang');
        });
    });

    // Developer Actions Handler (Solusi 1)
    $(document).on('click', '.btn-dev-action', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var prId = $(this).data('id');
        var noPr = $(this).data('no-pr') || ('PR #' + prId);
        var action = $(this).data('action');

        var title = '';
        var text = '';
        var confirmBtnText = '';
        var confirmBtnClass = '';
        var icon = 'warning';

        if (action === 'force_done') {
            title = 'Force Selesai (Done)?';
            text = 'PR ' + noPr + ' akan langsung dipaksa berstatus Selesai (status=3) dan PO terkait ditandai Received. Tidak ada penambahan stok fisik.';
            confirmBtnText = '<i class="mdi mdi-flash me-1"></i> Force Selesai';
            confirmBtnClass = 'btn btn-success me-2';
        } else if (action === 'rollback_approved') {
            title = 'Rollback ke Approved?';
            text = 'PR ' + noPr + ' akan dikembalikan ke tab Telah Disetujui (status=1) dan alokasi pengiriman/resi akan di-reset.';
            confirmBtnText = '<i class="mdi mdi-undo-variant me-1"></i> Ya, Rollback';
            confirmBtnClass = 'btn btn-warning me-2';
        } else {
            return;
        }

        Swal.fire({
            title: title,
            html: '<p class="text-muted font-13 mb-3 text-start">' + text + '</p>' +
                '<div class="text-start">' +
                '<label class="form-label font-12 fw-semibold">Alasan / Catatan Dev (Opsional)</label>' +
                '<input type="text" id="swal_dev_note" class="form-control form-control-sm" placeholder="Contoh: Koreksi data orphan / delivery fix">' +
                '</div>',
            icon: icon,
            showCancelButton: true,
            confirmButtonText: confirmBtnText,
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: confirmBtnClass,
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false,
            showLoaderOnConfirm: true,
            preConfirm: function () {
                var note = $('#swal_dev_note').val();
                return $.ajax({
                    url: '/purchase-request/' + prId + '/dev-action',
                    type: 'POST',
                    data: {
                        _token: window.csrfToken,
                        action: action,
                        note: note
                    }
                }).then(function (response) {
                    return response;
                }).catch(function (error) {
                    var msg = 'Terjadi kesalahan sistem.';
                    if (error.responseJSON && error.responseJSON.message) {
                        msg = error.responseJSON.message;
                    }
                    Swal.showValidationMessage(msg);
                });
            },
            allowOutsideClick: function () { return !Swal.isLoading(); }
        }).then(function (result) {
            if (result.isConfirmed && result.value) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.value.message || 'Aksi Developer berhasil dijalankan.',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });

                reloadPurchaseRequestTables();
                closePurchaseRequestDrawer();
            }
        });
    });
});

