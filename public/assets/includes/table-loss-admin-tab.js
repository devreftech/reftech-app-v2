$(function () {
    var dt_table = $(".datatable-loss-quote-admin-tab");
    var Url = "/db/loss/admin/tab";

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
                if (dt_loss_admin_tab.column(i).search() !== this.value) {
                    dt_loss_admin_tab.column(i).search(this.value).draw();
                }
            });
        });

        var dt_loss_admin_tab = dt_table.DataTable({
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
                { data: "harga_total" },
                { data: "description" },
                { data: "estimated_date" },
                { data: "status" },
                { data: "sales_image" },
            ],
            columnDefs: [
                { targets: [2, 4, 5], className: "text-center" },
                {
                    targets: 0,
                    className: "text-center text-nowrap",
                    responsivePriority: 1,
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var url;
                        if (full["row_type"] === "unit") {
                            url = "/smart-quote/" + full["id"];
                        } else if (full["type"] === "Service") {
                            url = route("show-service.quotation", full["id"]);
                        } else if (full["type"] === "Overhaul") {
                            url = route("show-overhaul.quotation", full["id"]);
                        } else {
                            url = route("quotation.show", full["id"]);
                        }
                        var unitBadge = full["row_type"] === "unit"
                            ? ' <span class="badge bg-label-info ms-1">Smart</span>'
                            : "";
                        return '<a class="fw-bold text-primary" href="' + url + '">' + (data || "-") + "</a>" + unitBadge;
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
                {
                    targets: 3,
                    render: function (data, type) {
                        if (type !== "display") return data;
                        return data || "-";
                    },
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
                    className: "text-center",
                    render: function (data, type, full) {
                        var titleTip = full["note"];
                        if (type !== "display") return data;
                        if (full["row_type"] === "unit") {
                            var uMap = {
                                loss:   { title: "Loss", class: "bg-label-danger" },
                                cancel: { title: "Cancel", class: "bg-label-secondary" },
                            };
                            var uInfo = uMap[data] || { title: data || "-", class: "bg-label-secondary" };
                            return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + (titleTip || "") + '" class="badge rounded-pill ' + uInfo.class + '">' + uInfo.title + "</span>";
                        }
                        return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + (titleTip || "") + '" class="badge rounded-pill bg-label-danger">Loss</span>';
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

        window.dtAdminLoss = dt_loss_admin_tab;

        dt_table.on("draw.dt", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    }
});
