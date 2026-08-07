$(function () {
    var $table = $(".datatable-helpdesk-admin");
    if (!$table.length) return;

    function statusBadge(status) {
        var cls = {
            Open: "bg-label-danger",
            "In Progress": "bg-label-warning",
            Resolved: "bg-label-success",
        }[status] || "bg-label-secondary";
        return '<span class="badge ' + cls + '">' + status + "</span>";
    }

    function dateCol(data) {
        if (!data) return "-";
        var m = moment(data);
        return m.isValid() ? m.format("DD-MM-YYYY HH:mm") : data;
    }

    var dt = $table.DataTable({
        ajax: {
            type: "GET",
            url: "/db/helpdesk/admin",
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
                    return '<a href="javascript:;" class="fw-semibold text-primary button-helpdesk-view">' + data + "</a>";
                },
            },
            {
                targets: 1,
                className: "text-center",
                orderable: false,
                render: function (data, type, full) {
                    if (type !== "display") return full.name || "";
                    var name = full.name || "-";
                    var initials = name.split(" ").map(function (w) { return w.charAt(0); }).slice(0, 2).join("").toUpperCase();
                    var colors = ["bg-label-primary", "bg-label-success", "bg-label-warning", "bg-label-danger", "bg-label-info", "bg-label-secondary"];
                    var colorClass = colors[name.charCodeAt(0) % colors.length];
                    var av = data
                        ? '<img src="/' + data + '" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;" alt="' + name + '">'
                        : '<div class="avatar-initial rounded-circle ' + colorClass + '" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:11px;font-weight:700;">' + initials + '</div>';
                    return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + name + '">' + av + '</span>';
                },
            },
            { targets: 3, render: function (data) { return statusBadge(data); } },
            { targets: 4, render: function (data) { return dateCol(data); } },
            {
                targets: 5,
                orderable: false,
                searchable: false,
                render: function () {
                    return '<a href="javascript:;" class="btn btn-sm btn-icon btn-text-secondary waves-effect waves-light rounded-pill button-helpdesk-view">' +
                        '<i class="menu-icon tf-icons mdi mdi-text-box-outline mdi-20px"></i></a>';
                },
            },
        ],
        order: [[4, "desc"]],
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        dom:
            '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
            '<"table-responsive"t>' +
            '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { emptyTable: "Belum ada tiket." },
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
    });

    $table.on("click", "tbody tr .button-helpdesk-view", function () {
        var data = dt.row($(this).closest("tr")).data();
        $("#detailHelpdesk").attr("data-id", data.id);
        $("#detailHelpdeskNoTicket").text(data.no_ticket);
        $("#detailHelpdeskRequester").text(data.name);
        $("#detailHelpdeskTitle").text(data.title);
        $("#detailHelpdeskStatus").html(statusBadge(data.status));
        $("#detailHelpdeskDate").text(dateCol(data.created_at));
        $("#detailHelpdeskDescription").text(data.description);
        if (data.resolution_note) {
            $("#detailHelpdeskResolutionNote").text(data.resolution_note);
            $("#detailHelpdeskResolutionWrapper").removeClass("d-none");
        } else {
            $("#detailHelpdeskResolutionWrapper").addClass("d-none");
        }
        var modal = new bootstrap.Modal(document.getElementById("detailHelpdesk"));
        modal.show();
    });

    // Status-change handler (needs csrf_token()) lives inline in
    // pages/helpdesk/index.blade.php's @push('script') block, not here,
    // since this file is a plain static asset without Blade access.
});
