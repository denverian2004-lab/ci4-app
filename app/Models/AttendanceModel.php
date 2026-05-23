<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table         = 'attendance';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['employee_id', 'date', 'time_in', 'time_out', 'status', 'remarks'];
    protected $useTimestamps = false;

    public function getAttendanceWithEmployee()
    {
        return $this->select('attendance.*, employees.first_name, employees.last_name, employees.employee_code')
                    ->join('employees', 'employees.id = attendance.employee_id')
                    ->orderBy('attendance.date', 'DESC')
                    ->findAll();
    }

    public function getByEmployee(int $employeeId)
    {
        return $this->where('employee_id', $employeeId)
                    ->orderBy('date', 'DESC')
                    ->findAll();
    }

    public function getByDate(string $date)
    {
        return $this->select('attendance.*, employees.first_name, employees.last_name, employees.employee_code')
                    ->join('employees', 'employees.id = attendance.employee_id')
                    ->where('attendance.date', $date)
                    ->findAll();
    }

    public function countTodayPresent()
    {
        return $this->where('date', date('Y-m-d'))
                    ->where('status', 'Present')
                    ->countAllResults();
    }
}