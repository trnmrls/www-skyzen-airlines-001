<?php
    session_start();
    require_once "../bl/userManagement.php";
    $userManagement = new userManagement();
    
    $airports = [];
    if (method_exists($userManagement, 'getAirports')) {
        $airports = $userManagement->getAirports();
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
    <title>Book Flights | SkyZen Airlines</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- PUBLIC NAVIGATION -->
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
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-plane"></i></div>
                                Flights
                            </a>
                            <a href="#" class="mega-icon-link">
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
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-location-dot"></i></div>
                                Check in
                            </a>
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-file-signature"></i></div>
                                Manage Booking
                            </a>
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-plane-circle-check"></i></div>
                                Flight Status
                            </a>
                        </div>
                    </div>
                </li>
                <li class="has-mega-menu">
                    <a href="#">Travel Info</a>
                    <div class="mega-menu">
                        <div class="mega-menu-top">
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-info-circle"></i></div>
                                Travel Guides
                            </a>
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-calendar-days"></i></div>
                                Travel Tips
                            </a>
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-globe"></i></div>
                                Destinations
                            </a>
                        </div>
                    </div>
                </li>
                <li class="has-mega-menu">
                    <a href="#">Explore</a>
                    <div class="mega-menu">
                        <div class="mega-menu-top">
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-location-dot"></i></div>
                                Philippine Destinations
                            </a>
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-plane"></i></div>
                                International Destinations
                            </a>
                            <a href="#" class="mega-icon-link">
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
                            <a href="#" class="mega-icon-link">
                                <div class="icon-circle"><i class="fa-solid fa-headset"></i></div>
                                Contact Us
                            </a>
                            <a href="#" class="mega-icon-link">
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

    <main class="skyzen-home-main">
        <!-- HERO SECTION & SEARCH WIDGET -->
        <div class="skyzen-hero" id="heroSlider">
            <div class="skyzen-hero-overlay"></div>
            
            <div class="skyzen-search-container">
                <h1 class="skyzen-hero-title">More flights, more adventures.</h1>
                
                <div class="skyzen-search-widget">
                    <div class="skyzen-widget-tabs">
                        <button type="button" class="skyzen-tab active" onclick="setTripType('round', this)">Round-trip</button>
                        <button type="button" class="skyzen-tab" onclick="setTripType('one', this)">One-way</button>
                        <button type="button" class="skyzen-tab" onclick="setTripType('multi', this)">Multi-city</button>
                    </div>

                    <form action="skyzenSelectFlight.php" method="GET" class="skyzen-widget-form">
                        <div class="skyzen-form-grid">
                            <div class="skyzen-input-group">
                                <label>Origin</label>
                                <div class="skyzen-input-wrapper">
                                    <i class="fa-solid fa-plane-departure"></i>
                                    <select name="origin" id="searchOrigin" required>
                                        <option value="" disabled selected>Where from?</option>
                                        <?php foreach($airports as $apt): ?>
                                            <option value="<?= htmlspecialchars($apt['airportsCode']) ?>"><?= htmlspecialchars($apt['airportsName']) ?> (<?= htmlspecialchars($apt['airportsCode']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <button type="button" class="skyzen-swap-btn" onclick="swapAirports()"><i class="fa-solid fa-right-left"></i></button>

                            <div class="skyzen-input-group">
                                <label>Destination</label>
                                <div class="skyzen-input-wrapper">
                                    <i class="fa-solid fa-plane-arrival"></i>
                                    <select name="dest" id="searchDest" required>
                                        <option value="" disabled selected>Where to?</option>
                                        <?php foreach($airports as $apt): ?>
                                            <option value="<?= htmlspecialchars($apt['airportsCode']) ?>"><?= htmlspecialchars($apt['airportsName']) ?> (<?= htmlspecialchars($apt['airportsCode']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="skyzen-input-group">
                                <label>Depart</label>
                                <div class="skyzen-input-wrapper">
                                    <i class="fa-regular fa-calendar"></i>
                                    <input type="date" name="dep_date" min="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>

                            <div class="skyzen-input-group" id="returnDateGroup">
                                <label>Return</label>
                                <div class="skyzen-input-wrapper">
                                    <i class="fa-regular fa-calendar-check"></i>
                                    <input type="date" name="ret_date" id="returnDate" min="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>

                            <!-- Passengers -->
                            <div class="skyzen-input-group">
                                <label>Passengers</label>
                                <div class="skyzen-input-wrapper">
                                    <i class="fa-solid fa-user-group"></i>
                                    <select name="pax" required>
                                        <option value="1">1 Pax(s)</option>
                                        <option value="2">2 Pax(s)</option>
                                        <option value="3">3 Pax(s)</option>
                                        <option value="4">4 Pax(s)</option>
                                        <option value="5">5+ Pax(s)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="skyzen-widget-footer">
                            <div class="skyzen-promo-wrapper">
                                <i class="fa-solid fa-tag"></i>
                                <input type="text" name="promo" placeholder="Promo Code">
                            </div>
                            <button type="submit" class="skyzen-submit-btn">Search flights</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- FEATURES SECTION (c-everyoneflies) -->
        <section class="skyzen-features-section">
            <div class="skyzen-container">
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fa-solid fa-check-to-slot"></i></div>
                        <h4>Check In</h4>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fa-solid fa-ticket"></i></div>
                        <h4>Super Pass</h4>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fa-solid fa-plane-circle-check"></i></div>
                        <h4>Flight Status</h4>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fa-solid fa-pen-to-square"></i></div>
                        <h4>Manage Booking</h4>
                    </div>
                </div>
            </div>
        </section>

        <!-- CHEAP FLIGHTS PROMO SECTION (c-cheap-flights) -->
        <section class="skyzen-promo-section">
            <div class="skyzen-container">
                <h2>Book cheap flights from</h2>
                <div class="promo-tabs">
                    <button class="active">Manila</button>
                    <button>Cebu</button>
                    <button>Davao</button>
                    <button>Clark</button>
                </div>
                
                <div class="promo-cards-grid">
                    <!-- Promo Card 1 -->
                    <div class="promo-card">
                        <img src="https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?q=80&w=600&auto=format&fit=crop" alt="Cebu">
                        <div class="promo-details">
                            <span class="promo-label">For as low as</span>
                            <h3 class="promo-price">₱388*</h3>
                            <span class="promo-dest">Cebu</span>
                            <button class="btn-book-now">Book now</button>
                        </div>
                    </div>
                    <!-- Promo Card 2 -->
                    <div class="promo-card">
                        <img src="https://images.unsplash.com/photo-1542296332-2e4473faf563?q=80&w=600&auto=format&fit=crop" alt="Boracay">
                        <div class="promo-details">
                            <span class="promo-label">For as low as</span>
                            <h3 class="promo-price">₱298*</h3>
                            <span class="promo-dest">Iloilo</span>
                            <button class="btn-book-now">Book now</button>
                        </div>
                    </div>
                    <!-- Promo Card 3 -->
                    <div class="promo-card">
                        <img src="https://images.unsplash.com/photo-1531804055935-76f44d22d204?q=80&w=600&auto=format&fit=crop" alt="Siargao">
                        <div class="promo-details">
                            <span class="promo-label">For as low as</span>
                            <h3 class="promo-price">₱799*</h3>
                            <span class="promo-dest">Laoag</span>
                            <button class="btn-book-now">Book now</button>
                        </div>
                    </div>
                </div>
                <div class="promo-disclaimer">*One-way base fares</div>
            </div>
        </section>

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
                        <a href="skyzenTermsPage.html">Privacy and Cookie Policy</a>
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