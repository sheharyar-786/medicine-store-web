<?php
$pageTitle = 'Register';
include 'includes/header.php';
?>

<main class="auth-page">
    <div class="auth-card reveal-init">
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
            <button type="submit" name="register" class="btn btn-block"><i class="fas fa-user-plus"></i> Register</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
