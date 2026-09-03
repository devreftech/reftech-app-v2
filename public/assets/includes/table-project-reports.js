$(function () {
    'use strict';

    var dt_project_reports_table = $('.datatable-project-reports');

    if (dt_project_reports_table.length) {
        var dt_project_reports = dt_project_reports_table.DataTable({
            ajax: {
                url: '/db/project-reports',
                type: 'GET'
            },
            columns: [
                { data: 'id', name: 'id', className: 'text-center' },
                { data: 'job_info', name: 'job_name' },
                { data: 'date_formatted', name: 'report_date' },
                { data: 'creator_name', name: 'creator.name' },
                { data: 'status_badge', name: 'status', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[0, 'desc']],
            dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [
                {
                    text: '<i class="mdi mdi-plus me-1"></i> <span class="d-none d-lg-inline-block">Create Project Report</span>',
                    className: 'create-new btn btn-primary waves-effect waves-light',
                    action: function () {
                        window.location.href = '/project-reports/create';
                    }
                }
            ],
            language: {
                paginate: {
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>'
                }
            }
        });

        $('div.head-label').html('<h5 class="card-title mb-0">Daily Project Reports</h5>');

        // Delete Handler
        $(document).on('click', '.delete-project-report', function () {
            var reportId = $(this).data('id');
            var deleteUrl = '/project-reports/' + reportId;

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data laporan proyek ini akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            dt_project_reports.ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: response.message || 'Laporan proyek berhasil dihapus.',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal menghapus data laporan proyek.',
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                }
                            });
                        }
                    });
                }
            });
        });
    }
});
