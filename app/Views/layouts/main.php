<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Employee Management System' ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    :root {
        --sidebar-width: 260px;
        --primary:       #1e3a5f;
        --primary-light: #2e5090;
        --accent:        #f0a500;
    }

    body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }

    /* SIDEBAR */
    #sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        background: var(--primary);
        position: fixed;
        top: 0; left: 0;
        z-index: 1000;
        transition: all 0.3s;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.2) transparent;
    }

    #sidebar::-webkit-scrollbar {
        width: 4px;
    }

    #sidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    #sidebar::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
    }

    #sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255,255,255,0.4);
    }

    #sidebar .sidebar-brand {
        padding: 20px 16px;
        background: rgba(0,0,0,0.2);
        color: #fff;
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #sidebar .sidebar-brand i { font-size: 1.5rem; color: var(--accent); }

    #sidebar .nav-label {
        font-size: 0.7rem;
        color: rgba(255,255,255,0.4);
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 16px 16px 4px;
    }

    #sidebar .nav-link {
        color: rgba(255,255,255,0.75);
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
        border-left: 3px solid transparent;
        transition: all 0.2s;
    }

    #sidebar .nav-link:hover,
    #sidebar .nav-link.active {
        color: #fff;
        background: rgba(255,255,255,0.1);
        border-left-color: var(--accent);
    }

    #sidebar .nav-link i { font-size: 1.1rem; width: 20px; }

    /* MAIN CONTENT */
    #main-content {
        margin-left: var(--sidebar-width);
        min-height: 100vh;
        transition: all 0.3s;
    }

    /* TOPBAR */
    #topbar {
        background: #fff;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        position: sticky;
        top: 0;
        z-index: 999;
    }

    #topbar .page-title { font-weight: 600; font-size: 1.1rem; color: var(--primary); }

    /* CARDS */
    .stat-card {
        border: none;
        border-radius: 12px;
        padding: 20px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .stat-card .stat-icon {
        font-size: 3rem;
        opacity: 0.2;
        position: absolute;
        right: 16px;
        bottom: 10px;
    }

    .stat-card .stat-number { font-size: 2rem; font-weight: 700; }
    .stat-card .stat-label  { font-size: 0.85rem; opacity: 0.85; }

    .bg-stat-1 { background: linear-gradient(135deg, #1e3a5f, #2e5090); }
    .bg-stat-2 { background: linear-gradient(135deg, #27ae60, #2ecc71); }
    .bg-stat-3 { background: linear-gradient(135deg, #e67e22, #f39c12); }
    .bg-stat-4 { background: linear-gradient(135deg, #8e44ad, #9b59b6); }

    .card { border: none; border-radius: 12px; box-shadow: 0 1px 6px rgba(0,0,0,0.06); }
    .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; font-weight: 600; }

    /* BADGE STATUS */
    .badge-active    { background: #d4edda; color: #155724; }
    .badge-inactive  { background: #f8d7da; color: #721c24; }
    .badge-pending   { background: #fff3cd; color: #856404; }
    .badge-approved  { background: #d4edda; color: #155724; }
    .badge-rejected  { background: #f8d7da; color: #721c24; }

    /* TABLE */
    .table thead th {
        background: #f8f9fa;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-bottom: 2px solid #e9ecef;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        #sidebar { left: calc(-1 * var(--sidebar-width)); }
        #sidebar.show { left: 0; }
        #main-content { margin-left: 0; }
    }
</style>
</head>
<body>

<!-- SIDEBAR -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-building-fill"></i>
        <span>EMS Portal</span>
    </div>

    <?php $role = session()->get('role'); ?>

    <?php if ($role === 'admin'): ?>

        <div class="nav-label">Main</div>
        <a href="/admin/dashboard" class="nav-link <?= (uri_string() === 'admin/dashboard') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-label">HR Management</div>
        <a href="/admin/employees" class="nav-link <?= str_contains(uri_string(), 'employees') ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> Employees
        </a>
        <a href="/admin/departments" class="nav-link <?= str_contains(uri_string(), 'departments') ? 'active' : '' ?>">
            <i class="bi bi-diagram-3-fill"></i> Departments
        </a>
        <a href="/admin/attendance" class="nav-link <?= str_contains(uri_string(), 'attendance') ? 'active' : '' ?>">
            <i class="bi bi-calendar-check-fill"></i> Attendance
        </a>
        <a href="/admin/attendance/threshold" class="nav-link <?= str_contains(uri_string(), 'threshold') ? 'active' : '' ?>">
            <i class="bi bi-sliders"></i> Thresholds
        </a>
        <a href="/admin/leaves" class="nav-link <?= str_contains(uri_string(), 'leaves') ? 'active' : '' ?>">
            <i class="bi bi-calendar2-x-fill"></i> Leave Requests
        </a>

        <div class="nav-label">Finance</div>
        <a href="/admin/payroll" class="nav-link <?= str_contains(uri_string(), 'payroll') ? 'active' : '' ?>">
            <i class="bi bi-cash-stack"></i> Payroll
        </a>

        <div class="nav-label">Performance</div>
        <a href="/admin/evaluations" class="nav-link <?= str_contains(uri_string(), 'evaluations') ? 'active' : '' ?>">
            <i class="bi bi-star-fill"></i> Evaluations
        </a>

        <div class="nav-label">Reports</div>
        <a href="/admin/reports" class="nav-link <?= str_contains(uri_string(), 'reports') ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-fill"></i> Reports
        </a>

        <div class="nav-label">System</div>
        <a href="/admin/users" class="nav-link <?= str_contains(uri_string(), 'users') ? 'active' : '' ?>">
            <i class="bi bi-person-gear"></i> User Accounts
        </a>

        <div class="nav-label">Account</div>
        <a href="/admin/change-password" class="nav-link <?= str_contains(uri_string(), 'change-password') ? 'active' : '' ?>">
            <i class="bi bi-key-fill"></i> Change Password
        </a>
        <a href="/logout" class="nav-link text-danger">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>

    <?php elseif ($role === 'manager'): ?>

        <div class="nav-label">Manager Portal</div>
        <a href="/manager/dashboard" class="nav-link <?= (uri_string() === 'manager/dashboard') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-label">My Team</div>
        <a href="/manager/team-attendance" class="nav-link <?= str_contains(uri_string(), 'team-attendance') ? 'active' : '' ?>">
            <i class="bi bi-calendar-check-fill"></i> Team Attendance
        </a>
        <a href="/manager/team-leaves" class="nav-link <?= str_contains(uri_string(), 'team-leaves') ? 'active' : '' ?>">
            <i class="bi bi-calendar2-x-fill"></i> Team Leave Requests
        </a>

        <div class="nav-label">My Self-Service</div>
        <a href="/employee/profile" class="nav-link <?= str_contains(uri_string(), 'profile') ? 'active' : '' ?>">
            <i class="bi bi-person-fill"></i> My Profile
        </a>
        <a href="/employee/my-attendance" class="nav-link <?= str_contains(uri_string(), 'my-attendance') ? 'active' : '' ?>">
            <i class="bi bi-calendar-check-fill"></i> My Attendance
        </a>
        <a href="/employee/my-leaves" class="nav-link <?= str_contains(uri_string(), 'my-leaves') ? 'active' : '' ?>">
            <i class="bi bi-calendar2-x-fill"></i> My Leaves
        </a>
        <a href="/employee/my-payroll" class="nav-link <?= str_contains(uri_string(), 'my-payroll') ? 'active' : '' ?>">
            <i class="bi bi-cash-stack"></i> My Payroll
        </a>

        <div class="nav-label">Account</div>
        <a href="/employee/change-password" class="nav-link <?= str_contains(uri_string(), 'change-password') ? 'active' : '' ?>">
            <i class="bi bi-key-fill"></i> Change Password
        </a>
        <a href="/logout" class="nav-link text-danger">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>

    <?php else: ?>

        <div class="nav-label">My Portal</div>
        <a href="/employee/dashboard" class="nav-link <?= (uri_string() === 'employee/dashboard') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="/employee/profile" class="nav-link <?= str_contains(uri_string(), 'profile') ? 'active' : '' ?>">
            <i class="bi bi-person-fill"></i> My Profile
        </a>
        <a href="/employee/my-attendance" class="nav-link <?= str_contains(uri_string(), 'my-attendance') ? 'active' : '' ?>">
            <i class="bi bi-calendar-check-fill"></i> My Attendance
        </a>
        <a href="/employee/my-leaves" class="nav-link <?= str_contains(uri_string(), 'my-leaves') ? 'active' : '' ?>">
            <i class="bi bi-calendar2-x-fill"></i> My Leaves
        </a>
        <a href="/employee/my-payroll" class="nav-link <?= str_contains(uri_string(), 'my-payroll') ? 'active' : '' ?>">
            <i class="bi bi-cash-stack"></i> My Payroll
        </a>

        <div class="nav-label">Account</div>
        <a href="/employee/change-password" class="nav-link <?= str_contains(uri_string(), 'change-password') ? 'active' : '' ?>">
            <i class="bi bi-key-fill"></i> Change Password
        </a>
        <a href="/logout" class="nav-link text-danger">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>

    <?php endif; ?>

</nav>

<!-- MAIN CONTENT -->
<div id="main-content">

    <!-- TOPBAR -->
<div id="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-light d-md-none" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="page-title"><?= $title ?? 'Dashboard' ?></span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted small d-none d-md-block">
            <i class="bi bi-calendar3"></i>
            <?= date('F d, Y') ?>
        </span>

        <!-- NOTIFICATION BELL -->
        <div class="dropdown" id="notifDropdown">
            <button class="btn btn-light btn-sm position-relative" id="notifBtn"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false" onclick="loadNotifications()">
                <i class="bi bi-bell-fill"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                      id="notifBadge" style="display:none;font-size:0.6rem;"></span>
            </button>

            <div class="dropdown-menu dropdown-menu-end p-0"
                 style="width:360px;max-height:480px;overflow:hidden;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.15);">

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom"
                     style="background:#1e3a5f;">
                    <span class="text-white fw-semibold">
                        <i class="bi bi-bell-fill me-2"></i>Notifications
                        <span id="notifHeaderCount" class="badge bg-danger ms-1" style="font-size:0.65rem;display:none;"></span>
                    </span>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm text-white opacity-75 p-0"
                                onclick="markAllRead()" title="Mark all as read"
                                style="font-size:0.75rem;">
                            <i class="bi bi-check2-all"></i> All Read
                        </button>
                        <button class="btn btn-sm text-white opacity-75 p-0"
                                onclick="clearAll()" title="Clear all"
                                style="font-size:0.75rem;">
                            <i class="bi bi-trash3"></i> Clear
                        </button>
                    </div>
                </div>

                <!-- Notification List -->
                <div id="notifList"
                     style="max-height:380px;overflow-y:auto;scrollbar-width:thin;">
                    <div class="text-center text-muted py-4" id="notifLoading">
                        <div class="spinner-border spinner-border-sm me-2"></div>
                        Loading...
                    </div>
                </div>

            </div>
        </div>

        <!-- USER DROPDOWN -->
        <div class="dropdown">
            <button class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
                <?= session()->get('username') ?>
                <span class="badge bg-primary ms-1" style="font-size:0.65rem;">
                    <?= ucfirst(session()->get('role')) ?>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="/logout">
                    <i class="bi bi-box-arrow-left me-2"></i>Logout
                </a></li>
            </ul>
        </div>
    </div>
</div>

    <!-- PAGE CONTENT -->
    <div class="p-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>

<script>
// -------------------------------------------------------
// NOTIFICATION SYSTEM
// -------------------------------------------------------
let notifLoaded = false;

function loadNotifications() {
    if (notifLoaded) return;
    notifLoaded = false; // Always reload on click

    fetch('/notifications/fetch')
        .then(r => r.json())
        .then(data => {
            renderNotifications(data.notifications, data.unread_count);
        })
        .catch(() => {
            document.getElementById('notifList').innerHTML =
                '<div class="text-center text-muted py-4"><i class="bi bi-wifi-off me-2"></i>Could not load notifications.</div>';
        });
}

function renderNotifications(notifications, unreadCount) {
    const list  = document.getElementById('notifList');
    const badge = document.getElementById('notifBadge');
    const headerCount = document.getElementById('notifHeaderCount');

    // Update badge
    if (unreadCount > 0) {
        badge.textContent      = unreadCount > 99 ? '99+' : unreadCount;
        badge.style.display    = 'inline';
        headerCount.textContent = unreadCount;
        headerCount.style.display = 'inline';
    } else {
        badge.style.display       = 'none';
        headerCount.style.display = 'none';
    }

    if (!notifications || notifications.length === 0) {
        list.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="bi bi-bell-slash" style="font-size:2rem;opacity:0.3;"></i>
                <p class="mt-2 mb-0" style="font-size:0.85rem;">No notifications yet</p>
            </div>`;
        return;
    }

    let html = '';
    notifications.forEach(n => {
        const isUnread = n.is_read == 0;
        const timeAgo  = formatTimeAgo(n.created_at);
        const bg       = isUnread ? '#f0f7ff' : '#fff';
        const dot      = isUnread ? '<span class="ms-auto"><i class="bi bi-circle-fill text-primary" style="font-size:0.5rem;"></i></span>' : '';

        html += `
        <a href="/notifications/read/${n.id}"
           class="d-flex align-items-start gap-2 px-3 py-2 text-decoration-none border-bottom notif-item"
           style="background:${bg};transition:background 0.2s;"
           onmouseover="this.style.background='#f8f9fa'"
           onmouseout="this.style.background='${bg}'">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                 style="width:34px;height:34px;background:linear-gradient(135deg,#1e3a5f,#2e5090);">
                <i class="bi bi-bell-fill text-white" style="font-size:0.75rem;"></i>
            </div>
            <div class="flex-fill" style="min-width:0;">
                <div class="d-flex justify-content-between align-items-start">
                    <span style="font-weight:${isUnread ? '700' : '500'};font-size:0.82rem;color:#1e3a5f;line-height:1.3;">
                        ${escapeHtml(n.title)}
                    </span>
                    ${dot}
                </div>
                <div style="font-size:0.78rem;color:#666;margin-top:2px;line-height:1.4;">
                    ${escapeHtml(n.message)}
                </div>
                <div style="font-size:0.7rem;color:#aaa;margin-top:4px;">
                    <i class="bi bi-clock me-1"></i>${timeAgo}
                </div>
            </div>
        </a>`;
    });

    list.innerHTML = html;
}

function markAllRead() {
    fetch('/notifications/read-all', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(() => {
            notifLoaded = false;
            loadNotifications();
        });
}

function clearAll() {
    if (!confirm('Clear all notifications?')) return;
    fetch('/notifications/clear-all', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(() => {
            notifLoaded = false;
            loadNotifications();
        });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

function formatTimeAgo(dateString) {
    const now  = new Date();
    const date = new Date(dateString);
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60)     return 'Just now';
    if (diff < 3600)   return Math.floor(diff / 60) + ' min ago';
    if (diff < 86400)  return Math.floor(diff / 3600) + ' hr ago';
    if (diff < 604800) return Math.floor(diff / 86400) + ' day(s) ago';
    return date.toLocaleDateString();
}

// Auto-fetch unread count every 30 seconds
function fetchUnreadCount() {
    fetch('/notifications/fetch')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notifBadge');
            const headerCount = document.getElementById('notifHeaderCount');
            if (data.unread_count > 0) {
                badge.textContent   = data.unread_count > 99 ? '99+' : data.unread_count;
                badge.style.display = 'inline';
                headerCount.textContent  = data.unread_count;
                headerCount.style.display = 'inline';
            } else {
                badge.style.display      = 'none';
                headerCount.style.display = 'none';
            }
        })
        .catch(() => {});
}

// Load count on page load
fetchUnreadCount();

// Reload notifications when dropdown opens
document.getElementById('notifBtn')?.addEventListener('click', () => {
    notifLoaded = false;
    loadNotifications();
});

// Refresh every 30 seconds
setInterval(fetchUnreadCount, 30000);
</script>

<?= $this->renderSection('scripts') ?>

</body>
</html>