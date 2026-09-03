$(function () {
    var dt_table_reports = $(".datatable-reports");
    var Url = "/db/reports";

    if (dt_table_reports.length) {
        var dt_reports = dt_table_reports.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
                data: function (d) {
                    d.year = $("#sr-tech-year-filter").val() || new Date().getFullYear();
                },
            },
            columns: [
                { data: null, defaultContent: '' },
                { data: "id" },
                { data: "no_service" },
                { data: "company", defaultContent: '-' },
                { data: "jobdesc", defaultContent: '-' },
                { data: "brand_type", defaultContent: '-' },
                { data: "serial_tag", defaultContent: '-' },
                { data: "date", defaultContent: '-' },
                { data: "approval_status", defaultContent: '-' },
            ],
            columnDefs: [
                {
                    // For Responsive
                    className: "control",
                    orderable: false,
                    searchable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return "";
                    },
                },
                {
                    targets: 1,
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 2,
                    className: "text-nowrap",
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $dataId = full["id"];
                            var detailRoute = route("service-reports.show", $dataId);
                            var displayText = data || "-";
                            if (data) {
                                var parts = data.split("/");
                                if (parts.length >= 2) {
                                    var numPart = parts[0];
                                    var codePart = parts[1];
                                    var codeSub = codePart.split("-");
                                    var initial = codeSub.length > 1 ? codeSub.slice(1).join("-") : codePart;
                                    displayText = numPart + " / " + initial;
                                }
                            }
                            return (
                                '<a class="text-primary fw-semibold" href="' + detailRoute + '" title="' + (data || '') + '">' + displayText + "</a>"
                            );
                        }
                        return data;
                    },
                },
                {
                    responsivePriority: 2,
                    targets: [2,3],
                },
                {
                    targets: 6,
                    render: function (data, type, full, meta) {
                        if (type === "display") {
                            return data && data.length ? data : '-';
                        }
                        return data;
                    },
                },
                {
                    targets: 7,
                    className: "text-nowrap",
                    render: function (data, type) {
                        if (type === "display") {
                            return data ? moment(data).format("DD-MM-YYYY") : "-";
                        }
                        return data;
                    },
                },
                {
                    targets: 8,
                    render: function (data, type, full, meta) {
                        if (type !== "display") return data;
                        var map = {
                            pending: ["warning", "Menunggu Approval"],
                            approved: ["success", "Disetujui"],
                            rejected: ["danger", "Ditolak"],
                        };
                        var m = map[data] || ["secondary", data || "-"];
                        var badge = '<span class="badge bg-label-' + m[0] + '">' + m[1] + "</span>";
                        if (data === "rejected" && full["reject_note"]) {
                            badge += '<div class="small text-danger mt-1">' +
                                $("<div>").text(full["reject_note"]).html() + "</div>";
                        }
                        return badge;
                    },
                },
            ],
            order: [[1, "desc"]],
            dom: '<"card-header flex-column flex-md-row align-items-start align-items-md-center justify-content-between"<"head-label hl-1 text-center"><"d-flex align-items-center gap-2 flex-wrap pt-3 pt-md-0"<"dt-year-filter-tech"><"dt-action-buttons text-end"B>>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            buttons: [
                {
                    text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">New reports</span>',
                    className: "btn btn-primary btn-new",
                    action: function (e, dt, node, config) {
                        window.location = route("service-reports.create");
                    },
                },
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return "Details of " + data["full_name"];
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.title !== "" // ? Do not show row in modal popup if title is blank (for check box)
                                ? '<tr data-dt-row="' +
                                      col.rowIndex +
                                      '" data-dt-column="' +
                                      col.columnIndex +
                                      '">' +
                                      "<td>" +
                                      col.title +
                                      ":" +
                                      "</td> " +
                                      "<td>" +
                                      col.data +
                                      "</td>" +
                                      "</tr>"
                                : "";
                        }).join("");

                        return data
                            ? $('<table class="table"/><tbody />').append(data)
                            : false;
                    },
                },
            },
        });
        $("div.hl-1").html(
            '<h5 class="card-title mb-0">Table Reports</h5>'
        );

        var currentYear = new Date().getFullYear();
        var yearOpts = '';
        for (var y = currentYear; y >= 2024; y--) {
            var selected = (y === currentYear) ? ' selected' : '';
            yearOpts += '<option value="' + y + '"' + selected + '>' + y + '</option>';
        }
        yearOpts += '<option value="all">Semua Tahun</option>';

        $("div.dt-year-filter-tech").html(
            '<div class="d-flex align-items-center gap-2 me-2">' +
            '<label class="form-label mb-0 fw-semibold small text-nowrap">Tahun</label>' +
            '<select class="form-select form-select-sm w-auto" id="sr-tech-year-filter">' + yearOpts + '</select>' +
            '</div>'
        );

        $("#sr-tech-year-filter").on("change", function () {
            dt_reports.ajax.reload();
        });
    }
});
