$(function () {
    var dt_table_product = $(".datatable-product-in-import");
    var Url = "db/product/in/import";

    if (!dt_table_product.length) return;

    var initialized = false;

    function initTable() {
        if (initialized) return;
        initialized = true;

        $('[data-toggle="tooltip"]').tooltip();
        var dt_product = dt_table_product.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
                dataSrc: function (json) {
                    var count = (json.data || []).length;
                    $("#product-in-import-count-badge").text(count).toggleClass("d-none", count === 0);
                    return json.data || [];
                },
            },
            columns: [
                { data: "" },
                { data: "id" },
                { data: "invoice" },
                { data: "supplier_name" },
                { data: "product" },
                { data: "qty" },
                { data: "total" },
                { data: "tax" },
                { data: "date" },
                { data: "" },
            ],
            columnDefs: [
                {
                    // For Responsive
                    className: "control",
                    orderable: false,
                    searchable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function () { return ""; },
                },
                {
                    targets: 1,
                    searchable: true,
                    visible: false,
                },
                {
                    // Invoice — badge "Manual" kalau id_purchase_order null (GR Manual,
                    // gak lahir dari alur PO).
                    responsivePriority: 1,
                    targets: 2,
                    render: function (data, type, full, meta) {
                        var $detailUrl = route("product-in.show", full["id"]);
                        var badge = full.id_purchase_order ? "" : ' <span class="badge bg-label-secondary">Manual</span>';
                        return '<a href="' + $detailUrl + '">' + (data ?? "-") + "</a>" + badge;
                    },
                },
                {
                    targets: [5, 6, 7, 8],
                    className: "text-center",
                },
                {
                    targets: 6,
                    render: function (data, type, full, meta) {
                        return data ? "IDR " + Number(data).toLocaleString("id-ID") : "-";
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, full, meta) {
                        return data ? "IDR " + Number(data).toLocaleString("id-ID") : "-";
                    },
                },
                {
                    targets: 8,
                    render: function (data, type, full, meta) {
                        if (!data) return "-";
                        var d = new Date(data);
                        var day = String(d.getDate()).padStart(2, "0");
                        var month = String(d.getMonth() + 1).padStart(2, "0");
                        var year = d.getFullYear();
                        return day + "-" + month + "-" + year;
                    },
                },
                {
                    // Actions
                    targets: -1,
                    title: "Actions",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full, meta) {
                        var $dataId = full["id"];
                        var $detailQUrl = route("product-in.show", $dataId);
                        var $revQUrl = route("product-in.edit", $dataId);
                        return (
                            '<div class="d-inline-block">' +
                            '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>' +
                            '<ul class="dropdown-menu dropdown-menu-end m-0">' +
                            '<li><a href="' + $detailQUrl + '" class="dropdown-item">Details</a></li>' +
                            '<li><a href="' + $revQUrl + '" class="dropdown-item">Edit</a></li>' +
                            "</ul>" +
                            "</div>"
                        );
                    },
                },
            ],
            order: [[1, "desc"]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return "Details of " + data["invoice"];
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.title !== ""
                                ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                                  "<td>" + col.title + ":</td> <td>" + col.data + "</td></tr>"
                                : "";
                        }).join("");
                        return data ? $('<table class="table"/><tbody />').append(data) : false;
                    },
                },
            },
        });

        dt_table_product.on("draw", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }

    // Tab "Import" bukan tab aktif default — DataTable-nya baru dibuat begitu
    // tabnya pertama kali dibuka, biar lebar kolom kehitung benar (nggak
    // dihitung 0 gara-gara containernya masih display:none).
    $('[data-bs-target="#tab-product-in-import"]').on("shown.bs.tab", initTable);
    if ($("#tab-product-in-import").hasClass("active")) initTable();
});
