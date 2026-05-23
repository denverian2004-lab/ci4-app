<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <span><i class="bi bi-calendar2-x-fill me-2 text-primary"></i>Leave Requests</span>

        <!-- Status Filter -->
        <div class="d-flex gap-2 flex-wrap">
            <a href="/admin/leaves" class="btn btn-sm <?= $status === '' ? 'btn-primary' : 'btn-outline-secondary' ?>">All</a>
            <a href="/admin/leaves?status=Pending"  class="btn btn-sm <?= $status === 'Pending'  ? 'btn-warning'        : 'btn-outline-warning' ?>">Pending</a>
            <a href="/admin/leaves?status=Approved" class="btn btn-sm <?= $status === 'Approved' ? 'btn-success'        : 'btn-outline-success' ?>">Approved</a>
            <a href="/admin/leaves?status=Rejected" class="btn btn-sm <?= $status === 'Rejected' ? 'btn-danger'         : 'btn-outline-danger'  ?>">Rejected</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days</th>
                        <th>Filed On</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($leaves)): ?>
                        <?php foreach ($leaves as $i => $leave): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <div style="font-weight:600;"><?= esc($leave['first_name'] . ' ' . $leave['last_name']) ?></div>
                                <small class="text-muted"><?= esc($leave['employee_code']) ?></small>
                            </td>
                            <td><?= esc($leave['leave_type_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($leave['start_date'])) ?></td>
                            <td><?= date('M d, Y', strtotime($leave['end_date'])) ?></td>
                            <td><span class="badge bg-light text-dark"><?= $leave['total_days'] ?> day(s)</span></td>
                            <td><?= date('M d, Y', strtotime($leave['created_at'])) ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower($leave['status']) ?>">
                                    <?= esc($leave['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/admin/leaves/view/<?= $leave['id'] ?>"
                                   class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No leave requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>