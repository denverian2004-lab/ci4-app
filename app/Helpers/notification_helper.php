<?php

if (!function_exists('notify_user')) {
    /**
     * Send an in-app notification to a specific user
     */
    function notify_user(int $userId, string $title, string $message, string $url = ''): void
    {
        $notificationModel = new \App\Models\NotificationModel();
        $notificationModel->insert([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'url'     => $url,
            'is_read' => 0,
        ]);
    }
}

if (!function_exists('notify_all_admins')) {
    /**
     * Send an in-app notification to all admin users
     */
    function notify_all_admins(string $title, string $message, string $url = ''): void
    {
        $userModel = new \App\Models\UserModel();
        $admins    = $userModel->where('role', 'admin')->where('is_active', 1)->findAll();

        foreach ($admins as $admin) {
            notify_user($admin['id'], $title, $message, $url);
        }
    }
}

if (!function_exists('notify_all_managers')) {
    /**
     * Send an in-app notification to all manager users
     */
    function notify_all_managers(string $title, string $message, string $url = ''): void
    {
        $userModel = new \App\Models\UserModel();
        $managers  = $userModel->where('role', 'manager')->where('is_active', 1)->findAll();

        foreach ($managers as $manager) {
            notify_user($manager['id'], $title, $message, $url);
        }
    }
}