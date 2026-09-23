<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-plus text-primary me-2"></i>Create New User</h5>
                    <a href="<?= site_url('users') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if (validation_errors()): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?= validation_errors('<li>', '</li>') ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?= form_open('users/create') ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= set_value('name') ?>" required placeholder="e.g. Jane Doe">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?= set_value('email') ?>" required placeholder="e.g. jane@example.com">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required placeholder="Minimum 6 characters">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" class="form-control" required placeholder="Repeat password">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="OPERATOR" <?= set_select('role', 'OPERATOR', TRUE) ?>>OPERATOR</option>
                                <option value="ADMIN" <?= set_select('role', 'ADMIN') ?>>ADMIN</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="ACTIVE" <?= set_select('status', 'ACTIVE', TRUE) ?>>ACTIVE</option>
                                <option value="INACTIVE" <?= set_select('status', 'INACTIVE') ?>>INACTIVE</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= site_url('users') ?>" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Save User</button>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>