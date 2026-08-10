$(function () {
    var dt_table = $(".datatable-quotation-admin-tab");
    var Url = "/db/quotation/admin/tab";

    if (dt_table.length) {
        dt_table.find("thead tr")
            .clone(true)
            .appendTo(dt_table.find("thead"));

        dt_table.find("thead tr:eq(1) th").each(function (i) {
            if (i === 6) { $(this).html(""); return; }
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
            );
            $("input", this).on("keyup change", function () {
                if (dt_quotation_admin_tab.column(i).search() !== this.value) {
                    dt_quotation_admin_tab.column(i).search(this.value).draw();
                }
            });
        });

        var dt_quotation_admin_tab = dt_table.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
                data: function (d) {
                    d.sales_id = window.adminSalesFilter || '';
                    d.year = window.adminQuotationYearFilter || 'all';
                    return d;
                },
            },
            columns: [
                { data: "no_quote" },
                { data: "company" },
                { data: "subtotal" },
                { data: "title" },
                { data: "estimated_date" },
                { data: "status" },
                { data: "sales_image" },
            ],
            columnDefs: [
                { targets: [2, 3, 4, 5, 6], className: "text-center" },
                {
                    targets: 0,
                    className: "text-center text-nowrap",
                    width: "60px",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var id      = full["id"];
                        var qType   = full["type"];
                        var rowType = full["row_type"];
                        var url;
                        if (rowType === 'unit' || qType === 'Unit') {
                            url = '/unit-quotation/' + id;
                        } else if (qType == "Sparepart") {
                            url = route("quotation.show", id);
                        } else if (qType == "Service") {
                            url = route("show-service.quotation", id);
                        } else if (qType == "Overhaul") {
                            url = route("show-overhaul.quotation", id);
                        } else {
                            url = route("quotation.show", id);
                        }
                        var full_no = data || "-";
                        var short   = full_no.length > 5 ? full_no.substring(0, 5) + "…" : full_no;
                        return '<a class="fw-bold text-primary" href="' + url + '"' +
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
                        var ru  = full["ru"];
                        var map = {
                            User:     '<span class="badge rounded-pill bg-label-info me-1">U</span>',
                            Reseller: '<span class="badge rounded-pill bg-label-dark me-1">R</span>',
                        };
                        return (map[ru] || "") + (data || "-");
                    },
                },
                {
                    targets: 2,
                    render: function (data, type) {
                        if (type !== "display") return data;
                        var formatted = parseInt(data || 0).toLocaleString("id-ID");
                        return '<div class="d-flex justify-content-between px-2"><span>Rp.</span><span>' + formatted + "</span></div>";
                    },
                },
                { targets: 3, render: function (data) { return data || "-"; } },
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
                        var status_number = full["status"];
                        var tip = full["tip"] || "Belum di update";
                        var qType = full["type"];
                        var statusMap = {
                            20:    { title: "Send WA / Email",     class: "bg-label-secondary", colorTip: "tooltip-secondary" },
                            30:    { title: "Inquiry Accepted",    class: "bg-label-dark",      colorTip: "tooltip-dark" },
                            40:    { title: "Follow Up",           class: "bg-label-info",      colorTip: "tooltip-info" },
                            60:    { title: "Negotiation / Revisi",class: "bg-label-primary",   colorTip: "tooltip-primary" },
                            80:    { title: "Hot Prospect",        class: "bg-label-warning",   colorTip: "tooltip-warning" },
                            90:    { title: "Hold",                class: "bg-warning",         colorTip: "tooltip-warning" },
                            100:   { title: "Done PO",             class: "bg-label-success",   colorTip: "tooltip-success" },
                            0:     { title: "Loss",                class: "bg-label-danger",    colorTip: "tooltip-danger" },
                            draft:        { title: "Draft",        class: "bg-label-secondary", colorTip: "" },
                            sent:         { title: "Sent",         class: "bg-label-info",      colorTip: "" },
                            negotiation:  { title: "Negotiation",  class: "bg-label-warning",   colorTip: "" },
                            revision:     { title: "Revisi",       class: "bg-label-primary",   colorTip: "" },
                            hot_prospect: { title: "Hot Prospect", class: "bg-label-warning",   colorTip: "" },
                            po_received:  { title: "PO Received",  class: "bg-label-success",   colorTip: "" },
                            loss:         { title: "Loss",         class: "bg-label-danger",    colorTip: "" },
                            cancel:       { title: "Cancel",       class: "bg-label-danger",    colorTip: "" },
                        };
                        var s = statusMap[status_number] || { title: status_number, class: "bg-label-secondary", colorTip: "tooltip-secondary" };
                        var badge = '<span class="badge rounded-pill ' + s.class + ' cursor-pointer"' +
                            ' data-bs-toggle="tooltip" data-bs-placement="top"' +
                            ' data-bs-custom-class="' + (s.colorTip || "") + '" title="' + tip + '">' +
                            s.title + '</span>';
                        if (qType === "Unit" || full["row_type"] === "unit") {
                            badge += ' <span class="badge bg-label-info ms-1">Smart</span>';
                        }
                        return badge;
                    },
                },
                {
                    targets: 6,
                    className: "text-center",
                    width: "48px",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full) {
                        if (type !== "display") return full.sales_name || "";
                        var name = full.sales_name || "-";
                        var initials = name.split(" ").map(function (w) { return w.charAt(0); }).slice(0, 2).join("").toUpperCase();
                        var colors = ["bg-label-primary","bg-label-success","bg-label-warning","bg-label-danger","bg-label-info","bg-label-secondary"];
                        var colorClass = colors[name.charCodeAt(0) % colors.length];
                        var av = data
                            ? '<img src="/' + data + '" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;" alt="' + name + '">'
                            : '<div class="avatar-initial rounded-circle ' + colorClass + '" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:11px;font-weight:700;">' + initials + '</div>';
                        return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + name + '">' + av + '</span>';
                    },
                },
            ],
            orderCellsTop: true,
            order: [],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            displayLength: 10,
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) { return "Detail: " + row.data()["no_quote"]; },
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
            initComplete: function () {
                var count = this.api().data().count();
                var badge = dt_table.data("badge");
                if (badge) $("#" + badge).text(count);
            },
        });

        window.dtAdminQuotation = dt_quotation_admin_tab;

        dt_table.on("draw.dt", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    }
});
