<?php

namespace App\Controllers;

use App\Models\LeaveModel;
use App\Models\LeaveTypeModel;
use App\Models\EmployeeModel;
use App\Models\LeaveBalanceModel;
use App\Models\UserModel;

class LeaveController extends BaseController
{
    protected $leaveModel;
    protected $leaveTypeModel;
    protected $employeeModel;
    protected $leaveBalanceModel;
    protected $userModel;

    public function __construct()
    {
        $this->leaveModel        = new LeaveModel();
        $this->leaveTypeModel    = new LeaveTypeModel();
        $this->employeeModel     = new EmployeeModel();
        $this->leaveBalanceModel = new LeaveBalanceModel();
        $this->userModel         = new UserModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status') ?? '';
        $query  = $this->leaveModel
            ->select('leave_requests.*, employees.first_name, employees.last_name, employees.employee_code, leave_types.name as leave_type_name')
            ->join('employees',   'employees.id = leave_requests.employee_id')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->orderBy('leave_requests.created_at', 'DESC');

        if ($status !== '') {
            $query->where('leave_requests.status', $status);
        }

        $leaves = $query->findAll();

        return view('admin/leaves/index', [
            'title'  => 'Leave Requests',
            'leaves' => $leaves,
            'status' => $status,
        ]);
    }

    public function view(int $id)
    {
        $leave = $this->leaveModel
            ->select('leave_requests.*, employees.first_name, employees.last_name, employees.employee_code, employees.position, employees.email, leave_types.name as leave_type_name')
            ->join('employees',   'employees.id = leave_requests.employee_id')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->find($id);

        if (!$leave) {
            return redirect()->to('/admin/leaves')->with('error', 'Leave request not found.');
        }

        // Get balance info
        $year    = date('Y', strtotime($leave['start_date']));
        $balance = $this->leaveBalanceModel->getBalance(
            $leave['employee_id'],
            $leave['leave_type_id'],
            $year
        );

        return view('admin/leaves/view', [
            'title'   => 'Leave Request Details',
            'leave'   => $leave,
            'balance' => $balance,
        ]);
    }

    public function approve(int $id)
    {
        $leave = $this->leaveModel->find($id);
        if (!$leave) {
            return redirect()->to('/admin/leaves')->with('error', 'Leave request not found.');
        }

        if ($leave['status'] !== 'Pending') {
            return redirect()->to('/admin/leaves/view/' . $id)
                             ->with('error', 'This leave request has already been processed.');
        }

        $year      = date('Y', strtotime($leave['start_date']));
        $leaveType = $this->leaveTypeModel->find($leave['leave_type_id']);

        // Initialize balance if not exists
        $balance = $this->leaveBalanceModel->initBalance(
            $leave['employee_id'],
            $leave['leave_type_id'],
            $leaveType['max_days'],
            $year
        );

        // Check if enough remaining days
        if ($balance['remaining_days'] < $leave['total_days']) {
            return redirect()->to('/admin/leaves/view/' . $id)
                             ->with('error', "Cannot approve. Employee only has {$balance['remaining_days']} remaining {$leaveType['name']} day(s) but requested {$leave['total_days']} day(s).");
        }

        // Approve and deduct balance
        $this->leaveModel->update($id, [
            'status'      => 'Approved',
            'approved_by' => session()->get('user_id'),
        ]);

        $this->leaveBalanceModel->deductDays(
            $leave['employee_id'],
            $leave['leave_type_id'],
            $leave['total_days'],
            $year
        );

        // Notify employee
        $empUser = $this->userModel->where('employee_id', $leave['employee_id'])->first();
        if ($empUser) {
            notify_user(
                $empUser['id'],
                'Leave Request Approved ✅',
                'Your ' . $leaveType['name'] . ' request for ' . $leave['total_days'] . ' day(s) has been approved.',
                '/employee/my-leaves'
            );
        }

        return redirect()->to('/admin/leaves/view/' . $id)
                         ->with('success', 'Leave request approved and balance updated!');
    }

    public function reject(int $id)
    {
        $leave = $this->leaveModel->find($id);
        if (!$leave) {
            return redirect()->to('/admin/leaves')->with('error', 'Leave request not found.');
        }

        $wasApproved = $leave['status'] === 'Approved';
        $leaveType   = $this->leaveTypeModel->find($leave['leave_type_id']);

        $this->leaveModel->update($id, [
            'status'      => 'Rejected',
            'approved_by' => session()->get('user_id'),
        ]);

        // Restore balance if it was previously approved
        if ($wasApproved) {
            $year = date('Y', strtotime($leave['start_date']));
            $this->leaveBalanceModel->restoreDays(
                $leave['employee_id'],
                $leave['leave_type_id'],
                $leave['total_days'],
                $year
            );
        }

        // Notify employee
        $empUser = $this->userModel->where('employee_id', $leave['employee_id'])->first();
        if ($empUser) {
            notify_user(
                $empUser['id'],
                'Leave Request Rejected ❌',
                'Your ' . $leaveType['name'] . ' request for ' . $leave['total_days'] . ' day(s) has been rejected.',
                '/employee/my-leaves'
            );
        }

        return redirect()->to('/admin/leaves/view/' . $id)
                         ->with('success', 'Leave request rejected.');
    }
}