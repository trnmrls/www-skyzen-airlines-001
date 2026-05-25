<?php
session_start();
require_once "../model/database_airlines.php";

// 1. Initialize default states for our variables
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
    <title>My Dashboard | SkyZen Airlines</title>
    <!-- Utilizing the unified CSS architecture -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <a href="skyzenMainPage.php" class="skyzen-login-btn">
                        <i class="fa fa-user-circle"></i> Log in
                    </a>
                <?php endif; ?>
        </div>
    </nav>

<!-- MAIN DASHBOARD CONTENT -->
    <main class="skyzen-container" style="min-height: 60vh;">
        
        <?php if($isLoggedIn): ?>
            <!-- ==========================================
                 AUTHENTICATED STATE: SHOW BOOKINGS
            ========================================== -->
            <div class="dashboard-header">
                <h1 style="margin: 0 0 10px 0; font-size: 2.5rem; font-weight: 800;">Welcome aboard, <?= $firstName ?>!</h1>
                <p style="margin: 0; font-size: 1.1rem; opacity: 0.9;">Manage your upcoming flights and review your travel history.</p>
            </div>

            <div style="margin: 40px 0;">
                <a href="skyzenHome.php" class="skyzen-submit-btn" style="text-decoration: none; display: inline-block; text-align: center; line-height: 45px; width: auto;"><i class="fa-solid fa-magnifying-glass"></i> Search New Flights</a>
            </div>

            <div class="booking-card">
                <h3 style="margin: 0 0 15px 0; color: var(--nav-dark); font-size: 1.5rem;"><i class="fa-solid fa-ticket" style="color: var(--theme-green);"></i> My Itineraries</h3>
                
                <div style="overflow-x: auto;">
                    <?php if (!empty($myBookings)): ?>
                        <table class="skyzen-table">
                            <thead>
                                <tr>
                                    <th>PNR Code</th>
                                    <th>Flight No.</th>
                                    <th>Route</th>
                                    <th>Departure Time</th>
                                    <th>Total Fare</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myBookings as $b): ?>
                                    <?php
                                        // Assign the CSS class based on the database status
                                        $badgeClass = match(strtolower($b['bookings_status'])) {
                                            'confirmed' => 'status-badge status-confirmed',
                                            'cancelled' => 'status-badge status-cancelled',
                                            default     => 'status-badge status-pending'
                                        };
                                    ?>
                                    <tr>
                                        <td><strong style="color: var(--theme-green);"><?= htmlspecialchars($b['bookings_pnrCode']) ?></strong></td>
                                        <td><i class="fa-solid fa-plane"></i> <?= htmlspecialchars($b['flightsNum']) ?></td>
                                        <td>
                                            <?= htmlspecialchars($b['flights_originCode']) ?> 
                                            <i class="fa-solid fa-arrow-right" style="color: #94A3B8; margin: 0 8px;"></i> 
                                            <?= htmlspecialchars($b['flights_destinationCode']) ?>
                                        </td>
                                        <td><?= date('F j, Y - g:i A', strtotime($b['flights_departureTime'])) ?></td>
                                        <td>₱<?= number_format($b['bookings_amount'], 2) ?></td>
                                        <td>
                                            <span class="<?= $badgeClass ?>"><?= htmlspecialchars($b['bookings_status']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div style="text-align: center; padding: 50px 20px; color: #94A3B8;">
                            <i class="fa-solid fa-suitcase-rolling" style="font-size: 3rem; margin-bottom: 15px; color: #E2E8F0;"></i>
                            <h4>No flights booked yet</h4>
                            <p>Your adventures await! Search for a flight to get started.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php else: ?>
            <!-- ==========================================
                 GUEST STATE: SHOW CALL TO ACTION
            ========================================== -->
            <div style="text-align: center; padding: 100px 20px; max-width: 600px; margin: 0 auto;">
                <i class="fa-solid fa-earth-americas" style="font-size: 5rem; color: var(--theme-green); margin-bottom: 25px;"></i>
                <h1 style="color: var(--nav-dark); font-size: 2.5rem; font-weight: 800; margin-bottom: 15px;">Want to explore the world?</h1>
                <p style="color: #64748B; font-size: 1.1rem; line-height: 1.6; margin-bottom: 35px;">
                    Log in to view your personalized travel itineraries, manage your upcoming flights, and discover exclusive seat sales tailored just for you.
                </p>
                <a href="skyzenLoginPage.php" class="skyzen-submit-btn" style="text-decoration: none; display: inline-block; text-align: center; line-height: 45px; width: 250px; font-size: 1.1rem;">
                    Log in
                </a>
                <div style="margin-top: 20px;">
                    <span style="color: #94A3B8;">Don't have an account? </span>
                    <a href="skyzenRegisPage.php" style="color: var(--theme-green); font-weight: 700; text-decoration: none;">Create one</a>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <!-- CEBU PACIFIC INSPIRED FOOTER -->
    <footer class="skyzen-footer">
        <div class="footer-top-container">
            <div class="footer-links-grid">
                <div class="footer-col">
                    <h4>BOOK</h4>
                    <ul>
                        <li><a href="#">Flights</a></li>
                        <li><a href="#">Seat Sale</a></li>
                        <li><a href="#">Passenger Account</a></li>
                        <li><a href="#">Sales & Group Bookings</a></li>
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
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>TRAVEL INFO</h4>
                    <ul>
                        <li><a href="#">Baggage Information</a></li>
                        <li><a href="#">Payment Options</a></li>
                        <li><a href="#">Travel Advisories</a></li>
                        <li><a href="#">Booking & Check-in</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>EXPLORE</h4>
                    <ul>
                        <li><a href="#">Explore</a></li>
                        <li><a href="#">Philippine Destinations</a></li>
                        <li><a href="#">International Destinations</a></li>
                        <li><a href="#">Discover with Smile</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>ABOUT</h4>
                    <ul>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Our Story</a></li>
                        <li><a href="#">Media Center</a></li>
                        <li><a href="#">Talk to Us</a></li>
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
                        <img src="https://cdn.media.amplience.net/i/cebupacificair/PayPal-logo?fmt=auto&maxW=1920&maxH=1920&h=80&qlt=100" alt="PayPal">
                        <img src="https://cdn.media.amplience.net/i/cebupacificair/GCash-276x96?fmt=auto&maxW=1920&maxH=1920&h=80&qlt=100" alt="GCash">
                    </div>
                </div>
                <div class="logo-group">
                    <h4>MEMBERSHIPS</h4>
                    <div class="accreditation-badges">
                        <img src="https://cdn.media.amplience.net/i/cebupacificair/CEB-7Star-Emblem?fmt=auto&maxW=240&maxH=240&h=80&qlt=100" alt="7 Star">
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom-bar">
            <div class="footer-bottom-container">
                <div class="footer-legal">
                    <a href="#">Privacy and Cookie Policy</a>
                    <a href="#">Website Terms of Use</a>
                    <a href="#">Security</a>
                </div>
                <div class="footer-copyright">
                    <p>© Copyright <?= date('Y') ?> SkyZen Airlines</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Include jQuery and Custom Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../scripts/service.js"></script>
</body>
</html>