<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="max-width:500px;">
    <div class="card-header py-3">
        <i class="bi bi-key-fill me-2 text-primary"></i>Change Password
    </div>
    <div class="card-body">
        <form action="/employee/change-password/update" method="POST">
            <?= csrf_field() ?>
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="current_password"
                               id="currentPass" class="form-control"
                               placeholder="Enter current password" required>
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePass('currentPass', 'eye1')">
                            <i class="bi bi-eye" id="eye1"></i>
                        </button>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="new_password"
                               id="newPass" class="form-control"
                               placeholder="Min. 6 characters" required
                               oninput="checkStrength(this.value)">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePass('newPass', 'eye2')">
                            <i class="bi bi-eye" id="eye2"></i>
                        </button>
                    </div>
                    <!-- Password Strength -->
                    <div class="mt-2" id="strengthBar" style="display:none;">
                        <div class="progress" style="height:6px;border-radius:10px;">
                            <div class="progress-bar" id="strengthProgress"
                                 style="border-radius:10px;transition:all 0.3s;"></div>
                        </div>
                        <small id="strengthText" class="mt-1 d-block"></small>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="confirm_password"
                               id="confirmPass" class="form-control"
                               placeholder="Re-enter new password" required
                               oninput="checkMatch()">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePass('confirmPass', 'eye3')">
                            <i class="bi bi-eye" id="eye3"></i>
                        </button>
                    </div>
                    <small id="matchText" class="mt-1 d-block"></small>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Update Password
                    </button>
                    <a href="/employee/dashboard" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    function checkStrength(value) {
        const bar      = document.getElementById('strengthBar');
        const progress = document.getElementById('strengthProgress');
        const text     = document.getElementById('strengthText');

        bar.style.display = value.length > 0 ? 'block' : 'none';

        let strength = 0;
        if (value.length >= 6)                        strength++;
        if (value.length >= 10)                       strength++;
        if (/[A-Z]/.test(value))                      strength++;
        if (/[0-9]/.test(value))                      strength++;
        if (/[^A-Za-z0-9]/.test(value))              strength++;

        const levels = [
            { width: '20%',  color: '#e74c3c', label: 'Very Weak',  textClass: 'text-danger'  },
            { width: '40%',  color: '#e67e22', label: 'Weak',       textClass: 'text-warning' },
            { width: '60%',  color: '#f1c40f', label: 'Fair',       textClass: 'text-warning' },
            { width: '80%',  color: '#2ecc71', label: 'Strong',     textClass: 'text-success' },
            { width: '100%', color: '#27ae60', label: 'Very Strong', textClass: 'text-success' },
        ];

        const level = levels[Math.min(strength, 4)];
        progress.style.width            = level.width;
        progress.style.backgroundColor  = level.color;
        text.textContent                = level.label;
        text.className                  = `mt-1 d-block ${level.textClass}`;
    }

    function checkMatch() {
        const newPass     = document.getElementById('newPass').value;
        const confirmPass = document.getElementById('confirmPass').value;
        const text        = document.getElementById('matchText');

        if (confirmPass.length === 0) {
            text.textContent = '';
            return;
        }

        if (newPass === confirmPass) {
            text.textContent  = '✓ Passwords match';
            text.className    = 'mt-1 d-block text-success';
        } else {
            text.textContent  = '✗ Passwords do not match';
            text.className    = 'mt-1 d-block text-danger';
        }
    }
</script>
<?= $this->endSection() ?>