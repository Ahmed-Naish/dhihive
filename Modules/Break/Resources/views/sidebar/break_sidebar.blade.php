<li class="sidebar-menu-item  {{ set_active(['admin/breaks/*']) }}">
    <a href="javascript:void(0)"
        class="parent-item-content has-arrow {{ menu_active_by_route(['break.index', 'break.create', 'break.type.create','break.edit','break.type.index','break.type.edit','meals.index','meals.search', 'break.qrcode']) }}">
        <i class="las la-coffee"></i>
        <span class="on-half-expanded">
            {{ _trans('common.Breaks') }}
        </span>
    </a>
    <ul class="child-menu-list  {{ set_active(['admin/breaks', 'admin/breaks/create', 'admin/breaks/edit/*','admin/breaks/types','admin/breaks/types/create', 'admin/breaks/types/edit/*','admin/breaks','admin/breaks/meals','admin/breaks/meals/search', 'admin/breaks/qrcodes']) }}">
        @if (hasPermission('generate_qr_code'))
            <li class="nav-item {{ menu_active_by_route(['break.qrcode']) }}">
                <a href="{{ route('break.qrcode') }}" class=" {{ set_active('admin/breaks/qrcodes') }}">
                    <span>{{ _trans('common.QR Code') }}</span>
                </a>
            </li>
        @endif

        {{-- types --}}
        @if (hasPermission('break_type_read'))
            <li class="nav-item {{ menu_active_by_route(['break.type.index', 'break.type.create', 'break.type.edit']) }}">
                <a href="{{ route('break.type.index') }}" class=" {{ set_active('admin/breaks/types/*') }}">
                    <span>{{ _trans('common.Types') }}</span>
                </a>
            </li>
        @endif

        @if (hasPermission('break_read'))
            <li class="nav-item {{ menu_active_by_route(['break.index', 'break.create', 'break.edit']) }}">
                <a href="{{ route('break.index') }}" class=" {{ set_active('admin/breaks') }}">
                    <span>{{ _trans('common.Breaks') }}</span>
                </a>
            </li>
        @endif
    </ul>
</li>
