<form action="{{ @$product[0] ? route('update.salon', @$product[0]->id) : route('store.salon') }}" method="post"
    enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="newProduct" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Upload Link Product</h4>
                        <form>
                            <div class="row">
                                <div class="col-4 mb-3">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <select class="form-select" id="desc_product" data-id="1"
                                            aria-label="Default select example" name="desc_product[]">
                                            <option value="Add" {{ @$product[0]->desc_product == 'Add' ? 'selected' : '' }}>Add
                                            </option>
                                            <option value="Edit" {{ @$product[0]->desc_product == 'Edit' ? 'selected' : '' }}>Edit
                                            </option>
                                        </select>
                                        <label for="exampleFormControlSelect1">Desc</label>
                                    </div>
                                </div>
                                <div class="col-8 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text"
                                            placeholder="Put Link New Product Here ...." id="product" name="product[]"
                                            value="{{ @$product[0]->product }}">
                                        <label for="product">Link 1</label>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <select class="form-select" id="desc_product" data-id="1"
                                            aria-label="Default select example" name="desc_product[]">
                                            <option value="Add" {{ @$product[1]->desc_product == 'Add' ? 'selected' : '' }}>Add
                                            </option>
                                            <option value="Edit" {{ @$product[1]->desc_product == 'Edit' ? 'selected' : '' }}>Edit
                                            </option>
                                        </select>
                                        <label for="exampleFormControlSelect1">Desc</label>
                                    </div>
                                </div>
                                <div class="col-8 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text"
                                            placeholder="Put Link New Product Here ...." id="product" name="product[]"
                                            value="{{ @$product[1]->product }}">
                                        <label for="product">Link 2</label>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <select class="form-select" id="desc_product" data-id="1"
                                            aria-label="Default select example" name="desc_product[]">
                                            <option value="Add" {{ @$product[2]->desc_product == 'Add' ? 'selected' : '' }}>Add
                                            </option>
                                            <option value="Edit" {{ @$product[2]->desc_product == 'Edit' ? 'selected' : '' }}>Edit
                                            </option>
                                        </select>
                                        <label for="exampleFormControlSelect1">Desc</label>
                                    </div>
                                </div>
                                <div class="col-8 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text"
                                            placeholder="Put Link New Product Here ...." id="product" name="product[]"
                                            value="{{ @$product[2]->product }}">
                                        <label for="product">Link 3</label>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <select class="form-select" id="desc_product" data-id="1"
                                            aria-label="Default select example" name="desc_product[]">
                                            <option value="Add" {{ @$product[3]->desc_product == 'Add' ? 'selected' : '' }}>Add
                                            </option>
                                            <option value="Edit" {{ @$product[3]->desc_product == 'Edit' ? 'selected' : '' }}>Edit
                                            </option>
                                        </select>
                                        <label for="exampleFormControlSelect1">Desc</label>
                                    </div>
                                </div>
                                <div class="col-8 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text"
                                            placeholder="Put Link New Product Here ...." id="product" name="product[]"
                                            value="{{ @$product[3]->product }}">
                                        <label for="product">Link 4</label>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <select class="form-select" id="desc_product" data-id="1"
                                            aria-label="Default select example" name="desc_product[]">
                                            <option value="Add" {{ @$product[4]->desc_product == 'Add' ? 'selected' : '' }}>Add
                                            </option>
                                            <option value="Edit" {{ @$product[4]->desc_product == 'Edit' ? 'selected' : '' }}>Edit
                                            </option>
                                        </select>
                                        <label for="exampleFormControlSelect1">Desc</label>
                                    </div>
                                </div>
                                <div class="col-8 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text"
                                            placeholder="Put Link New Product Here ...." id="product" name="product[]"
                                            value="{{ @$product[4]->product }}">
                                        <label for="product">Link 5</label>
                                    </div>
                                </div>
                                <input type="text" name="type" id="type" value="Product" hidden>
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
