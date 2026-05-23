<?php

namespace App\Controllers;

use App\Models\UserModel;

class AdminController extends BaseController
{
    public function changePassword()
    {
        return view('admin/change_password', [
            'title' => 'Change Password',
        ]);
    }

    public function updatePassword()
    {
        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->with('error', implode('<br>', $this->validator->getErrors()))
                             ->withInput();
        }

        $userModel       = new UserModel();
        $userId          = session()->get('user_id');
        $user            = $userModel->find($userId);
        $currentPassword = $this->request->getPost('current_password');

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()
                             ->with('error', 'Your current password is incorrect.')
                             ->withInput();
        }

        // Update password
        $userModel->update($userId, [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/admin/change-password')
                         ->with('success', 'Password changed successfully!');
    }
}