<?php

namespace Modules\Notify\Services;

use Carbon\Carbon;
use App\Models\User;
//use App\Traits\ActivityFeedTrait;
use \Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Modules\Notify\Entities\Notification;
use Modules\Notify\Entities\NotificationType;
use Modules\EmployeeDocuments\Entities\UserDocument;
use Modules\PushNotification\Services\PushNotificationService;

class NotificationService
{
    //    use ActivityFeedTrait;

    public function storeNotification($receiver_message = "", $sender_message = "", $redirectUrlForWeb = "", $redirectUrlForApp = "", $forSelf = true, $data = [], $sendOnlyAdmin = false)
    {
        try {
            if (!@$data['isNewlyCreateEmployee']) {
                $receivers = User::query()
                    ->when(@$data['document'], fn($q) => $q->where('role_id', 3))
                    ->when(!$sendOnlyAdmin && !@$data['document'], fn($q) => $q->where('role_id', '<=', 3))
                    ->when($sendOnlyAdmin && !@$data['document'], fn($q) => $q->where('role_id', '<=', 2))
                    ->where('id', '!=', auth()->id())
                    ->pluck('id')
                    ->toArray();
            } else {
                $receivers = [];
            }

            if (auth()->user()->manager_id && !@$data['isNewlyCreateEmployee']) {
                $receivers[] = auth()->user()->manager_id;
            }

            if (@$data['leaveRequest']->status_id == 1 || @$data['leaveRequest']->status_id == 6) {
                $receivers[] = $data['leaveRequest']->user_id;
            }

            if (@$data['leaveRequest']->substitute_id) {
                $receivers[] = $data['leaveRequest']->substitute_id;
            }

            if (@$data['tardyRequest']->user_id) {
                $receivers[] = $data['tardyRequest']->user_id;
            }

            if (@$data['employee']) {
                $receivers[] = $data['employee']->id;

                if ($data['employee']->manager_id) {
                    $receivers[] = $data['employee']->manager_id;
                }
            }

            if (@$data['complain']->user_id) {
                $receivers[] = $data['complain']->user_id;
            }

            if (@$data['verbalWarning']->user_id) {
                $receivers[] = $data['verbalWarning']->user_id;
            }

            if (@$data['receivers'] != null) {
                //merge receivers and $data['receivers']
                $receivers = array_merge($receivers, $data['receivers']);
            }

            $receivers = array_unique($receivers);


            foreach ($receivers ?? [] as $id) {

                $user = User::find($id);
                $channel = "user{$id}";
                if ($user) {
                    $channel = "user{$id}company{$user->company_id}";
                }
                if (!empty($receiver_message)) {
                    Notification::create([
                        'receiver_id' => $id,
                        'seen' => 0,
                        'web_redirect_url' => $redirectUrlForWeb,
                        'mobile_redirect_url' => $redirectUrlForApp,
                        'message' => $receiver_message,
                        'company_id' => getCurrentCompany(),
                        'branch_id' => getCurrentBranch(),
                    ]);

                    if ($user) {
                        (new PushNotificationService())->push($channel, 'New Notification', $receiver_message, $redirectUrlForApp);
                        (new PushNotificationService())->browserNotification($id, 'New Notification', $receiver_message, $redirectUrlForApp);
                    }
                }
            }


            // Only Super Admin/Admin no need to approval
            if (auth()->user()->role_id > 2 && $forSelf && !$sendOnlyAdmin) {
                $user  = auth()->user();
                $channel = "user{$user->id}company{$user->company_id}";

                if (!empty($sender_message)) {
                    Notification::create([
                        'receiver_id' => auth()->id(),
                        'seen' => 0,
                        'web_redirect_url' => $redirectUrlForWeb,
                        'mobile_redirect_url' => $redirectUrlForApp,
                        'message' => $sender_message,
                        'company_id' => getCurrentCompany(),
                        'branch_id' => getCurrentBranch(),
                    ]);

                    if ($user) {
                        (new PushNotificationService())->push($channel, 'New Notification', $sender_message, $redirectUrlForApp);
                        (new PushNotificationService())->browserNotification($user->id, 'New Notification', $sender_message, $redirectUrlForApp);
                    }
                }
            }
        } catch (\Throwable $th) {
            Log::error('Notification Store Error => ' . $th->getMessage());
        }
    }

