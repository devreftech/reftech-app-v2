$(function () {
    var $table = $(".datatable-helpdesk");
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
            url: "/db/helpdesk",
            headers: { "Content-Type": "application/json" },
        },
        columns: [
            { data: "no_ticket" },
            { data: "title" },
            { data: "status" },
            { data: "created_at" },
        ],
        columnDefs: [
            {
                targets: 0,
                render: function (data) {
                    return '<a href="javascript:;" class="fw-semibold text-primary button-helpdesk-view">' + data + "</a>";
                },
            },
            { targets: 2, render: function (data) { return statusBadge(data); } },
            { targets: 3, render: function (data) { return dateCol(data); } },
        ],
        order: [[3, "desc"]],
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        dom:
            '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
            '<"table-responsive"t>' +
            '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { emptyTable: "Belum ada tiket." },
    });

    $table.on("click", "tbody tr .button-helpdesk-view", function () {
        var data = dt.row($(this).closest("tr")).data();
        $("#detailHelpdesk").attr("data-id", data.id);
        $("#detailHelpdeskNoTicket").text(data.no_ticket);
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
});
