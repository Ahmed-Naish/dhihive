<?php

namespace App\Http\Controllers\Api\V11;

use App\Http\Controllers\Controller;
use Modules\Break\Entities\UserBreak;
use Illuminate\Support\Facades\Schema;
use App\Http\Requests\RemarkApiRequest;
use App\Models\Hrm\Attendance\Attendance;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;

class RemarkApiController extends Controller
{
    use ApiReturnFormatTrait;

    public function __invoke(RemarkApiRequest $request)
    {
        try {

            if ($request->type == 'break') {

                if (Schema::hasTable('user_breaks')) {
                    $break = UserBreak::find($request->id);

                    if (!$break) {
                        return $this->responseWithError(__('Break not found!'), [], 400);
                    }
    
                    $break->update([
                        'remark' => $request->remark
                    ]);

                    return $this->responseWithSuccess(__('Remark has been store successfully!'), [], 200);
                } else {
                    return $this->responseWithError(__('User Breaks table not found!'), [], 400);
                }
            } elseif ($request->type == 'attendance') {
                if (Schema::hasTable('attendances')) {
                    $attendance = Attendance::find($request->id);

                    if (!$attendance) {
                        return $this->responseWithError(__('Attendance not found!'), [], 400);
                    }
    
                    $attendance->update([
                        'late_reason' => $request->remark
                    ]);

                    return $this->responseWithSuccess(__('Remark has been store successfully!'), [], 200);
                } else {
                    return $this->responseWithError(__('Attendances table not found!'), [], 400);
                }
            } else {
                return $this->responseWithError(__('Not found!'), [], 400);
            }

        } catch (\Throwable $th) {
            return $this->responseWithError(__('Something went wrong.'), [$th->getMessage()], 400);
        }
    }
}