    public function storeLateCheckInNotification($time)
    {
        try {

            $hours = floor($time / 60);
            $remainingMinutes = $time % 60;
            $lateTime = sprintf('%02d:%02d', $hours, $remainingMinutes);

            $forReceiverMessage = '<b>' . auth()->user()->name . '</b> is <span class="text-warning">Late</span> by <span class="text-danger">' . convertTimeToReadableFormat($lateTime) . '</span>';
            $forSenderMessage = 'You are <span class="text-danger">' . convertTimeToReadableFormat($lateTime) . '</span> <span class="text-warning">late</span>. You may appeal if there is a valid reason.';
            $redirectUrlForWeb = '';
            $redirectUrlForApp = 'late-in';

            $this->storeNotification($forReceiverMessage, $forSenderMessage, $redirectUrlForWeb, $redirectUrlForApp, true);
        } catch (\Throwable $th) {
            Log::error('storeLateCheckInNotification Error => ' . $th->getMessage());
        }
    }

    public function storeEarlyCheckoutNotification($time)
    {
        try {
            $hours = floor($time / 60);
            $remainingMinutes = $time % 60;
            $lateTime = sprintf('%02d:%02d', $hours, $remainingMinutes);

            $forReceiverMessage = '<b>' . auth()->user()->name . '</b> is <span class="text-warning">early checkout</span> by <span class="text-danger">' . convertTimeToReadableFormat($lateTime) . '</span>';
            $forSenderMessage = 'You are <span class="text-danger">' . convertTimeToReadableFormat($lateTime) . '</span> <span class="text-warning">early checkout</span>. You may appeal if there is a valid reason.';
            $redirectUrlForWeb = '';
            $redirectUrlForApp = 'early-out';

            $this->storeNotification($forReceiverMessage, $forSenderMessage, $redirectUrlForWeb, $redirectUrlForApp);
        } catch (\Throwable $th) {
            Log::error('storeEarlyCheckoutNotification Error => ' . $th->getMessage());
        }
    }

    public function storeLeaveRequestNotification($leaveRequest)
    {
        try {
            $forReceiverMessage = '<b>' . auth()->user()->name . '</b> has requested ' . @$leaveRequest->days . ' days <span class="text-warning">' . @$leaveRequest->leaveType->name . '</span> which will run from <span class="text-danger">' . Carbon::parse($leaveRequest->leave_from)->format('jS F Y') . ' to ' . Carbon::parse($leaveRequest->leave_to)->format('jS F Y') . '</span>';
            $redirectUrlForWeb = Route::has('leaveRequest.approve.form') ? route('leaveRequest.approve.form', @$leaveRequest->id) : route('leaveRequest.index');
            $redirectUrlForApp = 'leave-request';

            $data['leaveRequest'] = $leaveRequest;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeLeaveRequestNotification Error => ' . $th->getMessage());
        }
    }

    public function storeLeaveRequestApproveNotification($leaveRequest)
    {
        try {
            $forReceiverMessage = '<b>' . $leaveRequest->user->name . '\'s</b> leave request has been <span class="text-success">approved</span> by <b class="text-info">' . auth()->user()->name . '</b>';
            $redirectUrlForWeb = Route::has('leaveRequest.approve.form') ? route('leaveRequest.approve.form', @$leaveRequest->id) : route('leaveRequest.index');
            $redirectUrlForApp = 'leave-request-approved';

            $data['leaveRequest'] = $leaveRequest;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeLeaveRequestApproveNotification Error => ' . $th->getMessage());
        }
    }

