<?php

namespace Modules\Break\Http\Controllers;

use App\Helpers\CoreApp\Traits\FileHandler;
use App\Models\Upload;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Break\Entities\BreakType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;
use Modules\SpecialAttendance\Entities\DutyCalendar;

class BreakTypeController extends Controller
{
    use FileHandler;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $data['class'] = 'break_type_list';
        $data['title'] = _trans('common.Break Types');
        $data['input'] = $request->all();
        $data['create_url'] = route('break.type.create');
        $data['url'] = route('break.type.table.list');
        return view('break::breaks.types.index', compact('data'));
    }
    public function TableList(Request $request)
    {
        // if ($request->ajax()) {
        $limit = $request->input('entries', 10);
        $page = $request->input('page', 1);

        $items = BreakType::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $items->where('name', 'LIKE', '%' . $search . '%');
        }
        if ($request->has('from') && $request->has('to')) {
            $from = $request->input('from');
            $to = $request->input('to');
            $items->whereBetween('created_at', [$from, $to]);
        }
        $items->where(currentCompanyCurrentBranch());
        $items = $items->orderBy('created_at', 'desc')->paginate($limit, ['*'], 'page', $page);
        $data['items'] = $items;
        return response()->json(['view' => view('break::breaks.types.tableList', compact('data'))->render()]);
        // }
        return response()->json(['view' => ""], 200);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $data['class'] = 'break_type_create';
        $data['title'] = _trans('common.Add Break Type');
        $data['url'] = route('break.type.store');
        return view('break::breaks.types.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'limit' => 'required|integer|min:1',
            'limit_type' => 'required',
            'max_duration' => 'required|integer|min:1',
            'duration_type' => 'required',
            'icon' => 'required|file|mimes:jpg,png,jpeg,gif',
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $breakType = new BreakType();
            $breakType->name = $request->title;
            $breakType->slug = Str::slug($request->title, '-');
            $breakType->description = $request->description;
            $breakType->limit = $request->limit;
            $breakType->limit_type = $request->limit_type;
            $breakType->max_duration = $request->max_duration;
            $breakType->duration_type = $request->duration_type;
            $breakType->is_remark_required = $request->is_remark_required ?? 0;
            // will_ask_next_meal
            $breakType->will_ask_next_meal = $request->will_ask_next_meal ?? 0;
            $breakType->status_id = 1;
            $breakType->company_id = auth()->user()->company_id;
            $breakType->branch_id = auth()->user()->branch_id;

            if ($request->icon) {
                $breakType->icon_id = $this->uploadImage($request->icon, 'uploads/break')->id;
            }

            $breakType->created_by  = auth()->id();
            $breakType->updated_by  = auth()->id();
            $breakType->save();
            Toastr::success("success", "Break type created successfully");
            return redirect()->route('break.type.index');
        } catch (\Throwable $th) {
            dd($th);
            Toastr::success("error", "Something went wrong");
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $data['class'] = 'break_type_create';
        $data['title'] = _trans('common.Edit Break Type');
        $data['url'] = route('break.type.update', $id);
        $data['edit'] = BreakType::find($id);
        return view('break::breaks.types.edit', compact('data'));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('break::show');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'limit' => 'required|integer|min:1',
            'limit_type' => 'required',
            'max_duration' => 'required|integer|min:1',
            'duration_type' => 'required',
            'icon' => 'nullable|file|mimes:jpg,png,jpeg,gif',
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $breakType = BreakType::find($id);

            if ($request->icon) {
                $upload = Upload::find($breakType->icon_id);
                $breakType->icon_id = null;
                $breakType->save();

                if ($upload &&file_exists($upload->img_path)) {
                    unlink($upload->img_path);
                    $upload->delete();
                }
            }

            $breakType->name = $request->title;
            $breakType->slug = Str::slug($request->title, '-');
            $breakType->description = $request->description;
            $breakType->limit = $request->limit;
            $breakType->limit_type = $request->limit_type;
            $breakType->max_duration = $request->max_duration;
            $breakType->duration_type = $request->duration_type;
            $breakType->is_remark_required = $request->is_remark_required ?? 0;
            // will_ask_next_meal
            $breakType->will_ask_next_meal = $request->will_ask_next_meal ?? 0;
            $breakType->status_id = $request->status_id;

            if ($request->icon) {
                $breakType->icon_id = $this->uploadImage($request->icon, 'uploads/break')->id;
            }

            $breakType->updated_by  = auth()->id();
            $breakType->save();
            Toastr::success("success", "Break type updated successfully");
            return redirect()->route('break.type.index');
        } catch (\Throwable $th) {
            dd($th);
            Toastr::success("error", "Something went wrong");
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //destroy
        $breakType = BreakType::find($id);

        $upload = Upload::find($breakType->icon_id);
        $breakType->icon_id = null;
        $breakType->save();

        if ($upload &&file_exists($upload->img_path)) {
            unlink($upload->img_path);
            $upload->delete();
        }

        $breakType->delete();
        Toastr::success("success", "Break type deleted successfully");
        return redirect()->route('break.type.index');
    }

   
}
