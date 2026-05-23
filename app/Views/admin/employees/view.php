<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-3">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card text-center">
            <div class="card-body py-4">
                <!-- Profile Photo -->
                <?php if (!empty($employee['profile_photo'])): ?>
                    <img src="<?= base_url('uploads/profiles/' . $employee['profile_photo']) ?>"
                        alt="Profile Photo"
                        class="rounded-circle mx-auto d-block mb-3"
                        style="width:80px;height:80px;object-fit:cover;">
                <?php else: ?>
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                        style="width:80px;height:80px;font-size:1.8rem;font-weight:700;">
                        <?= strtoupper(substr($employee['first_name'],0,1) . substr($employee['last_name'],0,1)) ?>
                    </div>
                <?php endif; ?>
                <h5 class="fw-bold mb-1"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></h5>
                <p class="text-muted mb-2"><?= esc($employee['position'] ?? 'No Position') ?></p>
                <span class="badge badge-<?= strtolower($employee['status']) ?> px-3 py-2">
                    <?= esc($employee['status']) ?>
                </span>
                <hr>
                <div class="text-start">
                    <div class="mb-2">
                        <small class="text-muted d-block">Employee Code</small>
                        <strong><?= esc($employee['employee_code']) ?></strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Department</small>
                        <strong><?= esc($employee['department_name'] ?? '—') ?></strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Employment Type</small>
                        <strong><?= esc($employee['employment_type']) ?></strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Date Hired</small>
                        <strong><?= date('F d, Y', strtotime($employee['date_hired'])) ?></strong>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2 justify-content-center">
                    <a href="/admin/employees/edit/<?= $employee['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="/admin/employees" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Card -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-person-lines-fill me-2 text-primary"></i>Employee Details
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Full Name</small>
                        <strong><?= esc($employee['first_name'] . ' ' . ($employee['middle_name'] ? $employee['middle_name'] . ' ' : '') . $employee['last_name']) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Gender</small>
                        <strong><?= esc($employee['gender']) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Birthdate</small>
                        <strong><?= date('F d, Y', strtotime($employee['birthdate'])) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Email</small>
                        <strong><?= esc($employee['email']) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Phone</small>
                        <strong><?= esc($employee['phone'] ?? '—') ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Address</small>
                        <strong><?= esc($employee['address'] ?? '—') ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Basic Salary</small>
                        <strong>₱ <?= number_format($employee['basic_salary'], 2) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Date Hired</small>
                        <strong><?= date('F d, Y', strtotime($employee['date_hired'])) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>