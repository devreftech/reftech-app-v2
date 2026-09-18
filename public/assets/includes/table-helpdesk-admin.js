$(function () {
    function statusBadge(status) {
        var map = {
            Open: {
                cls: "bg-label-danger",
                dot: "bg-danger",
            },
            "In Progress": {
                cls: "bg-label-warning",
                dot: "bg-warning",
            },
            Resolved: {
                cls: "bg-label-success",
                dot: "bg-success",
            },
        };
        var item = map[status] || {
            cls: "bg-label-secondary",
            dot: "bg-secondary",
        };
        return (
            '<span class="badge ' +
            item.cls +
            ' rounded-pill py-1 px-2 d-inline-flex align-items-center gap-1 fw-semibold" style="font-size:0.75rem;"><span class="helpdesk-stat-dot ' +
            item.dot +
            '"></span>' +
            status +
            "</span>"
        );
    }

    function dateCol(data) {
        if (!data) return "-";
        var m = moment(data);
        return m.isValid()
            ? '<span class="text-nowrap small text-muted"><i class="mdi mdi-clock-outline me-1"></i>' +
                  m.format("DD-MM-YYYY HH:mm") +
                  "</span>"
            : data;
    }

    function initHelpdeskTable(selector, ajaxUrl) {
        var $table = $(selector);
        if (!$table.length) return;

        var dt = $table.DataTable({
            ajax: {
                type: "GET",
                url: ajaxUrl,
                headers: { "Content-Type": "application/json" },
            },
            columns: [
                { data: "no_ticket" },
                { data: "user_image" },
                { data: "title" },
                { data: "status" },
                { data: "created_at" },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data) {
                        return (
                            '<a href="javascript:;" class="fw-bold font-monospace text-primary button-helpdesk-view d-inline-flex align-items-center gap-1">' +
                            '<i class="mdi mdi-ticket-outline fs-6"></i>' +
                            data +
                            "</a>"
                        );
                    },
                },
                {
                    targets: 1,
                    orderable: false,
                    render: function (data, type, full) {
                        var name = full.name || "System/Guest";
                        if (type !== "display") return name;
                        var initials = name
                            .split(" ")
                            .map(function (w) {
                                return w.charAt(0);
                            })
                            .slice(0, 2)
                            .join("")
                            .toUpperCase();
                        var colors = [
                            "bg-label-primary",
                            "bg-label-success",
                            "bg-label-warning",
                            "bg-label-danger",
                            "bg-label-info",
                            "bg-label-secondary",
                        ];
                        var colorClass = colors[name.charCodeAt(0) % colors.length];
                        var av = data
                            ? '<img src="/' +
                              data +
                              '" class="rounded-circle flex-shrink-0" style="width:28px;height:28px;object-fit:cover;" alt="' +
                              name +
                              '">'
                            : '<div class="avatar-initial rounded-circle flex-shrink-0 ' +
                              colorClass +
                              '" style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;font-size:10px;font-weight:700;">' +
                              initials +
                              "</div>";
                        return (
                            '<div class="d-flex align-items-center gap-2">' +
                            av +
                            '<span class="fw-medium text-dark text-truncate" style="max-width:130px;" title="' +
                            name +
                            '">' +
                            name +
                            "</span></div>"
                        );
                    },
                },
                {
                    targets: 2,
                    render: function (data) {
                        return '<span class="fw-semibold text-dark">' + (data || "-") + "</span>";
                    },
                },
                {
                    targets: 3,
                    render: function (data) {
                        return statusBadge(data);
                    },
                },
                {
                    targets: 4,
                    render: function (data, type) {
                        if (type === "sort" || type === "type") return data;
                        return dateCol(data);
                    },
                },
                {
                    targets: 5,
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function () {
                        return (
                            '<button type="button" class="btn btn-sm btn-label-primary waves-effect button-helpdesk-view px-2 py-1">' +
                            '<i class="mdi mdi-eye-outline me-1"></i>Detail</button>'
                        );
                    },
                },
            ],
            order: [[4, "desc"]],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom:
                '<"d-flex flex-column flex-md-row justify-content-between align-items-center mb-3 gap-2"<"d-flex align-items-center"l><"d-flex align-items-center"f>>' +
                '<"table-responsive"t>' +
                '<"d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2"<"small text-muted"i><"d-flex"p>>',
            language: {
                emptyTable: "Belum ada tiket.",
                search: "",
                searchPlaceholder: "Cari nomor tiket, judul...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ tiket",
                paginate: {
                    first: "«",
                    previous: "‹",
                    next: "›",
                    last: "»",
                },
            },
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
        });

        $table.on("click", "tbody tr .button-helpdesk-view", function () {
            var data = dt.row($(this).closest("tr")).data();
            if (!data) return;
            $("#detailHelpdesk").attr("data-id", data.id);
            $("#detailHelpdeskNoTicket").text(data.no_ticket);
            $("#detailHelpdeskRequester").text(data.name || "System/Guest");
            $("#detailHelpdeskTitle").text(data.title);
            $("#detailHelpdeskStatus").html(statusBadge(data.status));
            $("#detailHelpdeskDate").html(dateCol(data.created_at));
            $("#detailHelpdeskDescription").text(data.description);
            if (data.url_accessed) {
                var u = data.url_accessed.trim();
                var href =
                    u.indexOf("http://") === 0 || u.indexOf("https://") === 0
                        ? u
                        : u.indexOf("/") === 0
                        ? u
                        : "/" + u;
                $("#detailHelpdeskUrl").attr("href", href);
                $("#detailHelpdeskUrlText").text(u);
                $("#detailHelpdeskUrlWrapper").removeClass("d-none");
            } else {
                $("#detailHelpdeskUrlWrapper").addClass("d-none");
            }
            if (data.resolution_note) {
                $("#detailHelpdeskResolutionNote").text(data.resolution_note);
                $("#detailHelpdeskResolutionWrapper").removeClass("d-none");
            } else {
                $("#detailHelpdeskResolutionWrapper").addClass("d-none");
            }
            var modal = new bootstrap.Modal(document.getElementById("detailHelpdesk"));
            modal.show();
        });
    }

    initHelpdeskTable(".datatable-helpdesk-admin", "/db/helpdesk/admin?category=user_report");
    initHelpdeskTable(".datatable-helpdesk-system-errors", "/db/helpdesk/admin?category=system_error");
});
