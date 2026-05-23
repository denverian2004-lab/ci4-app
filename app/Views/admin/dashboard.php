<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-stat-1">
            <div class="stat-label">Total Employees</div>
            <div class="stat-number"><?= $totalEmployees ?></div>
            <i class="bi bi-people-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-stat-2">
            <div class="stat-label">Active Employees</div>
            <div class="stat-number"><?= $activeEmployees ?></div>
            <i class="bi bi-person-check-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-stat-3">
            <div class="stat-label">Present Today</div>
            <div class="stat-number"><?= $presentToday ?></div>
            <i class="bi bi-calendar-check-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-stat-4">
            <div class="stat-label">Pending Leaves</div>
            <div class="stat-number"><?= $pendingLeaves ?></div>
            <i class="bi bi-calendar2-x-fill stat-icon"></i>
        </div>
    </div>
</div>

<!-- RECENT EMPLOYEES + DEPARTMENT BREAKDOWN -->
<div class="row g-3 mb-4">

    <!-- Recent Employees -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-people-fill me-2 text-primary"></i>Recently Added Employees</span>
                <a href="/admin/employees" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Code</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentEmployees)): ?>
                                <?php foreach ($recentEmployees as $emp): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                 style="width:34px;height:34px;font-size:0.8rem;font-weight:600;">
                                                <?= strtoupper(substr($emp['first_name'],0,1) . substr($emp['last_name'],0,1)) ?>
                                            </div>
                                            <div>
                                                <div style="font-weight:600;font-size:0.88rem;">
                                                    <?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                                </div>
                                                <div style="font-size:0.75rem;color:#999;"><?= esc($emp['email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark"><?= esc($emp['employee_code']) ?></span></td>
                                    <td><?= esc($emp['department_name'] ?? '—') ?></td>
                                    <td><?= esc($emp['position'] ?? '—') ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($emp['status']) ?>">
                                            <?= esc($emp['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">No employees found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Breakdown -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-3">
                <span><i class="bi bi-diagram-3-fill me-2 text-primary"></i>Employees by Department</span>
            </div>
            <div class="card-body">
                <?php if (!empty($departments)): ?>
                    <?php
                        $total = array_sum(array_column($departments, 'emp_count'));
                    ?>
                    <?php foreach ($departments as $dept): ?>
                        <?php $pct = $total > 0 ? round(($dept['emp_count'] / $total) * 100) : 0; ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:0.85rem;font-weight:600;"><?= esc($dept['name']) ?></span>
                                <span style="font-size:0.8rem;color:#999;"><?= $dept['emp_count'] ?> (<?= $pct ?>%)</span>
                            </div>
                            <div class="progress" style="height:8px;border-radius:10px;">
                                <div class="progress-bar" style="width:<?= $pct ?>%;background:linear-gradient(90deg,#1e3a5f,#2e5090);border-radius:10px;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-4">No departments yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- RECENT LEAVE REQUESTS -->
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-calendar2-x-fill me-2 text-primary"></i>Recent Leave Requests</span>
                <a href="/admin/leaves" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentLeaves)): ?>
                                <?php foreach ($recentLeaves as $leave): ?>
                                <tr>
                                    <td>
                                        <span style="font-weight:600;">
                                            <?= esc($leave['first_name'] . ' ' . $leave['last_name']) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($leave['leave_type_name']) ?></td>
                                    <td><?= date('M d, Y', strtotime($leave['start_date'])) ?></td>
                                    <td><?= date('M d, Y', strtotime($leave['end_date'])) ?></td>
                                    <td><?= $leave['total_days'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($leave['status']) ?>">
                                            <?= esc($leave['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/admin/leaves/view/<?= $leave['id'] ?>"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">No leave requests yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ATTENDANCE THRESHOLD ALERTS -->
<?php if (!empty($absentViolators) || !empty($lateViolators)): ?>
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card border-0" style="border-left: 4px solid #e74c3c !important;">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                    <strong>Attendance Threshold Alerts</strong>
                    <small class="text-muted ms-2"><?= date('F Y', strtotime($monthStart)) ?></small>
                </span>
                <a href="/admin/attendance/threshold" class="btn btn-sm btn-outline-danger">
                    Manage Thresholds
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <!-- Absent Violators -->
                    <?php if (!empty($absentViolators)): ?>
                    <div class="col-lg-6">
                        <div class="p-3 rounded" style="background:#fdf2f2;">
                            <h6 class="fw-bold text-danger mb-3">
                                <i class="bi bi-calendar-x-fill me-2"></i>
                                Excessive Absences
                                <span class="badge bg-danger ms-1"><?= count($absentViolators) ?></span>
                                <small class="text-muted fw-normal ms-1">(≥ <?= $absentThreshold['max_allowed'] ?> absences)</small>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Department</th>
                                            <th class="text-center">Absences</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($absentViolators as $v): ?>
                                        <tr>
                                            <td>
                                                <strong style="font-size:0.85rem;">
                                                    <?= esc($v['first_name'] . ' ' . $v['last_name']) ?>
                                                </strong><br>
                                                <small class="text-muted"><?= esc($v['employee_code']) ?></small>
                                            </td>
                                            <td><small><?= esc($v['department_name'] ?? '—') ?></small></td>
                                            <td class="text-center">
                                                <span class="badge bg-danger"><?= $v['count'] ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Late Violators -->
                    <?php if (!empty($lateViolators)): ?>
                    <div class="col-lg-6">
                        <div class="p-3 rounded" style="background:#fffbf0;">
                            <h6 class="fw-bold text-warning mb-3">
                                <i class="bi bi-clock-fill me-2"></i>
                                Excessive Lates
                                <span class="badge bg-warning text-dark ms-1"><?= count($lateViolators) ?></span>
                                <small class="text-muted fw-normal ms-1">(≥ <?= $lateThreshold['max_allowed'] ?> lates)</small>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Department</th>
                                            <th class="text-center">Lates</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lateViolators as $v): ?>
                                        <tr>
                                            <td>
                                                <strong style="font-size:0.85rem;">
                                                    <?= esc($v['first_name'] . ' ' . $v['last_name']) ?>
                                                </strong><br>
                                                <small class="text-muted"><?= esc($v['employee_code']) ?></small>
                                            </td>
                                            <td><small><?= esc($v['department_name'] ?? '—') ?></small></td>
                                            <td class="text-center">
                                                <span class="badge bg-warning text-dark"><?= $v['count'] ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>