<style>
    .accordion-item:last-of-type .accordion-button.collapsed {
        border-bottom-right-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }

    .accordion-item, .accordion-button {
        border: none !important;
    }

    .accordion-button {
        background-color: #7dd3fc !important;
    }

    .spinner-border {
        width: 15px !important;
        height: 15px !important;
        border-width: .2em !important;
    }

    .accordion-button:focus {
        box-shadow: none !important;
    }
    .accordion-button:focus {
        box-shadow: none !important;
    }

    .accordion-button:not(.collapsed) {
        box-shadow: none !important;
    }
</style>



<div class="accordion accordion-flush mb-3 shadow-sm d-none" id="accordionProcessingSubscription">
    <div class="accordion-item">
        <h2 class="accordion-header" id="flush-heading">
            <button class="accordion-button collapsed py-3 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse" aria-expanded="true" aria-controls="flush-collapse">
                <div class="spinner-border me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                {{ _trans('common.Processing Subscriptions') }}...
            </button>
        </h2>
        <div id="flush-collapse" class="accordion-collapse collapse show" aria-labelledby="flush-heading" data-bs-parent="#accordionProcessingSubscription">
            <div class="accordion-body p-0">
                {{-- LOAD DATA VIA AJAX --}}
            </div>
        </div>
    </div>
</div>



<script src="{{ global_asset('vendors/') }}/jquery/jquery-3.6.0.min.js"></script>
<script>
    var intervalId = setInterval(fetchProcessingSubscriptions, 2000);

    async function fetchProcessingSubscriptions() {
        const url = `{{ route('saas.subscriptions.fetch-processing-subscriptions') }}`;

        await $.ajax({
            url,
            success: function ({
                status,
                html,
                isShow
            }) {
                if (status && isShow) {
                    $('#accordionProcessingSubscription').removeClass('d-none');
                    $('.accordion-body').html(html);
                } else if (status && !isShow) {
                    $('#accordionProcessingSubscription').addClass('d-none');
                    clearInterval(intervalId);
                    companyDatatable();
                } else {
                    $('#accordionProcessingSubscription').addClass('d-none');
                    clearInterval(intervalId);
                    companyDatatable();
                }
            },
            error: function (error) {
                console.log(error)
            },
        });
    }
</script>