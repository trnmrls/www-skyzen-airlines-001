<?php
session_start();
require_once "../model/database_airlines.php";
require_once "../bl/userManagement.php";

$isLoggedIn = isset($_SESSION['user']);
$firstName = $isLoggedIn ? htmlspecialchars($_SESSION['user']['users_firstName']) : '';

$userManagement = new UserManagement();

// 1. Capture the search parameters from the URL (sent by skyzenMainPage.php)
$origin = $_GET['origin'] ?? '';
$dest = $_GET['dest'] ?? '';
$dep_date = $_GET['dep_date'] ?? '';
$pax = (int)($_GET['pax'] ?? 1);

// 2. Fetch matching flights from the database
$flights = [];
if (!empty($origin) && !empty($dest) && !empty($dep_date)) {
    // Make sure searchAvailableFlights exists in your userManagement.php!
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
    <title>Flight Offers | SkyZen Airlines</title>
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


    <div class="offers-hero">
        <div class="skyzen-container">
            <h1>Flight Offers</h1>
            <p>Soar to new heights of savings with our limited-time flight promotions.</p>
        </div>
    </div>


    <main class="skyzen-container" style="padding: 60px 20px;">
        <h2 class="promos-section-title">Ongoing / Upcoming Promotions</h2>
        
        <div class="offers-grid" style="margin-bottom: 80px;">

        <div class="offer-card">
                <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=600&auto=format&fit=crop" alt="AUB Promo">
                <div class="offer-card-body">
                    <h3>AUB Mastercard Credit Card Exclusive Offer</h3>
                    <p>Enjoy 10% OFF Value and Plus base fares on all SkyZen Air routes when you pay using your AUB Mastercard Credit Card.</p>
                    <p>Use promo code AUB + last 6 digits of your AUB credit card (e.g., AUB123456) at checkout to avail of the discount.</p>
                    <a href="#" class="btn-learn-more">Learn more</a>
                </div>
            </div>

            <div class="offer-card">
                <img src="https://images.unsplash.com/photo-1542296332-2e4473faf563?q=80&w=600&auto=format&fit=crop" alt="SkyZen Day">
                <div class="offer-card-body">
                    <h3>SkyZen Day Seat Sale</h3>
                    <p>Celebrate SkyZen Day with <strong>one-way base fares for as low as PHP 99</strong> across all SkyZen Air routes. Book on June 22, 2026 only and travel from July 1 to December 15, 2026.</p>
                    <a href="#" class="btn-learn-more">Learn more</a>
                </div>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #E2E8F0; margin-bottom: 60px;">

        <h2 class="promos-section-title">Past Promotions</h2>
        
        <div class="offers-grid past-grid">
            <div class="offer-card">
                <div class="past-promo-img" style="background-color: #D23A84;">
                    <h2>Mother's Day<br>SEAT SALE</h2>
                    <h3>Take 10% OFF</h3>
                </div>
                <div class="offer-card-body">
                    <h3>Mother's Day Seat Sale</h3>
                    <p>Celebrate Mom with a well-deserved getaway! Enjoy 10% OFF Value and PLUS Fares to all SkyZen Air destinations. Use promo code: MOM10. Book from May 10-12, 2026.</p>
                </div>
            </div>

            <div class="offer-card">
                <img src="https://images.unsplash.com/photo-1531804055935-76f44d22d204?q=80&w=600&auto=format&fit=crop" alt="5.5 Fare">
                <div class="offer-card-body">
                    <h3>5.5 Fare Specials</h3>
                    <p>Fly to your favorite SkyZen Air destination for as low as PHP 188 — no promo codes needed! Book from May 1-5, 2026 and travel from May 5 to November 30, 2026.</p>
                </div>
            </div>

            <div class="offer-card">
                <div class="past-promo-img" style="background-color: #005A9C;">
                    <h2>CEBU<br>Low Fare Festival</h2>
                    <h3>From PHP 188</h3>
                </div>
                <div class="offer-card-body">
                    <h3>Cebu Low Fare Festival</h3>
                    <p>The Cebu Low Fare Festival is here! Fly from Cebu to Coron, Siargao, and Siquijor with one-way base fares from PHP 188. Book from May 1-5, 2026.</p>
                </div>
            </div>
        </div>
    </main>

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