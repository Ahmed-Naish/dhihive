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

                    <form action="{{ route('packageDetails.store') }}" enctype="multipart/form-data" method="post"
                        id="attendanceForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{ _trans('common.Date') }}</label>
                                            <input type="date" name="contract_date"
                                                class="form-control ot-form-control ot-input date" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{ _trans('common.package') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control ot-form-control batch-no" placeholder="{{ _trans('common.package name') }}" name="name"
                                                required="">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="col-12 mt-3">
                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn btn-gradian pull-right hit_modal"
                                                    id="addRow">+ {{ _trans('common.ADD') }}</button>
                                            </div>
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
                                                        <th>{{ _trans('common.Quantity') }}</th>
                                                        <th>{{ _trans('common.Warranty Period') }}</th>
                                                        <th>{{ _trans('common.Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="tbody">
                                                    <!-- Existing row -->
                                                    <tr>
                                                        <td style="width: 50%">
                                                            <select required name="machine_ids[]"
                                                                class="form-control form-select select2-input ot-input mb-3 modal_select2"
                                                                data-live-search="true" title="Select package...">
                                                                {{-- @dd($data['machines']) --}}
                                                                @foreach ($data['machines'] as $key => $machine)
                                                                    <option value="{{ @$machine->id }}">
                                                                        {{ @$machine->machine_name }}
                                                                        [{{ $machine->model->name }}]
                                                                        [{{ $machine->brand->name }}]</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" step="any"
                                                                class="form-control ot-form-control batch-no" name="qty[]" placeholder="{{_trans('common.Quantity')}}" required="">
                                                        </td>
                                                        <td><input type="number" step="any"
                                                                class="form-control ot-form-control batch-no" placeholder="{{_trans('common.Warranty Period')}}" name="warranty_period[]"
                                                                required=""></td>
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
            // Attach a click event to the "Add" button
            $("#addRow").click(function() {

                var str = `
                  <tr>
                                                        <td style="width: 50%">
                                                            <select required name="machine_ids[]" 
                                                                class="form-control form-select select2-input ot-input mb-3 modal_select2"
                                                                data-live-search="true" title="Select package...">
                                                            `;
                @foreach ($data['machines'] as $key => $machine)
                    str +=
                        `  <option value="{{ @$machine->id }}">
                                                                        {{ @$machine->machine_name }} [{{ $machine->model->name }}] [{{ $machine->brand->name }}]</option>`;
                @endforeach

                str += `
                                                            </select>
                                                        </td>
                                                        <td><input type="number" step="any"
                                                                class="form-control ot-form-control batch-no" name="qty[]" placeholder="{{_trans('common.Quantity')}}" required="">
                                                        <td><input type="number" step="any"
                                                                class="form-control ot-form-control batch-no" placeholder="{{_trans('common.Warranty Period')}}" name="warranty_period[]"
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
        $(document).ready(function() {
            var baseUrl = $('meta[name="base-url"]').attr('content');

            $('#machineSelect').on('change', function() {
                var selectedMachineId = $(this).val();
                fetchDataAndUpdateFields(selectedMachineId);
            });

            // Function to fetch brands and models and update the fields
            function fetchDataAndUpdateFields(selectedMachineId) {
                $.ajax({
                    url: baseUrl + '/services/packages/getBrandsAndModels',
                    method: 'POST',
                    data: {
                        machine_id: selectedMachineId
                    },
                    success: function(response) {
                        var brand = response.brand;
                        var model = response.model;
                        var origin = response.origin;
                        $('#brandInput').val(brand[0].id);
                        $('#brandNameInput').val(brand[0].name);
                        $('#modelInput').val(model[0].id);
                        $('#modelNameInput').val(model[0].name);
                        $('#origin').val(origin);
                    },
                    error: function(error) {
                        console.error('Error fetching brands , models and origin:', error);
                    }
                });
            }
            $('#machineSelect').trigger('change');

            
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
