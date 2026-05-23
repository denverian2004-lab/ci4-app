<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-3 justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-person-fill me-2 text-primary"></i>My Profile
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width:80px;height:80px;font-size:1.8rem;font-weight:700;">
                        <?= strtoupper(substr($employee['first_name'],0,1) . substr($employee['last_name'],0,1)) ?>
                    </div>
                    <h5 class="fw-bold mb-1"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></h5>
                    <p class="text-muted"><?= esc($employee['position'] ?? 'Employee') ?></p>
                    <span class="badge badge-<?= strtolower($employee['status']) ?> px-3 py-2">
                        <?= esc($employee['status']) ?>
                    </span>
                </div>

                <hr>

                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Employee Code</small>
                        <strong><?= esc($employee['employee_code']) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Department</small>
                        <strong><?= esc($employee['department_name'] ?? '—') ?></strong>
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
                        <small class="text-muted d-block">Date Hired</small>
                        <strong><?= date('F d, Y', strtotime($employee['date_hired'])) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Employment Type</small>
                        <strong><?= esc($employee['employment_type']) ?></strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Basic Salary</small>
                        <strong>₱ <?= number_format($employee['basic_salary'], 2) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>