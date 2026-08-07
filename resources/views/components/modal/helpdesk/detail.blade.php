<div class="modal fade" id="detailHelpdesk" tabindex="-1" style="display: none;" aria-hidden="true" data-id="">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detail Tiket <span id="detailHelpdeskNoTicket"></span></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if (Auth::user()->role == 'Admin')
                    <div class="row mb-1">
                        <div class="col-4">Requester</div>
                        <div class="col-8">: <span id="detailHelpdeskRequester"></span></div>
                    </div>
                @endif
                <div class="row mb-1">
                    <div class="col-4">Title</div>
                    <div class="col-8">: <span id="detailHelpdeskTitle"></span></div>
                </div>
                <div class="row mb-1">
                    <div class="col-4">Status</div>
                    <div class="col-8">: <span id="detailHelpdeskStatus"></span></div>
                </div>
                <div class="row mb-1">
                    <div class="col-4">Date</div>
                    <div class="col-8">: <span id="detailHelpdeskDate"></span></div>
                </div>
                <div class="row mb-1">
                    <div class="col-4">Description</div>
                    <div class="col-8">
                        <pre class="mb-0" id="detailHelpdeskDescription"
                            style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"></pre>
                    </div>
                </div>
                <div class="row mb-1 d-none" id="detailHelpdeskResolutionWrapper">
                    <div class="col-4">Keterangan Penyelesaian</div>
                    <div class="col-8">
                        <pre class="mb-0" id="detailHelpdeskResolutionNote"
                            style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"></pre>
                    </div>
                </div>
            </div>
            @if (Auth::user()->role == 'Admin')
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-info waves-effect waves-light button-helpdesk-status" data-status="In Progress">Proses</button>
                    <button type="button" class="btn btn-success waves-effect waves-light button-helpdesk-status" data-status="Resolved">Selesai</button>
                </div>
            @endif
        </div>
    </div>
</div>
