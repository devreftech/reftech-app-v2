<div class="modal modal-xl fade" id="pdfViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-lg-down">
        <div class="modal-content" style="height: 90vh;">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0" id="pdfViewerModalTitle">Preview File</h6>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <a href="#" id="pdfViewerModalDownload" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="mdi mdi-download-outline"></i> Download
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <iframe id="pdfViewerModalFrame" src="" style="width: 100%; height: 100%; border: 0;"></iframe>
            </div>
        </div>
    </div>
</div>
@once
    @push('script')
        <script>
            function openPdfViewer(url, title) {
                document.getElementById('pdfViewerModalFrame').src = url;
                document.getElementById('pdfViewerModalDownload').href = url;
                document.getElementById('pdfViewerModalTitle').innerText = title || 'Preview File';
                var modal = new bootstrap.Modal(document.getElementById('pdfViewerModal'));
                modal.show();
            }
            $('#pdfViewerModal').on('hidden.bs.modal', function() {
                document.getElementById('pdfViewerModalFrame').src = '';
            });
        </script>
    @endpush
@endonce
