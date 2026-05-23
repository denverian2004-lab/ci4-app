<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['employee_id', 'username', 'password', 'role', 'is_active'];
    protected $useTimestamps    = false;

    public function getActiveUsers()
    {
        return $this->where('is_active', 1)->findAll();
    }

    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->first();
    }
}