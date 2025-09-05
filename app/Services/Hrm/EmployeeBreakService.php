<?php

namespace App\Services\Hrm;

use DateTime;
use Validator;
use Carbon\Carbon;
use App\Services\Core\BaseService;
use Illuminate\Support\Facades\DB;
use App\Models\Expenses\HrmExpense;
use Modules\Break\Entities\UserBreak;
use App\Models\Hrm\Attendance\Attendance;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\CoreApp\Traits\DateHandler;
use App\Models\Hrm\Attendance\EmployeeBreak;
use Modules\Break\Transformers\BreakResource;
use App\Helpers\CoreApp\Traits\TimeDurationTrait;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use App\Http\Resources\Hrm\Attendance\BreakBackCollection;
use App\Http\Resources\Hrm\Attendance\BreakBackStaticsCollection;

class EmployeeBreakService extends BaseService
{
    use RelationshipTrait, DateHandler, ApiReturnFormatTrait, TimeDurationTrait;

    protected $attendance;

    public function __construct(Attendance $attendance, EmployeeBreak $employeeBreak)
    {
        $this->attendance = $attendance;
        $this->model = $employeeBreak;
    }

    public function breakBackList()
    {
        $validator = Validator::make(\request()->all(), [
            'date' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseWithError(__('Validation field required'), $validator->errors(), 422);
        }

        $breakList = $this->model->query()
            ->where('company_id', $this->companyInformation()->id)
            ->whereNotNull('back_time')
            ->when(\request()->get('date'), function (Builder $builder) {
                return $builder->where('date', \request()->get('date'));
            })->paginate(10);


        $totalBreakTime = 0;
        $totalBackTime = 0;
        $totalBreakTimeCount = 0;
        foreach ($breakList as $item) {
            $totalBreakTime += $this->timeToSeconds($item->break_time);
            $totalBackTime += $this->timeToSeconds($item->back_time);
        }
        $totalBreakTimeCount = $this->totalSpendTime($totalBreakTime, $totalBackTime);
        $data['total_break_time'] = $totalBreakTimeCount;
        $data['has_break'] = $breakList->count() > 0 ? true : false;
        $data['break_history'] = new BreakBackCollection($breakList);

        return $this->responseWithSuccess('Employees break history', $data, 200);
    }

    public function breakStartEnd($request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'time' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseWithError(__('Validation field required'), $validator->errors(), 422);
        }
        try {
            if ($slug === 'start') {
                $attendance = $this->attendance->query()
                    ->where('user_id', auth()->user()->id)
                    ->where('date', date('Y-m-d'))
                    ->first();
                if ($attendance) {
                    //take Break
                    $takeBreak = $this->model->query()
                        ->where([
                            'company_id' => $this->companyInformation()->id,
                            'user_id' => auth()->id(),
                            'date' => date('Y-m-d'),
                        ])->whereNull('back_time')
                        ->orderByDesc('id')->first();
                    if ($takeBreak) {
                        $takeBreak->back_time = $request->time;
                        $takeBreak->save();
                        $takeBreak['status'] = "break_out";
                        return $this->responseWithSuccess('Your last break has been end', $takeBreak, 200);
                    } else {
                        $break = $this->model->create([
                            'company_id' => $this->companyInformation()->id,
                            'user_id' => auth()->id(),
                            'date' => date('Y-m-d'),
                            'break_time' => $request->time,
                            'back_time' => null,
                            'reason' => 'Break'
                        ]);
                        $break['status'] = "break_in";
                        return $this->responseWithSuccess('Break start successfully', $break, 200);
                    }
                } else {
                    $break = $this->model->create([
                        'company_id' => $this->companyInformation()->id,
                        'user_id' => auth()->id(),
                        'date' => date('Y-m-d'),
                        'break_time' => $request->time,
                        'back_time' => null,
                        'reason' => $request->reason ?? 'Break'
                    ]);

                    $break['status'] = "break_in";
                    return $this->responseWithSuccess('Break start successfully', $break, 200);
                }
            } else {
                //Check break started
                $takeBreak = $this->model->query()
                    ->where([
                        'company_id' => $this->companyInformation()->id,
                        'user_id' => auth()->id(),
                        'date' => date('Y-m-d'),
                    ])->whereNull('back_time')
                    ->orderByDesc('id')->first();
                if ($takeBreak) {
                    $takeBreak->back_time = $request->time;
                    $takeBreak->save();
                    $takeBreak['status'] = "break_out";
                    return $this->responseWithSuccess('Break End successfully', $takeBreak, 200);
                } else {
                    return $this->responseWithSuccess('Already break end', [], 200);
                }
            }
        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('response.Something went wrong.'), [], 400);
        }
    }

    public function breakBackEnd($request)
    {
        try {
            $break  = UserBreak::where([
                        'user_id' => auth()->id(),
                        'date' => date('Y-m-d'),
                    ])
                    ->whereNotNull('start_time')
                    ->whereNull('end_time')
                    ->first();

            if (!$break) {
                $break              = UserBreak::create([
                    'user_id'       => auth()->id(),
                    'break_type_id' => $request->break_type_id,
                    'date'          => date('Y-m-d'),
                    'start_time'    => Carbon::parse(now())->format('H:i:s'),
                    'created_by'    => auth()->id()
                ]);

                $message = _trans("Let's take a break!");
            } else {
                $startTime = new DateTime($break->start_time);
                $endTime = new DateTime(now());
                
                if ($endTime < $startTime) {
                    $endTime->modify('+1 day');
                }
                
                $interval = $startTime->diff($endTime);
                $duration = $interval->format('%H:%I:%S');
                
                $break->update([
                    'end_time'      => Carbon::parse(now())->format('H:i:s'),
                    'duration'      => $duration,
                    'remark'        => $request->remark,
                    'updated_by'    => auth()->id()
                ]);

                $message = _trans("Thanks, Let's get back to work!");
            }

            $breaks = BreakResource::collection(
                        UserBreak::where([
                            'user_id' => auth()->id(),
                            'date' => date('Y-m-d'),
                        ])
                        ->get()
                    );

            $data['break'] = new BreakResource($break);
            $data['today_history'] = $breaks;
            
            return $this->responseWithSuccess($message, $data, 200);

        } catch (\Throwable $th) {
            info($th->getMessage());
            return $this->responseWithError(_trans('response.Something went wrong.'), [], 400);
        }
    }

    public function breakStartEndWeb($request, $slug)
    {
        try {
            $takeBreak = $this->model->query()
                ->where([
                    'company_id' => $this->companyInformation()->id,
                    'user_id' => auth()->id(),
                    'date' => date('Y-m-d'),
                ])->whereNull('back_time')
                ->orderByDesc('id')->first();
            if ($takeBreak) {
                $takeBreak->back_time = $request->time;
                $takeBreak->save();
                return $takeBreak;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function breakBackHistory($request)
    {
        // if (appSuperUser() || $request->user_id) {
        //     $validator = Validator::make($request->all(), [
        //         'user_id' => 'required',
        //     ]);

        //     if ($validator->fails()) {
        //         return $this->responseWithError(__('Validation field required'), $validator->errors(), 422);
        //     }

        //     $userId = $request->user_id;
        // } else {
        //     $userId = auth()->user()->id;
        // }

        // code from hrm-api-2.0  
        if(!$request->user_id){  // logic added new
            $userId = auth()->user()->id;
        } else if (appSuperUser() || $request->user_id) {
            $userId = $request->user_id;
        } else {
            $userId = auth()->user()->id;
        }
        // code from hrm-api-2.0 end

        $totalBreakBacks = $this->model->query()->where('user_id', $userId)->whereNotNull('back_time');
        $totalBreakBacks->when(\request()->get('date'), function (Builder $builder) {
            return $builder->where('date', \request()->get('date'));
        });
        if (!\request()->get('date')) {
            $totalBreakBacks = $totalBreakBacks->where('date', date('Y-m-d'));
        }
        $totalBreakBacks = $totalBreakBacks->orderBy('created_at', 'DESC')->paginate(10);
        $totalBreakTime = 0;
        $totalBackTime = 0;
        $totalBreakTimeCount = 0;
        foreach ($totalBreakBacks as $item) {
            $totalBreakTime += $this->timeToSeconds($item->break_time);
            $totalBackTime += $this->timeToSeconds($item->back_time);
        }
        $totalBreakTimeCount = $this->totalSpendTime($totalBreakTime, $totalBackTime);
        $data['total_break_time'] = $totalBreakTimeCount;
        $data['has_break'] = $totalBreakBacks->count() > 0 ? true : false;
        $data['break_history'] = new BreakBackCollection($totalBreakBacks);

        return $this->responseWithSuccess('Break history', $data, 200);
    }

    public function breakBackHistoryStatics($request)
    {
        $breaks     = UserBreak::where([
            'user_id' => auth()->id(),
            'date' => date('Y-m-d'),
        ])
        ->get();

        $totalBreakTimeCount = 0;

        foreach ($breaks as $break) {
        $totalBreakTimeCount += strtotime($break->duration) - strtotime('TODAY');
        }

        $data['total_break_time'] = gmdate('H:i:s', $totalBreakTimeCount);
        $data['has_break'] = count($breaks) ? true : false;
        $data['break_history'] = ['today_history' => BreakResource::collection($breaks)];

        return $this->responseWithSuccess('Break history', $data, 200);
    }

    public function breakBackTodayHistoryStatics($request)
    {
        if (appSuperUser()) {
            $userId = $request->user_id;
        } else {
            $userId = auth()->user()->id;
        }
    
        $currentDate = date('Y-m-d');
        $totalBreakBacks = $this->model->query()
            ->where('user_id', $userId)
            ->whereNotNull('back_time')
            ->where('date', $currentDate)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
    
        return new BreakBackStaticsCollection($totalBreakBacks);
    }
    
    public function userBreakHistory($request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseWithError(__('Validation field required'), $validator->errors(), 422);
        }
        $totalBreakBacks = $this->model->query()->where('user_id', $request->user_id)->whereNotNull('back_time');
        $totalBreakBacks->when(\request()->get('date'), function (Builder $builder) {
            return $builder->where('date', \request()->get('date'));
        });
        if (!\request()->get('date')) {
            $totalBreakBacks = $totalBreakBacks->where('date', date('Y-m-d'));
        }
        $totalBreakBacks = $totalBreakBacks->orderBy('created_at', 'DESC')->paginate(10);
        $totalBreakTime = 0;
        $totalBackTime = 0;
        $totalBreakTimeCount = 0;
        foreach ($totalBreakBacks as $item) {
            $totalBreakTime += $this->timeToSeconds($item->break_time);
            $totalBackTime += $this->timeToSeconds($item->back_time);
        }
        $totalBreakTimeCount = $this->totalSpendTime($totalBreakTime, $totalBackTime);
        $data['total_break_time'] = $totalBreakTimeCount;
        $data['has_break'] = $totalBreakBacks->count() > 0 ? true : false;
        $data['break_history'] = new BreakBackCollection($totalBreakBacks);

        return $this->responseWithSuccess('Break history', $data, 200);
    }

    public function isBreakRunning()
    {
        $break = UserBreak::where([
            'user_id' => auth()->id(),
            'date' => date('Y-m-d'),
        ])
        ->whereNotNull('start_time')
        ->whereNull('end_time')
        ->first();

        if ($break) {
            return new BreakResource($break);
        } else {
            return [
                'id'            => null,
                'break_type_id' => null,
                'break_type'    => null,
                'date'          => null,
                'start_time'    => null,
                'end_time'      => null,
                'duration'      => null,
                'reason'        => null
            ];
        }
    }
}
