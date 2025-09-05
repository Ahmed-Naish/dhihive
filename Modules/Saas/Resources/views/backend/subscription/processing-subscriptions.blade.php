@if (count($processingSubscriptions))
    <table class="table mb-0 p-0">
        <tbody>
            @foreach ($processingSubscriptions ?? [] as $processingSubscription)
                <tr>
                    <td class="py-3 ps-3" width="220px">{{ @$processingSubscription->company->name }}</th>
                    <td width="220px" class="py-3 ps-3 bg-light">{{ @$processingSubscription->pricingPlanPrice->pricingPlan->name }} {{ _trans('common.Plan') }}</th>
                    <td class="py-3 text-success text-end">
                        <div class="spinner-border me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        {{ _trans('common.Processing') }}...
                    </th>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif