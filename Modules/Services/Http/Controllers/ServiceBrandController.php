<?php

namespace Modules\Services\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Services\Entities\ServiceBrand;
use Illuminate\Contracts\Support\Renderable;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Http\Requests\ServiceReqeust;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Services\Repositories\BrandRepositoryInterface;

class ServiceBrandController extends Controller
{
    use RelationshipTrait,
        ApiReturnFormatTrait;

    protected  $serviceBrandRepo;
    protected $title;
    protected $viwPath;
    protected $createPath;
    protected $createModalPath;
    protected $editPath;
    protected $className;

    public function __construct(BrandRepositoryInterface $serviceBrandRepo)
    {
        $this->serviceBrandRepo = $serviceBrandRepo;
        $this->title = _trans('services.Service Brand');
        $this->viwPath = "services::backend.brand.index";
        $this->createPath = "services::backend.model.index.create";
        $this->createModalPath = "services::backend.modal.create";
        $this->editPath = "services::backend.model.index.edit";
        $this->className = "service_brand_table";
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->serviceBrandRepo->table($request);
        }
        $data = [
            'class' => $this->className,
            'fields' => $this->serviceBrandRepo->fields(),
            'checkbox' => true,
            'title' => $this->title,
        ];
        return view($this->viwPath, compact('data'));
    }
    public function createModal()
    {
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Create') . ' ' . $this->title,
                'url' => route('brands.store'),
                'attributes' => $this->serviceBrandRepo->createAttributes(),
                'button' => _trans('common.Save'),
            ]);
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }
    public function store(ServiceReqeust $request)
    {
        try {
            if (!$request->ajax()) {
                Toastr::error(_trans('response.Please click on button!'), 'Error');
                return redirect()->back();
            }
            return $this->serviceBrandRepo->newStore($request);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
    public function editModal( $id)
    {
        $serviceBrand = $this->serviceBrandRepo->find($id);
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'items' => $serviceBrand,
                'url' => route('brands.update', $serviceBrand->id),
                'attributes' => $this->serviceBrandRepo->editAttributes($serviceBrand),
                'button' => _trans('common.Update'),
            ]);
        } catch (\Throwable $th) {

            return response()->json('failed', 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if (!$request->ajax()) {
                Toastr::error(_trans('response.Please click on button!'), 'Error');
                return redirect()->back();
            }
            return $this->serviceBrandRepo->newUpdate($request, $id);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
    public function delete(ServiceBrand $serviceBrand)
    {

        return $this->serviceBrandRepo->destroy($serviceBrand);
    }

    public function statusUpdate(Request $request)
    {
        return $this->serviceBrandRepo->statusUpdate($request);
    }

    public function deleteData(Request $request)
    {
        return $this->serviceBrandRepo->destroyAll($request);
    }
}
