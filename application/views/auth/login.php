<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #e9ecef; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-card { width: 100%; max-width: 420px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="card login-card p-4 border-0">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary"><i class="bi bi-lock-fill"></i> Sign In</h3>
                <p class="text-muted small">CodeIgniter 3 User Management</p>
            </div>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                    <?= $this->session->flashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (validation_errors()): ?>
                <div class="alert alert-danger small">
                    <?= validation_errors() ?>
                </div>
            <?php endif; ?>

            <?= form_open('login') ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" value="<?= set_value('email') ?>" required autofocus placeholder="admin@example.com">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            <?= form_close() ?>

            <div class="mt-4 p-3 bg-light rounded small text-muted">
                <strong>Demo Credentials:</strong><br>
                Admin: <code>admin@example.com</code> / <code>Admin@123</code><br>
                Operator: <code>operator@example.com</code> / <code>Admin@123</code>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>