<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <span>
            <i class="bi bi-calendar-check-fill me-2 text-primary"></i>
            Team Attendance — <?= esc($manager['department_name']) ?>
        </span>
        <form method="GET" action="/manager/team-attendance" class="d-flex gap-2">
            <input type="date" name="date" class="form-control form-control-sm" value="<?= esc($date) ?>">
            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-search"></i> Filter
            </button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($records)): ?>
                        <?php foreach ($records as $i => $r): ?>
                        <?php
                            $badgeMap = [
                                'Present'  => 'success',
                                'Absent'   => 'danger',
                                'Late'     => 'warning',
                                'Half-day' => 'info',
                            ];
                            $badge = $badgeMap[$r['status']] ?? 'secondary';
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></strong><br>
                                <small class="text-muted"><?= esc($r['employee_code']) ?></small>
                            </td>
                            <td><?= date('M d, Y', strtotime($r['date'])) ?></td>
                            <td><?= $r['time_in']  ? date('h:i A', strtotime($r['time_in']))  : '—' ?></td>
                            <td><?= $r['time_out'] ? date('h:i A', strtotime($r['time_out'])) : '—' ?></td>
                            <td><span class="badge bg-<?= $badge ?>"><?= esc($r['status']) ?></span></td>
                            <td><?= esc($r['remarks'] ?? '—') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
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