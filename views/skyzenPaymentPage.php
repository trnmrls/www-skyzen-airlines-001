<?php
session_cache_limiter('private_no_expire');
session_start();
if (!isset($_SESSION['user'])) { header('Location: skyzenLoginPage.php'); exit; }

$flightID = $_POST['flightID'] ?? null;
$paxCount = $_POST['paxCount'] ?? 1;
$flightPrice = $_POST['flightPrice'] ?? 0;
$seats = $_POST['seats'] ?? 'TBA';

$baggagePrice = $_POST['baggage_price'] ?? 0;
$mealPrice = $_POST['meal_price'] ?? 0;

$paxNames = [];
for ($i = 1; $i <= $paxCount; $i++) {
    $paxNames[] = $_POST['pax_name_'.$i] ?? 'Passenger '.$i;
}

$baseTotal = $flightPrice * $paxCount;
$extrasTotal = ($baggagePrice + $mealPrice) * $paxCount; 
$grandTotal = $baseTotal + $extrasTotal;

if (!$flightID) { header('Location: skyzenMainPage.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Review & Pay | SkyZen</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Make previous steps look clickable */
        .progress-step.clickable { cursor: pointer; transition: opacity 0.2s; }
        .progress-step.clickable:hover { opacity: 0.7; }
        .required-asterisk { color: #DC2626; font-weight: bold; margin-left: 3px; }
        .input-error { border-color: #DC2626 !important; background-color: #FEF2F2 !important; }
    </style>
</head>
<body style="background-color: var(--bg-cream);">
    <div class="booking-progress-container" style="margin-bottom: 30px;">
        <div class="skyzen-container">
            <h2 class="progress-title" onclick="window.location.href='skyzenMainPage.php'">Booking Wizard</h2>
            <div class="progress-tracker">
                <div class="progress-step clickable" style="color:#16A34A; border-color:#16A34A;" onclick="window.history.go(-3)"><span>1/4</span> Flight</div>
                <div class="progress-step clickable" style="color:#16A34A; border-color:#16A34A;" onclick="window.history.go(-2)"><span>2/4</span> Seats</div>
                <div class="progress-step clickable" style="color:#16A34A; border-color:#16A34A;" onclick="window.history.go(-1)"><span>3/4</span> Extras</div>
                <div class="progress-step active" style="color:#005A9C; border-color:#005A9C;"><span>4/4</span> Review & Pay</div>
            </div>
        </div>
    </div>

    <main class="skyzen-container" style="max-width: 800px; padding-bottom: 60px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        
        <div style="background: white; padding: 30px; border-radius: 8px; height: max-content; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <h2 style="color: #005A9C; margin-top:0;">Payment Summary</h2>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>Base Fare (x<?= $paxCount ?>)</span>
                <strong>₱<?= number_format($baseTotal, 2) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>Extras (Baggage & Meals)</span>
                <strong>₱<?= number_format($extrasTotal, 2) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>Selected Seats</span>
                <strong><?= htmlspecialchars($seats) ?></strong>
            </div>
            <hr style="border:0; border-top: 1px dashed #CBD5E1; margin: 15px 0;">
            <div style="display: flex; justify-content: space-between; margin-top: 15px; font-size: 1.3rem;">
                <strong>Grand Total</strong>
                <strong style="color: #16A34A;">₱<?= number_format($grandTotal, 2) ?></strong>
            </div>
        </div>

        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <h2 style="color: #005A9C; margin-top:0;"><i class="fa-solid fa-credit-card"></i> Card Details</h2>
            <p style="color: #64748B; font-size: 0.85rem; margin-bottom: 20px;">All fields are required to process your transaction.</p>
            
            <form id="paymentForm">
                <input type="hidden" id="flightID" value="<?= htmlspecialchars($flightID) ?>">
                <input type="hidden" id="paxCount" value="<?= htmlspecialchars($paxCount) ?>">
                <input type="hidden" id="selectedMethod" value="cc">
                <?php foreach($paxNames as $name): ?>
                    <input type="hidden" class="forward-pax-name" value="<?= htmlspecialchars($name) ?>">
                <?php endforeach; ?>
                
                <div id="method-cc" class="pay-method-content active">
                    <div class="manage-input-group" style="margin-bottom: 15px;">
                        <label>Cardholder Name <span class="required-asterisk">*</span></label>
                        <input type="text" id="ccName" placeholder="e.g. JUAN DELA CRUZ" style="text-transform: uppercase;">
                    </div>
                
                <div class="manage-input-group" style="margin-bottom: 15px;">
                    <label>Card Number <span class="required-asterisk">*</span></label>
                    <input type="text" id="ccNum" placeholder="0000 0000 0000 0000" maxlength="19">
                </div>
                
                <div style="display:flex; gap:15px;">
                    <div class="manage-input-group" style="flex:1;">
                        <label>Expiry Date <span class="required-asterisk">*</span></label>
                        <input type="text" id="ccExpiry" placeholder="MM/YY" maxlength="5">
                    </div>
                    <div class="manage-input-group" style="flex:1;">
                        <label>CVV <span class="required-asterisk">*</span></label>
                        <input type="password" id="ccCvv" placeholder="123" maxlength="4">
                    </div>
                </div>

                <button type="button" class="skyzen-submit-btn" style="margin-top:25px; background:#16A34A;" onclick="validateAndProcessPayment()">Pay ₱<?= number_format($grandTotal, 2) ?></button>
            </form>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../scripts/service.js"></script>
</body>
</html>