<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\EmployeeModel;
use App\Models\AttendanceThresholdModel;

class AttendanceController extends BaseController
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
        $date       = $this->request->getGet('date') ?? date('Y-m-d');
        $attendance = $this->attendanceModel->getByDate($date);

        return view('admin/attendance/index', [
            'title'      => 'Attendance',
            'attendance' => $attendance,
            'date'       => $date,
        ]);
    }

    public function create()
    {
        return view('admin/attendance/create', [
            'title'     => 'Log Attendance',
            'employees' => $this->employeeModel->getActiveEmployees(),
        ]);
    }

    public function store()
    {
        $rules = [
            'employee_id' => 'required|integer',
            'date'        => 'required|valid_date',
            'status'      => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->with('error', implode('<br>', $this->validator->getErrors()))
                             ->withInput();
        }

        // Check for duplicate
        $existing = $this->attendanceModel
            ->where('employee_id', $this->request->getPost('employee_id'))
            ->where('date', $this->request->getPost('date'))
            ->first();

        if ($existing) {
            return redirect()->back()
                             ->with('error', 'Attendance for this employee on this date already exists.')
                             ->withInput();
        }

        $attEmpId  = $this->request->getPost('employee_id');
        $attStatus = $this->request->getPost('status');

        $this->attendanceModel->insert([
            'employee_id' => $attEmpId,
            'date'        => $this->request->getPost('date'),
            'time_in'     => $this->request->getPost('time_in') ?: null,
            'time_out'    => $this->request->getPost('time_out') ?: null,
            'status'      => $attStatus,
            'remarks'     => $this->request->getPost('remarks'),
        ]);

        // Check threshold and notify admins
        if (in_array($attStatus, ['Absent', 'Late'])) {
            $thresholdModel = new AttendanceThresholdModel();
            $type           = strtolower($attStatus);
            $threshold      = $thresholdModel->getThreshold($type);

            if ($threshold) {
                $monthStart = date('Y-m-01');
                $monthEnd   = date('Y-m-t');

                $count = $this->attendanceModel
                    ->where('employee_id', $attEmpId)
                    ->where('status', $attStatus)
                    ->where('date >=', $monthStart)
                    ->where('date <=', $monthEnd)
                    ->countAllResults();

                if ($count >= $threshold['max_allowed']) {
                    $attEmp = $this->employeeModel->find($attEmpId);
                    $empName = $attEmp
                        ? $attEmp['first_name'] . ' ' . $attEmp['last_name']
                        : 'An employee';

                    notify_all_admins(
                        '⚠️ Attendance Threshold Exceeded',
                        $empName . ' has reached ' . $count . ' ' . $attStatus . '(s) this month. Threshold is ' . $threshold['max_allowed'] . '.',
                        '/admin/attendance/threshold'
                    );
                }
            }
        }

        return redirect()->to('/admin/attendance')
                         ->with('success', 'Attendance logged successfully!');
    }

    public function edit(int $id)
    {
        $record = $this->attendanceModel->find($id);
        if (!$record) {
            return redirect()->to('/admin/attendance')->with('error', 'Record not found.');
        }

        return view('admin/attendance/edit', [
            'title'     => 'Edit Attendance',
            'record'    => $record,
            'employees' => $this->employeeModel->getActiveEmployees(),
        ]);
    }

    public function update(int $id)
    {
        $record = $this->attendanceModel->find($id);
        if (!$record) {
            return redirect()->to('/admin/attendance')->with('error', 'Record not found.');
        }

        $this->attendanceModel->update($id, [
            'employee_id' => $this->request->getPost('employee_id'),
            'date'        => $this->request->getPost('date'),
            'time_in'     => $this->request->getPost('time_in') ?: null,
            'time_out'    => $this->request->getPost('time_out') ?: null,
            'status'      => $this->request->getPost('status'),
            'remarks'     => $this->request->getPost('remarks'),
        ]);

        return redirect()->to('/admin/attendance')
                         ->with('success', 'Attendance updated successfully!');
    }

    public function delete(int $id)
    {
        $this->attendanceModel->delete($id);
        return redirect()->to('/admin/attendance')
                         ->with('success', 'Attendance record deleted.');
    }

    public function threshold()
    {
        $thresholdModel = new AttendanceThresholdModel();
        $monthStart     = date('Y-m-01');
        $monthEnd       = date('Y-m-t');

        return view('admin/attendance/threshold', [
            'title'           => 'Attendance Thresholds',
            'thresholds'      => $thresholdModel->getAll(),
            'absentViolators' => $thresholdModel->getViolators('absent', $monthStart, $monthEnd),
            'lateViolators'   => $thresholdModel->getViolators('late',   $monthStart, $monthEnd),
            'absentThreshold' => $thresholdModel->getThreshold('absent'),
            'lateThreshold'   => $thresholdModel->getThreshold('late'),
            'monthStart'      => $monthStart,
            'monthEnd'        => $monthEnd,
        ]);
    }

    public function updateThreshold()
    {
        $thresholdModel  = new AttendanceThresholdModel();
        $absentThreshold = $thresholdModel->getThreshold('absent');
        $lateThreshold   = $thresholdModel->getThreshold('late');

        $thresholdModel->update($absentThreshold['id'], [
            'max_allowed' => $this->request->getPost('absent_max'),
            'period'      => $this->request->getPost('absent_period'),
        ]);

        $thresholdModel->update($lateThreshold['id'], [
            'max_allowed' => $this->request->getPost('late_max'),
            'period'      => $this->request->getPost('late_period'),
        ]);

        return redirect()->to('/admin/attendance/threshold')
                         ->with('success', 'Attendance thresholds updated successfully!');
    }
}