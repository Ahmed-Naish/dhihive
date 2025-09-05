<?php

namespace Modules\Notify\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('jm_message_templates')->insert([
            // Welcome
            [
                'notification_type_id' => 1,
                'subject' => 'Welcome to Our Company!',
                'body' => 'Dear {employee_name}, Welcome to our company! We are excited to have you on board. If you have any questions, feel free to reach out to us.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Password Reset
            [
                'notification_type_id' => 2,
                'subject' => 'Password Reset Instructions',
                'body' => 'Dear {employee_name}, You have requested a password reset. Please click on the following link to reset your password: {reset_link}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Leave Request
            [
                'notification_type_id' => 3,
                'subject' => 'Leave Request Submitted',
                'body' => 'Dear {employee_name}, Your leave request for {leave_dates} has been submitted successfully. We will review and notify you shortly.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Leave Request Approved
            [
                'notification_type_id' => 4,
                'subject' => 'Leave Request Approved',
                'body' => 'Dear {employee_name}, Your leave request for {leave_dates} has been approved. Enjoy your time off!',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Leave Request Rejected
            [
                'notification_type_id' => 5,
                'subject' => 'Leave Request Rejected',
                'body' => 'Dear {employee_name}, We regret to inform you that your leave request for {leave_dates} has been rejected. Please contact HR for further assistance.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Performance Review Reminder
            [
                'notification_type_id' => 6,
                'subject' => 'Performance Review Reminder',
                'body' => 'Dear {employee_name}, This is a reminder that your performance review is scheduled for {review_date}. Please come prepared to discuss your achievements and goals.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Performance Review Completed
            [
                'notification_type_id' => 7,
                'subject' => 'Performance Review Completed',
                'body' => 'Dear {employee_name}, Your performance review has been completed. Please contact HR if you have any questions or require further feedback.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Training Session Invitation
            [
                'notification_type_id' => 8,
                'subject' => 'Invitation to Training Session',
                'body' => 'Dear {employee_name}, You are invited to attend a training session on {training_topic} scheduled for {training_date}. Please confirm your availability.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Training Session Reminder
            [
                'notification_type_id' => 9,
                'subject' => 'Training Session Reminder',
                'body' => 'Dear {employee_name}, This is a reminder of the upcoming training session on {training_topic} scheduled for {training_date}. We look forward to your participation.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Birthday Greeting
            [
                'notification_type_id' => 10,
                'subject' => 'Birthday Greetings!',
                'body' => 'Dear {employee_name}, Happy Birthday! Wishing you a wonderful day and a fantastic year ahead filled with joy and success.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Anniversary Recognition
            [
                'notification_type_id' => 11,
                'subject' => 'Work Anniversary Celebration',
                'body' => 'Dear {employee_name}, Congratulations on your {years_of_service}-year work anniversary with us! Your dedication and hard work are truly appreciated.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Promotion Announcement
            [
                'notification_type_id' => 12,
                'subject' => 'Congratulations on Your Promotion!',
                'body' => 'Dear {employee_name}, Congratulations on your promotion to {new_position}! Your hard work and commitment have paid off. Best wishes on your new journey.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // New Job Opening
            [
                'notification_type_id' => 13,
                'subject' => 'New Job Opening',
                'body' => 'Dear Team, We have a new job opening for the position of {job_title}. Please refer to the job description and apply if interested.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Company Event Invitation
            [
                'notification_type_id' => 14,
                'subject' => 'Invitation to Company Event',
                'body' => 'Dear {employee_name}, You are invited to join us for {event_name} on {event_date}. Please RSVP by {RSVP_deadline}. We look forward to seeing you there!',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Company Event Reminder
            [
                'notification_type_id' => 15,
                'subject' => 'Company Event Reminder',
                'body' => 'Dear Team, This is a reminder of the upcoming {event_name} scheduled for {event_date}. Please make sure to attend and participate.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Policy Update Notification
            [
                'notification_type_id' => 16,
                'subject' => 'Policy Update Notification',
                'body' => 'Dear Team, Please be informed of the recent update to our {policy_name}. You can review the updated version [here/link].',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Emergency Notification
            [
                'notification_type_id' => 17,
                'subject' => 'Emergency Notification',
                'body' => 'Dear Team, Due to an emergency situation, please [take action/proceed as instructed]. Your safety is our priority.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // System Maintenance Notification
            [
                'notification_type_id' => 18,
                'subject' => 'Scheduled System Maintenance',
                'body' => 'Dear Team, This is to inform you that we will be conducting scheduled maintenance on our system on {maintenance_date}. Please plan accordingly.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Survey Invitation
            [
                'notification_type_id' => 19,
                'subject' => 'Invitation to Employee Survey',
                'body' => 'Dear Team, Your feedback matters to us! Please take a moment to complete our employee satisfaction survey [here/link]. Your input is valuable.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Survey Reminder
            [
                'notification_type_id' => 20,
                'subject' => 'Reminder: Complete Employee Survey',
                'body' => 'Dear Team, This is a friendly reminder to complete our employee satisfaction survey if you haven\'t done so already. Your feedback is important to us.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Attendance Recorded
            [
                'notification_type_id' => 21,
                'subject' => 'Attendance Recorded',
                'body' => 'Dear {employee_name}, Your attendance for {attendance_date} has been recorded successfully.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Late In
            [
                'notification_type_id' => 22,
                'subject' => 'Late Arrival Notification',
                'body' => 'Dear {employee_name}, We noticed that you arrived late on {late_date}. Please ensure timely arrival in the future.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Early Out
            [
                'notification_type_id' => 23,
                'subject' => 'Early Departure Notification',
                'body' => 'Dear {employee_name}, We noticed that you left early on {early_date}. Please adhere to the scheduled working hours.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
