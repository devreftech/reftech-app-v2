$(function () {
    var dt_table_overview_call = $(".datatable-overview-call");
    var Url = "/db/overview/call/";
    var path = window.location.pathname;
    var segments = path.split("/").filter(function (s) { return s.length > 0; });

    var sales = segments[segments.length - 2];
    var dateRep = segments[segments.length - 1];
    var targetUrl = dt_table_overview_call.data("url") || (Url + sales + "/" + dateRep);

    function formatDate(rawDate) {
        if (!rawDate) return "-";
        if (typeof moment === "function") {
            var m = moment(rawDate);
            if (m.isValid()) return m.format("DD-MM-YYYY");
        }
        var parts = rawDate.split(" ")[0].split("-");
        if (parts.length === 3) {
            return parts[2] + "-" + parts[1] + "-" + parts[0];
        }
        return rawDate;
    }

    function renderStatusBadge(status) {
        var str = (status || "").toLowerCase().trim();
        if (str.includes("not") || str.includes("no") || str.includes("un") || str === "0" || str === "loss") {
            return '<span class="badge bg-label-danger rounded-pill"><i class="mdi mdi-close-circle-outline me-1"></i>Not Respon</span>';
        } else if (str.includes("respon") || str === "1" || str === "done" || str === "success") {
            return '<span class="badge bg-label-success rounded-pill"><i class="mdi mdi-check-circle-outline me-1"></i>Responded</span>';
        } else {
            return '<span class="badge bg-label-secondary rounded-pill">' + (status || "-") + "</span>";
        }
    }

    function formatMinimalistList(d) {
        var history = d.history || [];
        var itemsHtml = "";

        if (history.length === 0) {
            itemsHtml = '<div class="text-center text-muted py-3 fst-italic">Belum ada riwayat aktivitas detail.</div>';
        } else {
            history.forEach(function (item) {
                var actName = item.name || "Daily Call";
                var isResponded = (item.status || "").toLowerCase().includes("respon") && !(item.status || "").toLowerCase().includes("not");
                var feedClass = isResponded ? "feed-success" : "feed-danger";
                var avatarClass = isResponded ? "bg-label-success text-success" : "bg-label-danger text-danger";
                var iconName = isResponded ? "mdi-phone-check" : "mdi-phone-missed";
                var badgeClass = actName.toLowerCase().includes("follow") ? "bg-label-info" : "bg-label-primary";

                itemsHtml += 
                    '<div class="minimal-feed-item ' + feedClass + ' d-flex align-items-start gap-3 flex-wrap flex-md-nowrap">' +
                        '<div class="minimal-feed-avatar ' + avatarClass + ' flex-shrink-0">' +
                            '<i class="mdi ' + iconName + ' fs-5"></i>' +
                        '</div>' +
                        '<div class="flex-grow-1 min-w-0">' +
                            '<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">' +
                                '<div class="d-flex align-items-center gap-2 flex-wrap">' +
                                    '<span class="fw-bold text-dark"><i class="mdi mdi-calendar-outline me-1 text-muted"></i>' + formatDate(item.date) + '</span>' +
                                    '<span class="badge ' + badgeClass + ' rounded-pill fs-tiny">' + actName + '</span>' +
                                    (item.week ? '<span class="badge bg-label-secondary rounded-pill fs-tiny">Minggu ' + item.week + '</span>' : '') +
                                '</div>' +
                                '<div>' + renderStatusBadge(item.status) + '</div>' +
                            '</div>' +
                            '<div class="minimal-note-text bg-light p-2 rounded-2 mt-1">' +
                                '<i class="mdi mdi-message-text-outline text-muted me-1"></i>' +
                                (item.note || '<span class="text-muted fst-italic">Tanpa catatan</span>') +
                            '</div>' +
                        '</div>' +
                    '</div>';
            });
        }

        var clientRoute = d.client_id ? "/client/" + d.client_id : "#";
        var totalCount = history.length || 1;

        return (
            '<div class="p-3 my-2 rounded-3 bg-light border shadow-xs">' +
                '<div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">' +
                    '<div class="d-flex align-items-center gap-2">' +
                        '<div class="avatar avatar-sm">' +
                            '<div class="avatar-initial bg-label-primary rounded-circle">' +
                                '<i class="mdi mdi-format-list-bulleted-type mdi-18px"></i>' +
                            '</div>' +
                        '</div>' +
                        '<div>' +
                            '<span class="text-muted fs-tiny fw-semibold text-uppercase d-block">Riwayat Aktivitas (Minimalist List)</span>' +
                            '<h6 class="mb-0 fw-bold text-dark">' + (d.company || "-") + '</h6>' +
                        '</div>' +
                    '</div>' +
                    '<div class="d-flex align-items-center gap-2">' +
                        '<span class="badge bg-primary rounded-pill px-3 py-1 fw-semibold"><i class="mdi mdi-phone-in-talk me-1"></i>' + totalCount + ' Total Call / Follow Up</span>' +
                        (d.client_id ? '<a href="' + clientRoute + '" class="btn btn-xs btn-outline-primary rounded-pill px-2" target="_blank"><i class="mdi mdi-open-in-new me-1"></i>Detail Klien</a>' : '') +
                    '</div>' +
                '</div>' +
                '<div class="d-flex flex-column">' +
                    itemsHtml +
                '</div>' +
            '</div>'
        );
    }

    if (dt_table_overview_call.length) {
        var dt_call = dt_table_overview_call.DataTable({
            ajax: {
                type: "GET",
                url: targetUrl,
                headers: {
                    "Content-Type": "application/json",
                },
                error: function (error) {
                    console.error("Error:", error);
                },
            },
            columns: [
                {
                    className: "dt-control text-center",
                    orderable: false,
                    searchable: false,
                    data: null,
                    defaultContent: '<button type="button" class="dt-control-btn shadow-none" data-bs-toggle="tooltip" title="Lihat Riwayat Interaksi"><i class="mdi mdi-plus fs-5"></i></button>',
                },
                { data: "id", defaultContent: "-" },
                {
                    data: "company",
                    render: function (data, type, full) {
                        if (type === "display") {
                            var clientRoute = full["client_id"] ? "/client/" + full["client_id"] : "#";
                            var roleBadge = full["role"] === "Customers" ? ' <span class="badge bg-label-success fs-tiny py-0 px-1">Customer</span>' : ' <span class="badge bg-label-info fs-tiny py-0 px-1">Leads</span>';
                            return '<div class="fw-bold text-dark d-flex align-items-center gap-1"><a href="' + clientRoute + '" class="text-dark fw-bold">' + (data || "-") + '</a>' + roleBadge + '</div>';
                        }
                        return data || "-";
                    },
                },
                {
                    data: "total_activities",
                    className: "text-center",
                    render: function (data, type, full) {
                        if (type === "display") {
                            var count = data || (full.history ? full.history.length : 1);
                            var color = count > 1 ? "bg-label-primary" : "bg-label-secondary";
                            return '<span class="badge ' + color + ' rounded-pill fw-semibold px-2 py-1"><i class="mdi mdi-phone-in-talk-outline me-1"></i>' + count + 'x Call</span>';
                        }
                        return data || 1;
                    },
                },
                {
                    data: "latest_date",
                    className: "text-nowrap",
                    render: function (data, type) {
                        if (type === "display" || type === "filter") {
                            return formatDate(data);
                        }
                        return data || "-";
                    },
                },
                {
                    data: "latest_status",
                    className: "text-center",
                    render: function (data, type) {
                        if (type === "display") {
                            return renderStatusBadge(data);
                        }
                        return data || "-";
                    },
                },
                {
                    data: "latest_note",
                    render: function (data, type) {
                        if (type === "display") {
                            return data ? '<span class="text-truncate d-inline-block text-secondary" style="max-width: 260px;" data-bs-toggle="tooltip" title="' + data.replace(/"/g, '&quot;') + '">' + data + '</span>' : '<span class="text-muted fst-italic">-</span>';
                        }
                        return data || "-";
                    },
                },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    responsivePriority: 1,
                },
                {
                    targets: 1,
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 2,
                    responsivePriority: 1,
                },
                {
                    targets: 3,
                    responsivePriority: 2,
                },
                {
                    targets: 4,
                    responsivePriority: 2,
                },
            ],
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            order: [[4, "desc"]],
            dom: '<"row p-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row p-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            language: {
                paginate: {
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                },
                emptyTable: "Tidak ada data daily call",
            },
        });

        // Add event listener for opening and closing details
        dt_table_overview_call.find("tbody").on("click", "td.dt-control", function () {
            var tr = $(this).closest("tr");
            var row = dt_call.row(tr);
            var btn = $(this).find("button");
            var icon = $(this).find("i");

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass("shown");
                icon.removeClass("mdi-minus").addClass("mdi-plus");
                btn.attr("title", "Lihat Riwayat Interaksi").tooltip("dispose").tooltip();
            } else {
                row.child(formatMinimalistList(row.data())).show();
                tr.addClass("shown");
                icon.removeClass("mdi-plus").addClass("mdi-minus");
                btn.attr("title", "Tutup Riwayat Interaksi").tooltip("dispose").tooltip();
            }
        });
    }
});
