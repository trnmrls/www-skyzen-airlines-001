<?php
session_start();
require_once "../model/database_airlines.php";
require_once "../bl/userManagement.php";

$isLoggedIn = isset($_SESSION['user']);
$firstName = $isLoggedIn ? htmlspecialchars($_SESSION['user']['users_firstName']) : '';

$userManagement = new UserManagement();

// 1. Capture GET variables from skyzenMainPage.php
$origin = $_GET['origin'] ?? '';
$dest = $_GET['dest'] ?? '';
$dep_date = $_GET['dep_date'] ?? '';
$pax = (int)($_GET['pax'] ?? 1);

// 2. Query tbl_flights
$flights = [];
if (!empty($origin) && !empty($dest) && !empty($dep_date)) {
    $flights = method_exists($userManagement, 'searchAvailableFlights') 
        ? $userManagement->searchAvailableFlights($origin, $dest, $dep_date, $pax) 
        : [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Flight & Passenger Info | SkyZen</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: var(--bg-cream);">

    <div class="booking-progress-container" style="margin-bottom: 30px;">
        <div class="skyzen-container">
            <h2 class="progress-title" onclick="window.location.href='skyzenMainPage.php'">Booking Wizard</h2>
            <div class="progress-tracker">
                <div class="progress-step active" style="color:#005A9C; border-color:#005A9C;"><span>1/4</span> Flight & Passengers</div>
                <div class="progress-step"><span>2/4</span> Choose Seats</div>
                <div class="progress-step"><span>3/4</span> Add Extras</div>
                <div class="progress-step"><span>4/4</span> Review & Pay</div>
            </div>
        </div>
    </div>

    <main class="skyzen-container" style="max-width: 900px; padding-bottom: 60px;">
        <h2 style="color: #005A9C;">Available Flights</h2>
        
        <?php if (empty($flights)): ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 8px;">
                <h3>No flights found for this route/date.</h3>
                <a href="skyzenMainPage.php" class="btn-manage-search" style="display:inline-block; width:auto;">Search Again</a>
            </div>
        <?php else: ?>
            <?php foreach($flights as $flight): ?>
                <div class="dash-card flight-itinerary-card" style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="flex: 1;">
                        <h3 style="margin:0; font-size:1.8rem;"><?= date('H:i', strtotime($flight['flights_departureTime'])) ?> <span style="font-size:1rem; color:#64748B;"><?= htmlspecialchars($flight['flights_originCode']) ?></span></h3>
                        <p style="margin:5px 0; color:#94A3B8;"><i class="fa-solid fa-plane"></i> <?= htmlspecialchars($flight['aircraftsModel']) ?></p>
                        <h3 style="margin:0; font-size:1.8rem;"><?= date('H:i', strtotime($flight['flights_arrivalTime'])) ?> <span style="font-size:1rem; color:#64748B;"><?= htmlspecialchars($flight['flights_destinationCode']) ?></span></h3>
                    </div>
                    <div style="text-align: right; padding-left: 20px; border-left: 1px dashed #CBD5E1;">
                        <div style="color: #D97706; font-size: 2rem; font-weight: 800;">₱<?= number_format($flight['flights_basePrice'], 2) ?></div>
                        <button class="skyzen-submit-btn" style="background: #F59E0B; margin-top: 10px;" onclick="showPassengerForm(<?= $flight['flightsID'] ?>, <?= $flight['flights_basePrice'] ?>)">Select</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div id="passengerFormBox" style="display: none; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-top: 30px;">
            <h3 style="color: #005A9C;">Passenger Details</h3>
            <form action="skyzenSeatsPage.php" method="POST" onsubmit="showLoader()">
                <input type="hidden" name="flightID" id="selectedFlightID">
                <input type="hidden" name="flightPrice" id="selectedFlightPrice">
                <input type="hidden" name="paxCount" value="<?= $pax ?>">

                <?php for($i = 1; $i <= $pax; $i++): ?>
                    <div style="margin-bottom: 20px;">
                        <label><strong>Passenger <?= $i ?> Full Name *</strong></label>
                        <input type="text" name="pax_name_<?= $i ?>" required style="width: 100%; height: 40px; border: 1px solid #CBD5E1; border-radius: 4px; padding: 10px;" placeholder="e.g. John Doe">
                    </div>
                <?php endfor; ?>
                
                <button type="submit" class="skyzen-submit-btn">Continue to Seats <i class="fa-solid fa-arrow-right"></i></button>
            </form>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../scripts/service.js"></script>
    
</body>
</html>