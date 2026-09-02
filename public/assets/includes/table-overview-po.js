$(function () {
    var dt_table_overview_po = $(".datatable-overview-po");
    var Url = "/db/overview/po/";
    var path = window.location.pathname;
    var segments = path.split("/").filter(function(s) { return s.length > 0; });

    var sales = segments[segments.length - 2];
    var dateRep = segments[segments.length - 1];
    var targetUrl = dt_table_overview_po.data("url") || (Url + sales + "/" + dateRep);

    if (dt_table_overview_po.length) {
        var dt_po = dt_table_overview_po.DataTable({
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
                { data: null, defaultContent: "" },
                { data: "id", defaultContent: "-" },
                { data: "no_quote", defaultContent: "-" },
                { data: "company", defaultContent: "-" },
                { data: "title", defaultContent: "-" },
                { data: "po_date", defaultContent: "-" },
                { data: "nett", defaultContent: 0 },
            ],
            columnDefs: [
                {
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
                    responsivePriority: 1,
                    targets: 2,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var $dataId = full["id"];
                            var detailRoute = full["is_smart"] ? "/smart-quote/" + $dataId : (typeof route === "function" ? route("quotation.show", $dataId) : "/quotation/" + $dataId);
                            var smartBadge = full["is_smart"] ? ' <span class="badge bg-label-primary fs-tiny py-0 px-1">Smart</span>' : '';
                            return '<a class="text-dark fw-semibold" href="' + detailRoute + '">' + (data || "-") + smartBadge + "</a>";
                        }
                        return data || "-";
                    },
                },
                {
                    className: "text-nowrap",
                    targets: 5,
                },
                {
                    targets: 6,
                    render: function (data, type) {
                        if (type === "display" && data !== null && data !== undefined) {
                            return "Rp " + Number(data).toLocaleString("id-ID");
                        }
                        return data || "Rp 0";
                    },
                },
            ],
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            order: [[1, "desc"]],
            dom: '<"row p-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row p-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            language: {
                paginate: {
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                },
                emptyTable: "Tidak ada data purchase order",
            },
        });
    }
});
