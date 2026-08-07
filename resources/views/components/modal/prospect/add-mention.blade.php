<div class="modal-onboarding modal modal-xl fade animate__animated" id="addMention" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body mb-3">
                <div class="onboarding-content mb-0">
                    <h4 class="onboarding-title text-body">Add Mention Comment Prospect</h4>
                    <div class="form-floating form-floating-outline">
                        <div class="select2-primary">
                            <select id="select2Primary" class="select2 form-select" name="mention[]" multiple>
                                @foreach ($sales as $users)
                                    @if ($users->id != Auth::id())
                                        <option value="{{ $users->id }}">{{ $users->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <label for="select2Primary">Mention To</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>
