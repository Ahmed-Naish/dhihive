<?php

namespace Modules\Notify\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('jm_notification_types')->insert([
            [
                'name' => 'Welcome',
                'slug' => 'welcome', // Adding a slug column
                'description' => 'Welcome message for new users',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Password Reset',
                'slug' => 'password-reset', // Adding a slug column
                'description' => 'Password reset instructions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leave Request',
                'slug' => 'leave-request', // Adding a slug column
                'description' => 'Leave request created',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leave Request Approved',
                'slug' => 'leave-request-approved', // Adding a slug column
                'description' => 'Leave request approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leave Request Rejected',
                'slug' => 'leave-request-rejected', // Adding a slug column
                'description' => 'Leave request rejected',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Performance Review Reminder',
                'slug' => 'performance-review-reminder', // Adding a slug column
                'description' => 'Reminder for upcoming performance review',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Performance Review Completed',
                'slug' => 'performance-review-completed', // Adding a slug column
                'description' => 'Notification that performance review has been completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Training Session Invitation',
                'slug' => 'training-session-invitation', // Adding a slug column
                'description' => 'Invitation to attend a training session',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Training Session Reminder',
                'slug' => 'training-session-reminder', // Adding a slug column
                'description' => 'Reminder for upcoming training session',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Birthday Greeting',
                'slug' => 'birthday-greeting', // Adding a slug column
                'description' => 'Birthday greeting to employees',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Anniversary Recognition',
                'slug' => 'anniversary-recognition', // Adding a slug column
                'description' => 'Recognition of employee work anniversary',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Promotion Announcement',
                'slug' => 'promotion-announcement', // Adding a slug column
                'description' => 'Announcement of employee promotion',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'New Job Opening',
                'slug' => 'new-job-opening', // Adding a slug column
                'description' => 'Notification of a new job opening in the company',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Company Event Invitation',
                'slug' => 'company-event-invitation', // Adding a slug column
                'description' => 'Invitation to a company event',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Company Event Reminder',
                'slug' => 'company-event-reminder', // Adding a slug column
                'description' => 'Reminder for upcoming company event',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Policy Update Notification',
                'slug' => 'policy-update-notification', // Adding a slug column
                'description' => 'Notification of updates to company policies',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emergency Notification',
                'slug' => 'emergency-notification', // Adding a slug column
                'description' => 'Notification regarding emergency situations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'System Maintenance Notification',
                'slug' => 'system-maintenance-notification', // Adding a slug column
                'description' => 'Notification of scheduled system maintenance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Survey Invitation',
                'slug' => 'survey-invitation', // Adding a slug column
                'description' => 'Invitation to participate in a survey',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Survey Reminder',
                'slug' => 'survey-reminder', // Adding a slug column
                'description' => 'Reminder to complete a survey',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Attendance Recorded',
                'slug' => 'attendance-recorded', // Adding a slug column
                'description' => 'Notification that attendance has been recorded',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Late In',
                'slug' => 'late-in', // Adding a slug column
                'description' => 'Notification that an employee arrived late',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Early Out',
                'slug' => 'early-out', // Adding a slug column
                'description' => 'Notification that an employee left early',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tardy Request',
                'slug' => 'tardy-request', // Adding a slug column
                'description' => 'Notification for Tardy Request',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tardy Request Approved',
                'slug' => 'tardy-request-approved', // Adding a slug column
                'description' => 'Notification for Tardy Request Approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tardy Request Rejected',
                'slug' => 'tardy-request-rejected', // Adding a slug column
                'description' => 'Notification for Tardy Request Rejected',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Employee Type Updated',
                'slug' => 'employee-type-updated', // Adding a slug column
                'description' => 'Notification for Employee Type Updated',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Employee Activated',
                'slug' => 'employee-activated', // Adding a slug column
                'description' => 'Notification for Employee Activated',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Employee Inactivated',
                'slug' => 'employee-inactivated', // Adding a slug column
                'description' => 'Notification for Employee Inactivated',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Employee Terminated',
                'slug' => 'employee-terminated', // Adding a slug column
                'description' => 'Notification for Employee Terminated',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Change Temporary Password',
                'slug' => 'change-temporary-password', // Adding a slug column
                'description' => 'Notification for Change Temporary Password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'App Theme Changed',
                'slug' => 'app-theme-changed', // Adding a slug column
                'description' => 'Notification for App Theme Changed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Activation Update',
                'slug' => 'activation-update', // Adding a slug column
                'description' => 'Activation Update Notification',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Configuration Update',
                'slug' => 'configuration-update', // Adding a slug column
                'description' => 'Configuration Update Notification',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Complain',
                'slug' => 'complain', // Adding a slug column
                'description' => 'Complain Notification',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Verbal Warning',
                'slug' => 'verbal-warning', // Adding a slug column
                'description' => 'Verbal Warning Notification',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Document Request',
                'slug' => 'document-request', // Adding a slug column
                'description' => 'Document Request Notification',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'New Client',
                'slug' => 'new-client', // Adding a slug column
                'description' => 'New Client Notification',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Notice',
                'slug' => 'notice', // Adding a slug column
                'description' => 'New Notice Notification',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Notice Update',
                'slug' => 'notice-update', // Adding a slug column
                'description' => 'Notice Update Notification',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
