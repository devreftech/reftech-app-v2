$(function () {
    var dt_table_expense_ongkir = $(".datatable-expense-ongkir");
    var Url = "db/expense/ongkir";

    if (dt_table_expense_ongkir.length) {
        var dt_filter = dt_table_expense_ongkir.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "date" },
                { data: "no_pending" },
                { data: "title" },
                { data: "kurir" },
                { data: "no_track" },
                { data: "cost" },
                { data: "status" },
                { data: "id" },
            ],
            columnDefs: [
                {
                    targets: 5,
                    render: $.fn.dataTable.render.number(".", "", 0, "Rp."),
                },
                {
                    targets: 6,
                    render: function (data, type, full, row) {
                        if (full["status"] === "posted") {
                            return '<span class="badge bg-label-success">Sudah Diposting</span>';
                        }
                        return '<span class="badge bg-label-warning">Belum Diposting</span>';
                    },
                },
                {
                    targets: -1,
                    render: function (data, type, full, row) {
                        if (full["status"] === "posted") {
                            return "-";
                        }
                        return (
                            '<button type="button" class="btn btn-sm btn-primary btn-post-ongkir" ' +
                            'data-id="' + full["id"] + '" ' +
                            'data-kurir="' + full["kurir"] + '" ' +
                            'data-cost="' + full["cost"] + '" ' +
                            'data-pending="' + full["no_pending"] + '">' +
                            "Posting</button>"
                        );
                    },
                },
            ],
            order: [[0, "desc"]],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f><"dt-action-buttons text-end pt-3 pt-md-0"B>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });
    }

    $(document).on("click", ".btn-post-ongkir", function () {
        var id = $(this).data("id");
        var kurir = $(this).data("kurir");
        var cost = $(this).data("cost");
        var pending = $(this).data("pending");

        var formatted = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
        }).format(cost);

        $("#ongkir-info-text").text(
            "Pending PO #" + pending + " - Kurir " + kurir + " - Biaya " + formatted
        );
        $("#formPostOngkir").attr("action", "expense-ongkir/" + id);
        var modal = new bootstrap.Modal(document.getElementById("postOngkirModal"));
        modal.show();
    });
});
