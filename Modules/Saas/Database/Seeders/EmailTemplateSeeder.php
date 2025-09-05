<?php

namespace Modules\Saas\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('email_templates')->insert([
            'title' => 'Subscription Success',
            'slug' => 'subscription-success',
            'content' => "<p>Hello&nbsp;[name],<br />
            <br />
            Your order has been successfully placed and our dedicated team will meticulously review and approve your request within a timeframe ranging from 24 to 72 hours.</p>
            
            <p><br />
            Thanks,<br />
            [saas_admin_company]</p>",
            'subject' => 'Subscription Success',
            'status_id' => 1, // Active status
            'created_by' => 1,
            'updated_by' => 1,
            'company_id' => 1,
            'branch_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('email_templates')->insert([
            'title' => 'Subscription Approve',
            'slug' => 'subscription-approve',
            'content' => "<p>Hello&nbsp;[name],<br />
            <br />
            Your order has been approved successfully!</p>
            
            <p>[company_credentials_table]<br />
            <br />
            [company_subscription_plan_table]<br />
            <br />
            Note: Please change your temporary password!<br />
            <br />
            Thanks,<br />
            [saas_admin_company]</p>",
            'subject' => 'Subscription Approve',
            'status_id' => 1, // Active status
            'created_by' => 1,
            'updated_by' => 1,
            'company_id' => 1,
            'branch_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('email_templates')->insert([
            'title' => 'Subscription Upgrade',
            'slug' => 'subscription-upgrade',
            'content' => "<p>🎉 <strong>Congratulations! Your Subscription Upgrade Was Successful!</strong> 🚀</p>

            <p>Hello [name],</p>
            
            <p>We&#39;re thrilled to share the exciting news with you&mdash;your subscription upgrade is now complete! 🌟 Thank you for choosing to elevate your experience with [saas_admin_company].<br />
            <br />
            [company_subscription_plan_table]</p>
            Thanks,<br />
            [saas_admin_company]",
            'subject' => 'Subscription Upgrade',
            'status_id' => 1, // Active status
            'created_by' => 1,
            'updated_by' => 1,
            'company_id' => 1,
            'branch_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('email_templates')->insert([
            'title' => 'Subscription Reject',
            'slug' => 'subscription-reject',
            'content' => "<p>🚫 **Subscription Rejected**</p>

            <p>Hello [name],</p>

            <p>We regret to inform you that your subscription request has been rejected. We appreciate your interest in our plan, but after careful consideration, we are unable to approve your subscription at this time.</p>
            Thanks,<br />
            [saas_admin_company]<br />
            &nbsp;",
            'subject' => 'Subscription Reject',
            'status_id' => 1, // Active status
            'created_by' => 1,
            'updated_by' => 1,
            'company_id' => 1,
            'branch_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
