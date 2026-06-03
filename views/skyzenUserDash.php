<?php
session_start();
require_once "../model/database_airlines.php";

$isLoggedIn = isset($_SESSION['user']);
$firstName = $isLoggedIn ? $_SESSION['user']['users_firstName'] : '';
$myBookings = [];
$upcomingTrips = [];
$pastTrips = [];
$userInfo = [];

if ($isLoggedIn) {
    if ((int)$_SESSION['user']['rolesID'] === 1) {
        header('Location: skyzenAdminDash.php');
        exit;
    }

    try {
        $pdo = (new Database())->connect();
        $userID = (int)$_SESSION['user']['usersID'];
        
        // Fetch current user details for the Edit Profile form
        $stmtUser = $pdo->prepare("SELECT * FROM tbl_users WHERE usersID = :uid");
        $stmtUser->execute([':uid' => $userID]);
        $userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);

        // Fetch flight history
        $stmtFlights = $pdo->prepare("
            SELECT b.bookings_pnrCode, b.bookings_amount, b.bookings_status, b.bookings_createdAt,
                   f.flightsNum, f.flights_originCode, f.flights_destinationCode, 
                   f.flights_departureTime, f.flights_arrivalTime
            FROM tbl_bookings b
            JOIN tbl_tickets t ON t.tickets_bookingsID = b.bookingsID
            JOIN tbl_flights f ON f.flightsID = t.tickets_flightID
            WHERE b.usersID = :uid
            ORDER BY f.flights_departureTime ASC
        ");
        $stmtFlights->execute([':uid' => $userID]);
        $allTrips = $stmtFlights->fetchAll(PDO::FETCH_ASSOC);

        // Sort trips into Upcoming and Past based on current date
        $currentDate = date('Y-m-d H:i:s');
        foreach ($allTrips as $trip) {
            if ($trip['flights_departureTime'] > $currentDate) {
                $upcomingTrips[] = $trip;
            } else {
                $pastTrips[] = $trip;
            }
        }

    } catch (PDOException $e) {
        die("<p style='color:red;'>Database Error.</p>");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | SkyZen Airlines</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

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

    <!-- DASHBOARD CONTENT -->
    <main class="skyzen-container" style="min-height: 60vh; padding: 40px 0;">
        <?php if($isLoggedIn): ?>
            
            <!-- VIEW 1: THE MAIN DASHBOARD -->
            <div id="dashboardView">
                <div class="sunlight-header-banner">
                    <h1>Welcome back, <?= htmlspecialchars($userInfo['users_firstName']) ?>!</h1>
                </div>

                <!-- Profile & Loyalty Cards -->
                <div class="sunlight-card-row">
                    <div class="sunlight-profile-card">
                        <div class="profile-info">
                            <div class="profile-avatar"><i class="fa-solid fa-user"></i></div>
                            <h2><?= htmlspecialchars($userInfo['users_firstName'] . ' ' . $userInfo['users_lastName']) ?></h2>
                        </div>
                        <button onclick="toggleProfileView(true)" class="view-profile-link">View Profile Details <i class="fa-solid fa-angle-right"></i></button>
                    </div>

                    <div class="sunlight-loyalty-card">
                        <h3 class="loyalty-title">Frequent Flyer Details</h3>
                        <div class="loyalty-inner">
                            <div class="loyalty-col">
                                <span class="loyalty-label">Account ID:</span>
                                <strong>SZ<?= str_pad($userInfo['usersID'], 8, "0", STR_PAD_LEFT) ?></strong>
                                <span class="loyalty-label" style="margin-top:10px;">Account Level:</span>
                                <strong style="color: #005A9C;">BLUE</strong>
                            </div>
                            <div class="loyalty-col text-center">
                                <i class="fa-solid fa-life-ring" style="font-size: 2rem; color: #333; margin-bottom: 5px;"></i>
                                <p style="margin:0; font-size: 0.9rem;">You have no points</p>
                                <a href="skyzenMainPage.php" style="font-size: 0.85rem; color: #005A9C; text-decoration: none;">Make a new booking</a>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="sunlight-section-title">My Bookings</h2>

                <!-- Upcoming Trips -->
                <h3 class="sunlight-subsection-title">Upcoming trips</h3>
                <div class="sunlight-trips-card">
                    <?php if (empty($upcomingTrips)): ?>
                        <div class="empty-trips">
                            <p>You have no upcoming trips</p>
                            <div class="empty-links">
                                <a href="skyzenBookingPage.php">Find a booking</a> | <a href="skyzenBookingPage.php">Manage your booking</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <table class="user-bookings-table" class="modern-table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>PNR</th>
                                    <th>Flight</th>
                                    <th>Route</th>
                                    <th>Departure</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingTrips as $trip): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($trip['bookings_pnrCode']) ?></strong></td>
                                        <td><?= htmlspecialchars($trip['flightsNum']) ?></td>
                                        <td><?= htmlspecialchars($trip['flights_originCode']) ?> ➔ <?= htmlspecialchars($trip['flights_destinationCode']) ?></td>
                                        <td><?= date('M j, Y g:i A', strtotime($trip['flights_departureTime'])) ?></td>
                                        <td><span class="status-badge status-confirmed"><?= htmlspecialchars($trip['bookings_status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Past Trips -->
                <h3 class="sunlight-subsection-title">Past trips</h3>
                <div class="sunlight-trips-card">
                    <?php if (empty($pastTrips)): ?>
                        <div class="empty-trips">
                            <p>You have no past trips</p>
                            <div class="empty-links">
                                <a href="skyzenMainPage.php">Find a booking</a> | <a href="skyzenBookingPage.php">Manage your booking</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <table class="user-bookings-table" class="modern-table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>PNR</th>
                                    <th>Route</th>
                                    <th>Departure</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pastTrips as $trip): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($trip['bookings_pnrCode']) ?></strong></td>
                                        <td><?= htmlspecialchars($trip['flights_originCode']) ?> ➔ <?= htmlspecialchars($trip['flights_destinationCode']) ?></td>
                                        <td><?= date('M j, Y', strtotime($trip['flights_departureTime'])) ?></td>
                                        <td><span class="status-badge" style="background:#E2E8F0; color:#475569;">Completed</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Recommendations -->
                <h2 class="sunlight-section-title">You may be interested in:</h2>
                <div class="sunlight-recommendations">
                    <div class="rec-card">
                        <img src="https://images.unsplash.com/photo-1612282130134-4b68453483df?q=80&w=400&auto=format&fit=crop" alt="Check In">
                        <a href="skyzenCheckInPage.php">Check-in <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                    <div class="rec-card">
                        <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=400&auto=format&fit=crop" alt="Book Flight">
                        <a href="skyzenMainPage.php">Book a new flight <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- VIEW 2: UPDATE PROFILE FORM (Hidden by default) -->
            <div id="profileView" style="display: none;">
                <h2 class="sunlight-section-title">Update Profile</h2>
                
                <div class="sunlight-profile-form-container">
                    <form id="userUpdateForm" onsubmit="event.preventDefault(); updateMyProfileFunc();">
                        <input type="hidden" id="upd_id" value="<?= $userInfo['usersID'] ?>">

                        <div class="form-row-custom">
                            <div class="form-group-custom">
                                <label>First Name *</label>
                                <input type="text" id="upd_fname" value="<?= htmlspecialchars($userInfo['users_firstName']) ?>" required>
                            </div>
                            <div class="form-group-custom">
                                <label>Middle Name</label>
                                <input type="text" id="upd_mname" value="<?= htmlspecialchars($userInfo['users_middleName']) ?>" maxlength="5">
                            </div>
                        </div>

                        <div class="form-row-custom">
                            <div class="form-group-custom">
                                <label>Last Name *</label>
                                <input type="text" id="upd_lname" value="<?= htmlspecialchars($userInfo['users_lastName']) ?>" required>
                            </div>
                            <div class="form-group-custom">
                                <label>Date of Birth *</label>
                                <input type="date" id="upd_birthday" value="<?= htmlspecialchars($userInfo['users_birthday']) ?>" required>
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label>Loyalty ID (Read Only)</label>
                            <input type="text" value="SZ<?= str_pad($userInfo['usersID'], 8, "0", STR_PAD_LEFT) ?>" disabled style="background-color: #E2E8F0; cursor: not-allowed;">
                        </div>

                        <div class="form-row-custom">
                            <div class="form-group-custom">
                                <label>Email Address *</label>
                                <input type="email" id="upd_email" value="<?= htmlspecialchars($userInfo['users_email']) ?>" required>
                            </div>
                            <div class="form-group-custom">
                                <label>Mobile Number *</label>
                                <input type="text" id="upd_phone" value="<?= htmlspecialchars($userInfo['users_phoneNum']) ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                        </div>

                        <div class="form-row-custom">
                            <div class="form-group-custom">
                                <label>Username *</label>
                                <input type="text" id="upd_username" value="<?= htmlspecialchars($userInfo['users_username']) ?>" required>
                            </div>
                        <div class="form-row-custom" style="align-items: flex-start;">
                            <div class="form-group-custom">
                                <label>New Password (Leave blank to keep current)</label>
                                <div class="input-group" style="border: 1px solid #CBD5E1; border-radius: 6px; display: flex;">
                                    <input type="password" id="upd_password" class="form-control" style="border: none; flex: 1;" placeholder="••••••••" oninput="checkUpdatePasswordReqs(this.value)">
                                    <button type="button" class="toggle-password" style="border: none; background: transparent; padding: 0 15px; cursor: pointer;" onclick="togglePass('upd_password', 'updEye1')">
                                        <i class="fa-solid fa-eye" id="updEye1"></i>
                                    </button>
                                </div>
                                
                                <ul class="password-reqs" id="upd-password-reqs" style="display: none; margin-top: 10px;">
                                    <li id="upd-req-length"><i class="fa-solid fa-xmark"></i> At least 8 characters</li>
                                    <li id="upd-req-upper"><i class="fa-solid fa-xmark"></i> At least 1 uppercase</li>
                                    <li id="upd-req-lower"><i class="fa-solid fa-xmark"></i> At least 1 lowercase</li>
                                    <li id="upd-req-number"><i class="fa-solid fa-xmark"></i> At least 1 number</li>
                                    <li id="upd-req-special"><i class="fa-solid fa-xmark"></i> At least 1 underscore (_)</li>
                                </ul>
                            </div>

                            <div class="form-group-custom">
                                <label>Confirm New Password</label>
                                <div class="input-group" style="border: 1px solid #CBD5E1; border-radius: 6px; display: flex;">
                                    <input type="password" id="upd_confirm_password" class="form-control" style="border: none; flex: 1;" placeholder="••••••••">
                                    <button type="button" class="toggle-password" style="border: none; background: transparent; padding: 0 15px; cursor: pointer;" onclick="togglePass('upd_confirm_password', 'updEye2')">
                                        <i class="fa-solid fa-eye" id="updEye2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions-custom">
                            <button type="submit" class="btn-update">Update</button>
                        </div>
                    </form>
                </div>
                
                <button onclick="toggleProfileView(false)" class="btn-back">Back</button>
            </div>

        <?php else: ?>
            <!-- GUEST: INVITE TO LOG IN -->
            <div style="text-align: center; padding: 100px 20px; background: white; border-radius: 12px; border: 1px solid #E2E8F0;">
                <i class="fa-solid fa-earth-americas" style="font-size: 4rem; color: #005A9C; margin-bottom: 20px;"></i>
                <h1 style="color: var(--nav-dark);">Want to explore the world?</h1>
                <p style="color: #666; margin-bottom: 30px;">Log in to access your dashboard, manage bookings, and view exclusive offers.</p>
                <a href="skyzenLoginPage.php" class="skyzen-submit-btn" style="width: 200px; display: inline-block; background-color: #005A9C;">Log in</a>
            </div>
        <?php endif; ?>
    </main>

    <!-- FOOTER -->
    <footer class="skyzen-footer">
        <div class="footer-bottom-bar">
            <div class="footer-bottom-container">
                <p>© Copyright <?= date('Y') ?> SkyZen Airlines</p>
            </div>
        </div>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../scripts/service.js"></script>
    <script>
        // Turn the basic HTML table into a beautiful DataTable!
        $(document).ready(function() {
            if ($('#user-bookings-table').length > 0) {
                $('#user-bookings-table').DataTable({
                    responsive: true,
                    pageLength: 5, // Show 5 bookings per page
                    language: {
                        search: "Search Bookings:"
                    }
                });
            }
        });
    </script>
</body>
</html>