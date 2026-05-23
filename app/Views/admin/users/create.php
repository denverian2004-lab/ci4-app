<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="max-width:600px;">
    <div class="card-header py-3">
        <i class="bi bi-person-plus-fill me-2 text-primary"></i>Add User Account
    </div>
    <div class="card-body">
        <form action="/admin/users/store" method="POST">
            <?= csrf_field() ?>
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label fw-semibold">Link to Employee <small class="text-muted">(optional)</small></label>
                    <select name="employee_id" class="form-select">
                        <option value="">-- No linked employee --</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= old('employee_id') == $emp['id'] ? 'selected' : '' ?>>
                                <?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?> (<?= esc($emp['employee_code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control"
                           value="<?= old('username') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="employee" <?= old('role') === 'employee' ? 'selected' : '' ?>>Employee</option>
                        <option value="manager"  <?= old('role') === 'manager'  ? 'selected' : '' ?>>Manager</option>
                        <option value="admin"    <?= old('role') === 'admin'    ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Min. 6 characters" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" id="confirmPass" class="form-control"
                           placeholder="Re-enter password">
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Create Account
                    </button>
                    <a href="/admin/users" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>