<?php
session_start();
$isLoggedIn = isset($_SESSION['user']);
$firstName = $isLoggedIn ? htmlspecialchars($_SESSION['user']['users_firstName']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Web Check-In | SkyZen Airlines</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background-color: var(--bg-cream);">

    <div class="manage-booking-hero" style="padding-top: 60px;">
        <h1 style="color: #005A9C;">Web Check-In</h1>
        <p>Check in online to save time at the airport and head straight to the gate.</p>
    </div>

    <main class="skyzen-container" style="padding: 40px 20px; min-height: 60vh;">
        <div class="manage-search-card">
            <form id="checkInForm" onsubmit="event.preventDefault(); processCheckIn();">
                <div class="manage-input-row">
                    <div class="manage-input-group">
                        <label>Booking Reference (PNR) *</label>
                        <input type="text" id="ci_pnr" placeholder="e.g. A7X9ZQ" required maxlength="6" style="text-transform: uppercase;">
                    </div>
                    <div class="manage-input-group">
                        <label>Last Name *</label>
                        <input type="text" id="ci_lastName" placeholder="Enter last name" required>
                    </div>
                </div>
                <p class="manage-hint">Check-in is available from 48 hours up to 2 hours before your scheduled departure time.</p>
                <button type="submit" class="btn-manage-search" style="background: #16A34A;">Check-In Now <i class="fa-solid fa-plane-departure"></i></button>
            </form>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../scripts/service.js"></script>
</body>
</html>