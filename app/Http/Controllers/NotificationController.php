<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarkAllNotificationsReadRequest;
use App\Http\Requests\MarkNotificationReadRequest;

class NotificationController extends Controller
{
    public function markRead(MarkNotificationReadRequest $request)
    {
        $id = $request->validated('id');

        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        if ($notification->data['type'] === 'sale') {
            return redirect()->route('sales.show',
                $notification->data['sale_id']);
        }

        return redirect()->route('purchase.show',
            $notification->data['purchase_id']);
    }

    public function markAllRead(MarkAllNotificationsReadRequest $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
