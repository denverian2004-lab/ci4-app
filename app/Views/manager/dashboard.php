<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php if (!$manager || !$manager['department_id']): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Your account is not linked to an employee profile with a department. Please contact HR.
    </div>
<?php else: ?>

<!-- Welcome Banner -->
<div class="card mb-4" style="background:linear-gradient(135deg,#1e3a5f,#2e5090);border:none;">
    <div class="card-body py-4 d-flex align-items-center gap-4">
        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:64px;height:64px;font-size:1.5rem;font-weight:700;color:#1e3a5f;">
            <?= strtoupper(substr($manager['first_name'],0,1) . substr($manager['last_name'],0,1)) ?>
        </div>
        <div class="text-white">
            <h5 class="fw-bold mb-1">Welcome, <?= esc($manager['first_name']) ?>!</h5>
            <p class="mb-0 opacity-75">
                <?= esc($manager['position'] ?? 'Manager') ?> &bull;
                <?= esc($manager['department_name'] ?? 'No Department') ?> &bull;
                <?= esc($manager['employee_code']) ?>
            </p>
        </div>
    </div>
</div>

<!-- Team Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-stat-1">
            <div class="stat-label">Team Members</div>
            <div class="stat-number"><?= $teamCount ?></div>
            <i class="bi bi-people-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-stat-2">
            <div class="stat-label">Present Today</div>
            <div class="stat-number"><?= $presentCount ?></div>
            <i class="bi bi-calendar-check-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-stat-3">
            <div class="stat-label">Late Today</div>
            <div class="stat-number"><?= $lateCount ?></div>
            <i class="bi bi-clock-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-stat-4">
            <div class="stat-label">Pending Leave Requests</div>
            <div class="stat-number"><?= count($pendingLeaves) ?></div>
            <i class="bi bi-calendar2-x-fill stat-icon"></i>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">

    <!-- Today's Team Attendance -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-check-fill me-2 text-primary"></i>Today's Team Attendance</span>
                <a href="/manager/team-attendance" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($todayAttendance)): ?>
                                <?php foreach ($todayAttendance as $att): ?>
                                <?php
                                    $badgeMap = [
                                        'Present'  => 'success',
                                        'Absent'   => 'danger',
                                        'Late'     => 'warning',
                                        'Half-day' => 'info',
                                    ];
                                    $badge = $badgeMap[$att['status']] ?? 'secondary';
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($att['first_name'] . ' ' . $att['last_name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($att['employee_code']) ?></small>
                                    </td>
                                    <td><?= $att['time_in']  ? date('h:i A', strtotime($att['time_in']))  : '—' ?></td>
                                    <td><?= $att['time_out'] ? date('h:i A', strtotime($att['time_out'])) : '—' ?></td>
                                    <td><span class="badge bg-<?= $badge ?>"><?= esc($att['status']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        No attendance logged for today.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Leave Requests -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar2-x-fill me-2 text-primary"></i>Pending Leaves</span>
                <a href="/manager/team-leaves?status=Pending" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Type</th>
                                <th>Days</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pendingLeaves)): ?>
                                <?php foreach ($pendingLeaves as $leave): ?>
                                <tr>
                                    <td>
                                        <strong style="font-size:0.85rem;">
                                            <?= esc($leave['first_name'] . ' ' . $leave['last_name']) ?>
                                        </strong>
                                    </td>
                                    <td><small><?= esc($leave['leave_type_name']) ?></small></td>
                                    <td><?= $leave['total_days'] ?></td>
                                    <td>
                                        <a href="/manager/team-leaves/view/<?= $leave['id'] ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        No pending leave requests.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- My Recent Payroll -->
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cash-stack me-2 text-primary"></i>My Recent Payroll</span>
                <a href="/employee/my-payroll" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Basic Salary</th>
                                <th>Overtime</th>
                                <th>Deductions</th>
                                <th>Net Pay</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($myPayroll)): ?>
                                <?php foreach ($myPayroll as $p): ?>
                                <tr>
                                    <td>
                                        <?= date('M d', strtotime($p['period_start'])) ?>
                                        — <?= date('M d, Y', strtotime($p['period_end'])) ?>
                                    </td>
                                    <td>₱ <?= number_format($p['basic_salary'], 2) ?></td>
                                    <td>₱ <?= number_format($p['overtime_pay'], 2) ?></td>
                                    <td>₱ <?= number_format($p['deductions'], 2) ?></td>
                                    <td><strong>₱ <?= number_format($p['net_pay'], 2) ?></strong></td>
                                    <td>
                                        <span class="badge <?= $p['status'] === 'Finalized' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                            <?= esc($p['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No payroll records yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?= $this->endSection() ?>