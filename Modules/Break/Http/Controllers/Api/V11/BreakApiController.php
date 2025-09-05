<?php

namespace Modules\Break\Http\Controllers\Api\V11;

use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Break\Entities\BreakType;
use Modules\Break\Entities\UserBreak;
use Modules\Break\Entities\BreakSetting;
use Modules\Break\Transformers\BreakResource;
use Modules\Break\Http\Requests\EndBreakRequest;
use Modules\Break\Http\Requests\StartBreakRequest;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\Company\Company;
use Modules\Break\Transformers\BreakTypeApiResource;

class BreakApiController extends Controller
{
    use ApiReturnFormatTrait;

    public function index()
    {
        try {

            $breaks = BreakResource::collection(
                UserBreak::with('breakType')->where('user_id', auth()->id())->where('date', request('date') ?? date('Y-m-d'))->get()
            );

            return $this->responseWithSuccess('Success', $breaks, 200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('Something went wrong.'), [$th->getMessage()], 400);
        }
    }

    public function qrCodeVerify($code)
    {
        try {

            $parsedUrl = parse_url(url()->full());
            $host = $parsedUrl['host'];
            
            $company_id = Company::where('subdomain', $host)->first()?->id;

            $breakSetting = BreakSetting::where('company_id', $company_id)->first();

            if (config('app.mood') != 'Saas' && !isModuleActive('Saas')) {
                $encrypt_text = config('app.domain');
            } else {
                $encrypt_text = @$breakSetting->encrypt_text ?? @auth()->user()->company->subdomain;
            }
            
            $decrypt_text = decrypt($code);

            if ($decrypt_text === $encrypt_text) {

                $breakTypes = BreakType::query()
                    ->where('status_id', 1)
                    ->where(currentCompanyCurrentBranch())
                    ->get();

                return $this->responseWithSuccess('Success', BreakTypeApiResource::collection($breakTypes), 200);
            } else {
                return $this->responseWithError(__('Invalid Code'), [], 200);
            }
        } catch (\Throwable $th) {

            return $this->responseWithError(__('Invalid Code'), [], 200);
        }
    }

    public function start(StartBreakRequest $request, $break_type_id)
    {
        try {

            $break              = UserBreak::create([
                'user_id'       => auth()->id(),
                'break_type_id' => $break_type_id,
                'date'          => date('Y-m-d'),
                'start_time'    => Carbon::parse($request->start_time)->format('H:i:s'),
                'created_by'    => auth()->id()
            ]);

            $data['ask_for_next_day_meal'] = $break->breakType->will_ask_next_meal;
            $data['is_remark_required'] = $break->breakType->is_remark_required;
            $data['break'] = new BreakResource($break);

            return $this->responseWithSuccess(_trans("Let's take a break!"), $data, 200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('Something went wrong.'), [$th->getMessage()], 400);
        }
    }

    public function end(EndBreakRequest $request, $break_id)
    {
        try {

            $break = UserBreak::with('breakType')->find($break_id);

            if (!$break) {
                return $this->responseWithError(__('Break not found'), [], 404);
            }

            $startTime = new DateTime($break->start_time);
            $endTime = new DateTime($request->end_time);

            if ($endTime < $startTime) {
                $endTime->modify('+1 day');
            }

            $interval = $startTime->diff($endTime);
            $duration = $interval->format('%H:%I:%S');

            $break->update([
                'end_time'      => Carbon::parse($request->end_time)->format('H:i:s'),
                'duration'      => $duration,
                'remark'        => $request->remark,
                'updated_by'    => auth()->id()
            ]);

            return $this->responseWithSuccess(_trans("Thanks, Let's get back to work!"), new BreakResource($break), 200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('Something went wrong.'), [$th->getMessage()], 400);
        }
    }
}
