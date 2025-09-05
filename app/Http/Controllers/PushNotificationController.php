<?php


namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Helpers\CoreApp\Traits\FirebaseNotification;
class PushNotificationController
{
    use FirebaseNotification;

    public function storeFcmToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = Auth::user();  // Get the authenticated user
//        $user = User::find(2);

        if ($user) {
            // Update the user's FCM token
            $user->device_token = $request->input('token');
            $user->save();

            return response()->json(['message' => 'Token saved successfully.']);
        }

        return response()->json(['error' => 'User not authenticated.'], 401);
    }


    public function subscribeToTopic(Request $request)
    {
        $token = $request->input('token');
        $topic = auth()->user()->notification_channels();

        if (!$token || !$topic) {
            return response()->json(['status' => 'error', 'message' => 'Token and topic are required.'], 400);
        }

        try {
            $messaging = app('firebase.messaging');
            $messaging->subscribeToTopic($token, $topic);
            return response()->json(['status' => 'success', 'message' => 'Subscribed to topic.']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }




    // Send a test notification using user token
    public function sendTestNotification(Request $request)
    {
        $token = User::find(2)->device_token;

        if (!$token) {
            return response()->json(['status' => 'error', 'message' => 'Token is required.'], 400);
        }

        try {
            // Get the Firebase Messaging instance
            $messaging = app('firebase.messaging');

            // Create the notification message
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create('Test Notification', 'This is a test notification'))
                ->withData(['key' => 'value']);

            // Send the notification
            $messaging->send($message);

            return response()->json(['status' => 'success', 'message' => 'Notification sent.']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }



    // Send a notification using the specified topic
    public function sendTopicNotification(Request $request)
    {
        $topics = auth()->user()->notification_channels() ?? [];

        $title = $request->input('title', 'Hi');
        $body = $request->input('body', 'This is tushar from local');

        if (!$topics || !$title || !$body) {
            return response()->json(['status' => 'error', 'message' => 'Topic, title, and body are required.'], 400);
        }

        try {
            // Get the Firebase Messaging instance
            $messaging = app('firebase.messaging');

            // Create the notification message
            foreach ($topics as $topic) {
                $message = CloudMessage::withTarget('topic', $topic)
                    ->withNotification(Notification::create($title, $body))
                    ->withData(['key' => 'value']);

                // Send the notification to the current topic
                $messaging->send($message);
            }
//            // Create the notification message
//            $message = CloudMessage::withTarget('topic', $topic)
//                ->withNotification(Notification::create($title, $body))
//                ->withData(['key' => 'value']);

            // Send the notification to the topic
//            $messaging->send($message);

            return response()->json(['status' => 'success', 'message' => 'Notification sent to topic.']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }



    public function notification(Request $request)
    {
        $this->sendChannelFirebaseNotification('$channel', '$notification_type', $id = null, 'https://onesttech.com/', 'test', 'message body', $image = 'https://designyourownblog.com/wp-content/uploads/2013/08/how-to-create-a-favicon-sm.png');
    }
}
