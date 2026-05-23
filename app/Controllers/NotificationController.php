<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    // Get notifications as JSON for AJAX
    public function fetch()
    {
        $userId        = session()->get('user_id');
        $notifications = $this->notificationModel->getForUser($userId, 15);
        $unreadCount   = $this->notificationModel->countUnread($userId);

        return $this->response->setJSON([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    // Mark single as read and redirect
    public function read(int $id)
    {
        $notification = $this->notificationModel->find($id);

        if ($notification && $notification['user_id'] == session()->get('user_id')) {
            $this->notificationModel->markAsRead($id);

            if (!empty($notification['url'])) {
                return redirect()->to($notification['url']);
            }
        }

        return redirect()->back();
    }

    // Mark all as read
    public function readAll()
    {
        $userId = session()->get('user_id');
        $this->notificationModel->markAllAsRead($userId);

        return $this->response->setJSON(['success' => true]);
    }

    // Clear all notifications
    public function clearAll()
    {
        $userId = session()->get('user_id');
        $this->notificationModel->clearAll($userId);

        return $this->response->setJSON(['success' => true]);
    }
}