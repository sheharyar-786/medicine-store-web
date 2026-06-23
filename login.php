<?php
$pageTitle = 'Login';
include 'includes/header.php';
?>

<main class="auth-page">
    <div class="auth-card reveal-init">
        <h2>Welcome Back</h2>
        <p class="auth-subtitle">Sign in to your HealthCare Store account</p>

        <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> Registration successful! Please login.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form action="actions/login_action.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" name="login" class="btn btn-block"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>

        <p class="auth-footer">Don't have an account? <a href="register.php">Create one</a></p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
