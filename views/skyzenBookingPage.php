<?php
session_start();
require_once "../model/database_airlines.php";
require_once "../bl/userManagement.php";

$isLoggedIn = isset($_SESSION['user']);
$firstName = $isLoggedIn ? htmlspecialchars($_SESSION['user']['users_firstName']) : '';

$bookingData = [];
$errorMsg = '';
$hasSearched = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pnr_code']) && isset($_POST['last_name'])) {
    $pnr = strtoupper(trim($_POST['pnr_code']));
    $lname = trim($_POST['last_name']);
    $hasSearched = true;

    $um = new UserManagement();
    $bookingData = method_exists($um, 'getBookingByPNR') ? $um->getBookingByPNR($pnr, $lname) : [];

    if (empty($bookingData)) {
        $errorMsg = "We couldn't find a booking matching this PNR and Last Name. Please double-check and try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Booking | SkyZen Airlines</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: var(--bg-cream);">

    <nav class="skyzen-public-nav">
        <div class="skyzen-nav-container">
            <div class="skyzen-brand">
                <i class="fa-solid fa-plane"></i>
                <a href="skyzenMainPage.php">SkyZen Airlines</a>
            </div>
            
            <ul class="skyzen-nav-links">
                <li class="has-mega-menu">
                    <a href="#">Book</a>                    
                    <div class="mega-menu">
                        <div class="mega-menu-top">
                            <a href="skyzenOfferPage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-plane"></i></div>
                                Flight Offers
                            </a>
                            <a href="skyzenSeatSalePage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-tag"></i></div>
                                Seat Sale
                            </a>
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-handshake"></i></div>
                                Partner Agents
                        </div>
                    </div>
                </li>
                <li class="has-mega-menu">
                    <a href="#">Manage</a>
                    <div class="mega-menu">
                        <div class="mega-menu-top">
                            <a href="skyzenCheckInPage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-location-dot"></i></div>
                                Check in
                            </a>
                            <a href="skyzenBookingPage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-file-signature"></i></div>
                                Manage Booking
                            </a>
                            <a href="skyzenFlightsPage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-plane-circle-check"></i></div>
                                Flight Status
                            </a>
                        </div>
                    </div>
                </li>
                <li class="has-mega-menu">
                    <a href="#">Travel</a>
                    <div class="mega-menu">
                        <div class="mega-menu-top">
                            <a href="skyzenTravInfoPage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-info-circle"></i></div>
                                Travel Information
                            </a>
                            <a href="skyzenBagInfoPage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-calendar-days"></i></div>
                                Baggage Information
                            </a>
                            <a href="skyzenFleetPage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-globe"></i></div>
                                Fleet & Cabin
                            </a>
                        </div>
                    </div>
                </li>
                <li class="has-mega-menu">
                    <a href="#">Explore</a>
                    <div class="mega-menu">
                        <div class="mega-menu-top">
                            <a href="skyzenPHDestPage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-location-dot"></i></div>
                                Philippine Destinations
                            </a>
                            <a href="skyzenIntlDestPage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-plane"></i></div>
                                International Destinations
                            </a>
                            <a href="skyzenAirportsPage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-map"></i></div>
                                Where We Fly
                            </a>
                        </div>
                    </div>
                </li>
                <li class="has-mega-menu">
                    <a href="#">Need Help?</a>
                    <div class="mega-menu">
                        <div class="mega-menu-top">
                            <a href="skyzenContactPage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-headset"></i></div>
                                Contact Us
                            </a>
                            <a href="skyzenFAQsPage.php" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-question"></i></div>
                                FAQs
                            </a>
                            <a href="skyzenTermsPage.html" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-file-contract"></i></div>
                                Terms & Conditions
                            </a>
                        </div>
                    </div>
                </li>
                </ul>

            <div class="skyzen-nav-actions">
                    <a href="#" class="skyzen-search-icon" style="margin-right: 15px;"><i class="fa-solid fa-magnifying-glass"></i></a>
                    <?php if($isLoggedIn): ?>
                        <a href="skyzenUserDash.php" class="skyzen-login-btn active" style="background: var(--theme-green); color: white !important;">
                            <i class="fa fa-user-circle"></i> Hello, <?= $firstName ?>
                        </a>
                        <a href="../controllers/userController.php?logout=1" style="margin-left: 15px; color: var(--theme-red); text-decoration: none; font-weight: 700;" title="Logout">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </a>
                    <?php else: ?>
                        <a href="skyzenLoginPage.php" class="skyzen-login-btn">
                            <i class="fa fa-user-circle"></i> Log in
                        </a>
                    <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="skyzen-container" style="padding: 40px 20px; min-height: 70vh;">
        
        <?php if (!$hasSearched || !empty($errorMsg)): ?>
            <div class="manage-booking-hero">
                <h1>Manage Booking</h1>
                <p>View your itinerary, print your e-ticket, modify flight dates, or cancel your booking.</p>
            </div>

            <div class="manage-search-card">
                <?php if(!empty($errorMsg)): ?>
                    <div style="background: #FEE2E2; color: #DC2626; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: 600;">
                        <i class="fa-solid fa-circle-exclamation"></i> <?= $errorMsg ?>
                    </div>
                <?php endif; ?>

                <form action="skyzenBookingPage.php" method="POST" class="manage-form">
                    <div class="manage-input-row">
                        <div class="manage-input-group">
                            <label>Booking Reference (PNR) *</label>
                            <input type="text" name="pnr_code" placeholder="e.g. XYZ123" required maxlength="6" style="text-transform: uppercase;">
                        </div>
                        <div class="manage-input-group">
                            <label>Last Name *</label>
                            <input type="text" name="last_name" placeholder="Enter last name" required>
                        </div>
                    </div>
                    <p class="manage-hint">Your 6-character PNR code can be found on your booking confirmation email.</p>
                    <button type="submit" class="btn-manage-search">Continue <i class="fa-solid fa-arrow-right"></i></button>
                </form>
            </div>

        <?php else: ?>
            <?php 
                $primaryFlight = $bookingData[0]; // Get the main flight details
                $totalFare = $primaryFlight['bookings_amount'];
            ?>

            <div class="booking-dash-header">
                <div class="pnr-badge">
                    <span>Booking Reference</span>
                    <h2><?= htmlspecialchars($primaryFlight['bookings_pnrCode']) ?></h2>
                </div>
                <div class="booking-actions">
                    <button class="btn-outline-blue" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Itinerary</button>
                    <button class="btn-outline-blue"><i class="fa-solid fa-envelope"></i> Resend Email</button>
                </div>
            </div>

            <div class="booking-dash-grid">
                <div class="dash-col-main">
                    
                    <h3 class="dash-section-title"><i class="fa-solid fa-plane"></i> Flight Details</h3>
                    <?php foreach($bookingData as $flight): ?>
                        <div class="dash-card flight-itinerary-card">
                            <div class="itinerary-header">
                                <span class="flight-num">Flight <?= htmlspecialchars($flight['flightsNum']) ?></span>
                                <span class="status-badge <?= strtolower($flight['bookings_status']) == 'confirmed' ? 'status-confirmed' : 'status-pending' ?>">
                                    <?= htmlspecialchars($flight['bookings_status']) ?>
                                </span>
                            </div>
                            <div class="itinerary-body">
                                <div class="itin-time-col">
                                    <h3><?= date('H:i', strtotime($flight['flights_departureTime'])) ?></h3>
                                    <p><?= date('D, d M Y', strtotime($flight['flights_departureTime'])) ?></p>
                                    <strong><?= htmlspecialchars($flight['flights_originCode']) ?></strong>
                                    <small><?= htmlspecialchars($flight['originName']) ?></small>
                                </div>
                                <div class="itin-duration">
                                    <i class="fa-solid fa-plane" style="color: #005A9C;"></i>
                                    <p><?= htmlspecialchars($flight['aircraftsModel']) ?></p>
                                </div>
                                <div class="itin-time-col text-right">
                                    <h3><?= date('H:i', strtotime($flight['flights_arrivalTime'])) ?></h3>
                                    <p><?= date('D, d M Y', strtotime($flight['flights_arrivalTime'])) ?></p>
                                    <strong><?= htmlspecialchars($flight['flights_destinationCode']) ?></strong>
                                    <small><?= htmlspecialchars($flight['destName']) ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <h3 class="dash-section-title"><i class="fa-solid fa-users"></i> Passenger Details</h3>
                    <div class="dash-card passenger-card">
                        <div class="pax-header">
                            <i class="fa-solid fa-user"></i> <?= htmlspecialchars($primaryFlight['users_firstName'] . ' ' . $primaryFlight['users_lastName']) ?>
                        </div>
                        <div class="pax-addons">
                            <div class="addon-item">
                                <i class="fa-solid fa-suitcase-rolling"></i>
                                <span><strong>Baggage:</strong> 20KG Checked Baggage</span>
                            </div>
                            <div class="addon-item">
                                <i class="fa-solid fa-utensils"></i>
                                <span><strong>Meal:</strong> Standard Meal Option</span>
                            </div>
                        </div>
                    </div>

                    <h3 class="dash-section-title"><i class="fa-solid fa-address-book"></i> Contact Details</h3>
                    <div class="dash-card contact-card">
                        <div class="contact-row">
                            <strong>Email Address:</strong>
                            <span><?= htmlspecialchars($primaryFlight['users_email']) ?></span>
                        </div>
                        <div class="contact-row">
                            <strong>Mobile Number:</strong>
                            <span><?= htmlspecialchars($primaryFlight['users_phoneNum']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="dash-col-side">
                    <div class="dash-card payment-summary-card">
                        <h3>Payment Details</h3>
                        <div class="payment-row">
                            <span>Base Fare</span>
                            <span>₱<?= number_format($primaryFlight['flights_price'], 2) ?></span>
                        </div>
                        <div class="payment-row">
                            <span>Taxes & Fees</span>
                            <span>₱<?= number_format($totalFare - $primaryFlight['flights_price'], 2) ?></span>
                        </div>
                        <div class="payment-divider"></div>
                        <div class="payment-row total">
                            <span>Total Amount</span>
                            <span style="color: var(--theme-green);">₱<?= number_format($totalFare, 2) ?></span>
                        </div>
                        <div class="payment-status">
                            <i class="fa-solid fa-check-circle" style="color: var(--theme-green);"></i> Fully Paid
                        </div>
                    </div>

                    <div class="manage-actions-box">
                        <h3>Modify Booking</h3>
                        <button class="btn-action btn-modify"><i class="fa-solid fa-calendar-day"></i> Change Flight Date</button>
                        <button class="btn-action btn-addons"><i class="fa-solid fa-plus-circle"></i> Add Baggage / Meals</button>
                        <button class="btn-action btn-cancel"><i class="fa-solid fa-ban"></i> Cancel Booking</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="skyzen-footer">
        <div class="footer-bottom-bar">
            <div class="footer-bottom-container">
                <p>© Copyright <?= date('Y') ?> SkyZen Airlines</p>
            </div>
        </div>
    </footer>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../scripts/service.js"></script>
</body>
</html>