<form action="{{ route('expense-account.update', 1) }}" id="editForm" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    <div class="modal-onboarding modal fade animate__animated" id="editAccount" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Edit Account</h4>
                        <form>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="code" class="form-control edit_code" name="code"
                                            placeholder="Put Code Here.....">
                                        <label for="code">Code</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="category" class="form-control edit_category" name="category"
                                            placeholder="Put Category Here.....">
                                        <label for="category">Category</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="name" class="form-control edit_name" name="name"
                                            placeholder="Put Name Here.....">
                                        <label for="name">Name</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="currency" class="form-control edit_currency" name="currency"
                                            placeholder="Put currency Here.....">
                                        <label for="currency">Currency</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="saldo" class="form-control edit_saldo" name="saldo"
                                            placeholder="Put saldo Here.....">
                                        <label for="saldo">Saldo (D/K)</label>
                                    </div>
                                </div>
                                <p class="text-muted">Note : Bila Parents Maka Kosongkan</p>
                                <div class="col-12 ">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <select class="select2 form-select edit_parent" data-allow-clear="true" name="parent"
                                            id="parent">
                                            <option> ---- Choose Parents Account Here ---- </option>
                                            @foreach ($prim as $acc)
                                                <option value="{{ $acc->id }}" >
                                                    {{ $acc->code }} - {{ $acc->name }} {{ $acc->category }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="parent" class="mb-2">Parents</label>
                                    </div>
                                </div>
                                {{-- <input type="hidden" name="type" value="{{$type}}"> --}}
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
