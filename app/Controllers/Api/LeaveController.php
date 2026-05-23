<?php

namespace App\Controllers\Api;

use App\Models\LeaveModel;
use App\Models\LeaveTypeModel;
use App\Models\LeaveBalanceModel;
use App\Models\EmployeeModel;

class LeaveController extends BaseApiController
{
    protected $leaveModel;
    protected $leaveTypeModel;
    protected $leaveBalanceModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->leaveModel        = new LeaveModel();
        $this->leaveTypeModel    = new LeaveTypeModel();
        $this->leaveBalanceModel = new LeaveBalanceModel();
        $this->employeeModel     = new EmployeeModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        $empId  = $this->request->getGet('employee_id');

        $query = $this->leaveModel
            ->select('leave_requests.*, employees.first_name, employees.last_name, employees.employee_code, leave_types.name as leave_type_name')
            ->join('employees',   'employees.id = leave_requests.employee_id')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->orderBy('leave_requests.created_at', 'DESC');

        if ($status) $query->where('leave_requests.status', $status);
        if ($empId)  $query->where('leave_requests.employee_id', $empId);

        $leaves = $query->findAll();

        return $this->success($leaves);
    }

    public function store()
    {
        $input = $this->request->getJSON(true);

        $rules = [
            'employee_id'   => 'required|integer',
            'leave_type_id' => 'required|integer',
            'start_date'    => 'required|valid_date',
            'end_date'      => 'required|valid_date',
            'reason'        => 'required|min_length[5]',
        ];

        if (!$this->validate($rules, $input)) {
            return $this->error('Validation failed', 422, $this->validator->getErrors());
        }

        $start     = new \DateTime($input['start_date']);
        $end       = new \DateTime($input['end_date']);
        $totalDays = $start->diff($end)->days + 1;

        if ($end < $start) {
            return $this->error('End date cannot be before start date.', 422);
        }

        $leaveType = $this->leaveTypeModel->find($input['leave_type_id']);
        $year      = $start->format('Y');

        $balance = $this->leaveBalanceModel->initBalance(
            $input['employee_id'],
            $input['leave_type_id'],
            $leaveType['max_days'],
            $year
        );

        if ($totalDays > $balance['remaining_days']) {
            return $this->error("Insufficient leave balance. You only have {$balance['remaining_days']} remaining {$leaveType['name']} day(s).", 422);
        }

        $id = $this->leaveModel->insert([
            'employee_id'   => $input['employee_id'],
            'leave_type_id' => $input['leave_type_id'],
            'start_date'    => $input['start_date'],
            'end_date'      => $input['end_date'],
            'total_days'    => $totalDays,
            'reason'        => $input['reason'],
            'status'        => 'Pending',
        ]);

        $leave = $this->leaveModel->find($id);

        return $this->success($leave, 'Leave request submitted successfully.', 201);
    }

    public function approve(int $id)
    {
        $leave = $this->leaveModel->find($id);
        if (!$leave) {
            return $this->error('Leave request not found.', 404);
        }

        if ($leave['status'] !== 'Pending') {
            return $this->error('This leave request has already been processed.', 409);
        }

        $leaveType = $this->leaveTypeModel->find($leave['leave_type_id']);
        $year      = date('Y', strtotime($leave['start_date']));

        $balance = $this->leaveBalanceModel->initBalance(
            $leave['employee_id'],
            $leave['leave_type_id'],
            $leaveType['max_days'],
            $year
        );

        if ($balance['remaining_days'] < $leave['total_days']) {
            return $this->error("Cannot approve. Employee only has {$balance['remaining_days']} remaining day(s).", 422);
        }

        $decoded = $this->getAuthUser();

        $this->leaveModel->update($id, [
            'status'      => 'Approved',
            'approved_by' => $decoded->user_id,
        ]);

        $this->leaveBalanceModel->deductDays(
            $leave['employee_id'],
            $leave['leave_type_id'],
            $leave['total_days'],
            $year
        );

        return $this->success([], 'Leave request approved successfully.');
    }

    public function reject(int $id)
    {
        $leave = $this->leaveModel->find($id);
        if (!$leave) {
            return $this->error('Leave request not found.', 404);
        }

        $decoded = $this->getAuthUser();

        $this->leaveModel->update($id, [
            'status'      => 'Rejected',
            'approved_by' => $decoded->user_id,
        ]);

        return $this->success([], 'Leave request rejected.');
    }

    public function balance(int $employeeId)
    {
        $leaveTypes = $this->leaveTypeModel->findAll();
        $year       = date('Y');
        $balances   = [];

        foreach ($leaveTypes as $type) {
            $balance    = $this->leaveBalanceModel->initBalance(
                $employeeId,
                $type['id'],
                $type['max_days'],
                $year
            );
            $balances[] = array_merge($type, [
                'allocated_days' => $balance['allocated_days'],
                'used_days'      => $balance['used_days'],
                'remaining_days' => $balance['remaining_days'],
            ]);
        }

        return $this->success($balances);
    }
}