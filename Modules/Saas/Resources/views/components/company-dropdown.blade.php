{{-- if APP_MOOD=saas, then superadmin can switch companies --}}
@if(config('app.mood') === 'Saas'  && isModuleActive('Saas'))
    <div class="single-select">
        <input type="hidden" id="change_company_url" value="{{ route('company.ajaxCompanyChange') }}">
        <select name="company" class="company-select" id="company-select">
            @foreach ($data['companies'] as $company)
            <option value="{{ $company->id }}" {{ $company->id ==getCurrentCompany() ? 'selected' : '' }}>
                {{ $company->name }} </option>
            @endforeach
        </select>
    </div>
@endif
