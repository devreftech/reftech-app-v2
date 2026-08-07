$(function () {
    var dt_table_quotation = $(".datatable-quotation");
    var Url = "db/quotation";

    if (dt_table_quotation.length) {
        dt_table_quotation.find("thead tr")
            .clone(true)
            .appendTo(dt_table_quotation.find("thead"));

        dt_table_quotation.find("thead tr:eq(1) th").each(function (i) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
            );
            $("input", this).on("keyup change", function () {
                if (dt_quotation.column(i).search() !== this.value) {
                    dt_quotation.column(i).search(this.value).draw();
                }
            });
        });

        var dt_quotation = dt_table_quotation.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
                data: function (d) { d.year = window.quotationYearFilter || 'all'; return d; },
            },
            columns: [
                { data: "no_quote" },
                { data: "company" },
                { data: "subtotal" },
                { data: "title" },
                { data: "estimated_date" },
                { data: "status" },
            ],
            columnDefs: [
                {
                    targets: [2, 3, 4, 5],
                    className: "text-center",
                },
                {
                    targets: 0,
                    className: "text-center text-nowrap",
                    width: "60px",
                },
                {
                    responsivePriority: 1,
                    targets: 0,
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var $dataId = full["id"];
                        var rowType = full["row_type"];
                        var qType   = full["type"];
                        var detailRoute;
                        if (rowType === "unit") {
                            detailRoute = "/unit-quotation/" + $dataId;
                        } else if (qType == "Sparepart") {
                            detailRoute = route("quotation.show", $dataId);
                        } else if (qType == "Service") {
                            detailRoute = route("show-service.quotation", $dataId);
                        } else {
                            detailRoute = route("show-overhaul.quotation", $dataId);
                        }
                        var full_no = data || "-";
                        var short   = full_no.length > 5 ? full_no.substring(0, 5) + "…" : full_no;
                        return '<a class="fw-bold text-primary" href="' + detailRoute + '"' +
                            ' data-bs-toggle="tooltip" data-bs-placement="top"' +
                            ' data-bs-custom-class="tooltip-quote-no" title="' + full_no + '">' +
                            short + "</a>";
                    },
                },
                {
                    targets: 1,
                    className: "text-start",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var ru = full["ru"];
                        var ruMap = {
                            User:     '<span class="badge rounded-pill bg-label-info me-1">U</span>',
                            Reseller: '<span class="badge rounded-pill bg-label-dark me-1">R</span>',
                        };
                        var badge = ruMap[ru] || "";
                        return badge + (data || "-");
                    },
                },
                {
                    targets: 2,
                    render: function (data, type) {
                        if (type !== "display") return data;
                        var formatted = parseInt(data).toLocaleString("id-ID");
                        return '<div class="d-flex justify-content-between px-2"><span>Rp.</span><span>' + formatted + "</span></div>";
                    },
                },
                {
                    targets: 3,
                    render: function (data) { return data || "-"; },
                },
                {
                    targets: 4,
                    render: function (data, type) {
                        if (type !== "display") return data;
                        return data ? moment(data).format("DD-MM-YYYY") : "-";
                    },
                },
                {
                    targets: 5,
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var tip = full["tip"];
                        var $status = {
                            20:    { title: "Send WA / Email",     pct: "20%",   class: "bg-label-secondary", colorTip: "tooltip-secondary" },
                            30:    { title: "Inquiry Accepted",    pct: "30%",   class: "bg-label-dark",      colorTip: "tooltip-dark" },
                            40:    { title: "Progress Follow Up",  pct: "40%",   class: "bg-label-info",      colorTip: "tooltip-info" },
                            60:    { title: "Negotiation / Revisi",pct: "60%",   class: "bg-label-primary",   colorTip: "tooltip-primary" },
                            80:    { title: "Hot Prospect",        pct: "80%",   class: "bg-label-warning",   colorTip: "tooltip-warning" },
                            90:    { title: "Hold",                pct: "Hold",  class: "bg-warning",         colorTip: "tooltip-warning" },
                            100:   { title: "Done PO",             pct: "100%",  class: "bg-label-success",   colorTip: "tooltip-success" },
                            0:     { title: "Loss",                pct: "0%",    class: "bg-label-danger",    colorTip: "tooltip-danger" },
                            draft:        { title: "Draft",        pct: null, class: "bg-label-secondary", colorTip: "" },
                            sent:         { title: "Sent",         pct: null, class: "bg-label-info",      colorTip: "" },
                            negotiation:  { title: "Negotiation",  pct: null, class: "bg-label-warning",   colorTip: "" },
                            revision:     { title: "Revisi",       pct: null, class: "bg-label-primary",   colorTip: "" },
                            hot_prospect: { title: "Hot Prospect", pct: null, class: "bg-label-warning",   colorTip: "" },
                            po_received:  { title: "PO Received",  pct: null, class: "bg-label-success",   colorTip: "" },
                            loss:         { title: "Loss",         pct: null, class: "bg-label-danger",    colorTip: "" },
                        };
                        var s = $status[data];
                        if (!s) return data;
                        var label = s.pct ? s.title + " · " + s.pct : s.title;
                        var tooltip = s.colorTip
                            ? ' data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="' + s.colorTip + '" title="' + (tip || "") + '"'
                            : "";
                        var badge = '<span class="badge rounded-pill ' + s.class + ' cursor-pointer"' + tooltip + '>' + label + "</span>";
                        if (full["row_type"] === "unit") {
                            badge += ' <span class="badge bg-label-info ms-1">Smart</span>';
                        }
                        return badge;
                    },
                },
            ],
            orderCellsTop: true,
            order: [],
            dom: '<"row align-items-center mb-2"<"col-auto"l><"col-auto ms-auto dt-btn-quotation">><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            displayLength: 10,
            initComplete: function () {
                $(".dt-btn-quotation").html(
                    '<div class="dropdown">' +
                        '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">' +
                            '<i class="mdi mdi-plus me-1"></i>Quotation' +
                        '</button>' +
                        '<ul class="dropdown-menu dropdown-menu-end">' +
                            '<li><a class="dropdown-item" href="' + route("create.quotation") + '">Quotation Parts</a></li>' +
                            '<li><a class="dropdown-item" href="' + route("create-service.quotation") + '">Quotation General</a></li>' +
                            '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#chooseMachine">Quotation Service</a></li>' +
                            '<li><a class="dropdown-item" href="' + route("unit-quotation.create") + '">Quotation Unit</a></li>' +
                        '</ul>' +
                    '</div>'
                );
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            return "Detail: " + row.data()["no_quote"];
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col) {
                            return col.title !== ""
                                ? "<tr><td>" + col.title + ":</td><td>" + col.data + "</td></tr>"
                                : "";
                        }).join("");
                        return data ? $('<table class="table"/>').append("<tbody>" + data + "</tbody>") : false;
                    },
                },
            },
        });

        window.dtQuotation = dt_quotation;

        dt_table_quotation.on("draw.dt", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    }
});
