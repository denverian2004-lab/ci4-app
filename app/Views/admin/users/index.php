<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span><i class="bi bi-person-gear me-2 text-primary"></i>User Accounts</span>
        <a href="/admin/users/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Add User
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Linked Employee</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $i => $user): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                         style="width:34px;height:34px;font-size:0.8rem;font-weight:600;">
                                        <?= strtoupper(substr($user['username'], 0, 2)) ?>
                                    </div>
                                    <strong><?= esc($user['username']) ?></strong>
                                    <?php if ($user['id'] == session()->get('user_id')): ?>
                                        <span class="badge bg-info" style="font-size:0.65rem;">You</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($user['first_name']): ?>
                                    <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                    <small class="text-muted d-block"><?= esc($user['employee_code']) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $roleColors = [
                                        'admin'    => 'danger',
                                        'manager'  => 'warning',
                                        'employee' => 'primary',
                                    ];
                                    $roleColor = $roleColors[$user['role']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $roleColor ?>">
                                    <?= ucfirst(esc($user['role'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/admin/users/edit/<?= $user['id'] ?>"
                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ($user['id'] != session()->get('user_id')): ?>
                                <a href="/admin/users/delete/<?= $user['id'] ?>"
                                   class="btn btn-sm btn-outline-danger" title="Delete"
                                   onclick="return confirm('Delete this user account?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>