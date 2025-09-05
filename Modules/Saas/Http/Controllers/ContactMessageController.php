<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Saas\Http\Requests\CmsRequest;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use Modules\Saas\Repositories\ContactMessageRepository;

class ContactMessageController extends Controller
{
    use ApiReturnFormatTrait;

    protected $modelRepository;

    public function __construct(ContactMessageRepository $modelRepository)
    {
        $this->modelRepository = $modelRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->modelRepository->table($request);
        }
        $data = [
            'class' => 'contact_message_datatable',
            'fields' => $this->modelRepository->fields(),
            'checkbox' => false,
            'title' => _trans('saas.Contact Message'),
        ];
        return view('saas::backend.contact-message.index', compact('data'));
    }


    public function delete(Request $request, $id)
    {
        try {
            $result = $this->modelRepository->destroy($request, $id);
            
            if ($result->original['result']) {
                Toastr::success($result->original['message'], 'Success');
                return redirect()->route('saas.contact-messages.index');
            } else {
                Toastr::error($result->original['message'], 'Error');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }
}
