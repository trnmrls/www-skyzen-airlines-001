<?php
session_start();
require_once "../model/database_airlines.php";

if (!isset($_SESSION['user'])) {
    header('Location: skyzenLoginPage.php');
    exit;
}

$flightID = $_GET['flight_id'] == null ? null : $_GET['flight_id'];
$paxCount = $_GET['pax'] == 1 ? 1 : $_GET['pax'];

if (!$flightID) {
    header('Location: skyzenHome.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Details | SkyZen</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background-color: var(--bg-cream);">

    <div class="booking-progress-container" style="margin-bottom: 40px;">
        <div class="skyzen-container">
            <h2 class="progress-title">Checkout</h2>
            <div class="progress-tracker">
                <div class="progress-step" style="color: #005A9C; border-color: #005A9C;"><span>1/6</span> Select Flight</div>
                <div class="progress-step active"><span>2/6</span> Add Passenger Details</div>
                <div class="progress-step"><span>3/6</span> Review & Pay</div>
            </div>
        </div>
    </div>

    <main class="skyzen-container" style="max-width: 800px; padding-bottom: 60px;">
        <div class="manage-search-card">
            <h2 style="color: #005A9C; margin-top: 0;">Passenger Information</h2>
            <p style="color: #64748B; margin-bottom: 30px;">Please ensure names match government-issued IDs.</p>

            <form id="checkoutForm">
                <input type="hidden" id="flightID" value="<?= htmlspecialchars($flightID) ?>">
                <input type="hidden" id="paxCount" value="<?= htmlspecialchars($paxCount) ?>">

                <?php for($i = 1; $i <= $paxCount; $i++): ?>
                    <div style="background: #F8FAFC; padding: 20px; border: 1px solid #E2E8F0; border-radius: 8px; margin-bottom: 20px;">
                        <h4 style="margin: 0 0 15px 0;">Passenger <?= $i ?></h4>
                        <div class="manage-input-row">
                            <div class="manage-input-group">
                                <label>First Name</label>
                                <input type="text" required>
                            </div>
                            <div class="manage-input-group">
                                <label>Last Name</label>
                                <input type="text" required>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>

                <button type="button" class="btn-manage-search" onclick="processCheckout()">Confirm & Book Flight</button>
            </form>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../scripts/service.js"></script>
</body>
</html>