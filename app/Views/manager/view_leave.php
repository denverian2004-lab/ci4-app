<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-3">

    <!-- Leave Details -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-text-fill me-2 text-primary"></i>Leave Request Details</span>
                <a href="/manager/team-leaves" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Employee</small>
                        <strong><?= esc($leave['first_name'] . ' ' . $leave['last_name']) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Employee Code</small>
                        <strong><?= esc($leave['employee_code']) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Position</small>
                        <strong><?= esc($leave['position'] ?? '—') ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Email</small>
                        <strong><?= esc($leave['email']) ?></strong>
                    </div>
                    <div class="col-12"><hr class="my-1"></div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Leave Type</small>
                        <strong><?= esc($leave['leave_type_name']) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Total Days</small>
                        <strong><?= $leave['total_days'] ?> day(s)</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Start Date</small>
                        <strong><?= date('F d, Y', strtotime($leave['start_date'])) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">End Date</small>
                        <strong><?= date('F d, Y', strtotime($leave['end_date'])) ?></strong>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Reason</small>
                        <div class="p-3 bg-light rounded mt-1">
                            <?= esc($leave['reason'] ?? 'No reason provided.') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Filed On</small>
                        <strong><?= date('F d, Y', strtotime($leave['created_at'])) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Current Status</small>
                        <span class="badge badge-<?= strtolower($leave['status']) ?> px-3 py-2">
                            <?= esc($leave['status']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Panel -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-gear-fill me-2 text-primary"></i>Actions
            </div>
            <div class="card-body">
                <?php if ($leave['status'] === 'Pending'): ?>
                    <p class="text-muted small mb-3">This leave request is awaiting your decision.</p>

                    <form action="/manager/team-leaves/approve/<?= $leave['id'] ?>" method="POST" class="mb-2">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Approve this leave request?')">
                            <i class="bi bi-check-circle-fill me-2"></i>Approve
                        </button>
                    </form>

                    <form action="/manager/team-leaves/reject/<?= $leave['id'] ?>" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger w-100"
                                onclick="return confirm('Reject this leave request?')">
                            <i class="bi bi-x-circle-fill me-2"></i>Reject
                        </button>
                    </form>

                <?php elseif ($leave['status'] === 'Approved'): ?>
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:2.5rem;"></i>
                        <p class="mt-2 fw-semibold text-success">This leave has been approved.</p>
                    </div>

                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size:2.5rem;"></i>
                        <p class="mt-2 fw-semibold text-danger">This leave has been rejected.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>