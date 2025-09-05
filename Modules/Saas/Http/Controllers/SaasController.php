<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company\Company;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Support\Renderable;
use Modules\Saas\Repositories\CompanyRepository;

class SaasController extends Controller
{

    public function ajaxCompanyChange(Request $request)
    {

        try {
            // Get the company ID and encrypt it for use in the redirect URL
            $id = $request->company??1;
            $encryptedId = $request->company;

            setCurrentCompany($id);
            $default_branch = DB::table('branches')->where('company_id', $id)->first()->id;

            Session::put('saas_company', $id);
            Session::put('session_branch_id', $default_branch);

            // Find the company in the database
            $company = Company::find($id);
            if ($company) {
                // If the company exists, return a JSON response with data, success message, 200 status code, and a redirect URL to the company dashboard
                return response()->json([
                    'data' => $company,
                    'message' => 'success',
                    'code' => 200,
                    'redirect_url' => route('admin.company.dashboard', $encryptedId),
                    'default_branch' => $default_branch,
                ]);
            } else {
                // If the company doesn't exist, return a JSON response with empty data, failed message, 404 status code, and a redirect URL to the admin dashboard
                return response()->json([
                    'data' => $company,
                    'message' => 'failed',
                    'code' => 404,
                    'redirect_url' => route('admin.dashboard'),
                ]);
            }
        } catch (Exception $e) {
            // If an exception occurs, return a JSON response with an error message and a 500 status code
            return response()->json([
                'message' => 'error',
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function changeBranch(Request $request)
    {


        try {
            session(['session_branch_id' => $request->branch_id]);
            return response()->json([
                'success' => true,
                'message' => _trans('response.Branch changed successfully.'),
                'result' => null
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => _trans('response.Something went wrong.'),
                'result' => null
            ]);
        }
    }

}
