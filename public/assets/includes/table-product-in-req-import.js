$(function () {
    var dt_table_product = $(".datatable-product-in-req-import");
    var Url = "db/product/in/logistik/import";

    // Avatar Penerima — foto profil kalau ada, kalau tidak inisial nama dengan
    // warna acak (konsisten dengan gaya avatar Sign di tabel Purchase Request).
    function receiverCol(data, type, full) {
        var name = full.creator_name || "-";
        if (type !== "display") return name;
        var initials = name
            .split(" ")
            .map(function (w) { return w.charAt(0); })
            .slice(0, 2)
            .join("")
            .toUpperCase();
        var colors = ["bg-label-primary", "bg-label-success", "bg-label-warning", "bg-label-danger", "bg-label-info", "bg-label-secondary"];
        var colorClass = colors[name.charCodeAt(0) % colors.length];
        var av = full.creator_image
            ? '<img src="/' + full.creator_image + '" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;" alt="' + name + '">'
            : '<div class="avatar-initial rounded-circle ' + colorClass + '" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:11px;font-weight:700;">' + initials + "</div>";
        return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + name + '">' + av + "</span>";
    }

    if (dt_table_product.length) {
        var dt_product = dt_table_product.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
                dataSrc: function (json) {
                    var count = (json.data || []).length;
                    $("#menunggu-invoice-import-count-badge").text(count).toggleClass("d-none", count === 0);
                    return json.data || [];
                },
            },
            columns: [
                { data: "id" },
                { data: "no_gr" },
                { data: "no_product_in" },
                { data: "no_po" },
                { data: "supplier_name" },
                { data: "no_do" },
                { data: "date" },
                { data: "tax" },
                { data: "total_qty" },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    searchable: true,
                    visible: false,
                },
                {
                    // No. GR — diklik ke halaman detail Product In-nya (yang sekaligus
                    // jadi halaman GR sejak digabung). Belum tentu semua record punya
                    // GR (mis. input manual tanpa lewat PO), jadi tampil "-" kalau kosong.
                    targets: 1,
                    render: function (data, type, full, meta) {
                        if (!data) return "-";
                        if (type !== "display") return data;
                        var $url = route("product-in.show", full["id"]);
                        return '<a href="' + $url + '">' + data + "</a>";
                    },
                },
                {
                    // No. Product In — badge "Manual" kalau id_purchase_order null (GR
                    // Manual, gak lahir dari alur PO).
                    targets: 2,
                    render: function (data, type, full, meta) {
                        var $detailUrl = route("product-in.show", full["id"]);
                        var badge = full.id_purchase_order ? "" : ' <span class="badge bg-label-secondary">Manual</span>';
                        return '<a href="' + $detailUrl + '">#' + (data ?? "-") + "</a>" + badge;
                    },
                },
                {
                    // No PO — diklik ke halaman detail Purchase Order-nya kalau ada.
                    targets: 3,
                    render: function (data, type, full, meta) {
                        if (!data) return "-";
                        if (type !== "display" || !full.id_purchase_order) return data;
                        var $poUrl = route("purchase.show", full.id_purchase_order);
                        return '<a href="' + $poUrl + '">' + data + "</a>";
                    },
                },
                { targets: 4, render: function (data) { return data || "-"; } },
                {
                    targets: [5, 6, 7, 8],
                    className: "text-center",
                },
                {
                    targets: 6,
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
                    targets: 7,
                    render: function (data, type, full, meta) {
                        return data ? "IDR " + Number(data).toLocaleString("id-ID") : "-";
                    },
                },
                {
                    targets: 9,
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                    render: receiverCol,
                },
            ],
            order: [[0, "desc"]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
        });
    }
});