    public function storeLeaveRequestRejectNotification($leaveRequest)
    {
        try {
            $forReceiverMessage = '<b>' . $leaveRequest->user->name . '\'s</b> leave request has been <span class="text-danger">rejected</span> by <b class="text-warning">' . auth()->user()->name . '</b>';
            $redirectUrlForWeb = Route::has('leaveRequest.approve.form') ? route('leaveRequest.approve.form', @$leaveRequest->id) : route('leaveRequest.index');
            $redirectUrlForApp = 'leave-request-rejected';

            $data['leaveRequest'] = $leaveRequest;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeLeaveRequestRejectNotification Error => ' . $th->getMessage());
        }
    }

    public function storeEmployeeTypeChangeNotification($user)
    {
        try {
            $forReceiverMessage = '<b>' . $user->name . '\'s</b> Employee Type has been changed <b class="text-primary">' . $user->employee_type . '</b> by <b class="text-warning">' . auth()->user()->name . '</b>';
            $redirectUrlForWeb = '';
            $redirectUrlForApp = '';

            $data['employee'] = $user;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeEmployeeTypeChangeNotification Error => ' . $th->getMessage());
        }
    }

    public function storeEmployeeStatusActiveInactiveNotification($user)
    {
        try {
            $statusTextClass = $user->status_id == 1 ? 'text-success' : ($user->status_id == 4 ? 'text-danger' : '');

            $forReceiverMessage = '<b>' . $user->name . '\'s</b> has been <b class="' . $statusTextClass . '">' . @$user->status->name . 'ated</b> by <b class="text-warning">' . auth()->user()->name . '</b>';
            $redirectUrlForWeb = '';
            $redirectUrlForApp = '';

            $data['employee'] = $user;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeEmployeeStatusActiveInactiveNotification Error => ' . $th->getMessage());
        }
    }

    public function storeEmployeeTerminatedNotification($user)
    {
        try {
            $forReceiverMessage = '<b>' . $user->name . '\'s</b> has been <b class="text-danger">Terminated</b> by <b class="text-warning">' . auth()->user()->name . '</b>';
            $redirectUrlForWeb = '';
            $redirectUrlForApp = '';

            $data['employee'] = $user;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeEmployeeTerminatedNotification Error => ' . $th->getMessage());
        }
    }

    public function storeEmployeeChangeTemporaryPasswordNotification($user)
    {
        try {
            $forReceiverMessage = '<b>' . $user->name . '</b> please change your temporary password!';
            $redirectUrlForWeb = route('user.authProfile', ['settings']);
            $redirectUrlForApp = '';

            $data['employee'] = $user;
            $data['isNewlyCreateEmployee'] = true;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeEmployeeChangeTemporaryPasswordNotification Error => ' . $th->getMessage());
        }
    }

