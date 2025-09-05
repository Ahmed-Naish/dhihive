<?php

namespace Modules\Saas\Repositories;

use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Helpers\CoreApp\Traits\PermissionTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use App\Models\Permission\Permission;
use App\Models\Traits\RelationCheck;
use Illuminate\Support\Facades\DB;
use Modules\Saas\Entities\PricingPlan;

class PricingPlanRepository
{
    use RelationshipTrait, RelationCheck, ApiReturnFormatTrait, PermissionTrait;

    protected $pricingPlan;

    public function __construct(PricingPlan $pricingPlan)
    {
        $this->pricingPlan = $pricingPlan;
    }

    public function get($id)
    {
        return $this->pricingPlan->query()->where('company_id', getCurrentCompany())->findOrFail($id);
    }

    public function getAll()
    {

        return $this->pricingPlan->query()->where('company_id', getCurrentCompany())->where('status_id', 1)->where('id', '!=', '1')->get();
    }

    public function getPermission()
    {
        return Permission::get();
    }

    public function index()
    {

    }

    function store($request)
    {
        DB::beginTransaction();
        try {
            $pricingPlan = new $this->pricingPlan();
            $pricingPlan->title = $request->title;
            $pricingPlan->subtitle = $request->subtitle;
            $pricingPlan->price = $request->price;
            $pricingPlan->duration_type = $request->duration_type;
            $pricingPlan->is_popular = $request->is_popular;
            $pricingPlan->limitations = $request->limitations;
            $pricingPlan->status_id = $request->status_id;
            $pricingPlan->save();

            DB::commit();
            return true; // Return a success indicator
        } catch (\Exception $e) {
            DB::rollBack();
            return false; // Return a failure indicator
        }
    }

    public function update($request, $id)
    {
        $pricingPlan = $this->pricingPlan->where(['id' => $id])->first();
        if (!$pricingPlan) {
            return $this->responseWithError(_trans('message.Data not found'), 'id', 404);
        }
        try {
            $pricingPlan->title = $request->title;
            $pricingPlan->subtitle = $request->subtitle;
            $pricingPlan->price = $request->price;
            $pricingPlan->duration_type = $request->duration_type;
            $pricingPlan->is_popular = $request->is_popular;
            $pricingPlan->limitations = $request->limitations;
            $pricingPlan->status_id = $request->status_id;
            $pricingPlan->save();

            return $this->responseWithSuccess(_trans('message.Price Plan Update successfully.'), $pricingPlan);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    public function destroy($id)
    {
        $pricingPlan = $this->pricingPlan->where(['id' => $id]);
        if (!$pricingPlan) {
            return $this->responseWithError(_trans('Data not found'), 'id', 404);
        }
        try {
            $pricingPlan->delete();
            return $this->responseWithSuccess(_trans('message.Pricing Plan delete successfully.'), $pricingPlan);
        } catch (\Throwable $th) {
            return $this->responseExceptionError($th->getMessage(), [], 400);
        }
    }

    // new functions

    function fields()
    {
        return [
            _trans('common.ID'),
            _trans('common.Title'),
            _trans('common.Subtitle'),
            _trans('common.Duration'),
            _trans('common.Price'),
            _trans('common.Popular '),
            _trans('common.Limitation '),
            _trans('common.Status'),
            _trans('common.Action'),

        ];
    }

    // data table functions
    function table($request)
    {
        $data = $this->pricingPlan->query()->with('status');
        $where = array();
        if ($request->search) {
            $where[] = ['title', 'like', '%' . $request->search . '%'];
        }
        $data = $data
            ->where($where)
            ->orderBy('id', 'DESC')
            ->paginate($request->limit ?? 10);
        return [
            'data' => $data->map(function ($data) {
                $action_button = '';
                if (hasPermission('pricingPlan_update')) {
                    $action_button .= '<a href="' . route('saas.pricePlan.edit', $data->id) . '" class="dropdown-item"> ' . _trans('common.Edit') . '</a>';
                }
                if (hasPermission('pricingPlan_delete')) {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`admin/saas/price-plans/delete/`)', 'delete');
                }
                $button = ' <div class="dropdown dropdown-action">
                                        <button type="button" class="btn-dropdown" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                        ' . $action_button . '
                                        </ul>
                                    </div>';
                return [
                    'id' => $data->id,
                    'title' => $data->title,
                    'subtitle' => $data->subtitle,
                    'price' => $data->price,
                    'duration_type' => $data->duration_type,
                    'is_popular' => '<small class="badge badge-' . ($data->is_popular == 1 ? 'success' : 'danger') . '">' . ($data->is_popular == 1 ? 'Yes' : 'No') . '</small>',
                    'limitations' => $data->limitations,
                    'status' => '<small class="badge badge-' . @$data->status->class . '">' . @$data->status->name . '</small>',
                    'action' => $button,
                ];
            }),
            'pagination' => [
                'total' => $data->total(),
                'count' => $data->count(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'pagination_html' => $data->links('backend.pagination.custom')->toHtml(),
            ],
        ];

    }

    // statusUpdate
    public function statusUpdate($request)
    {
        try {
            // Log::info($request->all());
            if (@$request->action == 'active') {
                $pricingPlan = $this->pricingPlan->whereIn('id', $request->ids)->update(['status_id' => 1]);
                return $this->responseWithSuccess(_trans('message.Price Plan activate successfully.'), $pricingPlan);
            }
            if (@$request->action == 'inactive') {
                $pricingPlan = $this->pricingPlan->whereIn('id', $request->ids)->update(['status_id' => 4]);
                return $this->responseWithSuccess(_trans('message.Price Plan inactivate successfully.'), $pricingPlan);
            }
            return $this->responseWithError(_trans('message.Price Plan inactivate failed'), [], 400);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

}
