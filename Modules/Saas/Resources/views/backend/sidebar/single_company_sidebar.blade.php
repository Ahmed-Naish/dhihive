@if (hasPermission('subscription_read'))
    <li class="sidebar-menu-item {{ menu_active_by_route(['single-company.subscriptions.index']) }}">
        <a href="{{ route('single-company.subscriptions.index') }}" class="parent-item-content {{ menu_active_by_route(['single-company.subscriptions.index']) }}">
            <i class="las la-crown"></i>
            <span class="on-half-expanded">{{ _trans('subscription.Subscriptions') }}</span>
        </a>
    </li>
@endif
@if (hasPermission('subscription_upgrade'))
    <li class="sidebar-menu-item {{ menu_active_by_route(['single-company.upgrade-plan.index']) }}">
        <a href="{{ route('single-company.upgrade-plan.index') }}" class="parent-item-content {{ menu_active_by_route(['single-company.upgrade-plan.index']) }}">
            <i class="las la-file-invoice-dollar"></i>
            <span class="on-half-expanded">{{ _trans('subscription.Upgrade Plan') }}</span>
        </a>
    </li>
@endif