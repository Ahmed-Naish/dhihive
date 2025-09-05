<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ProfileRepository;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\User;
use Modules\PushNotification\Services\PushNotificationService;

class ProfileController extends Controller
{
    use ApiReturnFormatTrait;

    protected $profile;


    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profile = $profileRepository;
    }


    // update::ayman 05
    //get user profile with parameter
    public function profile(Request $request, $slug): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->profile->getProfile($request, $slug);
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage(), [], 500);
        }
    }
    public function UserProfileUpdate(Request $request)
    {
        try {
            $result = $this->profile->UserProfileUpdate($request);
            if ($result) {
                return $this->responseWithSuccess('Profile Updated Successfully', [], 200);
            } else {
                return $this->responseWithError('Profile Not Updated', [], 500);
            }
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage(), [], 500);
        }
    }
    public function checkTokenIsAlive(Request $request, $_token)
    {
        try {
            $user_token = $_token;
            // $user_token=$request->bearerToken();
            $token = PersonalAccessToken::findToken($user_token);
            if ($token->tokenable_type == 'App\Models\User') {
                return $this->responseWithSuccess('Token is alive', ['token' => $user_token]);
            } else {
                return $this->responseWithError('Token is not alive', [], 401);
            }
        } catch (\Exception $e) {
            return $this->responseWithError('Token is not alive', [], 401);
        }
    }
    public function profileInfo(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->profile->getProfileInfo($request);
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage(), [], 500);
        }
    }
    //get details
    public function details(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->profile->getProfileDetails($request, $id);
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage(), [], 500);
        }
    }


    // update::ayman 02
    //profile update
    public function profileUpdate(Request $request, $slug): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->profile->update($request, $slug);
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage(), [], 500);
        }
    }

    //password update
    public function passwordUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->profile->changepassword($request);
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage(), [], 500);
        }
    }

    //avatar image update
    public function avatarImageUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->profile->avatarUpdate($request);
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage(), [], 500);
        }
    }

    //notification
    public function notification(Request $request)
    {
        try {
            return $this->profile->getNotification($request);
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage(), [], 500);
        }
    }
    public function readNotification(Request $request)
    {
        try {
            return $this->profile->readNotification($request);
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage(), [], 500);
        }
    }

    public function notificationClear()
    {
        try {
            return $this->profile->clearNotification();
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage(), [], 500);
        }
    }
    public function getUserList(Request $request, $keywords = null)
    {
        try {
            $request['keywords'] = $keywords;
            return $this->profile->getUserList($request);
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage(), [], 500);
        }
    }


    public function firebaseMessaging(Request $request)
    {
        try {
            if (!isModuleActive('PushNotification')) {
                return $this->responseWithError('Notification module not available');
            }
            if (!$request->filled('receiver_id')) {
                return $this->responseWithError('Receiver is required!');
            }
            if (!$request->filled('message')) {
                return $this->responseWithError('Message is required!');
            }

            $user = User::find($request->receiver_id);

            if (!$user) {
                return $this->responseWithError('User not found!');
            }
            $channel = 'user' . $user->id . 'company' . $user->company_id;
            $title = 'New msg from ' . auth()->user()->name;
            $body = $request->message;
            (new PushNotificationService)->push($channel, $title, $body);
            (new PushNotificationService)->browserNotification($user->id, $title, $body);
            return $this->responseWithSuccess('message sent');
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage(), [], 500);
        }
    }
}
