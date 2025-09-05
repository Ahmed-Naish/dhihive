<div class="modal fade lead-modal" id="lead-modal" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content data">
            <div class="modal-header modal-header-image mb-3">
                <h5 class="modal-title text-white">{{ @$data['title'] }} </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-white" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ $data['url']  }}" class="row p-2" method="post" id="modal_values">
                    @csrf
                    {{-- dynamic attributes --}}
                    <div class="col-md-12 form-group mb-3">
                        <label class="form-label"> {{ _trans('common.Update Your Domain Name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control ot-form-control ot-input" name="domain"
                            id="domain" placeholder="Enper your domain" required autocomplete="off" value="">

                        @error('limit')
                            <div style="color: red;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group d-flex justify-content-end">
                        <button type="button"
                            class="btn btn-gradian pull-right hit_modal">{{ @$data['button'] }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ global_asset('backend/js/fs_d_ecma/modal/__modal.min.js') }}"></script>
<script src="{{ global_asset('backend/js/__loader.js') }}"></script>
