<?php
session_start();
require_once "../model/database_airlines.php";
require_once "../bl/userManagement.php";

if (!isset($_SESSION['user'])) { header('Location: skyzenLoginPage.php'); exit; }

$pnr = $_GET['pnr'] ?? null;
if (!$pnr) { header('Location: skyzenUserDash.php'); exit; }

$userManagement = new UserManagement();
// Fetch the specific booking for this user
$ticket = $userManagement->getBookingDetailsForTicket($pnr, $_SESSION['user']['usersID']);

if (!$ticket) {
    echo "<h2 style='text-align:center; margin-top: 50px;'>Error: Boarding Pass not found.</h2>";
    exit;
}

// Calculations for the UI
$depTime = new DateTime($ticket['flights_departureTime']);
$arrTime = new DateTime($ticket['flights_arrivalTime']);
$boardingTime = clone $depTime;
$boardingTime->modify('-45 minutes'); // Boarding starts 45 mins before flight
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Boarding Pass | SkyZen Airlines</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
    <style>
        body { background-color: #E2E8F0; padding: 40px 20px; font-family: 'Poppins', sans-serif; }
        .ticket-wrapper { max-width: 800px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); display: flex; }
        
        .ticket-main { flex: 2; padding: 30px; border-right: 2px dashed #CBD5E1; position: relative; }
        /* The cutouts for the ticket tear */
        .ticket-main::before, .ticket-main::after { content: ''; position: absolute; right: -15px; width: 30px; height: 30px; background: #E2E8F0; border-radius: 50%; }
        .ticket-main::before { top: -15px; }
        .ticket-main::after { bottom: -15px; }

        .ticket-stub { flex: 1; background: #005A9C; color: white; padding: 30px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
        
        .ticket-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #F1F5F9; padding-bottom: 20px; margin-bottom: 20px; }
        .ticket-brand { color: #005A9C; font-size: 1.5rem; font-weight: 900; }
        
        .route-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .route-point { text-align: center; }
        .route-point h2 { font-size: 3rem; margin: 0; color: #111827; line-height: 1; }
        .route-point p { margin: 5px 0 0 0; color: #64748B; font-size: 0.85rem; }
        
        .flight-details-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .detail-box label { font-size: 0.75rem; color: #94A3B8; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 5px; }
        .detail-box strong { font-size: 1.1rem; color: #111827; }
        
        .barcode { font-family: 'Libre Barcode 39', cursive; font-size: 4rem; color: #111827; line-height: 1; margin-top: 20px; }
        
        .btn-print { display: block; width: 100%; max-width: 800px; margin: 30px auto; text-align: center; background: #111827; color: white; padding: 15px; border-radius: 8px; text-decoration: none; font-weight: 700; cursor: pointer; border: none; }
        .btn-print:hover { background: #1F2937; }

        @media print {
            body { background: white; padding: 0; }
            .btn-print { display: none; }
            .ticket-wrapper { box-shadow: none; border: 1px solid #CBD5E1; }
        }
        @media (max-width: 768px) {
            .ticket-wrapper { flex-direction: column; }
            .ticket-main { border-right: none; border-bottom: 2px dashed #CBD5E1; }
            .ticket-main::before, .ticket-main::after { display: none; }
        }
    </style>
</head>
<body>

    <div class="ticket-wrapper">
        <div class="ticket-main">
            <div class="ticket-header">
                <div class="ticket-brand"><i class="fa-solid fa-plane"></i> SkyZen</div>
                <div style="background: #DCFCE7; color: #16A34A; padding: 5px 15px; border-radius: 20px; font-weight: 700; font-size: 0.85rem;">
                    <i class="fa-solid fa-check-circle"></i> Confirmed
                </div>
            </div>

            <div class="route-info">
                <div class="route-point">
                    <h2><?= htmlspecialchars($ticket['flights_originCode']) ?></h2>
                    <p><?= htmlspecialchars($ticket['originName']) ?></p>
                </div>
                <div style="color: #CBD5E1; font-size: 2rem;"><i class="fa-solid fa-plane"></i></div>
                <div class="route-point">
                    <h2><?= htmlspecialchars($ticket['flights_destinationCode']) ?></h2>
                    <p><?= htmlspecialchars($ticket['destName']) ?></p>
                </div>
            </div>

            <div class="flight-details-grid">
                <div class="detail-box">
                    <label>Passenger</label>
                    <strong><?= htmlspecialchars($ticket['users_firstName'] . ' ' . $ticket['users_lastName']) ?></strong>
                </div>
                <div class="detail-box">
                    <label>Flight</label>
                    <strong><?= htmlspecialchars($ticket['flightsNum']) ?></strong>
                </div>
                <div class="detail-box">
                    <label>Date</label>
                    <strong><?= $depTime->format('d M Y') ?></strong>
                </div>
                <div class="detail-box">
                    <label>Boarding Time</label>
                    <strong style="color: #D97706;"><?= $boardingTime->format('H:i') ?></strong>
                </div>
                <div class="detail-box">
                    <label>Departure</label>
                    <strong><?= $depTime->format('H:i') ?></strong>
                </div>
                <div class="detail-box">
                    <label>Aircraft</label>
                    <strong><?= htmlspecialchars($ticket['aircraftsModel']) ?></strong>
                </div>
            </div>

            <div class="barcode">*<?= htmlspecialchars($ticket['bookings_pnrCode']) ?>*</div>
        </div>

        <div class="ticket-stub">
            <p style="font-size: 0.85rem; color: #93C5FD; text-transform: uppercase; margin: 0 0 10px 0; font-weight: 700;">Booking Ref (PNR)</p>
            <h1 style="font-size: 3rem; margin: 0 0 20px 0; letter-spacing: 2px; color: #FBBF24;"><?= htmlspecialchars($ticket['bookings_pnrCode']) ?></h1>
            
            <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; width: 100%; margin-bottom: 20px;">
                <p style="margin: 0 0 5px 0; font-size: 0.85rem; color: #93C5FD;">GATE</p>
                <h2 style="margin: 0; font-size: 2rem;">TBA</h2>
            </div>
            
            <a href="skyzenUserDash.php" style="color: white; text-decoration: underline; font-size: 0.9rem;">Go to Dashboard</a>
        </div>
    </div>

    <button class="btn-print" onclick="window.print()"><i class="fa-solid fa-print"></i> Print E-Ticket</button>

</body>
</html>