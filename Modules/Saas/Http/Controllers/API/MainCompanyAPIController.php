<?php

namespace Modules\Saas\Http\Controllers\API;

use Illuminate\Routing\Controller;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;

class MainCompanyAPIController extends Controller
{
    use ApiReturnFormatTrait;
    
    public function basicInfo()
    {
        try {
            $data['company_name']           = @base_settings('company_name');
            $data['email']                  = @base_settings('email');
            $data['phone']                  = @base_settings('phone');
            $data['address']                = @base_settings('address');
            $data['address']                = @base_settings('address');
            $data['logo']                   = logo_dark(@base_settings('company_logo_frontend'));
            $data['icon']                   = company_fav_icon(@base_settings('company_icon'));
            $data['company_description']    = @base_settings('company_description');

            return $this->responseWithSuccess(_trans('message.Success'), $data);

        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), $th->getMessage());
        }
    }

    public function stripeToken()
    {
        try {
            $stripeToken = encrypt(@base_settings('stripe_key'));

            return $this->responseWithSuccess(_trans('message.Success'), $stripeToken);

        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), $th->getMessage());
        }
    }

    public function offlinePaymentTypes()
    {
        try {
            $data = [];

            if (@base_settings('is_payment_type_cash')) {
                $data[] = _trans('common.Cash');
            }

            if (@base_settings('is_payment_type_cheque')) {
                $data[] = _trans('common.Cheque');
            }

            if (@base_settings('is_payment_type_bank_transfer')) {
                $data[] = _trans('common.Bank Transfer');
            }

            return $this->responseWithSuccess(_trans('message.Success'), $data);

        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), $th->getMessage());
        }
    }
}
