$(function () {
    window.dtUnitQuotation = $('.datatable-unit-quotation').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: '/db/unit-quotation',
            type: 'GET',
            data: function (d) { d.year = window.quotationYearFilter || 'all'; return d; },
        },
        columns: [
            { data: 'no_quote', className: 'text-center text-nowrap' },
            { data: 'client',   className: '' },
            { data: 'title',    className: '' },
            { data: 'date',     className: 'text-center text-nowrap' },
            { data: 'total',    className: 'text-end text-nowrap' },
            { data: 'status',   className: 'text-center' },
        ],
        columnDefs: [
            { targets: 0, render: function (d, t, row) {
                return '<a href="/unit-quotation/' + row.id + '">' + d + '</a>';
            }},
            { targets: 4, className: 'text-center', render: function (d, t) {
                if (t !== 'display') return d;
                return '<div class="d-flex justify-content-between px-2"><span>Rp.</span><span>' + parseInt(d).toLocaleString('id-ID') + '</span></div>';
            }},
            { targets: 5, render: function (d, t, row) {
                var map = {
                    draft:        'bg-secondary',
                    sent:         'bg-info',
                    negotiation:  'bg-warning',
                    revision:     'bg-primary',
                    hot_prospect: 'bg-danger',
                    po_received:  'bg-success',
                    loss:         'bg-dark',
                };
                var label = {
                    draft:        'Draft',
                    sent:         'Sent',
                    negotiation:  'Negotiation',
                    revision:     'Revisi',
                    hot_prospect: 'Hot Prospect',
                    po_received:  'PO Received',
                    loss:         'Loss',
                };
                var tip = row.last_note_date
                    ? (row.last_note_date + ' | ' + (row.last_note || 'Belum di update'))
                    : 'Belum di update';
                return '<span class="badge ' + (map[d] || 'bg-label-secondary') + ' cursor-pointer"' +
                    ' data-bs-toggle="tooltip" data-bs-placement="top" title="' + tip + '">' +
                    (label[d] || d) + '</span>';
            }},
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
    });
});
