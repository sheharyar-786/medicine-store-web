<?php
$pageTitle = 'Register';
$pageStyles = ['auth'];
include 'includes/header.php';
?>

<main class="auth-page">
    <div class="auth-card reveal-init">
        <div style="margin-bottom: 20px;">
            <a href="index.php" style="color: var(--primary); font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px;"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
        <div class="auth-icon"><i class="fas fa-user-plus"></i></div>
        <h2>Create Account</h2>
        <p class="auth-subtitle">Join HealthCare Store for fast medicine delivery</p>

        <form action="actions/register_action.php" method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Your full name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" class="form-control" placeholder="+92 300 1234567">
            </div>
            <div class="form-group">
                <label for="address">Delivery Address</label>
                <textarea id="address" name="address" class="form-control" placeholder="Your default delivery address"></textarea>
            </div>
            <div class="form-group">
                <label for="role">Account Role</label>
                <select id="role" name="role" class="form-control" style="width: 100%; padding: 10px; border-radius: var(--radius); border: 1px solid var(--border); font-size: 0.95rem; font-family: inherit;">
                    <option value="customer">Customer</option>
                    <option value="pharmacist">Store Pharmacist</option>
                    <option value="driver">Delivery Driver</option>
                    <option value="admin">Store Admin (Owner)</option>
                </select>
            </div>
            <button type="submit" name="register" class="btn btn-block"><i class="fas fa-user-plus"></i> Register</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
