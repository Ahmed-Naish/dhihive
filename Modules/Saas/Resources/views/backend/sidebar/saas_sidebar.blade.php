<li class="sidebar-menu-item {{ menu_active_by_route(['saas.subscriptions.list']) }}">
    <a href="{{ route('saas.subscriptions.list') }}" class="parent-item-content {{ menu_active_by_route(['saas.subscriptions.list']) }}">
        <i class="las la-crown"></i>
        <span class="on-half-expanded">{{ _trans('subscription.Subscriptions') }}</span>
    </a>
</li>
<li class="sidebar-menu-item {{ menu_active_by_route(['saas.company.list', 'saas.company.trash.list']) }}">
    <a href="{{ route('saas.company.list') }}" class="parent-item-content {{ menu_active_by_route(['saas.company.list', 'saas.company.trash.list']) }}">
        <i class="las la-building"></i>
        <span class="on-half-expanded">
            {{ _trans('common.Companies') }}
        </span>
    </a>
</li>
<li class="sidebar-menu-item {{ menu_active_by_route(['saas.cms.index']) }} ">
    <a href="{{ route('saas.cms.index') }}" class="parent-item-content {{ menu_active_by_route(['saas.cms.*']) }}">
        <i class="las la-book-open"></i>
        <span class="on-half-expanded">{{ _trans('subscription.CMS') }}</span>
    </a>
</li>
<li class="sidebar-menu-item {{ menu_active_by_route(['saas.pricing-plans.index']) }}">
    <a href="{{ route('saas.pricing-plans.index') }}" class="parent-item-content {{ menu_active_by_route(['saas.pricing-plans.index']) }}">
        <i class="las la-file-invoice-dollar"></i>
        <span class="on-half-expanded">{{ _trans('subscription.Pricing Plan') }}</span>
    </a>
</li>
<li class="sidebar-menu-item {{ menu_active_by_route(['saas.report.transactions']) }}">
    <a href="{{ route('saas.report.transactions') }}" class="parent-item-content {{ menu_active_by_route(['saas.report.transactions']) }}">
        <i class="las la-chart-bar"></i>
        <span class="on-half-expanded">{{ _trans('subscription.Transactions') }}</span>
    </a>
</li>
<li class="sidebar-menu-item {{ menu_active_by_route(['saas.contact-messages']) }}">
    <a href="{{ route('saas.contact-messages.index') }}" class="parent-item-content {{ menu_active_by_route(['saas.contact-messages.index']) }}">
        <i class="las la-comment"></i>
        <span class="on-half-expanded">{{ _trans('subscription.Contact Message') }}</span>
    </a>
</li>
<li class="sidebar-menu-item {{ menu_active_by_route(['saas.subscribers']) }}">
    <a href="{{ route('saas.subscribers.index') }}" class="parent-item-content {{ menu_active_by_route(['saas.subscribers.index']) }}">
        <i class="las la-users"></i>
        <span class="on-half-expanded">{{ _trans('subscription.Subscribers') }}</span>
    </a>
</li>

@include('backend.partials.configurations-sidebar')
@include('backend.partials.settings-sidebar')


<li class="sidebar-menu-item">
    <a href="/" class="parent-item-content" target="_blank">
        <i class="las la-external-link-alt"></i>
        <span class="on-half-expanded">{{ _trans('subscription.Go to website') }}</span>
    </a>
</li>