<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth_check.php';

// Protect page
restrictToAdmin();

// Handle role update
if (isset($_POST['update_role'])) {
    $user_id = (int)$_POST['user_id'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Admin manually setting a role automatically approves the account
    $update_sql = "UPDATE users SET role = '$role', is_approved = 1 WHERE id = $user_id";
    if (mysqli_query($conn, $update_sql)) {
        $msg = "success";
    } else {
        $error = "Error updating role: " . mysqli_error($conn);
    }
}

// Handle manual approval
if (isset($_POST['approve_user'])) {
    $user_id = (int)$_POST['user_id'];
    $update_sql = "UPDATE users SET is_approved = 1 WHERE id = $user_id";
    if (mysqli_query($conn, $update_sql)) {
        $msg = "success";
    } else {
        $error = "Error approving user: " . mysqli_error($conn);
    }
}

$pageTitle = 'Manage Staff';
include '../includes/header.php';

// Query all users, showing pending approvals first, then staff, then alphabetically
$query = "SELECT id, full_name, email, phone, role, is_approved, created_at 
          FROM users 
          ORDER BY is_approved ASC, role DESC, full_name ASC";
$result = mysqli_query($conn, $query);
?>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Staff Management</h1>
            <p>Review permissions, approve new staff registrations, and configure roles.</p>
        </div>

        <?php if (isset($msg) && $msg === 'success'): ?>
        <div class="alert alert-success">Staff database updated successfully.</div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo clean($error); ?></div>
        <?php endif; ?>

        <div class="admin-card reveal-init">
            <div class="admin-card-header">
                <h2>All Users & Permissions</h2>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email / Phone</th>
                        <th>Role</th>
                        <th>Approval Status</th>
                        <th>Modify Access Level</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr style="<?php echo !$row['is_approved'] ? 'background-color: #fffbeb;' : ''; ?>">
                            <td>#<?php echo $row['id']; ?></td>
                            <td><strong><?php echo clean($row['full_name']); ?></strong></td>
                            <td>
                                <?php echo clean($row['email']); ?><br>
                                <span style="font-size: 0.8rem; color: var(--muted);"><?php echo clean($row['phone'] ?: 'No Phone'); ?></span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $row['role']; ?>" style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">
                                    <?php echo clean($row['role']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['is_approved']): ?>
                                <span style="color: var(--accent-dark); font-weight: 700;"><i class="fas fa-check-circle"></i> Active / Approved</span>
                                <?php else: ?>
                                <div style="display: flex; flex-direction: column; gap: 6px; align-items: flex-start;">
                                    <span style="color: var(--danger); font-weight: 700;"><i class="fas fa-hourglass-half"></i> Pending Approval</span>
                                    <form action="manage-staff.php" method="POST">
                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="approve_user" class="btn btn-sm" style="background-color: var(--accent-dark); border-color: var(--accent-dark); color: white; padding: 3px 8px; font-size: 0.75rem;">
                                            <i class="fas fa-user-check"></i> Approve Account
                                        </button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="manage-staff.php" method="POST" style="display: flex; gap: 8px; align-items: center;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <select name="role" class="status-select" style="padding: 6px 10px; font-size: 0.85rem;">
                                        <option value="customer" <?php if($row['role'] == 'customer') echo 'selected'; ?>>Customer</option>
                                        <option value="pharmacist" <?php if($row['role'] == 'pharmacist') echo 'selected'; ?>>Pharmacist</option>
                                        <option value="driver" <?php if($row['role'] == 'driver') echo 'selected'; ?>>Driver</option>
                                        <option value="admin" <?php if($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                    </select>
                                    <button type="submit" name="update_role" class="btn btn-sm" style="font-size: 0.8rem;">Save</button>
                                </form>
                            </td>
                            <td style="font-size: 0.85rem; color: var(--muted);">
                                <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--muted);">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