    public function storeAppThemeChangeNotification()
    {
        try {
            $forReceiverMessage = '<b>' . auth()->user()->name . ' [' . auth()->user()->employee_id . ']</b> has been changed <span class="text-warning">App Theme</span>.';
            $redirectUrlForWeb = route('manage.settings.view', ['app_theme_setup' => true]);
            $redirectUrlForApp = '';

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false);
        } catch (\Throwable $th) {
            Log::error('storeAppThemeChangeNotification Error => ' . $th->getMessage());
        }
    }

    public function storeActivationUpdateNotification()
    {
        try {
            $forReceiverMessage = '<b>' . auth()->user()->name . ' [' . auth()->user()->employee_id . ']</b> has been updated <span class="text-warning">Activation Setting</span>.';
            $redirectUrlForWeb = route('company.settings.activation');
            $redirectUrlForApp = '';

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false);
        } catch (\Throwable $th) {
            Log::error('storeActivationUpdateNotification Error => ' . $th->getMessage());
        }
    }

    public function storeConfigurationUpdateNotification()
    {
        try {
            $forReceiverMessage = '<b>' . auth()->user()->name . ' [' . auth()->user()->employee_id . ']</b> has been updated <span class="text-warning">Configuration Setting</span>.';
            $redirectUrlForWeb = route('company.settings.configuration');
            $redirectUrlForApp = '';

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false);
        } catch (\Throwable $th) {
            Log::error('storeConfigurationUpdateNotification Error => ' . $th->getMessage());
        }
    }

    public function storeComplainNotification($complain)
    {
        try {
            $forReceiverMessage = '<b class="text-danger">' . $complain->title . ':</b> <span class="text-dark">' . @$complain->createdBy->name . ' [' . @$complain->createdBy->employee_id . ']</span> has created a complain against <span class="text-warning">' . @$complain->user->name . ' [' . @$complain->user->employee_id . ']</span>';
            $redirectUrlForWeb = '';
            $redirectUrlForApp = '';

            $data['complain'] = $complain;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeComplainNotification Error => ' . $th->getMessage());
        }
    }

    public function storeVerbalWarningNotification($verbalWarning)
    {
        try {
            $forReceiverMessage = '<b class="text-danger">' . $verbalWarning->title . ':</b> <span class="text-dark">' . @$verbalWarning->createdBy->name . ' [' . @$verbalWarning->createdBy->employee_id . ']</span> has created a verbal warning against <span class="text-warning">' . @$verbalWarning->user->name . ' [' . @$verbalWarning->user->employee_id . ']</span>';
            $redirectUrlForWeb = '';
            $redirectUrlForApp = '';

            $data['verbalWarning'] = $verbalWarning;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeVerbalWarningNotification Error => ' . $th->getMessage());
        }
    }

    public function storeDocumentRequestNotificationToHR($document)
    {
        try {
            $forReceiverMessage = '<b class="text-primary">' . $document->user->name . ' [' . $document->user->employee_id . '</b> has created a request for <span class="text-warning">' . @$document->userDocumentType->name . '</span>';
            $redirectUrlForWeb = Route::has('document-request.approve.form') ? route('document-request.approve.form', $document->id) : '';
            $redirectUrlForApp = '';
            $data['document'] = $document;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeDocumentRequestNotificationToHR Error => ' . $th->getMessage());
        }
    }

    public function storeNewClientNotification($client)
    {
        try {
            $forReceiverMessage = 'A new <span class="text-primary">Client</span> (<b class="text-warning">' . $client->name . ')</b> has been created';
            $redirectUrlForWeb = '';
            $redirectUrlForApp = '';

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, true, [], true);
        } catch (\Throwable $th) {
            Log::error('storeVerbalWarningNotification Error => ' . $th->getMessage());
        }
    }

    public function storeNoticeNotification($notice, $noticeUser)
    {
        try {
            $forReceiverMessage = '<span class="text-dark">Notice:</span> <b class="text-warning">' . $notice->subject . '</b>';
            $redirectUrlForWeb = '';
            $redirectUrlForApp = '';

            $data['receivers'][] = $noticeUser->id;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeNoticeNotification Error => ' . $th->getMessage());
        }
    }

    public function storeNoticeUpdateNotification($notice, $noticeUser)
    {
        try {
            $forReceiverMessage = '<span class="text-dark">Notice Update:</span> <b class="text-warning">' . $notice->subject . '</b>';
            $redirectUrlForWeb = '';
            $redirectUrlForApp = '';

            $data['receivers'][] = $noticeUser->id;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeNoticeNotification Error => ' . $th->getMessage());
        }
    }

    public function storeConferenceNotification($notice, $noticeUser)
    {
        try {
            $forReceiverMessage = '<span class="text-dark">Conference:</span> <b class="text-warning">' . $notice->subject . '</b>';
            $redirectUrlForWeb = '';
            $redirectUrlForApp = '';

            $data['receivers'][] = $noticeUser->user_id;

            $this->storeNotification($forReceiverMessage, '', $redirectUrlForWeb, $redirectUrlForApp, false, $data);
        } catch (\Throwable $th) {
            Log::error('storeNoticeNotification Error => ' . $th->getMessage());
        }
    }
}
