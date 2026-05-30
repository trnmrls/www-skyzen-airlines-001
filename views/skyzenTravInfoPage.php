<?php
    session_start();
    require_once "../bl/userManagement.php";
    $userManagement = new UserManagement();
    
    // 1. Capture and sanitize the search parameters from the URL
    $origin = isset($_GET['origin']) ? strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $_GET['origin'])) : '';
    $dest = isset($_GET['dest']) ? strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $_GET['dest'])) : '';
    if (isset($_GET['dep_date'])) {
        $date = DateTime::createFromFormat('Y-m-d', $_GET['dep_date']);
        $dep_date = ($date && $date->format('Y-m-d') === $_GET['dep_date']) ? $_GET['dep_date'] : '';
    } else {
        $dep_date = '';
    }
    $pax = isset($_GET['pax']) ? max(1, (int)$_GET['pax']) : 1;
    
    // 2. Fetch matching flights from the database
    $flights = [];
    if (!empty($origin) && !empty($dest) && !empty($dep_date)) {
        $flights = $userManagement;
    }

    $isLoggedIn = false;
$myBookings = [];
$firstName = '';

// 2. Evaluate the user's session state safely
if (isset($_SESSION['user'])) {
    
    // If an Admin accidentally navigates here, safely route them to their proper workspace
    if ((int)$_SESSION['user']['rolesID'] === 1) {
        header('Location: skyzenAdminDash.php');
        exit;
    }

    // The user is a valid customer, so we update our application state
    $isLoggedIn = true;
    $firstName = htmlspecialchars($_SESSION['user']['users_firstName']);

    // 3. Fetch the secure data ONLY because we verified the user's identity
    try {
        $pdo    = (new Database())->connect();
        $userID = (int)$_SESSION['user']['usersID'];
        
        $stmt = $pdo->prepare("
            SELECT b.bookings_pnrCode,
                   b.bookings_amount,
                   b.bookings_status,
                   b.bookings_createdAt,
                   f.flightsNum,
                   f.flights_originCode,
                   f.flights_destinationCode,
                   f.flights_departureTime,
                   f.flights_arrivalTime
            FROM   tbl_bookings b
            JOIN   tbl_tickets  t ON t.tickets_bookingsID = b.bookingsID
            JOIN   tbl_flights  f ON f.flightsID = t.tickets_flightID
            WHERE  b.usersID = :uid
            ORDER  BY b.bookings_createdAt DESC
        ");
        $stmt->execute([':uid' => $userID]);
        $myBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        // Graceful error handling in case the database connection fails
        die("<p style='color:red;padding:20px'>Database Error: " . htmlspecialchars($e->getMessage()) . "</p>");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Information | SkyZen Airlines</title>
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

    
        <!-- FOOTER -->
     <!-- CEBU PACIFIC INSPIRED FOOTER -->
        <footer class="skyzen-footer">
            <div class="footer-top-container">
                <div class="footer-links-grid">
                    <div class="footer-col">
                        <h4>BOOK</h4>
                        <ul>
                            <li><a href="#">Flight Offers</a></li>
                            <li><a href="#">Seat Sale</a></li>
                            <li><a href="#">Partner Agents</a></li>
                        </ul>
                        <div class="country-selector">
                            <h4>SELECT COUNTRY</h4>
                            <div class="selector-box">
                                <img src="https://cdn.media.amplience.net/i/cebupacificair/philippines-flag-round-icon-32" alt="PH"> Philippines <i class="fa-solid fa-globe"></i>
                            </div>
                        </div>
                    </div>
                    <div class="footer-col">
                        <h4>MANAGE</h4>
                        <ul>
                            <li><a href="skyzenCheckInPage.php">Check in</a></li>
                            <li><a href="skyzenBookingPage.php">Manage Booking</a></li>
                            <li><a href="skyzenFlightsPage.php">Flight Status</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>TRAVEL</h4>
                        <ul>
                            <li><a href="skyzenTravInfoPage.php">Travel Information</a></li>
                            <li><a href="skyzenBagInfoPage.php">Baggage Information</a></li>
                            <li><a href="skyzenFleetPage.php">Fleet & Cabin</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>EXPLORE</h4>
                        <ul>
                            <li><a href="skyzenPHDestPage.php">Philippine Destinations</a></li>
                            <li><a href="skyzenIntlDestPage.php">International Destinations</a></li>
                            <li><a href="skyzenAirportsPage.php">Where We Fly</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>NEED HELP?</h4>
                        <ul>
                            <li><a href="skyzenContactPage.php">Contact Us</a></li>
                            <li><a href="skyzenFAQsPage.php">FAQs</a></li>
                            <li><a href="#">Feedback</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>ABOUT</h4>
                        <ul>
                            <li><a href="#">About</a></li>
                            <li><a href="#">Our Story</a></li>
                            <li><a href="#">Media Center</a></li>
                            <li><a href="#">Talk to Us</a></li>
                            <li><a href="#">Campaign & Partners</a></li>
                            <li><a href="#">Company Information</a></li>
                            <li><a href="#">Careers</a></li>
                        </ul>
                        <div class="social-connect">
                            <h4>CONNECT WITH US</h4>
                            <div class="social-icons">
                                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                                <a href="#"><i class="fa-brands fa-youtube"></i></a>
                                <a href="#"><i class="fa-brands fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer-logos-col">
                    <div class="logo-group">
                        <h4>DOWNLOAD THE SKYZEN APP</h4>
                        <div class="app-badges">
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/AppStore-4800x1424?fmt=auto&maxW=1920&maxH=1920" alt="App Store">
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/GooglePlay-4800x1416?fmt=auto&maxW=1920&maxH=1920" alt="Google Play">
                        </div>
                    </div>
                    <div class="logo-group">
                        <h4>PAYMENT PARTNERS</h4>
                        <div class="payment-badges">
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/Visa-logo?fmt=auto&maxW=1920&maxH=1920&h=80&qlt=100" alt="Visa">
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/Mastercard_logo-128x80?fmt=auto&maxW=1920&maxH=1920&h=80&qlt=100" alt="Mastercard">
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/Amex_2-128x84?fmt=auto&maxW=1920&maxH=1920&h=80&qlt=100" alt="Amex">
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/jcb-logo_1?fmt=auto&maxW=1920&maxH=1920&h=80&qlt=100" alt="JCB">
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/PayPal-logo?fmt=auto&maxW=1920&maxH=1920&h=80&qlt=100" alt="PayPal">
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/GCash-276x96?fmt=auto&maxW=1920&maxH=1920&h=80&qlt=100" alt="GCash">
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/GrabPay-244x92?fmt=auto&maxW=1920&maxH=1920&h=80&qlt=100" alt="GrabPay">
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/Maya-res?fmt=auto&maxW=1920&maxH=1920&h=80&qlt=100" alt="Maya">
                        </div>
                    </div>
                    <div class="logo-group">
                        <h4>MEMBERSHIPS AND ACCREDITATIONS</h4>
                        <div class="accreditation-badges">
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/CEB-7Star-Emblem?fmt=auto&maxW=240&maxH=240&h=80&qlt=100" alt="7 Star">
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/New_NPC_Logo?fmt=auto&maxW=1920&maxH=1920&h=80&qlt=100" alt="NPC">
                        </div>
                    </div>
                </div>
            </div>

            <!-- The vibrant bottom bar -->
            <div class="footer-bottom-bar">
                <div class="footer-bottom-container">
                    <div class="footer-legal">
                        <<a href="skyzenTermsPage.html">Privacy and Cookie Policy</a>
                        <a href="skyzenTermsPage.html">Website Terms of Use</a>
                        <a href="skyzenTermsPage.html">Security</a>
                        <a href="#">Accessibility</a>
                        <a href="#">Site Map</a>
                    </div>
                    <div class="footer-copyright">
                        <p>© Copyright <?= date('Y') ?> SkyZen Airlines</p>
                    </div>
                </div>
            </div>
        </footer>
    
    <script src="../scripts/service.js"></script>
</body>
</html>