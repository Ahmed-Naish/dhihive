<?php

namespace Modules\Saas\Repositories;

use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Helpers\CoreApp\Traits\AuthorInfoTrait;
use App\Helpers\CoreApp\Traits\FileHandler;
use App\Models\coreApp\Relationship\RelationshipTrait;
use App\Models\Traits\RelationCheck;
use Illuminate\Support\Str;
use Modules\Saas\Entities\ContactMessage;

class ContactMessageRepository
{

    use AuthorInfoTrait, ApiReturnFormatTrait, FileHandler;
    protected $model;

    public function __construct(ContactMessage $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function getAll()
    {
        return $this->model::query()->get();
    }

    public function getActiveAll()
    {
        return $this->model::query()->get();
    }

    public function fields()
    {
        return [
            _trans('common.ID'),
            _trans('common.Subject'),
            _trans('common.Name'),
            _trans('common.Email'),
            _trans('common.Message'),
            _trans('common.Action'),
        ];
    }

    public function table($request)
    {
        $data = $this->model->query();
        $where = array();

        if ($request->search) {
            $where[] = ['subject', 'like', '%' . $request->search . '%'];
        }
        $data = $data
            ->where($where)
            ->paginate($request->limit ?? 2);

        return [
            'data' => $data->map(function ($data) {
                $action_button = '';

                $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`admin/saas/contact-messages/delete/`)', 'delete');

                $button   = '<div class="dropdown dropdown-action">
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
                    'subject' => $data->subject,
                    'name' => $data->name,
                    'email' => $data->email,
                    'message' => $data->message,
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

    public function dataTable($request, $id = null)
    {
        $model = $this->model->query()->with('status');
        if (@$request->from && @$request->to) {
            $model = $model->whereBetween('created_at', start_end_datetime($request->from, $request->to));
        }
        if (@$id) {
            $model = $model->where('id', $id);
        }

        return datatables()->of($model->orderBy('id', 'DESC')->get())
            ->addColumn('action', function ($data) {
                $action_button = '';
                $edit = _trans('common.Edit');
                $delete = _trans('common.Delete');
                if (hasPermission('cms_edit')) {
                    $action_button .= '<a href="' . route('saas.cms.edit', $data->id) . '" class="dropdown-item"> ' . $edit . '</a>';
                }
                if (hasPermission('cms_delete')) {
                    $action_button .= actionButton($delete, '__globalDelete(' . $data->id . ',`admin/saas/currencies/delete/`)', 'delete');
                }
                $button = getActionButtons($action_button);
                return $button;
            })
            ->addColumn('title', function ($data) {
                return @$data->title;
            })
            ->addColumn('status', function ($data) {
                return '<span class="badge badge-' . @$data->status->class . '">' . @$data->status->name . '</span>';
            })
            ->rawColumns(array('title', 'status', 'action'))
            ->make(true);
    }

    public function destroy($request, $id)
    {
        try {
            if ($id) {
                $model = $this->model->where('id', $id)->delete();
                return $this->responseWithSuccess(_trans('message.Delete successfully.'), $model);
            } else {
                return $this->responseWithError(_trans('message.Item not found'), [], 400);
            }
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
