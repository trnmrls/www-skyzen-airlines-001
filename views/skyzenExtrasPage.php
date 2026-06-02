<?php
session_cache_limiter('private_no_expire');
session_start();
if (!isset($_SESSION['user'])) { header('Location: skyzenLoginPage.php'); exit; }

$flightID = $_POST['flightID'] ?? null;
$flightPrice = $_POST['flightPrice'] ?? 0;
$paxCount = $_POST['paxCount'] ?? 1;
$seats = isset($_POST['seats']) ? implode(", ", $_POST['seats']) : 'TBA';

if (!$flightID) { header('Location: skyzenMainPage.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Extras | SkyZen</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: var(--bg-cream);">
    <div class="booking-progress-container" style="margin-bottom: 30px;">
        <div class="skyzen-container">
            <h2 class="progress-title" onclick="window.location.href='skyzenMainPage.php'">Booking Wizard</h2>
            <div class="progress-tracker">
                <div class="progress-step clickable" onclick="window.history.go(-1)" style="color:#16A34A; border-color:#16A34A;"><span>1/4</span> Flight</div>
                <div class="progress-step clickable" onclick="window.history.go(-2)" style="color:#16A34A; border-color:#16A34A;"><span>2/4</span> Seats</div>
                <div class="progress-step active" style="color:#005A9C; border-color:#005A9C;"><span>3/4</span> Add Extras</div>
                <div class="progress-step"><span>4/4</span> Review & Pay</div>
            </div>
        </div>
    </div>

    <main class="skyzen-container" style="max-width: 600px; padding-bottom: 60px;">
        <div style="background: white; padding: 30px; border-radius: 8px;">
            <h2 style="color: #005A9C;"><i class="fa-solid fa-suitcase-rolling"></i> Baggage & Meals</h2>
            
            <form action="skyzenPaymentPage.php" method="POST" onsubmit="showLoader()">
                <input type="hidden" name="flightID" value="<?= htmlspecialchars($flightID) ?>">
                <input type="hidden" name="flightPrice" value="<?= htmlspecialchars($flightPrice) ?>">
                <input type="hidden" name="paxCount" value="<?= htmlspecialchars($paxCount) ?>">
                <input type="hidden" name="seats" value="<?= htmlspecialchars($seats) ?>">
                
                <div class="manage-input-group" style="margin: 20px 0;">
                    <label>Select Baggage Allowance</label>
                    <select name="baggage_price" style="width:100%; height:45px; border-radius:4px; padding:0 10px;">
                        <option value="0">Carry-on only (7kg) - Free</option>
                        <option value="1000">20kg Checked Baggage - ₱1,000</option>
                        <option value="1800">32kg Checked Baggage - ₱1,800</option>
                    </select>
                </div>

                <div class="manage-input-group" style="margin: 20px 0;">
                    <label>Select In-flight Meal</label>
                    <select name="meal_price" style="width:100%; height:45px; border-radius:4px; padding:0 10px;">
                        <option value="0">No Meal</option>
                        <option value="350">Chicken Adobo with Rice - ₱350</option>
                        <option value="350">Beef Caldereta with Rice - ₱350</option>
                    </select>
                </div>

                <button type="submit" class="skyzen-submit-btn">Continue to Payment</button>
            </form>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../scripts/service.js"></script>
</body>
</html>