<?php

namespace Modules\Saas\Services\Backend;

use App\Models\Company\Company;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Saas\Entities\EmailTemplate;
use Modules\Saas\Jobs\SubscriptionApprovedJob;
use Modules\Saas\Jobs\SubscriptionRejectedJob;
use Modules\Saas\Jobs\SubscriptionUpgradedJob;
use Modules\Saas\Emails\SubscriptionApprovedMail;
use Modules\Saas\Emails\SubscriptionRejectedMail;
use Modules\Saas\Emails\SubscriptionUpgradedMail;
use Modules\Saas\Events\SendSubscriptionApprovedMailEvent;
use Modules\Saas\Events\SendSubscriptionRejectedMailEvent;
use Modules\Saas\Events\SendSubscriptionUpgradedMailEvent;


class SubscriptionService
{
    public function sendSubscriptionApproveMail($subscription)
    {
        $data['email']              = @$subscription->company->email;
        $data['name']               = @$subscription->company->name;
        $data['company_name']       = @$subscription->company->company_name;
        $data['phone']              = @$subscription->company->phone;
        $data['password']           = '12345678';
        $data['subdomain']          = @Company::where('id', @$subscription->company_id)->first()->subdomain;
        $data['plan_name']          = $subscription->pricingPlanPrice->pricingPlan->name;
        $data['employee_limit']     = $subscription->is_employee_limit ? $subscription->employee_limit : 'Unlimited';
        $data['expiry_date']        = date('M d, Y', strtotime($subscription->expiry_date));
        $data['payment_gateway']    = $subscription->is_demo_checkout ? 'Demo Checkout' : $subscription->payment_gateway;
        $data['payment_status']     = $subscription->paymentStatus->name;
        $data['status']             = $subscription->status->name;
        $data['price']              = $subscription->price;

        $company_credentials_table = '<table class="w-100 table-striped table-bordered mb-5">
                                        <tbody>
                                            <tr>
                                                <th>'._trans("emailTemplate.Url").'</th>
                                                <td>
                                                    <a href="http://'. $data['subdomain'] .'" target="_blank">'. $data['subdomain'].'</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>'. _trans("emailTemplate.Email").'</th>
                                                <td>'.$data["email"].'</td>
                                            </tr>
                                            <tr>
                                                <th>'. _trans("emailTemplate.Password").'</th>
                                                <td>'.$data["password"].'</td>
                                            </tr>
                                        </tbody>
                                    </table>';

        $subscription_plan_table = "<table class='table-striped table-bordered'>
                                        <tbody>
                                            <tr>
                                                <th>"._trans('emailTemplate.Plan Info')."</th>
                                                <th>"._trans('emailTemplate.Employee Limit')."</th>
                                                <th>"._trans('emailTemplate.Expiry Date')."</th>
                                                <th>"._trans('emailTemplate.Payment Gateway')."</th>
                                                <th>"._trans('emailTemplate.Payment Status')."</th>
                                                <th>"._trans('emailTemplate.Status')."</th>
                                                <th class='text-right'>". _trans('emailTemplate.Price')."</th>
                                            </tr>
                                            <tr>
                                                <td>".@$data["plan_name"]."</td>
                                                <td>".@$data['employee_limit']."</td>
                                                <td>".showDate(@$data['expiry_date'])."</td>
                                                <td>".@$data['payment_gateway']."</td>
                                                <td>".@$data['payment_status']."</td>
                                                <td>".@$data['status']."</td>
                                                <td class='text-right'>".currency_format(number_format(@$data['price'], 2))."</td>
                                            </tr>
                                        </tbody>
                                    </table>";

        $url = '<a href="http://'.$data['subdomain'].'" target="_blank">'.$data['subdomain'].'</a>';
        $subscriptionApproveTemplate = EmailTemplate::where('slug', 'subscription-approve')->first();
        $content = str_replace('[name]', $data['name'], $subscriptionApproveTemplate->content);
        $content = str_replace('[company_credentials_table]', $company_credentials_table, $content);
        $content = str_replace('[company_subscription_plan_table]', $subscription_plan_table, $content);

        $content = str_replace('[saas_admin_company]', @base_settings('company_name'), $content);
        $data['subject'] = $subscriptionApproveTemplate->subject;
        $data['approve_content'] = $content;
        
        
        if (config('app.single_db')) {
            try {
                Mail::to($data['email'])->send(new SubscriptionApprovedMail($data));
            } catch (\Throwable $th) {
                Log::error('Subscription Approve Mail not Send');
            }
        } else {
            event(new SendSubscriptionApprovedMailEvent($data));
        }
    }

    
    public function dispatchSubscriptionUpgradedJob($subscription)
    {
        $data['email']              = @$subscription->company->email;
        $data['name']               = @$subscription->company->name;
        $data['company_name']       = @$subscription->company->company_name;
        $data['phone']              = @$subscription->company->phone;
        $data['plan_name']          = $subscription->pricingPlanPrice->pricingPlan->name;
        $data['employee_limit']     = $subscription->is_employee_limit ? $subscription->employee_limit : 'Unlimited';
        $data['expiry_date']        = date('M d, Y', strtotime($subscription->expiry_date));
        $data['payment_gateway']    = $subscription->is_demo_checkout ? 'Demo Checkout' : $subscription->payment_gateway;
        $data['payment_status']     = $subscription->paymentStatus->name;
        $data['status']             = $subscription->status->name;
        $data['price']              = $subscription->price;

        $subscription_plan_table = "<table class='table-striped table-bordered'>
                                        <tbody>
                                            <tr>
                                                <th>"._trans('emailTemplate.Plan Info')."</th>
                                                <th>"._trans('emailTemplate.Employee Limit')."</th>
                                                <th>"._trans('emailTemplate.Expiry Date')."</th>
                                                <th>"._trans('emailTemplate.Payment Gateway')."</th>
                                                <th>"._trans('emailTemplate.Payment Status')."</th>
                                                <th>"._trans('emailTemplate.Status')."</th>
                                                <th class='text-right'>". _trans('emailTemplate.Price')."</th>
                                            </tr>
                                            <tr>
                                                <td>".@$data["plan_name"]."</td>
                                                <td>".@$data['employee_limit']."</td>
                                                <td>".showDate(@$data['expiry_date'])."</td>
                                                <td>".@$data['payment_gateway']."</td>
                                                <td>".@$data['payment_status']."</td>
                                                <td>".@$data['status']."</td>
                                                <td class='text-right'>".currency_format(number_format(@$data['price'], 2))."</td>
                                            </tr>
                                        </tbody>
                                    </table>";

        $subscriptionUpgradeTemplate = EmailTemplate::where('slug', 'subscription-upgrade')->first();

        if ($subscriptionUpgradeTemplate) {
            $content = str_replace('[name]', $data['name'], $subscriptionUpgradeTemplate->content);
            $content = str_replace('[company_subscription_plan_table]', $subscription_plan_table, $content);
    
            $content = str_replace('[saas_admin_company]', @base_settings('company_name'), $content);
            $data['subject'] = $subscriptionUpgradeTemplate->subject;
            $data['upgrade_content'] = $content;

            if(config('app.single_db')) {
                try {
                    Mail::to($data['email'])->send(new SubscriptionUpgradedMail($data));
                } catch (\Throwable $th) {
                    info('Subscription Upgraded Mail not Send->'.$th->getMessage());
                }
            }else{
            // SubscriptionUpgradedJob::dispatch($data);
            event(new SendSubscriptionUpgradedMailEvent($data));
            }
            
            
        }
    }

    
    public function dispatchSubscriptionRejectJob($subscription)
    {
        $subscriptionSuccessTemplate = EmailTemplate::where('slug', 'subscription-reject')->first();
        $content = str_replace('[name]', $subscription->company->name, $subscriptionSuccessTemplate->content);
        $content = str_replace('[saas_admin_company]', @base_settings('company_name'), $content);
        $data = [
            'email'     => @$subscription->company->email,
            'subject'   => @$subscriptionSuccessTemplate->subject,
            'content'   => $content
        ];

        // SubscriptionRejectedJob::dispatch($data);
        if(config('app.single_db')) {
            try {
                Mail::to(@$data['email'])->send(new SubscriptionRejectedMail($data));
            } catch (\Throwable $th) {
                Log::info('Subscription Rejected Mail not Send->'.$th->getMessage());
            }
        }else{
            event(new SendSubscriptionRejectedMailEvent($data));
        }
        
    }
}