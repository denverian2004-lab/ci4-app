<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <span><i class="bi bi-calendar-check-fill me-2 text-primary"></i>Attendance Log</span>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <!-- Date Filter -->
            <form method="GET" action="/admin/attendance" class="d-flex gap-2">
                <input type="date" name="date" class="form-control form-control-sm" value="<?= esc($date) ?>">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
            </form>
            <a href="/admin/attendance/create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Log Attendance
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Code</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($attendance)): ?>
                        <?php foreach ($attendance as $i => $att): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <span style="font-weight:600;">
                                    <?= esc($att['first_name'] . ' ' . $att['last_name']) ?>
                                </span>
                            </td>
                            <td><span class="badge bg-light text-dark"><?= esc($att['employee_code']) ?></span></td>
                            <td><?= date('M d, Y', strtotime($att['date'])) ?></td>
                            <td><?= $att['time_in'] ? date('h:i A', strtotime($att['time_in'])) : '—' ?></td>
                            <td><?= $att['time_out'] ? date('h:i A', strtotime($att['time_out'])) : '—' ?></td>
                            <td>
                                <?php
                                    $badgeMap = [
                                        'Present'  => 'success',
                                        'Absent'   => 'danger',
                                        'Late'     => 'warning',
                                        'Half-day' => 'info',
                                    ];
                                    $badge = $badgeMap[$att['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= esc($att['status']) ?></span>
                            </td>
                            <td><?= esc($att['remarks'] ?? '—') ?></td>
                            <td>
                                <a href="/admin/attendance/edit/<?= $att['id'] ?>"
                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/admin/attendance/delete/<?= $att['id'] ?>"
                                   class="btn btn-sm btn-outline-danger" title="Delete"
                                   onclick="return confirm('Delete this attendance record?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                No attendance records for <?= date('F d, Y', strtotime($date)) ?>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>