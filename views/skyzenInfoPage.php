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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Flight | SkyZen Airlines</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: var(--bg-cream);">

    <!-- REUSABLE PUBLIC NAVIGATION -->
    <nav class="skyzen-public-nav">
        <div class="skyzen-nav-container">
            <div class="skyzen-brand">
                <i class="fa-solid fa-plane"></i> SkyZen Airlines
            </div>
            <div class="skyzen-nav-links">
                <a href="skyzenMainPage.php">Book</a>
                <a href="skyzenManagePage.php">Manage</a>
                <a href="skyzenInfoPage.php" class="active">Travel Info</a>
                <a href="skyzenExplorePage.php">Explore</a>
                <a href="skyzenFAQsPage.php">Need Help?</a>
                <a href="skyzenLoginPage.php" class="skyzen-login-btn"><i class="fa fa-user-circle"></i> Log in</a>
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
                            <li><a href="#">Flights</a></li>
                            <li><a href="#">Seat Sale</a></li>
                            <li><a href="#">Passenger & Cargo Account Registration</a></li>
                            <li><a href="#">Sales & Group Bookings</a></li>
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
                            <li><a href="#">Check in</a></li>
                            <li><a href="#">Manage Booking</a></li>
                            <li><a href="#">Flight Status</a></li>
                            <li><a href="#">Add-ons</a></li>
                            <li><a href="#">Special Assistance</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>TRAVEL INFO</h4>
                        <ul>
                            <li><a href="#">Baggage Information</a></li>
                            <li><a href="#">Payment Options</a></li>
                            <li><a href="#">Travel Advisories</a></li>
                            <li><a href="#">Booking & Check-in</a></li>
                            <li><a href="#">Travel Documents</a></li>
                            <li><a href="#">Special Assistance</a></li>
                            <li><a href="#">COVID-19 Information</a></li>
                            <li><a href="#">Service Fees</a></li>
                            <li><a href="#">Add-ons</a></li>
                            <li><a href="#">Flight Status</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>EXPLORE</h4>
                        <ul>
                            <li><a href="#">Explore</a></li>
                            <li><a href="#">Philippine Destinations</a></li>
                            <li><a href="#">International Destinations</a></li>
                            <li><a href="#">Discover with Smile</a></li>
                            <li><a href="#">Where We Fly</a></li>
                            <li><a href="#">City Guides</a></li>
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
                            <img src="https://cdn.media.amplience.net/i/cebupacificair/huaweiappgallery_black?fmt=auto&maxW=1920&maxH=1920" alt="AppGallery">
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
                        <a href="#">Privacy and Cookie Policy</a>
                        <a href="#">Website Terms of Use</a>
                        <a href="#">Security</a>
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