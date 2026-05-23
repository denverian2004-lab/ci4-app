<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-pencil-square me-2 text-warning"></i>Edit Employee — <?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?>
    </div>
    <div class="card-body">
        <form action="/admin/employees/update/<?= $employee['id'] ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-12"><h6 class="text-muted fw-bold border-bottom pb-2">Personal Information</h6></div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="<?= esc($employee['first_name']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" value="<?= esc($employee['middle_name']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="<?= esc($employee['last_name']) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="Male"   <?= $employee['gender'] === 'Male'   ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $employee['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other"  <?= $employee['gender'] === 'Other'  ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Birthdate <span class="text-danger">*</span></label>
                    <input type="date" name="birthdate" class="form-control" value="<?= esc($employee['birthdate']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= esc($employee['phone']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= esc($employee['email']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" name="address" class="form-control" value="<?= esc($employee['address']) ?>">
                </div>

                <!-- Profile Photo -->
                <div class="col-12 mt-2">
                    <h6 class="text-muted fw-bold border-bottom pb-2">Profile Photo</h6>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Upload Photo <small class="text-muted">(JPG/PNG, max 2MB)</small></label>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Current Photo Preview -->
                        <div id="photoPreview"
                            style="width:80px;height:80px;flex-shrink:0;border-radius:50%;overflow:hidden;">
                            <?php if ($employee['profile_photo']): ?>
                                <img src="<?= base_url('uploads/profiles/' . $employee['profile_photo']) ?>"
                                    style="width:80px;height:80px;object-fit:cover;" id="currentPhoto">
                            <?php else: ?>
                                <div class="bg-primary text-white d-flex align-items-center justify-content-center"
                                    style="width:80px;height:80px;font-size:1.5rem;font-weight:700;" id="currentPhoto">
                                    <?= strtoupper(substr($employee['first_name'],0,1) . substr($employee['last_name'],0,1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex-fill">
                            <input type="file" name="profile_photo" id="photoInput"
                                class="form-control mb-2" accept="image/jpg,image/jpeg,image/png">
                            <?php if ($employee['profile_photo']): ?>
                                <div class="form-check">
                                    <input type="checkbox" name="remove_photo" value="1"
                                        class="form-check-input" id="removePhoto">
                                    <label class="form-check-label text-danger small" for="removePhoto">
                                        Remove current photo
                                    </label>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted d-block mt-1">Leave empty to keep current photo</small>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-2"><h6 class="text-muted fw-bold border-bottom pb-2">Employment Information</h6></div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">-- Select Department --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= $employee['department_id'] == $dept['id'] ? 'selected' : '' ?>>
                                <?= esc($dept['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Position</label>
                    <input type="text" name="position" class="form-control" value="<?= esc($employee['position']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Employment Type</label>
                    <select name="employment_type" class="form-select">
                        <option value="Full-time"   <?= $employee['employment_type'] === 'Full-time'   ? 'selected' : '' ?>>Full-time</option>
                        <option value="Part-time"   <?= $employee['employment_type'] === 'Part-time'   ? 'selected' : '' ?>>Part-time</option>
                        <option value="Contractual" <?= $employee['employment_type'] === 'Contractual' ? 'selected' : '' ?>>Contractual</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Date Hired <span class="text-danger">*</span></label>
                    <input type="date" name="date_hired" class="form-control" value="<?= esc($employee['date_hired']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Basic Salary <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" name="basic_salary" step="0.01" class="form-control" value="<?= esc($employee['basic_salary']) ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="Active"      <?= $employee['status'] === 'Active'      ? 'selected' : '' ?>>Active</option>
                        <option value="Inactive"    <?= $employee['status'] === 'Inactive'    ? 'selected' : '' ?>>Inactive</option>
                        <option value="Resigned"    <?= $employee['status'] === 'Resigned'    ? 'selected' : '' ?>>Resigned</option>
                        <option value="Terminated"  <?= $employee['status'] === 'Terminated'  ? 'selected' : '' ?>>Terminated</option>
                    </select>
                </div>

                <div class="col-12 mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save me-1"></i> Update Employee
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
        const file    = this.files[0];
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