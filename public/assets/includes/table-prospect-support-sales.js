$(function () {
    // Status mapping dictionary for Quotes and Smart Quotes
    var statusMap = {
        100: { label: "Done PO", color: "success", icon: "mdi-cart-check" },
        80:  { label: "Hot Prospect", color: "warning", icon: "mdi-fire" },
        60:  { label: "Negotiation", color: "primary", icon: "mdi-handshake" },
        40:  { label: "Progress FU", color: "info", icon: "mdi-progress-clock" },
        30:  { label: "Inquiry Accepted", color: "dark", icon: "mdi-check-all" },
        20:  { label: "Send WA/Email", color: "secondary", icon: "mdi-email-outline" },
        0:   { label: "Loss", color: "danger", icon: "mdi-close-circle-outline" },
        // Smart Quote (unit_quotation)
        draft:        { label: "Draft", color: "secondary", icon: "mdi-file-outline" },
        sent:         { label: "Sent", color: "info", icon: "mdi-email-outline" },
        negotiation:  { label: "Negotiation", color: "warning", icon: "mdi-handshake" },
        revision:     { label: "Revisi", color: "primary", icon: "mdi-file-document-edit-outline" },
        hot_prospect: { label: "Hot Prospect", color: "danger", icon: "mdi-fire" },
        po_received:  { label: "PO Received", color: "success", icon: "mdi-cart-check" },
        loss:         { label: "Loss", color: "dark", icon: "mdi-close-circle-outline" },
        cancel:       { label: "Cancel", color: "dark", icon: "mdi-cancel" },
    };

    function renderCompanyPic(data, type, full) {
        if (type === "display") {
            var companyName = data ? data : "—";
            var picName = full.name_pic ? full.name_pic : "—";
            var phoneClean = full.phone_pic ? full.phone_pic.replace(/[^0-9]/g, "") : "";
            var phoneBadge = full.phone_pic
                ? `<a href="https://wa.me/${phoneClean}" target="_blank" class="badge bg-label-success text-decoration-none py-1 px-2" data-bs-toggle="tooltip" title="Chat WhatsApp">
                     <i class="mdi mdi-whatsapp me-1"></i>${full.phone_pic}
                   </a>`
                : "";
            var areaBadge = full.area
                ? `<span class="badge bg-label-secondary small py-0 px-2 text-truncate" style="max-width:140px;">
                     <i class="mdi mdi-map-marker-outline me-1"></i>${full.area}
                   </span>`
                : "";

            return `
                <div class="d-flex flex-column gap-1 py-1">
                    <a href="/prospect/${full.id}" class="fw-bold text-heading text-primary-hover mb-0" style="font-size: 0.95rem;">
                        ${companyName}
                    </a>
                    <div class="d-flex align-items-center gap-2 flex-wrap text-muted small">
                        <span class="d-flex align-items-center text-secondary">
                            <i class="mdi mdi-account-outline me-1"></i> ${picName}
                        </span>
                        ${phoneBadge}
                    </div>
                    ${areaBadge ? `<div>${areaBadge}</div>` : ""}
                </div>
            `;
        }
        return data;
    }

    function renderCategoryKebutuhan(data, type, full) {
        if (type === "display") {
            var category = full.category ? full.category : "General";
            var kebutuhan = full.kebutuhan ? full.kebutuhan : "—";
            var escapedKebutuhan = $("<div>").text(kebutuhan).html();

            return `
                <div class="d-flex flex-column gap-1 py-1" style="max-width: 280px;">
                    <span class="badge bg-label-primary rounded-pill align-self-start fw-semibold" style="font-size: 0.72rem;">
                        ${category}
                    </span>
                    <p class="mb-0 text-muted small text-truncate" style="max-width: 270px;" data-bs-toggle="tooltip" title="${escapedKebutuhan}">
                        ${kebutuhan}
                    </p>
                </div>
            `;
        }
        return data;
    }

    function renderDateSource(data, type, full) {
        if (type === "display") {
            var dateVal = full.date ? full.date : "—";
            var sourceVal = full.source ? full.source : "Direct";

            var sourceColor = "info";
            if (sourceVal.toLowerCase().includes("whatsapp")) sourceColor = "success";
            if (sourceVal.toLowerCase().includes("web")) sourceColor = "primary";
            if (sourceVal.toLowerCase().includes("instagram")) sourceColor = "danger";

            return `
                <div class="d-flex flex-column gap-1 py-1">
                    <span class="small fw-semibold text-heading">
                        <i class="mdi mdi-calendar-blank-outline me-1 text-muted"></i>${dateVal}
                    </span>
                    <span class="badge bg-label-${sourceColor} rounded-pill align-self-start small">
                        <i class="mdi mdi-bullhorn-outline me-1"></i>${sourceVal}
                    </span>
                </div>
            `;
        }
        return data;
    }

    function renderSupport(data, type, full) {
        if (type === "display") {
            var suppName = full.support_name ? full.support_name : "Marketing";
            var initial = suppName.charAt(0).toUpperCase();
            var avatarHtml = full.support_image
                ? `<img src="/${full.support_image}" class="rounded-circle shadow-xs" width="30" height="30" style="object-fit:cover;" onerror="this.outerHTML='<span class=\\'avatar-initial rounded-circle bg-label-info small\\'>${initial}</span>'">`
                : `<span class="avatar-initial rounded-circle bg-label-info small fw-bold">${initial}</span>`;

            return `
                <div class="d-flex align-items-center gap-2 py-1">
                    <div class="avatar avatar-sm flex-shrink-0">
                        ${avatarHtml}
                    </div>
                    <span class="small fw-medium text-heading text-truncate" style="max-width: 110px;">
                        ${suppName}
                    </span>
                </div>
            `;
        }
        return data;
    }

    function renderQuoteStatus(data, type, full) {
        if (type === "display") {
            if (full.quotation_id || (full.status !== null && full.status !== undefined)) {
                var st = statusMap[full.status] || { label: "Quoted", color: "primary", icon: "mdi-file-document-outline" };
                var quoteHref = full.is_smart ? "/smart-quote/" + full.quotation_id : "/quotation/" + full.quotation_id;
                var quoteLink = full.no_quote
                    ? `<a href="${quoteHref}" class="small fw-bold text-primary d-block mb-1 text-decoration-none">
                         <i class="mdi mdi-file-outline me-1"></i>${full.no_quote}
                       </a>`
                    : "";
                var statusBadge = `<span class="badge bg-label-${st.color} rounded-pill px-2 py-1 small">
                                     <i class="mdi ${st.icon} me-1"></i>${st.label}
                                   </span>`;
                var nettVal = full.nett
                    ? `<div class="small fw-bold text-success mt-1">Rp ${Number(full.nett).toLocaleString("id-ID")}</div>`
                    : "";

                return `<div class="d-flex flex-column gap-1 py-1">${quoteLink}${statusBadge}${nettVal}</div>`;
            } else {
                return `<span class="badge bg-label-secondary rounded-pill small px-2 py-1">Belum ada Quote</span>`;
            }
        }
        return data;
    }

    function renderNoQuoteReason(data, type, full) {
        if (type === "display") {
            if (full.level === "2" || full.level === 2) {
                return `
                    <div class="d-flex flex-column gap-1 py-1">
                        <span class="badge bg-label-dark rounded-pill px-2 py-1 small align-self-start">
                            <i class="mdi mdi-phone-missed me-1"></i>No Respon
                        </span>
                        <small class="text-muted">Klien tidak merespon follow up</small>
                    </div>
                `;
            } else {
                return `
                    <div class="d-flex flex-column gap-1 py-1">
                        <span class="badge bg-label-secondary rounded-pill px-2 py-1 small align-self-start">
                            <i class="mdi mdi-close-circle-outline me-1"></i>Without Quote
                        </span>
                        <small class="text-muted">Proses langsung tanpa quotation</small>
                    </div>
                `;
            }
        }
        return data;
    }

    function getExportButtons(cols) {
        return [
            {
                extend: "collection",
                className: "btn btn-outline-secondary dropdown-toggle waves-effect shadow-xs",
                text: '<i class="mdi mdi-export-variant me-1"></i> Export',
                buttons: [
                    {
                        extend: "excel",
                        text: '<i class="mdi mdi-file-excel-outline me-1"></i> Excel',
                        className: "dropdown-item",
                        exportOptions: { columns: cols || [2, 3, 4, 5, 6] },
                    },
                    {
                        extend: "csv",
                        text: '<i class="mdi mdi-file-document-outline me-1"></i> CSV',
                        className: "dropdown-item",
                        exportOptions: { columns: cols || [2, 3, 4, 5, 6] },
                    },
                    {
                        extend: "print",
                        text: '<i class="mdi mdi-printer-outline me-1"></i> Print',
                        className: "dropdown-item",
                        exportOptions: { columns: cols || [2, 3, 4, 5, 6] },
                    },
                ],
            },
        ];
    }

    var commonDom = '<"row mx-2 my-2"<"col-sm-12 col-md-6 d-flex align-items-center"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end gap-2"f<"dt-action-buttons"B>>>t<"row mx-2 my-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"p>>';

    // 1. TAB NEW PROSPECTS
    if ($("#table-prospect-new").length && !$.fn.DataTable.isDataTable("#table-prospect-new")) {
        $("#table-prospect-new").DataTable({
            ajax: { type: "GET", url: "/db/prospect/sales?tab=new" },
            columns: [
                { data: "id" }, { data: "id" }, { data: "company" },
                { data: "kebutuhan" }, { data: "date" }, { data: "support_name" },
                { data: "status" }, { data: "id" }
            ],
            columnDefs: [
                { className: "control", orderable: false, searchable: false, targets: 0, render: function () { return ""; } },
                { targets: 1, searchable: true, visible: false },
                { targets: 2, responsivePriority: 1, render: renderCompanyPic },
                { targets: 3, responsivePriority: 3, render: renderCategoryKebutuhan },
                { targets: 4, responsivePriority: 4, render: renderDateSource },
                { targets: 5, responsivePriority: 5, render: renderSupport },
                { targets: 6, responsivePriority: 2, render: renderQuoteStatus },
                {
                    targets: 7, orderable: false, searchable: false, responsivePriority: 1, className: "text-center",
                    render: function (data, type, full) {
                        return `
                            <div class="d-flex align-items-center gap-1 justify-content-center flex-wrap">
                                <button type="button" class="btn btn-sm btn-primary waves-effect py-1 px-2 text-nowrap fw-semibold shadow-xs" id="withQuote" data-id="${full.id}" data-bs-toggle="tooltip" title="Buat Smart Quote">
                                    <i class="mdi mdi-lightning-bolt me-1"></i>Quote
                                </button>
                                <button type="button" class="btn btn-sm btn-label-warning waves-effect py-1 px-2 text-nowrap onProcessFU" data-id="${full.id}" data-bs-toggle="tooltip" title="Pindahkan ke Follow-Up">
                                    <i class="mdi mdi-progress-clock me-1"></i>FU
                                </button>
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-icon btn-sm btn-outline-secondary rounded-pill waves-effect" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><a class="dropdown-item" href="/prospect/${full.id}"><i class="mdi mdi-eye-outline me-2 text-primary"></i>Detail Prospek</a></li>
                                        <li><a class="dropdown-item withoutQuote" href="javascript:void(0);" data-id="${full.id}"><i class="mdi mdi-close-circle-outline me-2 text-secondary"></i>Without Quote</a></li>
                                        <li><a class="dropdown-item noRespond" href="javascript:void(0);" data-id="${full.id}"><i class="mdi mdi-phone-missed me-2 text-warning"></i>No Respond</a></li>
                                    </ul>
                                </div>
                            </div>
                        `;
                    }
                }
            ],
            order: [[1, "desc"]],
            dom: commonDom,
            displayLength: 10,
            lengthMenu: [10, 25, 50, 100],
            buttons: getExportButtons(),
            drawCallback: function () { $('[data-bs-toggle="tooltip"]').tooltip(); }
        });
    }

    // 2. TAB FOLLOW UP
    if ($("#table-prospect-fu").length && !$.fn.DataTable.isDataTable("#table-prospect-fu")) {
        $("#table-prospect-fu").DataTable({
            ajax: { type: "GET", url: "/db/prospect/sales?tab=fu" },
            columns: [
                { data: "id" }, { data: "id" }, { data: "company" },
                { data: "kebutuhan" }, { data: "date" }, { data: "support_name" },
                { data: "status" }, { data: "id" }
            ],
            columnDefs: [
                { className: "control", orderable: false, searchable: false, targets: 0, render: function () { return ""; } },
                { targets: 1, searchable: true, visible: false },
                { targets: 2, responsivePriority: 1, render: renderCompanyPic },
                { targets: 3, responsivePriority: 3, render: renderCategoryKebutuhan },
                { targets: 4, responsivePriority: 4, render: renderDateSource },
                { targets: 5, responsivePriority: 5, render: renderSupport },
                { targets: 6, responsivePriority: 2, render: renderQuoteStatus },
                {
                    targets: 7, orderable: false, searchable: false, responsivePriority: 1, className: "text-center",
                    render: function (data, type, full) {
                        return `
                            <div class="d-flex align-items-center gap-1 justify-content-center flex-wrap">
                                <button type="button" class="btn btn-sm btn-primary waves-effect py-1 px-2 text-nowrap fw-semibold shadow-xs" id="withQuote" data-id="${full.id}" data-bs-toggle="tooltip" title="Buat Smart Quote">
                                    <i class="mdi mdi-lightning-bolt me-1"></i>Quote
                                </button>
                                <a href="/prospect/${full.id}" class="btn btn-icon btn-sm btn-outline-secondary rounded-pill waves-effect" data-bs-toggle="tooltip" title="Detail & Diskusi">
                                    <i class="mdi mdi-eye-outline"></i>
                                </a>
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-icon btn-sm btn-outline-secondary rounded-pill waves-effect" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><a class="dropdown-item withoutQuote" href="javascript:void(0);" data-id="${full.id}"><i class="mdi mdi-close-circle-outline me-2 text-secondary"></i>Without Quote</a></li>
                                        <li><a class="dropdown-item noRespond" href="javascript:void(0);" data-id="${full.id}"><i class="mdi mdi-phone-missed me-2 text-warning"></i>No Respond</a></li>
                                    </ul>
                                </div>
                            </div>
                        `;
                    }
                }
            ],
            order: [[1, "desc"]],
            dom: commonDom,
            displayLength: 10,
            lengthMenu: [10, 25, 50, 100],
            buttons: getExportButtons(),
            drawCallback: function () { $('[data-bs-toggle="tooltip"]').tooltip(); }
        });
    }

    // 3. TAB QUOTED (PENAWARAN AKTIF)
    if ($("#table-prospect-quoted").length && !$.fn.DataTable.isDataTable("#table-prospect-quoted")) {
        $("#table-prospect-quoted").DataTable({
            ajax: { type: "GET", url: "/db/prospect/sales?tab=quoted" },
            columns: [
                { data: "id" }, { data: "id" }, { data: "company" },
                { data: "kebutuhan" }, { data: "date" }, { data: "support_name" },
                { data: "status" }, { data: "id" }
            ],
            columnDefs: [
                { className: "control", orderable: false, searchable: false, targets: 0, render: function () { return ""; } },
                { targets: 1, searchable: true, visible: false },
                { targets: 2, responsivePriority: 1, render: renderCompanyPic },
                { targets: 3, responsivePriority: 3, render: renderCategoryKebutuhan },
                { targets: 4, responsivePriority: 4, render: renderDateSource },
                { targets: 5, responsivePriority: 5, render: renderSupport },
                { targets: 6, responsivePriority: 2, render: renderQuoteStatus },
                {
                    targets: 7, orderable: false, searchable: false, responsivePriority: 1, className: "text-center",
                    render: function (data, type, full) {
                        var quoteHref = full.is_smart ? "/smart-quote/" + full.quotation_id : "/quotation/" + full.quotation_id;
                        var quoteBtn = full.quotation_id
                            ? `<a href="${quoteHref}" class="btn btn-sm btn-label-primary waves-effect py-1 px-2 text-nowrap fw-semibold" data-bs-toggle="tooltip" title="Buka Dokumen Penawaran">
                                 <i class="mdi mdi-file-document-outline me-1"></i>Lihat Quote
                               </a>`
                            : `<button type="button" class="btn btn-sm btn-primary waves-effect py-1 px-2" id="withQuote" data-id="${full.id}">Buat Quote</button>`;

                        return `
                            <div class="d-flex align-items-center gap-1 justify-content-center">
                                ${quoteBtn}
                                <a href="/prospect/${full.id}" class="btn btn-icon btn-sm btn-outline-secondary rounded-pill waves-effect" data-bs-toggle="tooltip" title="Detail Prospect">
                                    <i class="mdi mdi-eye-outline"></i>
                                </a>
                            </div>
                        `;
                    }
                }
            ],
            order: [[1, "desc"]],
            dom: commonDom,
            displayLength: 10,
            lengthMenu: [10, 25, 50, 100],
            buttons: getExportButtons(),
            drawCallback: function () { $('[data-bs-toggle="tooltip"]').tooltip(); }
        });
    }

    // 4. TAB NO QUOTE / NO RESPON
    if ($("#table-prospect-noquote").length && !$.fn.DataTable.isDataTable("#table-prospect-noquote")) {
        $("#table-prospect-noquote").DataTable({
            ajax: { type: "GET", url: "/db/prospect/sales?tab=no_quote" },
            columns: [
                { data: "id" }, { data: "id" }, { data: "company" },
                { data: "kebutuhan" }, { data: "date" }, { data: "support_name" },
                { data: "level" }, { data: "id" }
            ],
            columnDefs: [
                { className: "control", orderable: false, searchable: false, targets: 0, render: function () { return ""; } },
                { targets: 1, searchable: true, visible: false },
                { targets: 2, responsivePriority: 1, render: renderCompanyPic },
                { targets: 3, responsivePriority: 3, render: renderCategoryKebutuhan },
                { targets: 4, responsivePriority: 4, render: renderDateSource },
                { targets: 5, responsivePriority: 5, render: renderSupport },
                { targets: 6, responsivePriority: 2, render: renderNoQuoteReason },
                {
                    targets: 7, orderable: false, searchable: false, responsivePriority: 1, className: "text-center",
                    render: function (data, type, full) {
                        return `
                            <div class="d-flex align-items-center gap-1 justify-content-center">
                                <a href="/prospect/${full.id}" class="btn btn-icon btn-sm btn-outline-secondary rounded-pill waves-effect" data-bs-toggle="tooltip" title="Detail Prospek">
                                    <i class="mdi mdi-eye-outline"></i>
                                </a>
                                <button type="button" class="btn btn-icon btn-sm btn-outline-primary rounded-pill waves-effect onProcessFU" data-id="${full.id}" data-bs-toggle="tooltip" title="Follow Up Kembali">
                                    <i class="mdi mdi-refresh"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[1, "desc"]],
            dom: commonDom,
            displayLength: 10,
            lengthMenu: [10, 25, 50, 100],
            buttons: getExportButtons(),
            drawCallback: function () { $('[data-bs-toggle="tooltip"]').tooltip(); }
        });
    }

    // 5. TAB DONE PO
    if ($("#table-prospect-po").length && !$.fn.DataTable.isDataTable("#table-prospect-po")) {
        $("#table-prospect-po").DataTable({
            ajax: { type: "GET", url: "/db/prospect/sales?tab=po" },
            columns: [
                { data: "id" }, { data: "id" }, { data: "company" },
                { data: "kebutuhan" }, { data: "date" }, { data: "support_name" },
                { data: "status" }, { data: "id" }
            ],
            columnDefs: [
                { className: "control", orderable: false, searchable: false, targets: 0, render: function () { return ""; } },
                { targets: 1, searchable: true, visible: false },
                { targets: 2, responsivePriority: 1, render: renderCompanyPic },
                { targets: 3, responsivePriority: 3, render: renderCategoryKebutuhan },
                { targets: 4, responsivePriority: 4, render: renderDateSource },
                { targets: 5, responsivePriority: 5, render: renderSupport },
                {
                    targets: 6, responsivePriority: 2,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var quoteHref = full.is_smart ? "/smart-quote/" + full.quotation_id : "/quotation/" + full.quotation_id;
                            var quoteLink = full.no_quote
                                ? `<a href="${quoteHref}" class="small fw-bold text-primary d-block mb-1 text-decoration-none">
                                     <i class="mdi mdi-file-outline me-1"></i>${full.no_quote}
                                   </a>`
                                : "";
                            var poBadge = `<span class="badge bg-label-success rounded-pill px-2 py-1 small">
                                             <i class="mdi mdi-cart-check me-1"></i>Done PO
                                           </span>`;
                            var nettVal = full.nett
                                ? `<div class="small fw-bold text-success mt-1">Rp ${Number(full.nett).toLocaleString("id-ID")}</div>`
                                : "";

                            return `<div class="d-flex flex-column gap-1 py-1">${quoteLink}${poBadge}${nettVal}</div>`;
                        }
                        return data;
                    }
                },
                {
                    targets: 7, orderable: false, searchable: false, responsivePriority: 1, className: "text-center",
                    render: function (data, type, full) {
                        var quoteHref = full.is_smart ? "/smart-quote/" + full.quotation_id : "/quotation/" + full.quotation_id;
                        return `
                            <div class="d-flex align-items-center gap-1 justify-content-center">
                                <a href="${quoteHref}" class="btn btn-sm btn-label-success waves-effect py-1 px-2 text-nowrap fw-semibold" data-bs-toggle="tooltip" title="Lihat PO / Quote">
                                    <i class="mdi mdi-file-check-outline me-1"></i>Lihat PO
                                </a>
                                <a href="/prospect/${full.id}" class="btn btn-icon btn-sm btn-outline-secondary rounded-pill waves-effect" data-bs-toggle="tooltip" title="Detail Prospect">
                                    <i class="mdi mdi-eye-outline"></i>
                                </a>
                            </div>
                        `;
                    }
                }
            ],
            order: [[1, "desc"]],
            dom: commonDom,
            displayLength: 10,
            lengthMenu: [10, 25, 50, 100],
            buttons: getExportButtons(),
            drawCallback: function () { $('[data-bs-toggle="tooltip"]').tooltip(); }
        });
    }

    // Auto recalculate DataTable columns when switching tabs
    $('button[data-bs-toggle="tab"], a[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
    });
});
