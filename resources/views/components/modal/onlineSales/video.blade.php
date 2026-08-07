<form action="{{@$video ? route('update.salon',@$video->id) : route('store.salon')}}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="video" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Video Product</h4>
                        <form>
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <h5 class="mb-4">1 Video / Days</h5>
                                </div>
                                <div class="col-4 mb-3">
                                    <h5 class="text-start m-0">Link IG</h5>
                                </div>
                                <div class="col-8 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text"
                                            placeholder="Put Link Instagram Here ...." id="instagram" name="instagram"
                                            value="{{@$video->ig}}">
                                        <label for="instagram">Link IG</label>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <h5 class="text-start m-0">Link TikTok</h5>
                                </div>
                                <div class="col-8 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text"
                                            placeholder="Put Link Tiktok Here ...." id="tiktok" name="tiktok"
                                            value="{{@$video->tiktok}}">
                                        <label for="tiktok">Link Tiktok</label>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <h5 class="text-start m-0">Link TokPed</h5>
                                </div>
                                <div class="col-8 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text"
                                            placeholder="Put Link Tokopedia Here ...." id="tokopedia" name="tokopedia"
                                            value="{{@$video->tokped}}">
                                        <label for="tokopedia">Link Tokped</label>
                                    </div>
                                </div>
                                <input type="text" name="type" id="type" value="Video" hidden>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
