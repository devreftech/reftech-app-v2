$(function () {
    var dt_table_prospect_sales = $(".datatable-prospect-sales");
    var Url = "/db/prospect/sales";

    if (dt_table_prospect_sales.length) {
        var dt_prospect_sales = dt_table_prospect_sales.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "id" }, // 0: responsive control
                { data: "id" }, // 1: hidden id
                { data: "company" }, // 2: company & pic
                { data: "kebutuhan" }, // 3: category & kebutuhan
                { data: "date" }, // 4: date & source
                { data: "support_name" }, // 5: marketing support
                { data: "status" }, // 6: quote status & value
                { data: "id" }, // 7: actions
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
                    // Hidden ID column for sorting
                    targets: 1,
                    searchable: true,
                    visible: false,
                },
                {
                    // Company & PIC
                    targets: 2,
                    responsivePriority: 1,
                    render: function (data, type, full) {
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
                    },
                },
                {
                    // Category & Kebutuhan
                    targets: 3,
                    responsivePriority: 3,
                    render: function (data, type, full) {
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
                    },
                },
                {
                    // Date & Source
                    targets: 4,
                    responsivePriority: 4,
                    render: function (data, type, full) {
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
                    },
                },
                {
                    // Marketing Support PIC
                    targets: 5,
                    responsivePriority: 5,
                    render: function (data, type, full) {
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
                    },
                },
                {
                    // Quotation Status & Value
                    targets: 6,
                    responsivePriority: 2,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var statusMap = {
                                100: { label: "Done PO", color: "success", icon: "mdi-cart-check" },
                                80:  { label: "Hot Prospect", color: "warning", icon: "mdi-fire" },
                                60:  { label: "Negotiation", color: "primary", icon: "mdi-handshake" },
                                40:  { label: "Progress FU", color: "info", icon: "mdi-progress-clock" },
                                30:  { label: "Inquiry Accepted", color: "dark", icon: "mdi-check-all" },
                                20:  { label: "Send WA/Email", color: "secondary", icon: "mdi-email-outline" },
                                0:   { label: "Loss", color: "danger", icon: "mdi-close-circle-outline" },
                            };

                            if (full.quotation_id || full.status !== null) {
                                var st = statusMap[full.status] || { label: "Quoted", color: "primary", icon: "mdi-file-document-outline" };
                                var quoteLink = full.no_quote
                                    ? `<a href="/unit-quotation/detail/${full.quotation_id}" class="small fw-bold text-primary d-block mb-1 text-decoration-none">
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
                    },
                },
                {
                    // Actions
                    targets: 7,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 1,
                    className: "text-center",
                    render: function (data, type, full) {
                        if (type === "display") {
                            if (full.quotation_id) {
                                return `
                                    <div class="d-flex align-items-center gap-1 justify-content-center">
                                        <a href="/unit-quotation/detail/${full.quotation_id}" class="btn btn-icon btn-sm btn-outline-primary waves-effect rounded-pill" data-bs-toggle="tooltip" title="Lihat Quotation">
                                            <i class="mdi mdi-file-document-outline"></i>
                                        </a>
                                        <a href="/prospect/${full.id}" class="btn btn-icon btn-sm btn-outline-secondary waves-effect rounded-pill" data-bs-toggle="tooltip" title="Detail Prospect">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                    </div>
                                `;
                            } else {
                                return `
                                    <div class="d-flex align-items-center gap-1 justify-content-center">
                                        <button type="button" class="btn btn-sm btn-primary waves-effect py-1 px-3 text-nowrap fw-semibold shadow-xs" id="withQuote" data-id="${full.id}">
                                            <i class="mdi mdi-lightning-bolt me-1"></i> Smart Quote
                                        </button>
                                        <a href="/prospect/${full.id}" class="btn btn-icon btn-sm btn-outline-secondary waves-effect rounded-pill" data-bs-toggle="tooltip" title="Detail Prospect">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                    </div>
                                `;
                            }
                        }
                        return data;
                    },
                },
            ],
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            order: [[1, "desc"]],
            dom: '<"card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between border-bottom pb-3"<"head-label-prospect hl-1"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row mx-2 my-2"<"col-sm-12 col-md-6 d-flex align-items-center"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row mx-2 my-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 100],
            buttons: [
                {
                    extend: "collection",
                    className: "btn btn-outline-secondary dropdown-toggle waves-effect shadow-xs",
                    text: '<i class="mdi mdi-export-variant me-1"></i> Export',
                    buttons: [
                        {
                            extend: "excel",
                            text: '<i class="mdi mdi-file-excel-outline me-1"></i> Excel',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6] },
                        },
                        {
                            extend: "csv",
                            text: '<i class="mdi mdi-file-document-outline me-1"></i> CSV',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6] },
                        },
                        {
                            extend: "print",
                            text: '<i class="mdi mdi-printer-outline me-1"></i> Print',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6] },
                        },
                    ],
                },
            ],
            responsive: {
                details: {
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col) {
                            return col.title !== ""
                                ? `<tr>
                                     <td class="fw-semibold">${col.title}:</td>
                                     <td>${col.data}</td>
                                   </tr>`
                                : "";
                        }).join("");

                        return data ? $('<table class="table table-sm mb-0"/><tbody />').append(data) : false;
                    },
                },
            },
        });

        $("div.hl-1").html(`
            <div>
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="mdi mdi-inbox-arrow-down-outline me-2 text-primary"></i>New Assigned Prospects
                </h5>
                <small class="text-muted">Prospek baru masuk dari tim Marketing yang siap difollow-up</small>
            </div>
        `);
    }

    dt_table_prospect_sales.on("draw", function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
});
