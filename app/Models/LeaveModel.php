<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveModel extends Model
{
    protected $table         = 'leave_requests';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'employee_id', 'leave_type_id', 'start_date',
        'end_date', 'total_days', 'reason', 'status', 'approved_by'
    ];
    protected $useTimestamps = false;

    public function getLeavesWithDetails()
    {
        return $this->select('leave_requests.*, employees.first_name, employees.last_name, employees.employee_code, leave_types.name as leave_type_name')
                    ->join('employees', 'employees.id = leave_requests.employee_id')
                    ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
                    ->orderBy('leave_requests.created_at', 'DESC')
                    ->findAll();
    }

    public function getByEmployee(int $employeeId)
    {
        return $this->select('leave_requests.*, leave_types.name as leave_type_name')
                    ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
                    ->where('leave_requests.employee_id', $employeeId)
                    ->orderBy('leave_requests.created_at', 'DESC')
                    ->findAll();
    }

    public function countPending()
    {
        return $this->where('status', 'Pending')->countAllResults();
    }
}