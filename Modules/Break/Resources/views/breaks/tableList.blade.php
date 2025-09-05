<table class="table table-bordered">
    <thead class="thead">
        <tr>
            <th class="sorting_asc">{{ _trans('common.SL') }}</th>
            <th class="sorting_asc">{{ _trans('common.Employee') }}</th>
            <th class="sorting_asc">{{ _trans('common.Break Type') }}</th>
            <th class="sorting_asc">{{ _trans('common.Date') }}</th>
            <th class="sorting_asc">{{ _trans('common.Start Time') }}</th>
            <th class="sorting_asc">{{ _trans('common.End Time') }}</th>
            <th class="sorting_asc">{{ _trans('common.Duration') }}</th>
            <th class="sorting_asc">{{ _trans('common.Remark') }}</th>
        </tr>
    </thead>
    <tbody class="tbody ">
        @if (count($data['items']) > 0)
            @foreach ($data['items'] as $key => $row)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ @$row->user->name }}</td>
                    <td>{{ $row->breakType->name }}</td>
                    <td>{{ $row->date }}</td>
                    <td>{{ date('h:i:s A', strtotime($row->start_time)) }}</td>
                    <td>{{ $row->end_time ? date('h:i:s A', strtotime($row->end_time)) : '' }}</td>
                    <td>
                        @php
                            // calculate break duration
                            $start = new DateTime($row->start_time);
                            $end = new DateTime($row->end_time);
                            $diff = $start->diff($end);
                            echo $diff->format('%H:%I:%S');

                        @endphp
                    </td>
                    <td>
                        {{ $row->remark ?? _trans('common.N/A') }}
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
