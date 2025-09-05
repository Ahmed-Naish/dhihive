<?php

namespace Modules\Services\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Services\Entities\ServiceInstitution;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Http\Requests\ServiceReqeust;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Services\Repositories\InstitutionRepositoryInterface;

class ServiceInstitutionController extends Controller
{
    use RelationshipTrait,
        ApiReturnFormatTrait;

    protected  $serviceInstitutionRepo;
    protected $title;
    protected $viwPath;
    protected $createPath;
    protected $createModalPath;
    protected $editPath;
    protected $className;

    public function __construct(InstitutionRepositoryInterface $serviceInstitutionRepo)
    {
        $this->serviceInstitutionRepo = $serviceInstitutionRepo;
        $this->title = _trans('services.Service institution');
        $this->viwPath = "services::backend.institution.index";
        $this->createPath = "services::backend.model.index.create";
        $this->createModalPath = "services::backend.modal.create";
        $this->editPath = "services::backend.model.index.edit";
        $this->className = "service_institution_table";
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->serviceInstitutionRepo->table($request);
        }
        $data = [
            'class' => $this->className,
            'fields' => $this->serviceInstitutionRepo->fields(),
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
                'url' => route('institutions.store'),
                'attributes' => $this->serviceInstitutionRepo->createAttributes(),
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
            return $this->serviceInstitutionRepo->newStore($request);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
    public function editModal( $id)
    {
        $serviceInstitution = $this->serviceInstitutionRepo->find($id);
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'items' => $serviceInstitution,
                'url' => route('institutions.update', $serviceInstitution->id),
                'attributes' => $this->serviceInstitutionRepo->editAttributes($serviceInstitution),
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
            return $this->serviceInstitutionRepo->newUpdate($request, $id);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
    public function delete(ServiceInstitution $serviceInstitution)
    {

        return $this->serviceInstitutionRepo->destroy($serviceInstitution);
    }

    public function statusUpdate(Request $request)
    {
        return $this->serviceInstitutionRepo->statusUpdate($request);
    }

    public function deleteData(Request $request)
    {
        return $this->serviceInstitutionRepo->destroyAll($request);
    }
}
