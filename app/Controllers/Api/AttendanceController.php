<?php

namespace App\Controllers\Api;

use App\Models\AttendanceModel;
use App\Models\EmployeeModel;

class AttendanceController extends BaseApiController
{
    protected $attendanceModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->employeeModel   = new EmployeeModel();
    }

    public function index()
    {
        $date   = $this->request->getGet('date');
        $empId  = $this->request->getGet('employee_id');
        $from   = $this->request->getGet('from');
        $to     = $this->request->getGet('to');

        $query = $this->attendanceModel
            ->select('attendance.*, employees.first_name, employees.last_name, employees.employee_code')
            ->join('employees', 'employees.id = attendance.employee_id')
            ->orderBy('attendance.date', 'DESC');

        if ($date)  $query->where('attendance.date', $date);
        if ($empId) $query->where('attendance.employee_id', $empId);
        if ($from)  $query->where('attendance.date >=', $from);
        if ($to)    $query->where('attendance.date <=', $to);

        $records = $query->findAll();

        return $this->success($records);
    }

    public function clockIn()
    {
        $decoded = $this->getAuthUser();
        $input   = $this->request->getJSON(true);

        $employeeId = $input['employee_id'] ?? null;

        if (!$employeeId) {
            return $this->error('Employee ID is required.', 422);
        }

        // Check if already clocked in today
        $existing = $this->attendanceModel
            ->where('employee_id', $employeeId)
            ->where('date', date('Y-m-d'))
            ->first();

        if ($existing) {
            return $this->error('Already clocked in today.', 409);
        }

        $id = $this->attendanceModel->insert([
            'employee_id' => $employeeId,
            'date'        => date('Y-m-d'),
            'time_in'     => date('H:i:s'),
            'status'      => $input['status'] ?? 'Present',
            'remarks'     => $input['remarks'] ?? null,
        ]);

        $record = $this->attendanceModel->find($id);

        return $this->success($record, 'Clocked in successfully.', 201);
    }

    public function clockOut()
    {
        $input      = $this->request->getJSON(true);
        $employeeId = $input['employee_id'] ?? null;

        if (!$employeeId) {
            return $this->error('Employee ID is required.', 422);
        }

        $existing = $this->attendanceModel
            ->where('employee_id', $employeeId)
            ->where('date', date('Y-m-d'))
            ->first();

        if (!$existing) {
            return $this->error('No clock-in record found for today.', 404);
        }

        if ($existing['time_out']) {
            return $this->error('Already clocked out today.', 409);
        }

        $this->attendanceModel->update($existing['id'], [
            'time_out' => date('H:i:s'),
        ]);

        $record = $this->attendanceModel->find($existing['id']);

        return $this->success($record, 'Clocked out successfully.');
    }

    public function byEmployee(int $employeeId)
    {
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return $this->error('Employee not found.', 404);
        }

        $records = $this->attendanceModel
            ->where('employee_id', $employeeId)
            ->orderBy('date', 'DESC')
            ->findAll();

        return $this->success([
            'employee' => $employee,
            'records'  => $records,
        ]);
    }
}