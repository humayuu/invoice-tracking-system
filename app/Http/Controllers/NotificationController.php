<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    public function markRead(string $id)
    {
        $notification = auth()->user()
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

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
