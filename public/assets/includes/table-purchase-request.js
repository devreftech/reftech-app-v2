$(function () {
    function dateCol(data) {
        if (!data) return "-";
        var m = moment(data);
        return m.isValid() ? m.format("DD-MM-YYYY") : data;
    }

    function signCol(data, type, full) {
        var name = full.user_name || "-";
        if (type !== "display") return name;
        var initials = name
            .split(" ")
            .map(function (w) {
                return w.charAt(0);
            })
            .slice(0, 2)
            .join("")
            .toUpperCase();
        var colors = ["bg-label-primary", "bg-label-success", "bg-label-warning", "bg-label-danger", "bg-label-info", "bg-label-secondary"];
        var colorClass = colors[name.charCodeAt(0) % colors.length];
        var av = full.user_image
            ? '<img src="/' + full.user_image + '" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;" alt="' + name + '">'
            : '<div class="avatar-initial rounded-circle ' + colorClass + '" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:11px;font-weight:700;">' + initials + "</div>";
        return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + name + '">' + av + "</span>";
    }

    function initPurchaseRequestTable(selector, ajaxUrl, withDelivery) {
        var $table = $(selector);
        if (!$table.length) return;

        var columns = [
            { data: "no_pr" },
            { data: "no_po" },
            { data: "no_pending" },
            { data: "company" },
            { data: "item" },
            { data: "qty_full" },
            { data: "date" },
            { data: null },
        ];

        if (withDelivery) {
            columns.push(
                { data: "purchase_type" },
                { data: "cargo" },
                { data: "no_resi" },
                { data: "purchase_date" }
            );
        }

        var columnDefs = [
            {
                targets: 0,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    var url = route("purchase-request.show", full.id);
                    return '<a href="' + url + '" class="fw-semibold text-primary">' + (data || "-") + "</a>";
                },
            },
            { targets: [1, 2, 3, 4], render: function (data) { return data || "-"; } },
            { targets: 6, render: function (data) { return dateCol(data); } },
            { targets: 7, className: "text-center", orderable: false, searchable: false, render: signCol },
        ];

        if (withDelivery) {
            columnDefs.push(
                { targets: 8, render: function (data) { return data || "-"; } },
                { targets: 9, render: function (data) { return data || "-"; } },
                { targets: 10, render: function (data) { return data || "-"; } },
                { targets: 11, render: function (data) { return dateCol(data); } }
            );
        }

        $table.DataTable({
            ajax: {
                type: "GET",
                url: ajaxUrl,
                headers: { "Content-Type": "application/json" },
            },
            columns: columns,
            columnDefs: columnDefs,
            order: [[6, "desc"]],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { emptyTable: "Tidak ada data Purchase Request." },
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
        });
    }

    initPurchaseRequestTable(".datatable-purchase-request-new", "/db/purchase-request/new", false);
    initPurchaseRequestTable(".datatable-purchase-request-acc", "/db/purchase-request/acc", false);
    initPurchaseRequestTable(".datatable-purchase-request-delivery", "/db/purchase-request/delivery", true);
    initPurchaseRequestTable(".datatable-purchase-request-done", "/db/purchase-request/done", false);
});
