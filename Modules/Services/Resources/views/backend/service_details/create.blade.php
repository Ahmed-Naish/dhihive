@extends('backend.layouts.app')
@section('title', $data['title'])
@section('content')
    {!! breadcrumb([
        'title' => $data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => $data['title'],
    ]) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="card ot-card">
                <div class="card-body">

                    <form action="{{ route('services.store') }}" enctype="multipart/form-data" method="post"
                        id="attendanceForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{ _trans('common.Contact Date') }}</label>
                                            <input type="date" name="contract_date"
                                                class="form-control ot-form-control ot-input date" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{ _trans('common.Institution Name') }} <span
                                                    class="text-danger">*</span></label>
                                            <select required name="institution"
                                                class="form-control form-select select2-input ot-input mb-3 modal_select2"
                                                data-live-search="true" title="Select package...">
                                                {{-- @dd($data['machines']) --}}
                                                @foreach ($data['institutions'] as $key => $institution)
                                                    <option value="{{ @$institution->id }}">
                                                        {{ @$institution->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{ _trans('common.Package Name') }} <span
                                                    class="text-danger">*</span></label>
                                            <select required name="package" id="package"
                                                class="form-control form-select select2-input ot-input mb-3 modal_select2"
                                                data-live-search="true" title="Select package...">
                                                {{-- @dd($data['machines']) --}}
                                                @foreach ($data['packages'] as $key => $package)
                                                    <option value="{{ @$package->id }}">
                                                        {{ @$package->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4 table-content table-basic">
                                    <div class="col-md-12">
                                        <h5>{{ _trans('common.Package Details Table') }} *</h5>
                                        <div class="table-responsive mt-3">
                                            <table id="myTable" class="table table-hover order-list">
                                                <thead class="thead">
                                                    <tr>
                                                        <th>{{ _trans('common.machine name') }}</th>
                                                        <th>{{ _trans('common.Assign User') }}</th>
                                                        <th>{{ _trans('common.Installation Date') }}</th>
                                                        <th>{{ _trans('common.Serial Number') }}</th>
                                                        <th>{{ _trans('common.Supply Date') }}</th>
                                                        <th>{{ _trans('common.Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="machineTable" class="tbody">
                                                    <!-- Existing row -->
                                                    <tr>
                                                        <td style="width: 20%">
                                                            <select required name="machine_ids[]"
                                                                class="form-control form-select select2-input ot-input mb-3 modal_select2"
                                                                data-live-search="true" title="Select package...">
                                                                {{-- @dd($data['machines']) --}}
                                                                @foreach ($data['institutions'] as $key => $institution)
                                                                    <option value="{{ @$institution->id }}">
                                                                        {{ @$institution->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><select required name="user"
                                                                class="form-control form-select select2-input ot-input mb-3 modal_select2"
                                                                data-live-search="true" title="Select package...">
                                                                {{-- @dd($data['machines']) --}}
                                                                @foreach ($data['users'] as $key => $user)
                                                                    <option value="{{ @$user->id }}">
                                                                        {{ @$user->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger delete-row"
                                                                onclick="deleteItem(event);">{{ _trans('common.Delete') }}</button>
                                                        </td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="d-flex justify-content-end">

                                <button type="submit"
                                    class="btn btn-gradian pull-right">{{ _trans('common.Save') }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        var baseUrl = $('meta[name="base-url"]').attr('content');

        $('#package').on('change', function() {
            var selectedPackageId = $(this).val();
            fetchDataAndUpdateFields(selectedPackageId);
        });

        // Function to fetch brands and models and update the fields
        function fetchDataAndUpdateFields(selectedPackageId) {
            $.ajax({
                url: baseUrl + '/services/module-services/getModels',
                method: 'POST',
                data: {
                    package_id: selectedPackageId
                },
                success: function(response) {
                    var machineTableBody = $('#machineTable');
                    machineTableBody.empty();

                    $.each(response.machine_data, function(index, machine) {
                        var str = `
                            <tr>
                                <td style="width: 20%">
                                    
                                    <input type="hidden" class="form-control ot-form-control" value="${machine.machine_id}" name="machine_ids[]" readonly>
                                    <input type="text" class="form-control ot-form-control" value="${machine.machine_name}" name="machine_name[]" readonly>
  
                                </td>
                                <td style="width: 20%">
                                    <select required name="user_ids[]" class="form-control form-select select2-input ot-input mb-3 modal_select2" data-live-search="true" title="Select user...">
                                                            `;
                         @foreach ($data['users'] as $key => $user)
                              str += `  <option value="{{ @$user->id }}">{{ @$user->name }} </option>`;
                         @endforeach  
                        str += `         
                                    
                                </td>
                                 <td>
                                    <input type="date" class="form-control ot-form-control" name="installation_date[]">
  
                                </td>
                                <td>
                                    <input type="text" class="form-control ot-form-control" placeholder="Serial Number" name="serial_number[]">
                                </td>
                                <td>
                                    <input type="date" class="form-control ot-form-control" name="supply_date[]">
                                </td>
                               
                                <td>
                                    <button class="btn btn-danger delete-row"
                                        onclick="deleteItem(event);">{{ _trans('common.Delete') }}</button>
                                </td>
                            </tr>`;
                        machineTableBody.append(str);
                         $('.modal_select2').select2();
                    });
                },

                error: function(error) {
                    console.error('Error fetching machine data:', error);
                }
            });
        }

        $('#package').trigger('change');
    });
</script>

    <script>
        $(document).ready(function() {
            // Attach a click event to the "Add" button
            $("#addRow").click(function() {

                var str = `
                  <tr>
                                                        <td style="width: 50%">
                                                            <select required name="machine_ids[]" 
                                                                class="form-control form-select select2-input ot-input mb-3 modal_select2"
                                                                data-live-search="true" title="Select package...">
                                                            `;
                @foreach ($data['institutions'] as $key => $institution)
                    str +=
                        `  <option value="{{ @$institution->id }}">
                                                                        {{ @$institution->name }}</option>`;
                @endforeach

                str += `
                                                            </select>
                                                        </td>
                                                        <td><input type="number" step="any"
                                                                class="form-control ot-form-control batch-no" name="qty[]" placeholder="{{ _trans('common.Quantity') }}" required="">
                                                        <td><input type="number" step="any"
                                                                class="form-control ot-form-control batch-no" placeholder="{{ _trans('common.Warranty Period') }}" name="warranty_period[]"
                                                                required=""></td>
                                                               <td>
                                                                    <button class="btn btn-danger delete-row" onclick="deleteItem(event);">{{ _trans('common.Delete') }}</button>
                                                                </td>

                                                    </tr>`;

                console.log(str);
                $("#myTable tbody").append(str);
                $('.modal_select2').select2();
            });
        });
    </script>
    <script>
        function deleteItem(event) {
            // console.log(event);
            event.preventDefault();
            $(event.target).closest('tr').remove();
        }
    </script>

@endsection
