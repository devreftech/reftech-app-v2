$(function () {
    var dt_table = $(".datatable-quotation-prospect");
    var Url = "/db/quotation/prospect";

    if (dt_table.length) {
        var dt_prospect = dt_table.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
            },
            columns: [
                { data: "no_quote" },
                { data: "company" },
                { data: "harga_total" },
                { data: "title" },
                { data: "estimated_date" },
                { data: "status" },
                { data: "name" },
                { data: "id" },
            ],
            columnDefs: [
                {
                    // Quote No. & Title
                    targets: 0,
                    responsivePriority: 1,
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var url;
                        if (full["type"] === "Service") {
                            url = route("show-service.quotation", full["id"]);
                        } else if (full["type"] === "Overhaul") {
                            url = route("show-overhaul.quotation", full["id"]);
                        } else {
                            url = route("quotation.show", full["id"]);
                        }
                        var typeBadge = full["type"]
                            ? `<span class="badge bg-label-info rounded-pill small py-0 px-2 me-1">${full["type"]}</span>`
                            : "";
                        return `
                            <div class="d-flex flex-column gap-1 py-1">
                                <a class="fw-bold text-heading text-primary-hover" href="${url}" style="font-size: 0.95rem;">
                                    ${data || "-"}
                                </a>
                                <div>${typeBadge}<span class="text-muted small">${full["title"] || "Quotation Prospect"}</span></div>
                            </div>
                        `;
                    },
                },
                {
                    // Company & RU
                    targets: 1,
                    responsivePriority: 2,
                    className: "text-start",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var ru = full["ru"];
                        var ruBadge = ru === "User"
                            ? '<span class="badge rounded-pill bg-label-info py-0 px-2 me-1" data-bs-toggle="tooltip" title="End User">U</span>'
                            : (ru === "Reseller"
                                ? '<span class="badge rounded-pill bg-label-dark py-0 px-2 me-1" data-bs-toggle="tooltip" title="Reseller">R</span>'
                                : '');
                        return `
                            <div class="d-flex align-items-center gap-1 py-1">
                                ${ruBadge}
                                <span class="fw-semibold text-heading">${data || "—"}</span>
                            </div>
                        `;
                    },
                },
                {
                    // Total Amount
                    targets: 2,
                    responsivePriority: 3,
                    className: "text-end",
                    render: function (data, type) {
                        if (type !== "display") return data;
                        var formatted = parseInt(data || 0).toLocaleString("id-ID");
                        return `<span class="fw-bold text-success fs-6">Rp ${formatted}</span>`;
                    },
                },
                {
                    // Description / Notes
                    targets: 3,
                    className: "text-start",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var desc = data || full["note"] || "—";
                        var escaped = $("<div>").text(desc).html();
                        return `<span class="d-inline-block text-truncate text-muted small" style="max-width: 220px;" data-bs-toggle="tooltip" title="${escaped}">${escaped}</span>`;
                    },
                },
                {
                    // Target / Estimated Date
                    targets: 4,
                    responsivePriority: 4,
                    className: "text-start",
                    render: function (data, type) {
                        if (type !== "display") return data;
                        var formatted = data ? moment(data).format("DD MMM YYYY") : "—";
                        return `<span class="small fw-semibold text-heading"><i class="mdi mdi-calendar-blank-outline text-muted me-1"></i>${formatted}</span>`;
                    },
                },
                {
                    // Status Badge
                    targets: 5,
                    responsivePriority: 2,
                    className: "text-center",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var $status = {
                            20: { title: "Send WA/Email", pct: "20%", class: "bg-label-secondary", icon: "mdi-email-outline" },
                            30: { title: "Inquiry Accepted", pct: "30%", class: "bg-label-dark", icon: "mdi-check-all" },
                            40: { title: "Progress Follow Up", pct: "40%", class: "bg-label-info", icon: "mdi-progress-clock" },
                            60: { title: "Negotiation", pct: "60%", class: "bg-label-primary", icon: "mdi-handshake" },
                            80: { title: "Hot Prospect", pct: "80%", class: "bg-label-warning", icon: "mdi-fire" },
                        };
                        var s = $status[data];
                        if (!s) return data ? `<span class="badge bg-label-primary">${data}</span>` : "—";
                        return `<span class="badge rounded-pill ${s.class} py-1 px-3 fw-semibold"><i class="mdi ${s.icon} me-1"></i>${s.title} · ${s.pct}</span>`;
                    },
                },
                {
                    // Assigned Sales
                    targets: 6,
                    responsivePriority: 3,
                    className: "text-start",
                    render: function (data, type, full) {
                        if (type !== "display") return data || "";
                        var name = data || "-";
                        var initial = name.charAt(0).toUpperCase();
                        var avatarHtml = full["sales_image"]
                            ? `<img src="/${full["sales_image"]}" class="rounded-circle shadow-xs" width="28" height="28" style="object-fit:cover;" onerror="this.outerHTML='<span class=\\'avatar-initial rounded-circle bg-label-primary small\\'>${initial}</span>'">`
                            : `<span class="avatar-initial rounded-circle bg-label-primary small fw-bold" style="width:28px;height:28px;font-size:11px;">${initial}</span>`;
                        return `
                            <div class="d-flex align-items-center gap-2 py-1">
                                <div class="avatar avatar-xs flex-shrink-0">
                                    ${avatarHtml}
                                </div>
                                <span class="small fw-semibold text-heading text-truncate" style="max-width: 120px;">
                                    ${name}
                                </span>
                            </div>
                        `;
                    },
                },
                {
                    // Actions
                    targets: 7,
                    responsivePriority: 1,
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var url = full["type"] === "Service"
                            ? route("show-service.quotation", full["id"])
                            : (full["type"] === "Overhaul" ? route("show-overhaul.quotation", full["id"]) : route("quotation.show", full["id"]));
                        return `
                            <a href="${url}" class="btn btn-icon btn-sm btn-outline-primary waves-effect rounded-pill" data-bs-toggle="tooltip" title="Lihat Detail Quotation">
                                <i class="mdi mdi-eye-outline"></i>
                            </a>
                        `;
                    },
                },
            ],
            order: [[4, "asc"]],
            dom: '<"card-header flex-column flex-md-row d-flex justify-content-between align-items-center py-3"<"head-label hl-qp-admin"><"d-flex align-items-center gap-2"f>>t<"card-footer d-flex flex-column flex-md-row justify-content-between align-items-center py-3"<"small text-muted"i><"pagination-wrapper"p>>',
            lengthMenu: [10, 25, 50, 100],
            displayLength: 10,
            language: {
                search: "",
                searchPlaceholder: "Cari nomor quote / customer...",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ quotation",
                paginate: {
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                },
            },
        });

        $("div.hl-qp-admin").html(
            '<div class="d-flex align-items-center gap-2">' +
                '<div class="avatar avatar-sm bg-label-primary rounded p-1">' +
                    '<i class="mdi mdi-file-document-multiple-outline fs-4"></i>' +
                '</div>' +
                '<div>' +
                    '<h6 class="fw-bold mb-0 text-heading">Daftar Quotation Prospect (All Sales)</h6>' +
                    '<small class="text-muted">Daftar penawaran aktif hasil prospek marketing</small>' +
                '</div>' +
            '</div>'
        );

        dt_table.on("draw.dt", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    }
});
