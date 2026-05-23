<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a5f 0%, #2e5090 50%, #1a2f4e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .login-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #1e3a5f, #2e5090);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .login-logo i { font-size: 2rem; color: #f0a500; }

        .login-title {
            text-align: center;
            font-weight: 700;
            font-size: 1.4rem;
            color: #1e3a5f;
            margin-bottom: 4px;
        }

        .login-subtitle {
            text-align: center;
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 30px;
        }

        .form-label { font-weight: 600; font-size: 0.85rem; color: #444; }

        .form-control {
            border-radius: 10px;
            padding: 10px 14px;
            border: 1.5px solid #e0e0e0;
            font-size: 0.9rem;
        }

        .form-control:focus {
            border-color: #2e5090;
            box-shadow: 0 0 0 3px rgba(46,80,144,0.1);
        }

        .input-group .form-control { border-right: none; }
        .input-group .btn { border-radius: 0 10px 10px 0; border: 1.5px solid #e0e0e0; border-left: none; background: #f8f9fa; }

        .btn-login {
            background: linear-gradient(135deg, #1e3a5f, #2e5090);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            margin-top: 8px;
            transition: opacity 0.2s;
        }

        .btn-login:hover { opacity: 0.9; color: #fff; }

        .footer-text {
            text-align: center;
            color: #aaa;
            font-size: 0.75rem;
            margin-top: 24px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-logo">
        <i class="bi bi-building-fill"></i>
    </div>
    <div class="login-title">EMS Portal</div>
    <div class="login-subtitle">Employee Management System</div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2" style="font-size:0.85rem;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show py-2" style="font-size:0.85rem;">
            <i class="bi bi-check-circle-fill me-1"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="/login" method="POST">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
                <input type="text"
                       name="username"
                       class="form-control"
                       placeholder="Enter your username"
                       value="<?= old('username') ?>"
                       required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <input type="password"
                       name="password"
                       id="passwordInput"
                       class="form-control"
                       placeholder="Enter your password"
                       required>
                <button class="btn" type="button" onclick="togglePassword()">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </button>
    </form>

    <div class="footer-text">
        &copy; <?= date('Y') ?> Employee Management System. All rights reserved.
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'password' === 'password' ? 'text' : 'password';
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
</body>
</html>