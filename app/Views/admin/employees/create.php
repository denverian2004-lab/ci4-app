<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-person-plus-fill me-2 text-primary"></i>Add New Employee
    </div>
    <div class="card-body">
        <form action="/admin/employees/store" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row g-3">
                <!-- Personal Info -->
                <div class="col-12"><h6 class="text-muted fw-bold border-bottom pb-2">Personal Information</h6></div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="<?= old('first_name') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" value="<?= old('middle_name') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="<?= old('last_name') ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="">-- Select --</option>
                        <option value="Male"   <?= old('gender') === 'Male'   ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= old('gender') === 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other"  <?= old('gender') === 'Other'  ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Birthdate <span class="text-danger">*</span></label>
                    <input type="date" name="birthdate" class="form-control" value="<?= old('birthdate') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" name="address" class="form-control" value="<?= old('address') ?>">
                </div>

                <!-- Profile Photo -->
                <div class="col-12 mt-2">
                    <h6 class="text-muted fw-bold border-bottom pb-2">Profile Photo</h6>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Upload Photo <small class="text-muted">(JPG/PNG, max 2MB)</small></label>
                    <div class="d-flex align-items-center gap-3">
                        <div id="photoPreview"
                            class="rounded-circle bg-light border d-flex align-items-center justify-content-center"
                            style="width:80px;height:80px;flex-shrink:0;overflow:hidden;">
                            <i class="bi bi-person-fill text-secondary" style="font-size:2.5rem;"></i>
                        </div>
                        <div class="flex-fill">
                            <input type="file" name="profile_photo" id="photoInput"
                                class="form-control" accept="image/jpg,image/jpeg,image/png">
                            <small class="text-muted">Leave empty to use initials avatar</small>
                        </div>
                    </div>
                </div>

                <!-- Employment Info -->
                <div class="col-12 mt-2"><h6 class="text-muted fw-bold border-bottom pb-2">Employment Information</h6></div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">-- Select Department --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= old('department_id') == $dept['id'] ? 'selected' : '' ?>>
                                <?= esc($dept['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Position</label>
                    <input type="text" name="position" class="form-control" value="<?= old('position') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Employment Type <span class="text-danger">*</span></label>
                    <select name="employment_type" class="form-select" required>
                        <option value="Full-time"    <?= old('employment_type') === 'Full-time'    ? 'selected' : '' ?>>Full-time</option>
                        <option value="Part-time"    <?= old('employment_type') === 'Part-time'    ? 'selected' : '' ?>>Part-time</option>
                        <option value="Contractual"  <?= old('employment_type') === 'Contractual'  ? 'selected' : '' ?>>Contractual</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Date Hired <span class="text-danger">*</span></label>
                    <input type="date" name="date_hired" class="form-control" value="<?= old('date_hired') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Basic Salary <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" name="basic_salary" step="0.01" class="form-control" value="<?= old('basic_salary') ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-12 mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Employee
                    </button>
                    <a href="/admin/employees" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('photoInput').addEventListener('change', function() {
        const file   = this.files[0];
        const preview = document.getElementById('photoPreview');
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.innerHTML = `<img src="${e.target.result}"
                    style="width:80px;height:80px;border-radius:50%;object-fit:cover;">`;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>