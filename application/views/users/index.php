<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">User Directory</h3>
        <p class="text-muted small mb-0">Total users found: <?= $total_rows ?></p>
    </div>
    <?php if ($is_admin): ?>
        <a href="<?= site_url('users/create') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Add New User
        </a>
    <?php endif; ?>
</div>

<!-- AJAX Notification Toast -->
<div id="ajaxAlert" class="alert d-none" role="alert"></div>

<!-- Search & Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= site_url('users') ?>" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= html_escape($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="ADMIN" <?= ($selected_role === 'ADMIN') ? 'selected' : '' ?>>ADMIN</option>
                    <option value="OPERATOR" <?= ($selected_role === 'OPERATOR') ? 'selected' : '' ?>>OPERATOR</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="ACTIVE" <?= ($selected_status === 'ACTIVE') ? 'selected' : '' ?>>ACTIVE</option>
                    <option value="INACTIVE" <?= ($selected_status === 'INACTIVE') ? 'selected' : '' ?>>INACTIVE</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-filter"></i> Filter</button>
                <a href="<?= site_url('users') ?>" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table Card -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td class="fw-semibold"><?= html_escape($user['name']) ?></td>
                            <td><?= html_escape($user['email']) ?></td>
                            <td>
                                <span class="badge <?= $user['role'] === 'ADMIN' ? 'bg-primary' : 'bg-info text-dark' ?>">
                                    <?= $user['role'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($is_admin): ?>
                                    <!-- AJAX Toggle switch for Admin -->
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                            data-id="<?= $user['id'] ?>"
                                            <?= $user['status'] === 'ACTIVE' ? 'checked' : '' ?>
                                            <?= ($user['id'] == $this->session->userdata('user_id')) ? 'disabled title="Cannot deactivate self"' : '' ?>>
                                        <label class="form-check-label small fw-semibold status-label-<?= $user['id'] ?>">
                                            <?= $user['status'] ?>
                                        </label>
                                    </div>
                                <?php else: ?>
                                    <!-- Non-editable badge for Operator -->
                                    <span class="badge <?= $user['status'] === 'ACTIVE' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $user['status'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d M Y, h:i A', strtotime($user['created_at'])) ?></td>
                            <td class="text-end">
                                <a href="<?= site_url('users/edit/' . $user['id']) ?>" class="btn btn-outline-primary btn-sm" title="Edit">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <?php if ($is_admin): ?>
                                    <?php if ($user['id'] != $this->session->userdata('user_id')): ?>
                                        <a href="<?= site_url('users/delete/' . $user['id']) ?>" 
                                           class="btn btn-outline-danger btn-sm" 
                                           onclick="return confirm('Are you sure you want to delete this user?');" 
                                           title="Delete">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            No users found matching the given criteria.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($pagination)): ?>
        <div class="card-footer bg-white py-3">
            <?= $pagination ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- AJAX Status Update Script -->
<script>
$(document).ready(function() {
    $('.status-toggle').on('change', function() {
        var $checkbox = $(this);
        var userId = $checkbox.data('id');
        var newStatus = $checkbox.is(':checked') ? 'ACTIVE' : 'INACTIVE';
        var $label = $('.status-label-' + userId);

        $checkbox.prop('disabled', true);

        $.ajax({
            url: '<?= site_url('users/update-status-ajax') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                id: userId,
                status: newStatus
            },
            success: function(response) {
                var $alert = $('#ajaxAlert');
                $alert.removeClass('d-none alert-success alert-danger');

                if (response.status === true) {
                    $alert.addClass('alert-success').text(response.message);
                    $label.text(newStatus);
                } else {
                    $alert.addClass('alert-danger').text(response.message);
                    // Revert checkbox switch
                    $checkbox.prop('checked', newStatus !== 'ACTIVE');
                }

                $alert.fadeIn().delay(3000).fadeOut();
            },
            error: function(xhr) {
                var $alert = $('#ajaxAlert');
                $alert.removeClass('d-none alert-success').addClass('alert-danger');
                
                var msg = 'Unable to update user status';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $alert.text(msg).fadeIn().delay(3000).fadeOut();
                
                // Revert checkbox switch
                $checkbox.prop('checked', newStatus !== 'ACTIVE');
            },
            complete: function() {
                $checkbox.prop('disabled', false);
            }
        });
    });
});
</script>