<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\LeaveModel;
use App\Models\PayrollModel;
use App\Models\DepartmentModel;
use App\Models\AttendanceThresholdModel;

class ReportController extends BaseController
{
    protected $employeeModel;
    protected $attendanceModel;
    protected $leaveModel;
    protected $payrollModel;
    protected $departmentModel;

    public function __construct()
    {
        $this->employeeModel   = new EmployeeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->leaveModel      = new LeaveModel();
        $this->payrollModel    = new PayrollModel();
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        $totalEmployees  = $this->employeeModel->countAll();
        $activeEmployees = $this->employeeModel->countByStatus('Active');
        $totalDepts      = $this->departmentModel->countAll();
        $pendingLeaves   = $this->leaveModel->countPending();
        $monthStart      = date('Y-m-01');
        $monthEnd        = date('Y-m-t');

        $presentCount = $this->attendanceModel->where('date >=', $monthStart)->where('date <=', $monthEnd)->where('status', 'Present')->countAllResults();
        $absentCount  = $this->attendanceModel->where('date >=', $monthStart)->where('date <=', $monthEnd)->where('status', 'Absent')->countAllResults();
        $lateCount    = $this->attendanceModel->where('date >=', $monthStart)->where('date <=', $monthEnd)->where('status', 'Late')->countAllResults();

        $payrollTotal = $this->payrollModel->selectSum('net_pay')->where('period_start >=', $monthStart)->where('period_end <=', $monthEnd)->first();

        $approvedLeaves = $this->leaveModel->where('status', 'Approved')->countAllResults();
        $rejectedLeaves = $this->leaveModel->where('status', 'Rejected')->countAllResults();

        $departments = $this->departmentModel
            ->select('departments.name, COUNT(employees.id) as emp_count')
            ->join('employees', 'employees.department_id = departments.id', 'left')
            ->groupBy('departments.id')
            ->findAll();

        return view('admin/reports/index', [
            'title'           => 'Reports & Analytics',
            'totalEmployees'  => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'totalDepts'      => $totalDepts,
            'pendingLeaves'   => $pendingLeaves,
            'presentCount'    => $presentCount,
            'absentCount'     => $absentCount,
            'lateCount'       => $lateCount,
            'payrollTotal'    => $payrollTotal['net_pay'] ?? 0,
            'approvedLeaves'  => $approvedLeaves,
            'rejectedLeaves'  => $rejectedLeaves,
            'departments'     => $departments,
            'monthStart'      => $monthStart,
            'monthEnd'        => $monthEnd,
        ]);
    }

