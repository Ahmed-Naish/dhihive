<?php

namespace Modules\Break\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Models\Hrm\Department\Department;
use Illuminate\Support\Facades\Validator;
use Modules\SpecialAttendance\Entities\DutyCalendar;

class MealController extends Controller
{

    // meals
    public function meals()
    {
        $data['class'] = 'meals_table';
        $data['title'] = 'Meals';
        $data['input'] = request()->all();
        $data['url'] = route('meals.search');
        $data['departments'] = Department::where('status_id', 1)->where(currentCompanyCurrentBranch())->get();
        return view('break::meals.index', compact('data'));
    }

    public function search(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');
        $nextDate = date('Y-m-d', strtotime($date . ' +1 day'));

        //today formated date 5 math 2024
        $data['today'] = date('d M Y', strtotime($date));
        $data['next_day'] = date('d M Y', strtotime($nextDate));
        $data['class'] = 'meals_search_table';
        $data['title'] = 'Meals';
        $data['input'] = request()->all();
        $query = DutyCalendar::with(['employee'])
            ->select('id', 'employee_id', 'date', 'is_take_meal', 'created_by', 'updated_by')
            ->addSelect([
                'next_day_meal' => DB::table('duty_calendars as dc2')
                    ->select('is_take_meal')
                    ->whereColumn('dc2.employee_id', 'duty_calendars.employee_id')
                    ->where('dc2.date', '=', $nextDate)
                    ->limit(1)
            ])
            ->where('date', $date)
            ->orderBy('employee_id', 'asc');

        if ($request->department_id != null && $request->department_id != "All") {
            $query = $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->type != "both" && $request->type != null) {
            $query = $query->where('is_take_meal', $request->type);
        }

        $data['data'] = $query->clone()->paginate(30);
        $data['today_meal'] = $query->clone()->where('is_take_meal', 1)->count();

        $data['next_day_meal_count'] = $query->clone()->get()->filter(function ($item) {
            return $item->next_day_meal == 1;
        })->count();


        $data['departments'] = Department::where('status_id', 1)->where(currentCompanyCurrentBranch())->get();

        $data['url'] = route('meals.search');
        return view('break::meals.index', compact('data'));
    }



    /**
     * Update the status of a meal for a user on a specific date
     *
     * @
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @param Request $request 
     */
    public function updateStatus(Request $request)
    {
        // Validate request data
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'type' => 'required|in:is_take_meal,next_day_meal',
            'status' => 'required|boolean',
        ]);

        $userId = $request->input('user_id');
        $date = $request->input('date');
        $type = $request->input('type');
        $status = $request->input('status');

        try {
            // Find the record by user_id and date
            $dutyCalendar = DutyCalendar::where('employee_id', $userId)
                ->where('date', $date)
                ->first();

            if (!$dutyCalendar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Duty Schedule not found'
                ], 404);
            }

            // Check for required duty_schedule_id if updating next_day_meal
            if ($type == 'next_day_meal' && is_null($dutyCalendar->duty_schedule_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Duty Schedule not found'
                ], 400);
            }
            // Update the status field
            $dutyCalendar->is_take_meal = $status;
            $dutyCalendar->updated_by = Auth::id();
            $dutyCalendar->updated_at = now();
            $dutyCalendar->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            // Log error and return error response
            Log::error('Failed to update status', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'date' => $date,
                'type' => $type,
                'status' => $status
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
