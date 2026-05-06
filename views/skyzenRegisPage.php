<?php
    session_start();
    require_once "../bl/userManagement.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | SkyZen Airlines</title>
    
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

    <div class="nk-auth-form-panel">
        <div class="auth-card" style="max-width: 550px;"> 
            <h2>Create Account</h2>
            <p class="subtitle">Fill in the details to get started</p>

            <form onsubmit="event.preventDefault(); registerUserFunc();">
                
                <div class="form-row">
                    <div class="form-group form-col" style="flex: 2;">
                        <label class="form-label">First Name</label>
                        <div class="input-group">
                            <div class="input-icon"><i class="fa-solid fa-user"></i></div>
                            <input type="text" id="regFirstName" class="form-control" maxlength="30" placeholder="First Name" required>
                        </div>
                    </div>
                    
                    <div class="form-group form-col" style="flex: 0.8;">
                        <label class="form-label">M.I.</label>
                        <div class="input-group">
                            <input type="text" id="regmiddleName" class="form-control" placeholder="M.I." maxlength="5" required>
                        </div>
                    </div>

                    <div class="form-group form-col" style="flex: 2;">
                        <label class="form-label">Last Name</label>
                        <div class="input-group">
                            <input type="text" id="regLastName" class="form-control" maxlength="30"placeholder="Last Name" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <div class="input-icon"><i class="fa-solid fa-envelope"></i></div>
                        <input type="email" id="regEmail" class="form-control" maxlength="30" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-col">
                        <label class="form-label">Phone Number</label>
                        <div class="input-group">
                            <div class="input-icon"><i class="fa-solid fa-phone"></i></div>
                            <input type="text" id="regPhone" class="form-control" maxlength="15" placeholder="Phone Number" required>
                        </div>
                    </div>
                    
                    <div class="form-group form-col">
                        <label class="form-label">Birthday</label>
                        <div class="input-group">
                            <div class="input-icon"><i class="fa-solid fa-cake-candles"></i></div>
                            <input type="date" id="regBirthday" class="form-control" required style="padding-right: 14px;">
                        </div>
                    </div>
                </div>


                <div class="form-row">
                    <div class="form-group form-col">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <div class="input-icon"><i class="fa-solid fa-at"></i></div>
                            <input type="text" id="regUsername" class="form-control" maxlength="10" placeholder="Choose a username" required>
                        </div>
                    </div>
                    
                    <div class="form-group form-col">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <div class="input-icon"><i class="fa-solid fa-lock"></i></div>
                            <input type="password" id="regPassword" class="form-control" maxlength="15" placeholder="Create a password" required>
                            <button type="button" class="toggle-password" onclick="togglePass()"><i class="fa-solid fa-eye" id="eyeIcon"></i></button>
                        </div>
                    </div>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-user-plus"></i> Create Account
                </button>

                <div class="auth-divider"><span>or continue with</span></div>

                <div class="social-btns">
                    <button type="button" class="social-btn"><i class="fa-brands fa-google"></i></button>
                    <button type="button" class="social-btn"><i class="fa-brands fa-github"></i></button>
                    <button type="button" class="social-btn"><i class="fa-brands fa-facebook-f"></i></button>
                </div>

                <div class="auth-bottom">
                    Already have an account? <a href="skyzenLoginPage.php">Log In</a>
                </div>

            </form>
        </div>
    </div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../scripts/service.js"></script>
</body>
</html>