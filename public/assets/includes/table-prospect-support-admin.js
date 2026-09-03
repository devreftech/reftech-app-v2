$(function () {
    var dt_table_prospect = $(".datatable-prospect-admin");
    var Url = "/db/prospect/admin";

    if (dt_table_prospect.length) {
        var dt_prospect = dt_table_prospect.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
                data: function (d) {
                    d.sales_id = window.prospectSalesFilter || "";
                    d.year = window.prospectYearFilter || "";
                    return d;
                },
            },
            columns: [
                { data: "company" },
                { data: "category" },
                { data: "date" },
                { data: "support" },
                { data: "sales" },
                { data: "status" },
                { data: "id" },
            ],
            columnDefs: [
                {
                    // Company & PIC
                    targets: 0,
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
                    targets: 1,
                    responsivePriority: 3,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var category = full.category ? full.category : "General";
                            var kebutuhan = full.kebutuhan ? full.kebutuhan : "—";
                            var escapedKebutuhan = $("<div>").text(kebutuhan).html();

                            return `
                                <div class="d-flex flex-column gap-1 py-1" style="max-width: 260px;">
                                    <span class="badge bg-label-primary rounded-pill align-self-start fw-semibold" style="font-size: 0.72rem;">
                                        ${category}
                                    </span>
                                    <p class="mb-0 text-muted small text-truncate" style="max-width: 250px;" data-bs-toggle="tooltip" title="${escapedKebutuhan}">
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
                    targets: 2,
                    responsivePriority: 4,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var dateVal = full.date ? moment(full.date).format("DD MMM YYYY") : "—";
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
                    targets: 3,
                    responsivePriority: 5,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var suppName = full.support ? full.support : "Marketing";
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
                    // Assigned Sales
                    targets: 4,
                    responsivePriority: 3,
                    render: function (data, type, full) {
                        if (type === "display") {
                            if (full.sales) {
                                var salesName = full.sales;
                                var initial = salesName.charAt(0).toUpperCase();
                                var avatarHtml = full.sales_image
                                    ? `<img src="/${full.sales_image}" class="rounded-circle shadow-xs" width="30" height="30" style="object-fit:cover;" onerror="this.outerHTML='<span class=\\'avatar-initial rounded-circle bg-label-primary small\\'>${initial}</span>'">`
                                    : `<span class="avatar-initial rounded-circle bg-label-primary small fw-bold">${initial}</span>`;

                                return `
                                    <div class="d-flex align-items-center gap-2 py-1">
                                        <div class="avatar avatar-sm flex-shrink-0">
                                            ${avatarHtml}
                                        </div>
                                        <span class="small fw-semibold text-heading text-truncate" style="max-width: 110px;">
                                            ${salesName}
                                        </span>
                                    </div>
                                `;
                            } else {
                                return `<span class="badge bg-label-secondary rounded-pill small">Unassigned</span>`;
                            }
                        }
                        return data;
                    },
                },
                {
                    // Quotation Status & Value
                    targets: 5,
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
                                // Smart Quote (unit_quotation) statuses
                                draft:        { label: "Draft", color: "secondary", icon: "mdi-file-outline" },
                                sent:         { label: "Sent", color: "info", icon: "mdi-email-outline" },
                                negotiation:  { label: "Negotiation", color: "warning", icon: "mdi-handshake" },
                                revision:     { label: "Revisi", color: "primary", icon: "mdi-file-document-edit-outline" },
                                hot_prospect: { label: "Hot Prospect", color: "danger", icon: "mdi-fire" },
                                po_received:  { label: "PO Received", color: "success", icon: "mdi-cart-check" },
                                loss:         { label: "Loss", color: "dark", icon: "mdi-close-circle-outline" },
                                cancel:       { label: "Cancel", color: "dark", icon: "mdi-cancel" },
                            };

                            if (full.quotation_id || full.status !== null) {
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
                    },
                },
                {
                    // Provide Status & Actions
                    targets: 6,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 1,
                    className: "text-center",
                    render: function (data, type, full) {
                        if (type === "display") {
                            var provideBadge = full.provide === '1' || full.provide === 1
                                ? `<span class="badge bg-label-success rounded-pill small py-1 px-2 mb-1"><i class="mdi mdi-check-circle-outline me-1"></i>Provided</span>`
                                : (full.provide === '0' || full.provide === 0
                                    ? `<span class="badge bg-label-danger rounded-pill small py-1 px-2 mb-1"><i class="mdi mdi-close-circle-outline me-1"></i>No Provide</span>`
                                    : `<span class="badge bg-label-warning rounded-pill small py-1 px-2 mb-1"><i class="mdi mdi-help-circle-outline me-1"></i>Pending</span>`);

                            return `
                                <div class="d-flex flex-column align-items-center gap-1 justify-content-center">
                                    ${provideBadge}
                                    <a href="/prospect/${full.id}" class="btn btn-icon btn-sm btn-outline-primary waves-effect rounded-pill" data-bs-toggle="tooltip" title="Lihat Detail Prospek">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </a>
                                </div>
                            `;
                        }
                        return data;
                    },
                },
            ],
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            order: [[2, "desc"]],
            dom: '<"card-header flex-column flex-md-row d-flex justify-content-between align-items-center py-3"<"head-label hl-admin"><"d-flex align-items-center gap-2"f>>t<"card-footer d-flex flex-column flex-md-row justify-content-between align-items-center py-3"<"small text-muted"i><"pagination-wrapper"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "",
                searchPlaceholder: "Cari prospek admin...",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ prospek",
                paginate: {
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                },
            },
        });

        $("div.hl-admin").html(
            '<div class="d-flex align-items-center gap-2">' +
                '<div class="avatar avatar-sm bg-label-primary rounded p-1">' +
                    '<i class="mdi mdi-account-group-outline fs-4"></i>' +
                '</div>' +
                '<div>' +
                    '<h6 class="fw-bold mb-0 text-heading">Daftar Prospek Marketing (All Sales)</h6>' +
                    '<small class="text-muted">Kelola seluruh leads prospek masuk dan status penugasan</small>' +
                '</div>' +
            '</div>'
        );

        // Filter events
        $("#prospect-sales-filter").on("change", function () {
            window.prospectSalesFilter = $(this).val();
            dt_prospect.ajax.reload();
        });

        $("#prospect-year-filter").on("change", function () {
            window.prospectYearFilter = $(this).val();
            dt_prospect.ajax.reload();
        });
    }
});
