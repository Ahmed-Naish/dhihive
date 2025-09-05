<?php

namespace Modules\Saas\Http\Controllers\API;

use Illuminate\Routing\Controller;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\Company\Company;
use Illuminate\Http\Request;

class TenantCompanyAPIController extends Controller
{
    use ApiReturnFormatTrait;
    
    public function toggleTenantCompanyStatus(Request $request)
    {
        try {
            $status_id = $request->status == 1 ? 4 : 1;

            Company::where('id', 1)->update([
                'status_id' => $status_id
            ]);

            $message = $request->status ? _trans('message.Status has been inactivated successfully') : _trans('message.Status has been activated successfully');

            return $this->responseWithSuccess($message);

        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), $th->getMessage());
        }
    }
}
