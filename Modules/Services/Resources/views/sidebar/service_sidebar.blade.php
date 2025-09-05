
<li class="sidebar-menu-item">
    <a href="javascript:void(0)"
        class="parent-item-content has-arrow {{ menu_active_by_route(['models.index', 'brands.index', 'machines.index', 'packages.index', 'institutions.index', 'services.index']) }}">
        <i class="las la-info-circle"></i>
        <span class="on-half-expanded">
            {{ _trans('services.Services') }}
        </span>
    </a>
    <ul
        class="child-menu-list  {{ set_active(['services/models*', 'services/brands*', 'services/machines*', 'services/packages*', 'services/institutions*', 'services/module-services']) }}">
        @if (hasPermission('model_read'))
            <li class="nav-item {{ menu_active_by_route('models.index') }}">
                <a href="{{ route('models.index') }}" class=" {{ set_active('services/models') }}">
                    <span>{{ _trans('services.Model') }}</span>
                </a>
            </li>
        @endif

        @if (hasPermission('brand_read'))
            <li class="nav-item {{ menu_active_by_route('brands.index') }}">
                <a href="{{ route('brands.index') }}" class=" {{ set_active('services/brands') }}">
                    <span>{{ _trans('services.Brand') }}</span>
                </a>
            </li>
        @endif

        @if (hasPermission('machine_read'))
            <li class="nav-item {{ menu_active_by_route('machines.index') }}">
                <a href="{{ route('machines.index') }}" class=" {{ set_active('services/machines') }}">
                    <span>{{ _trans('services.Machine') }}</span>
                </a>
            </li>
        @endif

        @if (hasPermission('package_read'))
            <li class="nav-item {{ menu_active_by_route('packages.index') }}">
                <a href="{{ route('packages.index') }}" class=" {{ set_active('services/packages') }}">
                    <span>{{ _trans('services.Package') }}</span>
                </a>
            </li>
        @endif

        @if (hasPermission('institution_read'))
            <li class="nav-item {{ menu_active_by_route('institutions.index') }}">
                <a href="{{ route('institutions.index') }}" class=" {{ set_active('services/institutions') }}">
                    <span>{{ _trans('services.Institution') }}</span>
                </a>
            </li>
        @endif

        @if (hasPermission('service_read'))
            <li class="nav-item {{ menu_active_by_route('services.index') }}">
                <a href="{{ route('services.index') }}" class=" {{ set_active('services/module-services') }}">
                    <span>{{ _trans('services.Module Service') }}</span>
                </a>
            </li>
        @endif
    </ul>
</li>
