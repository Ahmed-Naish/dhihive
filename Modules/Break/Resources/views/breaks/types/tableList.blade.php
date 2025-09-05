<table class="table table-bordered">
    <thead class="thead">
        <tr>
            {{-- SELECT `id`, `name`, `slug`, `description`, `status_id`, `limit`, `limit_type`, `duration_type`, `max_duration`, `company_id`, `branch_id`, `created_at`, `updated_at`, `is_remark_required` FROM `break_types` WHERE 1 --}}
            <th class="sorting_asc">{{ _trans('common.SL') }}</th>
            <th class="sorting_asc">{{ _trans('common.Icon') }}</th>
            <th class="sorting_asc">{{ _trans('common.Title') }}</th>
            <th class="sorting_asc">{{ _trans('common.Description') }}</th>
            <th class="sorting_asc">{{ _trans('common.Limit') }}</th>
            <th class="sorting_asc">{{ _trans('common.Limit Type') }}</th>
            <th class="sorting_asc">{{ _trans('common.Max Duration') }}</th>
            <th class="sorting_asc">{{ _trans('common.Duration Type') }}</th>
            <th class="sorting_asc">{{ _trans('common.Is Remark Required') }}</th>
            {{-- <th class="sorting_asc">{{ _trans('common.Will Ask Next Meal') }}</th> --}}
            <th class="sorting_asc">{{ _trans('common.Status') }}</th>
            <th class="sorting_asc">{{ _trans('common.Actions') }}</th>
        </tr>
    </thead>
    <tbody class="tbody ">
        @if (count($data['items']) > 0)
            @foreach ($data['items'] as $key => $row)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        <img src="{{ uploaded_asset($row->icon_id) }}" class="staff-profile-image-small" >
                    </td>
                    <td>{{ @$row->name }}</td>
                    <td>{{ $row->description }}</td>
                    <td>{{ $row->limit }}</td>
                    <td>{{ $row->limit_type }}</td>
                    <td>{{ $row->max_duration }}</td>
                    <td>{{ $row->duration_type }}</td>
                    <td>
                        @if ($row->is_remark_required == 1)
                            <?= '<small class="badge badge-primary">' . _trans('common.Yes') . '</small>' ?>
                        @else
                            <?= '<small class="badge badge-danger">' . _trans('common.No') . '</small>' ?>
                        @endif
                    </td>
                    {{-- <td>
                        @if ($row->will_ask_next_meal == 1)
                            <?= '<small class="badge badge-success">' . _trans('common.Yes') . '</small>' ?>
                        @else
                            <?= '<small class="badge badge-danger">' . _trans('common.No') . '</small>' ?>
                        @endif
                    </td> --}}
                    <td>
                        <?= '<small class="badge badge-' . @$row->status->class . '">' . @$row->status->name .
                        '</small>' ?>
                    </td>
                    <td>
                        <div class="dropdown dropdown-action">
                            <button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                 @if (hasPermission('break_type_update'))
                                    <a class="dropdown-item" href="{{ route('break.type.edit', $row->id) }}">{{ _trans('common.Edit') }}</a>
                                @endif

                                @if (hasPermission('break_type_delete'))
                                    <a class="dropdown-item" href="{{ route('break.type.delete', $row->id) }}">{{ _trans('common.Delete') }}</a>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="odd">
                <td valign="top" colspan="9" class="dataTables_empty">
                    <div class="no-data-found-wrapper text-center ">
                        <img src="{{ asset('assets/images/empty.png') }}" alt="" class="mb-primary empty_table"
                            width="200">
                    </div>
                </td>
            </tr>
        @endif
    </tbody>
</table>
{{ $data['items']->links('break::custom_pagination') }}
