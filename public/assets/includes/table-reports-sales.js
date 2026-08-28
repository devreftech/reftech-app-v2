$(function () {
    var dt_table_reports_sales = $(".datatable-reports-sales");
    var Url = "/db/service-reports/sales";

    if (dt_table_reports_sales.length) {
        var dt_reports_sales = dt_table_reports_sales.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: null, defaultContent: "" },
                { data: "id" },
                { data: "no_service", defaultContent: "-" },
                { data: "company", defaultContent: "-" },
                { data: "jobdesc", defaultContent: "-" },
                { data: "brand_type", defaultContent: "-" },
                { data: "serial_tag", defaultContent: "-" },
                { data: "date", defaultContent: "-" },
                { data: "name", defaultContent: "-" },
                { data: "approval_status", defaultContent: "-" },
            ],
            columnDefs: [
                {
                    // For Responsive
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
                    targets: 1,
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 2,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var detailRoute = route("service-reports.show", full["id"]);
                            return (
                                '<a class="view-report text-primary fw-semibold" data-id="' +
                                full["id"] +
                                '" href="' +
                                detailRoute +
                                '">' +
                                data +
                                "</a>"
                            );
                        }
                        return data;
                    },
                },
                {
                    responsivePriority: 2,
                    targets: [2, 3],
                },
                {
                    targets: 7,
                    render: function (data, type) {
                        if (type === "display") {
                            return data ? moment(data).format("DD-MM-YYYY") : "-";
                        }
                        return data;
                    },
                },
                {
                    targets: 9,
                    className: "text-center",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var map = {
                            pending: ["warning", "Menunggu Approval"],
                            approved: ["success", "Disetujui"],
                            rejected: ["danger", "Ditolak"],
                        };
                        var m = map[data] || ["secondary", data || "-"];
                        var badge = '<span class="badge bg-label-' + m[0] + '">' + m[1] + "</span>";
                        if (data === "rejected" && full["reject_note"]) {
                            badge +=
                                '<div class="small text-danger mt-1">' +
                                $("<div>").text(full["reject_note"]).html() +
                                "</div>";
                        }
                        return badge;
                    },
                },
            ],
            order: [[1, "desc"]],
            dom: '<"card-header flex-column flex-md-row"<"head-label hl-1 text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            buttons: [
                {
                    extend: "collection",
                    className: "btn btn-label-primary dropdown-toggle me-2",
                    text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                    buttons: [
                        {
                            extend: "print",
                            text: '<i class="mdi mdi-printer-outline me-1"></i>Print',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9] },
                        },
                        {
                            extend: "csv",
                            text: '<i class="mdi mdi-file-document-outline me-1"></i>Csv',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9] },
                        },
                        {
                            extend: "excel",
                            text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9] },
                        },
                        {
                            extend: "pdf",
                            text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9] },
                        },
                        {
                            extend: "copy",
                            text: '<i class="mdi mdi-content-copy me-1"></i>Copy',
                            className: "dropdown-item",
                            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9] },
                        },
                    ],
                },
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return "Details of " + (data ? data["no_service"] : "");
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col) {
                            return col.title !== ""
                                ? '<tr data-dt-row="' +
                                      col.rowIndex +
                                      '" data-dt-column="' +
                                      col.columnIndex +
                                      '">' +
                                      "<td>" +
                                      col.title +
                                      ":</td> " +
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

        $("div.hl-1").html('<h5 class="card-title mb-0">Table Service Reports</h5>');
    }
});
