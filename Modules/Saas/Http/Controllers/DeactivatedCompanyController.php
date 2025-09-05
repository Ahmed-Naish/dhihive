<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Routing\Controller;

class DeactivatedCompanyController extends Controller
{
    public function __invoke()
    {
        if (
            !isMainCompany() && 
            config('app.mood') == 'Saas' && 
            isModuleActive('Saas') && 
            !checkSingleCompanyIsDeactivated()
        ) {
            return redirect()->route('admin.dashboard');
        }

        return view('saas::deactivated.index');
    }
    
}
