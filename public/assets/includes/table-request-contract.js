$(function () {
    var dt_table_request_contract = $(".datatable-request-contract");

    if (dt_table_request_contract.length) {
        $('[data-toggle="tooltip"]').tooltip();
        dt_table_request_contract.DataTable({
            ajax: {
                type: "GET",
                url: "/db/request-contract",
                headers: { "Content-Type": "application/json" },
            },
            columns: [
                { data: "" },
                { data: "id" },
                { data: "no_contract" },
                { data: "company" },
                { data: "ppn" },
                { data: "harga_total" },
                { data: "date" },
                { data: "name" },
            ],
            columnDefs: [
                {
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
                    targets: 2,
                    responsivePriority: 1,
                    render: function (data, type, full) {
                        if (type === "display") {
                            return '<a href="javascript:void(0)" class="text-primary fw-semibold cursor-pointer" data-bs-toggle="modal" data-bs-target="#acceptContract' + full["id"] + '" title="Klik untuk Approve">' + data + '</a>';
                        }
                        return data;
                    },
                },
                {
                    targets: 4,
                    className: "text-center",
                    render: function (data, type) {
                        if (type !== "display") return data;
                        return Number(data) === 1
                            ? '<span class="badge bg-label-primary">PPN</span>'
                            : '<span class="badge bg-label-danger">Non-PPN</span>';
                    },
                },
                {
                    targets: 5,
                    render: function (data, type) {
                        if (type !== "display") return data;
                        var formatted = (parseInt(data) || 0).toLocaleString("id-ID");
                        return '<div class="d-flex justify-content-between px-2"><span>Rp.</span><span>' + formatted + "</span></div>";
                    },
                },
                {
                    targets: 6,
                    className: "text-center",
                    render: function (data, type) {
                        if (!data) return "-";
                        if (type === "display" || type === "filter") {
                            return (typeof moment !== "undefined" && moment(data).isValid())
                                ? moment(data).format("DD-MM-YYYY")
                                : data;
                        }
                        return data;
                    },
                },
                {
                    targets: 7,
                    className: "text-center",
                    orderable: false,
                    searchable: true,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "";
                        var name = data || "-";
                        var img = full.image;
                        var initials = name.split(" ").map(function (w) { return w.charAt(0); }).slice(0, 2).join("").toUpperCase();
                        var colors = ["bg-label-primary","bg-label-success","bg-label-warning","bg-label-danger","bg-label-info","bg-label-secondary"];
                        var colorClass = colors[name.charCodeAt(0) % colors.length];
                        var av = img
                            ? '<img src="/' + img + '" class="rounded-circle shadow-xs" style="width:32px;height:32px;object-fit:cover;" alt="' + name + '">'
                            : '<div class="avatar-initial rounded-circle ' + colorClass + '" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:11px;font-weight:700;">' + initials + '</div>';
                        return '<span data-toggle="tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="' + name + '">' + av + '</span>';
                    },
                },
            ],
            drawCallback: function () {
                $('[data-toggle="tooltip"]').tooltip();
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            order: [[1, "desc"]],
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            return "Details of " + row.data()["company"];
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col) {
                            return col.title !== ""
                                ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '"><td>' + col.title + ':</td><td>' + col.data + '</td></tr>'
                                : "";
                        }).join("");
                        return data ? $('<table class="table"/><tbody />').append(data) : false;
                    },
                },
            },
        });
    }
});
