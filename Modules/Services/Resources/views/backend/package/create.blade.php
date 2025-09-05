<div class="modal fade lead-modal" id="lead-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content data">
            <div class="modal-header modal-header-image mb-3">
                <h5 class="modal-title text-white">{{ @$title }} </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row p-2">
                    <div class="col-md-12">
                        <form action="{{ $url }}" class="row p-0" method="post" id="modal_values"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label">{{ _trans('common.Name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control ot-form-control ot-input" name="name" placeholder="{{ _trans('common.Enter name') }}"
                                    id="name" />
                            </div>
                            <select name="machine_name" id="machine_name"
                                        class="form-select select2-input ot_input mb-3 modal_select2" aria-label="Default select example">
                                        @foreach (@$attribute['options'] as $option)
                                            <option value="{{ $option['value'] }}"
                                                {{ @$option['active'] ? 'selected' : '' }}>
                                                <?= $option['text'] ?>
                                            </option>
                                        @endforeach
                                    </select>
                            <div class="form-group mb-3">
                                <label class="form-label">{{ _trans('common.Quantity') }} <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control ot-form-control ot-input"
                                    placeholder="{{ _trans('common.Enter quantity') }}" id="quantity" />
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">{{ _trans('common.Contract Date') }} <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="contract_date" class="form-control ot-form-control ot-input" placeholder="" id="contract_date" />
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">{{ _trans('common.Warranty Period') }} <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="warranty_period" class="form-control ot-form-control ot-input"
                                    placeholder="{{ _trans('common.Enter warranty_period') }}" id="warranty_period" />
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">{{ _trans('common.Expiration Date') }} <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control ot-form-control ot-input" name="expiration_date" placeholder="" id="expiration_date" />
                            </div>

                            <div class="form-group text-right">
                                <button type="submit"
                                    class="btn btn-primary pull-right">{{ @$data['button'] }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
