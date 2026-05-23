<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <span>
            <i class="bi bi-calendar2-x-fill me-2 text-primary"></i>
            Team Leave Requests — <?= esc($manager['department_name']) ?>
        </span>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/manager/team-leaves"                  class="btn btn-sm <?= $status === ''         ? 'btn-primary'        : 'btn-outline-secondary' ?>">All</a>
            <a href="/manager/team-leaves?status=Pending"   class="btn btn-sm <?= $status === 'Pending'  ? 'btn-warning'        : 'btn-outline-warning'   ?>">Pending</a>
            <a href="/manager/team-leaves?status=Approved"  class="btn btn-sm <?= $status === 'Approved' ? 'btn-success'        : 'btn-outline-success'   ?>">Approved</a>
            <a href="/manager/team-leaves?status=Rejected"  class="btn btn-sm <?= $status === 'Rejected' ? 'btn-danger'         : 'btn-outline-danger'    ?>">Rejected</a>
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
                                <strong><?= esc($leave['first_name'] . ' ' . $leave['last_name']) ?></strong><br>
                                <small class="text-muted"><?= esc($leave['employee_code']) ?></small>
                            </td>
                            <td><?= esc($leave['leave_type_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($leave['start_date'])) ?></td>
                            <td><?= date('M d, Y', strtotime($leave['end_date'])) ?></td>
                            <td><?= $leave['total_days'] ?> day(s)</td>
                            <td><?= date('M d, Y', strtotime($leave['created_at'])) ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower($leave['status']) ?>">
                                    <?= esc($leave['status']) ?>
                                </span>
                            </td>
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
                            <td colspan="9" class="text-center text-muted py-4">No leave requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>