<?php

namespace App\Controllers\Api;

use App\Models\NotificationModel;

class NotificationController extends BaseApiController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function index()
    {
        $decoded = $this->getAuthUser();
        if (!$decoded) {
            return $this->error('Unauthorized', 401);
        }

        $notifications = $this->notificationModel->getForUser($decoded->user_id, 20);
        $unreadCount   = $this->notificationModel->countUnread($decoded->user_id);

        return $this->success([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    public function markRead(int $id)
    {
        $decoded      = $this->getAuthUser();
        $notification = $this->notificationModel->find($id);

        if (!$notification || $notification['user_id'] != $decoded->user_id) {
            return $this->error('Notification not found.', 404);
        }

        $this->notificationModel->markAsRead($id);

        return $this->success([], 'Notification marked as read.');
    }

    public function markAllRead()
    {
        $decoded = $this->getAuthUser();
        $this->notificationModel->markAllAsRead($decoded->user_id);

        return $this->success([], 'All notifications marked as read.');
    }
}