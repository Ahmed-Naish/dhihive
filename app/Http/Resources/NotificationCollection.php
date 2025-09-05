<?php

namespace App\Http\Resources;

use App\Models\Notification;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'notifications' => $this->collection->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'sender' => null,
                    'sender_id' => null,
                    'receiver_id' => @$notification->receiver_id,
                    'title' => 'New Notification',
                    'body' => strip_tags(@$notification->message),
                    'image' => uploaded_asset(@base_settings('company_icon')),
                    'date' => @$notification->created_at->diffForHumans(),
                    'slag' => @$notification->web_redirect_url,
                    'read_at' => @$notification->seen_at,
                    'is_read' => @$notification->seen ? true : false,
                ];
            })
        ];
    }
}
