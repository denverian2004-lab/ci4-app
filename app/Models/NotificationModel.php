<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table         = 'notifications';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['user_id', 'title', 'message', 'url', 'is_read'];
    protected $useTimestamps = false;

    // Get all notifications for a user
    public function getForUser(int $userId, int $limit = 10)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    // Count unread notifications
    public function countUnread(int $userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    // Mark single notification as read
    public function markAsRead(int $id)
    {
        return $this->update($id, ['is_read' => 1]);
    }

    // Mark all as read for a user
    public function markAllAsRead(int $userId)
    {
        return $this->where('user_id', $userId)
                    ->set(['is_read' => 1])
                    ->update();
    }

    // Delete all notifications for a user
    public function clearAll(int $userId)
    {
        return $this->where('user_id', $userId)->delete();
    }
}