    public function attendance()
    {
        $from = $this->request->getGet('from') ?? date('Y-m-01');
        $to   = $this->request->getGet('to')   ?? date('Y-m-t');

        $records = $this->attendanceModel
            ->select('attendance.*, employees.first_name, employees.last_name, employees.employee_code, departments.name as department_name')
            ->join('employees',   'employees.id = attendance.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('attendance.date >=', $from)
            ->where('attendance.date <=', $to)
            ->orderBy('attendance.date', 'DESC')
            ->findAll();

        $summary = ['Present' => 0, 'Absent' => 0, 'Late' => 0, 'Half-day' => 0];
        foreach ($records as $r) {
            if (isset($summary[$r['status']])) $summary[$r['status']]++;
        }

        return view('admin/reports/attendance', [
            'title'   => 'Attendance Report',
            'records' => $records,
            'summary' => $summary,
            'from'    => $from,
            'to'      => $to,
        ]);
    }

    public function payroll()
    {
        $from = $this->request->getGet('from') ?? date('Y-m-01');
        $to   = $this->request->getGet('to')   ?? date('Y-m-t');

        $records = $this->payrollModel
            ->select('payroll.*, employees.first_name, employees.last_name, employees.employee_code, employees.position, departments.name as department_name')
            ->join('employees',   'employees.id = payroll.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('payroll.period_start >=', $from)
            ->where('payroll.period_end <=',   $to)
            ->orderBy('payroll.period_start',  'DESC')
            ->findAll();

        $totals = [
            'basic'      => array_sum(array_column($records, 'basic_salary')),
            'overtime'   => array_sum(array_column($records, 'overtime_pay')),
            'deductions' => array_sum(array_column($records, 'deductions')),
            'net'        => array_sum(array_column($records, 'net_pay')),
        ];

        return view('admin/reports/payroll', [
            'title'   => 'Payroll Report',
            'records' => $records,
            'totals'  => $totals,
            'from'    => $from,
            'to'      => $to,
        ]);
    }

    public function leave()
    {
        $from = $this->request->getGet('from') ?? date('Y-m-01');
        $to   = $this->request->getGet('to')   ?? date('Y-m-t');

        $records = $this->leaveModel
            ->select('leave_requests.*, employees.first_name, employees.last_name, employees.employee_code, leave_types.name as leave_type_name')
            ->join('employees',   'employees.id = leave_requests.employee_id')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->where('leave_requests.start_date >=', $from)
            ->where('leave_requests.end_date <=',   $to)
            ->orderBy('leave_requests.start_date',  'DESC')
            ->findAll();

        $summary = ['Pending' => 0, 'Approved' => 0, 'Rejected' => 0];
        foreach ($records as $r) {
            if (isset($summary[$r['status']])) $summary[$r['status']]++;
        }

        return view('admin/reports/leave', [
            'title'   => 'Leave Report',
            'records' => $records,
            'summary' => $summary,
            'from'    => $from,
            'to'      => $to,
        ]);
    }

    // -------------------------------------------------------
    // ATTENDANCE EXPORTS
    // -------------------------------------------------------
    public function exportAttendancePdf()
    {
        $from    = $this->request->getGet('from') ?? date('Y-m-01');
        $to      = $this->request->getGet('to')   ?? date('Y-m-t');
        $records = $this->attendanceModel
            ->select('attendance.*, employees.first_name, employees.last_name, employees.employee_code, departments.name as department_name')
            ->join('employees',   'employees.id = attendance.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('attendance.date >=', $from)
            ->where('attendance.date <=', $to)
            ->orderBy('attendance.date', 'DESC')
            ->findAll();

        $html = $this->buildAttendanceHtml($records, $from, $to);
        $this->generatePdf($html, 'Attendance_Report_' . $from . '_to_' . $to);
    }

    public function exportAttendanceExcel()
    {
        $from    = $this->request->getGet('from') ?? date('Y-m-01');
        $to      = $this->request->getGet('to')   ?? date('Y-m-t');
        $records = $this->attendanceModel
            ->select('attendance.*, employees.first_name, employees.last_name, employees.employee_code, departments.name as department_name')
            ->join('employees',   'employees.id = attendance.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('attendance.date >=', $from)
            ->where('attendance.date <=', $to)
            ->orderBy('attendance.date', 'DESC')
            ->findAll();

        $headers = ['#', 'Employee', 'Code', 'Department', 'Date', 'Time In', 'Time Out', 'Status', 'Remarks'];
        $rows    = [];
        foreach ($records as $i => $r) {
            $rows[] = [
                $i + 1,
                $r['first_name'] . ' ' . $r['last_name'],
                $r['employee_code'],
                $r['department_name'] ?? '—',
                date('M d, Y', strtotime($r['date'])),
                $r['time_in']  ? date('h:i A', strtotime($r['time_in']))  : '—',
                $r['time_out'] ? date('h:i A', strtotime($r['time_out'])) : '—',
                $r['status'],
                $r['remarks'] ?? '—',
            ];
        }

        $this->generateExcel('Attendance Report', $headers, $rows, 'Attendance_Report_' . $from . '_to_' . $to);
    }

    // -------------------------------------------------------
    // PAYROLL EXPORTS
    // -------------------------------------------------------
    public function exportPayrollPdf()
    {
        $from    = $this->request->getGet('from') ?? date('Y-m-01');
        $to      = $this->request->getGet('to')   ?? date('Y-m-t');
        $records = $this->payrollModel
            ->select('payroll.*, employees.first_name, employees.last_name, employees.employee_code, employees.position, departments.name as department_name')
            ->join('employees',   'employees.id = payroll.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('payroll.period_start >=', $from)
            ->where('payroll.period_end <=',   $to)
            ->orderBy('payroll.period_start',  'DESC')
            ->findAll();

        $html = $this->buildPayrollHtml($records, $from, $to);
        $this->generatePdf($html, 'Payroll_Report_' . $from . '_to_' . $to);
    }

    public function exportPayrollExcel()
    {
        $from    = $this->request->getGet('from') ?? date('Y-m-01');
        $to      = $this->request->getGet('to')   ?? date('Y-m-t');
        $records = $this->payrollModel
            ->select('payroll.*, employees.first_name, employees.last_name, employees.employee_code, employees.position, departments.name as department_name')
            ->join('employees',   'employees.id = payroll.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('payroll.period_start >=', $from)
            ->where('payroll.period_end <=',   $to)
            ->orderBy('payroll.period_start',  'DESC')
            ->findAll();

        $headers = ['#', 'Employee', 'Code', 'Department', 'Position', 'Period', 'Basic Salary', 'Overtime', 'Deductions', 'Net Pay', 'Status'];
        $rows    = [];
        foreach ($records as $i => $r) {
            $rows[] = [
                $i + 1,
                $r['first_name'] . ' ' . $r['last_name'],
                $r['employee_code'],
                $r['department_name'] ?? '—',
                $r['position'] ?? '—',
                date('M d', strtotime($r['period_start'])) . ' - ' . date('M d, Y', strtotime($r['period_end'])),
                '₱' . number_format($r['basic_salary'], 2),
                '₱' . number_format($r['overtime_pay'], 2),
                '₱' . number_format($r['deductions'], 2),
                '₱' . number_format($r['net_pay'], 2),
                $r['status'],
            ];
        }

        $this->generateExcel('Payroll Report', $headers, $rows, 'Payroll_Report_' . $from . '_to_' . $to);
    }

    // -------------------------------------------------------
    // LEAVE EXPORTS
    // -------------------------------------------------------
    public function exportLeavePdf()
    {
        $from    = $this->request->getGet('from') ?? date('Y-m-01');
        $to      = $this->request->getGet('to')   ?? date('Y-m-t');
        $records = $this->leaveModel
            ->select('leave_requests.*, employees.first_name, employees.last_name, employees.employee_code, leave_types.name as leave_type_name')
            ->join('employees',   'employees.id = leave_requests.employee_id')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->where('leave_requests.start_date >=', $from)
            ->where('leave_requests.end_date <=',   $to)
            ->orderBy('leave_requests.start_date',  'DESC')
            ->findAll();

        $html = $this->buildLeaveHtml($records, $from, $to);
        $this->generatePdf($html, 'Leave_Report_' . $from . '_to_' . $to);
    }

    public function exportLeaveExcel()
    {
        $from    = $this->request->getGet('from') ?? date('Y-m-01');
        $to      = $this->request->getGet('to')   ?? date('Y-m-t');
        $records = $this->leaveModel
            ->select('leave_requests.*, employees.first_name, employees.last_name, employees.employee_code, leave_types.name as leave_type_name')
            ->join('employees',   'employees.id = leave_requests.employee_id')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->where('leave_requests.start_date >=', $from)
            ->where('leave_requests.end_date <=',   $to)
            ->orderBy('leave_requests.start_date',  'DESC')
            ->findAll();

        $headers = ['#', 'Employee', 'Code', 'Leave Type', 'Start Date', 'End Date', 'Days', 'Reason', 'Status'];
        $rows    = [];
        foreach ($records as $i => $r) {
            $rows[] = [
                $i + 1,
                $r['first_name'] . ' ' . $r['last_name'],
                $r['employee_code'],
                $r['leave_type_name'],
                date('M d, Y', strtotime($r['start_date'])),
                date('M d, Y', strtotime($r['end_date'])),
                $r['total_days'] . ' day(s)',
                $r['reason'] ?? '—',
                $r['status'],
            ];
        }

        $this->generateExcel('Leave Report', $headers, $rows, 'Leave_Report_' . $from . '_to_' . $to);
    }

    // -------------------------------------------------------
    // PDF GENERATOR
    // -------------------------------------------------------
    private function generatePdf(string $html, string $filename)
    {
        $mpdf = new \Mpdf\Mpdf([
            'margin_top'    => 15,
            'margin_bottom' => 15,
            'margin_left'   => 15,
            'margin_right'  => 15,
        ]);

        $mpdf->SetTitle($filename);
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename . '.pdf', 'D');
        exit;
    }

    // -------------------------------------------------------
    // EXCEL GENERATOR
    // -------------------------------------------------------
    private function generateExcel(string $title, array $headers, array $rows, string $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle($title);

        // Title row
        $sheet->mergeCells('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1');
        $sheet->setCellValue('A1', $title . ' (' . date('Y-m-d') . ')');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Header row
        foreach ($headers as $col => $header) {
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '2';
            $sheet->setCellValue($cellCoord, $header);
            $sheet->getStyle($cellCoord)->getFont()->setBold(true);
            $sheet->getStyle($cellCoord)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('1e3a5f');
            $sheet->getStyle($cellCoord)->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cellCoord)->getAlignment()->setHorizontal('center');
        }

        // Data rows
        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $col => $value) {
                $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . ($rowIndex + 3);
                $sheet->setCellValue($cellCoord, $value);

                // Alternate row colors
                if ($rowIndex % 2 === 0) {
                    $sheet->getStyle($cellCoord)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('f8f9fa');
                }
            }
        }

        // Auto size columns
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        // Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // -------------------------------------------------------
    // HTML BUILDERS FOR PDF
    // -------------------------------------------------------
    private function buildAttendanceHtml(array $records, string $from, string $to): string
    {
        $rows = '';
        foreach ($records as $i => $r) {
            $bg   = $i % 2 === 0 ? '#ffffff' : '#f8f9fa';
            $rows .= "
            <tr style='background:{$bg};'>
                <td>" . ($i + 1) . "</td>
                <td>" . htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) . "</td>
                <td>" . htmlspecialchars($r['employee_code']) . "</td>
                <td>" . htmlspecialchars($r['department_name'] ?? '—') . "</td>
                <td>" . date('M d, Y', strtotime($r['date'])) . "</td>
                <td>" . ($r['time_in']  ? date('h:i A', strtotime($r['time_in']))  : '—') . "</td>
                <td>" . ($r['time_out'] ? date('h:i A', strtotime($r['time_out'])) : '—') . "</td>
                <td>" . htmlspecialchars($r['status']) . "</td>
                <td>" . htmlspecialchars($r['remarks'] ?? '—') . "</td>
            </tr>";
        }

        return $this->pdfTemplate('Attendance Report', $from, $to, '
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Employee</th><th>Code</th><th>Department</th>
                        <th>Date</th><th>Time In</th><th>Time Out</th><th>Status</th><th>Remarks</th>
                    </tr>
                </thead>
                <tbody>' . $rows . '</tbody>
            </table>
        ');
    }

    private function buildPayrollHtml(array $records, string $from, string $to): string
    {
        $rows  = '';
        $total = 0;
        foreach ($records as $i => $r) {
            $bg    = $i % 2 === 0 ? '#ffffff' : '#f8f9fa';
            $total += $r['net_pay'];
            $rows  .= "
            <tr style='background:{$bg};'>
                <td>" . ($i + 1) . "</td>
                <td>" . htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) . "</td>
                <td>" . htmlspecialchars($r['employee_code']) . "</td>
                <td>" . htmlspecialchars($r['department_name'] ?? '—') . "</td>
                <td>" . date('M d', strtotime($r['period_start'])) . ' - ' . date('M d, Y', strtotime($r['period_end'])) . "</td>
                <td>&#8369;" . number_format($r['basic_salary'], 2) . "</td>
                <td>&#8369;" . number_format($r['overtime_pay'], 2) . "</td>
                <td>&#8369;" . number_format($r['deductions'], 2) . "</td>
                <td><strong>&#8369;" . number_format($r['net_pay'], 2) . "</strong></td>
                <td>" . htmlspecialchars($r['status']) . "</td>
            </tr>";
        }

        $rows .= "
        <tr style='background:#1e3a5f;color:#fff;font-weight:bold;'>
            <td colspan='8' style='text-align:right;'>Total Net Pay:</td>
            <td colspan='2'>&#8369;" . number_format($total, 2) . "</td>
        </tr>";

        return $this->pdfTemplate('Payroll Report', $from, $to, '
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Employee</th><th>Code</th><th>Department</th>
                        <th>Period</th><th>Basic</th><th>Overtime</th><th>Deductions</th>
                        <th>Net Pay</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>' . $rows . '</tbody>
            </table>
        ');
    }

    private function buildLeaveHtml(array $records, string $from, string $to): string
    {
        $rows = '';
        foreach ($records as $i => $r) {
            $bg   = $i % 2 === 0 ? '#ffffff' : '#f8f9fa';
            $rows .= "
            <tr style='background:{$bg};'>
                <td>" . ($i + 1) . "</td>
                <td>" . htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) . "</td>
                <td>" . htmlspecialchars($r['employee_code']) . "</td>
                <td>" . htmlspecialchars($r['leave_type_name']) . "</td>
                <td>" . date('M d, Y', strtotime($r['start_date'])) . "</td>
                <td>" . date('M d, Y', strtotime($r['end_date'])) . "</td>
                <td>" . $r['total_days'] . " day(s)</td>
                <td>" . htmlspecialchars($r['status']) . "</td>
            </tr>";
        }

        return $this->pdfTemplate('Leave Report', $from, $to, '
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Employee</th><th>Code</th><th>Leave Type</th>
                        <th>Start Date</th><th>End Date</th><th>Days</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>' . $rows . '</tbody>
            </table>
        ');
    }

    private function pdfTemplate(string $title, string $from, string $to, string $content): string
    {
        return '
        <html>
        <head>
        <style>
            body        { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
            h2          { color: #1e3a5f; margin-bottom: 4px; }
            .subtitle   { color: #666; font-size: 10px; margin-bottom: 16px; }
            table       { width: 100%; border-collapse: collapse; margin-top: 10px; }
            thead tr    { background: #1e3a5f; color: #fff; }
            th, td      { padding: 7px 8px; text-align: left; border: 1px solid #ddd; font-size: 10px; }
            th          { font-weight: bold; }
            .footer     { margin-top: 20px; font-size: 9px; color: #999; text-align: right; }
        </style>
        </head>
        <body>
            <h2>Employee Management System</h2>
            <h3 style="color:#2e5090;margin-top:0;">' . $title . '</h3>
            <p class="subtitle">Period: ' . date('F d, Y', strtotime($from)) . ' &mdash; ' . date('F d, Y', strtotime($to)) . ' &nbsp;|&nbsp; Generated: ' . date('F d, Y h:i A') . '</p>
            ' . $content . '
            <div class="footer">Generated by EMS Portal &copy; ' . date('Y') . '</div>
        </body>
        </html>';
    }
}