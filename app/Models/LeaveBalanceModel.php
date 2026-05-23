<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveBalanceModel extends Model
{
    protected $table         = 'leave_balances';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'employee_id', 'leave_type_id', 'year',
        'allocated_days', 'used_days', 'remaining_days'
    ];
    protected $useTimestamps = false;

    // Get balance for a specific employee, leave type and year
    public function getBalance(int $employeeId, int $leaveTypeId, int $year)
    {
        return $this->where('employee_id', $employeeId)
                    ->where('leave_type_id', $leaveTypeId)
                    ->where('year', $year)
                    ->first();
    }

    // Get all balances for an employee for a given year
    public function getEmployeeBalances(int $employeeId, int $year)
    {
        return $this->select('leave_balances.*, leave_types.name as leave_type_name, leave_types.max_days')
                    ->join('leave_types', 'leave_types.id = leave_balances.leave_type_id')
                    ->where('leave_balances.employee_id', $employeeId)
                    ->where('leave_balances.year', $year)
                    ->findAll();
    }

    // Auto-initialize balance if not yet created
    public function initBalance(int $employeeId, int $leaveTypeId, int $maxDays, int $year)
    {
        $existing = $this->getBalance($employeeId, $leaveTypeId, $year);
        if (!$existing) {
            $this->insert([
                'employee_id'    => $employeeId,
                'leave_type_id'  => $leaveTypeId,
                'year'           => $year,
                'allocated_days' => $maxDays,
                'used_days'      => 0,
                'remaining_days' => $maxDays,
            ]);
        }
        return $this->getBalance($employeeId, $leaveTypeId, $year);
    }

    // Deduct days when leave is approved
    public function deductDays(int $employeeId, int $leaveTypeId, int $days, int $year)
    {
        $balance = $this->getBalance($employeeId, $leaveTypeId, $year);
        if ($balance) {
            $this->update($balance['id'], [
                'used_days'      => $balance['used_days'] + $days,
                'remaining_days' => $balance['remaining_days'] - $days,
            ]);
        }
    }

    // Restore days when leave is rejected
    public function restoreDays(int $employeeId, int $leaveTypeId, int $days, int $year)
    {
        $balance = $this->getBalance($employeeId, $leaveTypeId, $year);
        if ($balance) {
            $this->update($balance['id'], [
                'used_days'      => max(0, $balance['used_days'] - $days),
                'remaining_days' => $balance['remaining_days'] + $days,
            ]);
        }
    }
}