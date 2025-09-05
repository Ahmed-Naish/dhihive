<?php

namespace Modules\MultiShift\Repositories;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\AttendanceStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Hrm\Attendance\Attendance;
use Illuminate\Support\Facades\Validator;
use App\Helpers\CoreApp\Traits\DateHandler;
use App\Helpers\CoreApp\Traits\FileHandler;
use App\Models\Hrm\Attendance\DutySchedule;
use App\Models\Hrm\Attendance\LateInOutReason;
use App\Helpers\CoreApp\Traits\GeoLocationTrait;
use App\Helpers\CoreApp\Traits\TimeDurationTrait;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use App\Models\UserShiftAssign;

class MultiShiftRepository
{
    use ApiReturnFormatTrait, RelationshipTrait, TimeDurationTrait, GeoLocationTrait, DateHandler, FileHandler;

    protected $user;
    protected $userShiftAssign;

    public function __construct(User $user, UserShiftAssign $userShiftAssign) {
        $this->user = $user;
        $this->userShiftAssign = $userShiftAssign;
    }

    // public function getMultipleShift()
    // {
    //     $user = $this->user->query()
    //         ->where('id', auth()->user()->id)
    //         ->where('company_id', getCurrentCompany())
    //         ->first();

    //     foreach ($user->shifts as $key => $item) {
    //         $user_shift_list[] = [
    //             'id' => $item->id,
    //             'name' => $item->shift->name,
    //         ];
    //     }
    //     return $user_shift_list;
    // }
    
}
