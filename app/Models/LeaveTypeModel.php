<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveTypeModel extends Model
{
    protected $table         = 'leave_types';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'max_days'];
    protected $useTimestamps = false;
}