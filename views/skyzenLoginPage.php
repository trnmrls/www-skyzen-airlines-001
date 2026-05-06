<?php
session_start();
require_once "../bl/userManagement.php";

if (isset($_SESSION['user'])) {
    $dest = ((int)$_SESSION['user']['rolesID'] === 1) ? 'skyzenAdminDash.php' : 'skyzenUserDash.php';
    header("Location: $dest");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | SkyZen Airlines</title>
    
    <link rel="stylesheet" href="../views/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="nk-auth-wrapper">
    
    <div class="nk-auth-brand">
        <div class="brand-logo">
            <div class="brand-logo-icon"><i class="fa-solid fa-plane"></i></div>
            SkyZen Airlines
        </div>
        
        <h1>Welcome to SkyZen</h1>
        <p class="brand-sub">Modern airline reservation system providing exceptional flight experiences globally.</p>
        
        <div class="feature-list">
            <div class="feature-item"><i class="fa-solid fa-shield-halved"></i> Secure Authentication</div>
            <div class="feature-item"><i class="fa-solid fa-chart-line"></i> Real-time Flight Data</div>
            <div class="feature-item"><i class="fa-solid fa-bolt"></i> Lightning Fast Bookings</div>
        </div>
    </div>

    <!-- Right Login Form -->
    <div class="nk-auth-form-panel">
        <div class="auth-card">
            <h2>Sign In</h2>
            <p class="subtitle">Enter your credentials to access your account</p>

            <form onsubmit="event.preventDefault(); loginAdminFunc();">
                
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <div class="input-icon"><i class="fa-solid fa-user"></i></div>
                        <input type="text" id="users_username" class="form-control" maxlength="10" placeholder="Enter your username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <div class="input-icon"><i class="fa-solid fa-lock"></i></div>
                        <input type="password" id="users_password" class="form-control" maxlength="15" placeholder="Enter your password" required>
                        <button type="button" class="toggle-password" onclick="togglePass()"><i class="fa-solid fa-eye" id="eyeIcon"></i></button>
                    </div>
                </div>

                <div class="form-check" style="justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="remember" class="i-checks">
                        <label for="remember">Keep me signed in</label>
                    </div>
                    <a href="#" style="font-size: 0.85rem;">Forgot password?</a>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Log In
                </button>

                <div class="auth-divider"><span>or continue with</span></div>

                <div class="social-btns">
                    <button type="button" class="social-btn"><i class="fa-brands fa-google"></i></button>
                    <button type="button" class="social-btn"><i class="fa-brands fa-github"></i></button>
                    <button type="button" class="social-btn"><i class="fa-brands fa-facebook-f"></i></button>
                </div>

                <div class="auth-bottom">
                    Don't have an account? <a href="skyzenRegisPage.php">Create one</a>
                </div>

            </form>
        </div>
    </div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../scripts/service.js"></script>
<script>
    function togglePass() {
        var x = document.getElementById("users_password");
        var icon = document.getElementById("eyeIcon");
        if (x.type === "password") {
            x.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            x.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>
</body>
</html>