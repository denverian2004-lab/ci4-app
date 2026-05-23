<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span><i class="bi bi-calendar2-x-fill me-2 text-primary"></i>My Leave Requests</span>
        <a href="/employee/my-leaves/apply" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Apply for Leave
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($leaves)): ?>
                        <?php foreach ($leaves as $i => $leave): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($leave['leave_type_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($leave['start_date'])) ?></td>
                            <td><?= date('M d, Y', strtotime($leave['end_date'])) ?></td>
                            <td><?= $leave['total_days'] ?> day(s)</td>
                            <td><?= esc($leave['reason'] ?? '—') ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower($leave['status']) ?>">
                                    <?= esc($leave['status']) ?>
                                </span>
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

<?= $this->endSection() ?>