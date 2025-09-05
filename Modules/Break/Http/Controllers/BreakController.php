<?php

namespace Modules\Break\Http\Controllers;

use BaconQrCode\Writer;

use Illuminate\Http\Request;
use App\Models\Company\Company;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Break\Entities\BreakType;
use Modules\Break\Entities\UserBreak;
use BaconQrCode\Renderer\ImageRenderer;
use Modules\Break\Entities\BreakSetting;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class BreakController extends Controller
{
    use ApiReturnFormatTrait;
    public function index(Request $request)
    {
        $data['class'] = 'break_list';
        $data['title'] = _trans('common.Breaks');
        $data['input'] = $request->all();
        $data['url']   = route('break.table.list');

        return view('break::breaks.index', compact('data'));
    }

    public function TableList(Request $request)
    {
        if ($request->ajax()) {
            $limit = $request->input('entries', 10);
            $page = $request->input('page', 1);

            $items = UserBreak::query();

            if ($request->has('search')) {
                $search = $request->input('search');
                $items->where('name', 'LIKE', '%' . $search . '%');
            }
            if ($request->has('from') && $request->has('to')) {
                $from = $request->input('from');
                $to = $request->input('to');
                $items->whereBetween('created_at', [$from, $to]);
            }

            $items = $items->orderBy('created_at', 'desc')->paginate($limit, ['*'], 'page', $page);

            $data['items'] = $items;

            return response()->json(['view' => view('break::breaks.tableList', compact('data'))->render()]);
        }

        return response()->json(['view' => ""], 200);
    }

    public function create()
    {
        $data['class']       = 'break_create';
        $data['title']       = _trans('common.Add Break');
        $data['break_types'] = BreakType::where('status_id', 1)->where(currentCompanyCurrentBranch())->get();
        $data['url']         = route('break.store');

        return view('break::breaks.create', compact('data'));
    }

    public function edit($id)
    {
        $data['break'] = UserBreak::find($id);
        if ($data['break']) {
            $data['class'] = 'break_create';
            $data['title'] = _trans('common.Back Break');
            $data['break_types'] = BreakType::all();
            $data['url'] = route('break.update');
            $data['today_breaks'] = UserBreak::with('breakType')
                ->where('user_id', Auth::user()->id)
                ->where('date', date('Y-m-d'))
                ->whereNotNull('end_time')
                ->get();
            // Calculate the sum of durations
            $totalDuration = DB::table('user_breaks')
                ->where('user_id', Auth::user()->id)
                ->where('date', date('Y-m-d'))
                ->whereNotNull('end_time')
                ->sum(DB::raw("TIME_TO_SEC(duration)"));

            $totalDurationFormatted = sprintf('%02d:%02d:%02d', ($totalDuration / 3600), ($totalDuration / 60 % 60), $totalDuration % 60);
            $data['totalDuration'] = $totalDurationFormatted;

            return view('break::breaks.edit', compact('data'));
        } else {
            return redirect()->route('break.index');
        }
    }

    public function store(Request $request)
    {
        try {
            $breakType = BreakType::find($request->break_type_id ?? 1);
            $redirectUrl = route('break.index');

            if ($request->type == "break_back") {
                $user_break_id = $request->user_break_id;
                $break         = UserBreak::find($user_break_id);
            } else {
                $break = new UserBreak();

                $break->user_id       = Auth::user()->id;
                $break->break_type_id = $request->break_type_id ?? 1;
                $break->date          = date('Y-m-d');
                $break->start_time    = date('H:i:s');
                $break->end_time      = null;
                $break->reason        = $request->reason ?? $breakType->name;
                $break->remark        = $request->remark ?? null;
                $break->created_by    = auth()->id();
                $break->updated_by    = auth()->id();
                $break->save();
                $redirectUrl = route('break.edit', $break->id);
            }

            return response()->json([
                'status'   => 'success',
                'message'  => 'Let\'s take a break',
                'input'    => $request->all(),
                'redirect' => $redirectUrl,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'   => 'error',
                'message'  => $th->getMessage(),
                'input'    => $request->all(),
                'redirect' => $redirectUrl,
            ]);
        }
    }

    public function show($id)
    {
        return view('break::show');
    }

    public function update(Request $request)
    {
        try {
            $breakType = BreakType::find($request->break_type_id ?? 1);
            $redirectUrl = route('break.index');

            if ($request->type == "break_back") {
                $user_break_id = $request->user_break_id;
                $break = UserBreak::find($user_break_id);
                $break->end_time = date('H:i:s');
                $break->reason = $request->reason ?? $breakType->name;
                $break->remark = $request->remark ?? null;
                // duration
                $start = strtotime($break->start_time);
                $end = strtotime(date('H:i:s'));
                $diff = $end - $start;
                $break->duration = gmdate('H:i:s', $diff); // 00:00:00
                $break->updated_by  = auth()->id();
                $break->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Thanks, Let\'s get back to work.',
                'input' => $request->all(),
                'redirect' => $redirectUrl,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'input' => $request->all(),
                'redirect' => $redirectUrl,
            ]);
        }
    }

    public function destroy($id)
    {
        //destroy
        $break = UserBreak::find($id);
        $break->delete();
        return response()->json(['success', 'Break deleted successfully']);
    }

    // qrcode || qr code
    public  function qrcode()
    {
        $data['class'] = 'break_qrcode';
        $data['title'] = "QR Code";
        $data['url'] = route('break.qrcode.generate');
        $data['break_settings'] = BreakSetting::where(currentCompanyCurrentBranch())->first();
        return view('break::breaks.qrcode', compact('data'));
    }

    public function generate(Request $request)
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

            $encrypt_code = encrypt($encrypt_text);

            if ($request->has('title')) {
                $encrypt_text = $request->title;
                $encrypt_code = encrypt($encrypt_text);
            }

            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new SvgImageBackEnd()
            );
            
            $writer = new Writer($renderer);
            $qrCode = $writer->writeString($encrypt_code);

            $directory = public_path('uploads/qrcodes');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $svgFilePath = 'uploads/qrcodes/' . uniqid() . '.svg';
            file_put_contents(public_path($svgFilePath), $qrCode);

            BreakSetting::where('company_id', userCompanies())
                ->update([
                    'path' => $svgFilePath,
                    'encrypt_text' => $encrypt_text,
                    'encrypt_code' => $encrypt_code
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'QR Code generated successfully',
                'text' => 'Generated at ' . date('d M Y h:i A') . ' By ' . auth()->user()->name,
                'path' => url($svgFilePath)
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => 'failed',
                'text' => 'Failed to generate QR Code',
                'path' => "",
                'message' => $th->getMessage(),
            ]);
        }
    }

    // verify barcode
    public function verify(Request $request)
    {
        $code = BreakSetting::where(currentCompanyCurrentBranch())->where('encrypt_code', $request->code)->first();
        if ($code) {
            return response()->json([
                'status' => 'success',
                'message' => 'Code verified successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid code',
            ]);
        }
    }
    // Verify barcode
    public function verifyTest($code)
    {
        try {

            $decrypt_text = decrypt($code);

            if ($decrypt_text === 'hrm.imprintdhaka.com') {

                $breakTypes = BreakType::query()
                    ->where('status_id', 1)
                    ->where(currentCompanyCurrentBranch())
                    ->get(['id', 'name', 'will_ask_next_meal', 'is_remark_required']);

                return $this->responseWithSuccess('Success', $breakTypes, 200);
            } else {
                return $this->responseWithError(__('Invalid Code'), [], 200);
            }
        } catch (\Throwable $th) {

            return $this->responseWithError(__('Invalid Code'), [], 200);
        }
    }
}
