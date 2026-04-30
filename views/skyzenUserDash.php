<?php
session_start();
require_once "../model/database_airlines.php";

// DO NOT OPEN, IN PROGRESS: The final one is Admin Dashboard. This is just a placeholder for the user dashboard to test the bookings query and display.

if (!isset($_SESSION['user'])) {
    header('Location: skyzenLoginPage.php');
    exit;
}
if ((int)$_SESSION['user']['rolesID'] !== 2) {
    header('Location: adminDashboard.php');
    exit;
}

try {
    $pdo    = (new Database())->connect();
    $userID = (int)$_SESSION['user']['usersID'];

    $myBookings = $pdo->prepare("
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
        JOIN   tbl_flights  f ON f.flightsID           = t.tickets_flightID
        WHERE  b.usersID = :uid
        ORDER  BY b.bookings_createdAt DESC
    ");
    $myBookings->execute([':uid' => $userID]);
    $myBookings = $myBookings->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("<p style='color:red;padding:20px'>DB Error: " . htmlspecialchars($e->getMessage()) . "</p>");
}

$firstName = htmlspecialchars($_SESSION['user']['users_firstName']);
$fullName  = htmlspecialchars($_SESSION['user']['users_firstName'] . ' ' . $_SESSION['user']['users_lastName']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Dashboard | SkyZen Airlines</title>

    <!-- Notika Core CSS -->
    <link rel="stylesheet" href="../../green-horizotal/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../green-horizotal/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../green-horizotal/css/animate.css">
    <link rel="stylesheet" href="../../green-horizotal/css/wave/waves.min.css">
    <link rel="stylesheet" href="../../green-horizotal/css/notika-custom-icon.css">
    <link rel="stylesheet" href="../../green-horizotal/css/main.css">
    <link rel="stylesheet" href="../../green-horizotal/style.css">
    <link rel="stylesheet" href="../../green-horizotal/css/responsive.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .skyzen-welcome {
            background: linear-gradient(135deg, #00c292, #006b54);
            color: #fff;
            border-radius: 8px;
            padding: 28px 30px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .skyzen-welcome h3 { margin: 0; font-size: 22px; font-weight: 700; }
        .skyzen-welcome p  { margin: 6px 0 0; opacity: .8; font-size: 13px; }
        .skyzen-welcome .plane-icon { font-size: 52px; opacity: .25; }

        /* ── Search card ─────────────────────────────────────────────────── */
        .search-card {
            background: #fff;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
            margin-bottom: 24px;
        }
        .search-card h5 {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 16px;
            border-left: 4px solid #00c292;
            padding-left: 10px;
        }
        .search-card .form-control {
            border-radius: 4px;
            font-size: 13px;
            height: 38px;
        }
        .btn-search {
            background: #00c292;
            color: #fff;
            border: none;
            padding: 9px 22px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: background .2s;
        }
        .btn-search:hover { background: #00967a; }
        .btn-search i     { margin-right: 6px; }

        /* ── Results / Bookings table ─────────────────────────────────────── */
        .result-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
            margin-bottom: 24px;
        }
        .result-card h5 {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 14px;
            border-left: 4px solid #4c9be8;
            padding-left: 10px;
        }
        .skyzen-table { width: 100%; font-size: 13px; }
        .skyzen-table thead tr { background: #f5f5f5; }
        .skyzen-table thead th { font-weight: 600; padding: 10px 12px; color: #555; }
        .skyzen-table tbody td { padding: 9px 12px; border-top: 1px solid #f0f0f0; vertical-align: middle; }
        .skyzen-table tbody tr:hover { background: #fafffe; }

        .badge-status {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-confirmed { background: #e6f7ef; color: #1a7a4a; }
        .badge-pending   { background: #fff8e6; color: #9a6e00; }
        .badge-cancelled { background: #fdecea; color: #8b1a1a; }

        .btn-book {
            background: #00c292;
            color: #fff;
            border: none;
            padding: 5px 14px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
        }
        .btn-book:hover { background: #00967a; }

        /* ── Empty state ─────────────────────────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #aaa;
        }
        .empty-state i { font-size: 40px; margin-bottom: 12px; display: block; }

        .table-scroll { overflow-x: auto; }
    </style>
</head>
<body>
<!-- ── Top Navigation ──────────────────────────────────────────────────────── -->
<div class="nk-navigation top-navigation-style">
    <div class="nk-nav-inner clearfix">
        <div class="nk-nav-left clearfix">
            <div class="nk-logo-box">
                <a href="userDashboard.php" style="color:#00c292; font-weight:700; font-size:16px;">
                    ✈ SkyZen Airlines
                </a>
            </div>
            <div class="nk-toggle-icon">
                <a href="#"><i class="fa fa-bars"></i></a>
            </div>
        </div>
        <div class="nk-nav-right clearfix">
            <ul class="nk-nav-items">
                <li class="nk-user-tp">
                    <a href="#">
                        <span class="nk-user-img">
                            <i class="fa fa-user-circle" style="font-size:26px; color:#00c292;"></i>
                        </span>
                        <span class="nk-user-tp-nm"><?= $fullName ?></span>
                    </a>
                    <div class="nk-user-tp-drop">
                        <a href="../controllers/userController.php?logout=1">
                            <i class="fa fa-sign-out"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- ── Main Wrapper ────────────────────────────────────────────────────────── -->
<div class="nk-df-container nk-df-boxed clearfix">

    <!-- ── Sidebar ──────────────────────────────────────────────────────────── -->
    <div class="nk-sidebar">
        <div class="nk-nav-scroll">
            <ul class="nk-menu">
                <li class="nk-menu-category">My Portal</li>

                <li class="nk-menu-list active-page">
                    <a href="userDashboard.php">
                        <span class="nk-menu-arrow"><i class="fa fa-angle-right"></i></span>
                        <i class="fa fa-home"></i>
                        <span>Dashboard. DO NOT OPEN, IN PROGRESS: The final one is Admin Dashboard. This is just a placeholder for the user dashboard to test the bookings query and display.
</span>
                    </a>
                </li>

                <li class="nk-menu-list">
                    <a href="#search-section"
                       onclick="document.getElementById('search-section').scrollIntoView({behavior:'smooth'})">
                        <span class="nk-menu-arrow"><i class="fa fa-angle-right"></i></span>
                        <i class="fa fa-search"></i>
                        <span>Search Flights</span>
                    </a>
                </li>

                <li class="nk-menu-list">
                    <a href="#bookings-section"
                       onclick="document.getElementById('bookings-section').scrollIntoView({behavior:'smooth'})">
                        <span class="nk-menu-arrow"><i class="fa fa-angle-right"></i></span>
                        <i class="fa fa-ticket"></i>
                        <span>My Bookings</span>
                    </a>
                </li>

                <li class="nk-menu-category">Account</li>

                <li class="nk-menu-list">
                    <a href="../controllers/userController.php?logout=1">
                        <span class="nk-menu-arrow"><i class="fa fa-angle-right"></i></span>
                        <i class="fa fa-sign-out"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- ── Main Content ──────────────────────────────────────────────────────── -->
    <div class="nk-content">
        <div class="container-fluid">

            <!-- Welcome Banner -->
            <div class="skyzen-welcome">
                <div>
                    <h3>Welcome back, <?= $firstName ?>! ✈</h3>
                    <p>Ready for your next flight? Search for available routes below.</p>
                </div>
                <i class="fa fa-plane plane-icon"></i>
            </div>

            <!-- ── Flight Search ───────────────────────────────────────────── -->
            <div class="search-card" id="search-section">
                <h5><i class="fa fa-search"></i> &nbsp;Search Available Flights</h5>
                <div class="row">
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label style="font-size:12px; color:#888;">From (Airport Code)</label>
                            <input type="text" id="search_origin"
                                   class="form-control" placeholder="e.g. MNL" maxlength="10">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label style="font-size:12px; color:#888;">To (Airport Code)</label>
                            <input type="text" id="search_destination"
                                   class="form-control" placeholder="e.g. CEB" maxlength="10">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label style="font-size:12px; color:#888;">Departure Date</label>
                            <input type="date" id="search_date" class="form-control"
                                   min="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3" style="display:flex; align-items:flex-end; padding-bottom:15px;">
                        <button class="btn-search" onclick="searchFlights()" style="width:100%;">
                            <i class="fa fa-search"></i> Search Flights
                        </button>
                    </div>
                </div>
            </div>

            <!-- ── Search Results ─────────────────────────────────────────── -->
            <div class="result-card" id="results-section" style="display:none;">
                <h5><i class="fa fa-plane"></i> &nbsp;Available Flights</h5>
                <div class="table-scroll" id="flightResultsTable"></div>
            </div>

            <!-- ── My Bookings ────────────────────────────────────────────── -->
            <div class="result-card" id="bookings-section">
                <h5><i class="fa fa-ticket"></i> &nbsp;My Bookings</h5>
                <div class="table-scroll">
                <?php if (!empty($myBookings)): ?>
                    <table class="skyzen-table">
                        <thead>
                            <tr>
                                <th>PNR Code</th>
                                <th>Flight No.</th>
                                <th>Route</th>
                                <th>Departure</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($myBookings as $b): ?>
                            <?php
                                $badgeClass = match(strtolower($b['bookings_status'])) {
                                    'confirmed' => 'badge-confirmed',
                                    'cancelled' => 'badge-cancelled',
                                    default     => 'badge-pending'
                                };
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($b['bookings_pnrCode']) ?></strong></td>
                                <td><?= htmlspecialchars($b['flightsNum']) ?></td>
                                <td>
                                    <?= htmlspecialchars($b['flights_originCode']) ?>
                                    <i class="fa fa-long-arrow-right" style="color:#00c292; margin:0 4px;"></i>
                                    <?= htmlspecialchars($b['flights_destinationCode']) ?>
                                </td>
                                <td><?= date('M j, Y g:i A', strtotime($b['flights_departureTime'])) ?></td>
                                <td>₱<?= number_format($b['bookings_amount'], 2) ?></td>
                                <td>
                                    <span class="badge-status <?= $badgeClass ?>">
                                        <?= htmlspecialchars($b['bookings_status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa fa-ticket"></i>
                        <p>You have no bookings yet.</p>
                        <p style="font-size:12px;">Search for a flight above to get started!</p>
                    </div>
                <?php endif; ?>
                </div>
            </div>

            <div style="height:40px;"></div>
        </div><!-- /.container-fluid -->
    </div><!-- /.nk-content -->
</div>

<script src="../../green-horizotal/js/vendor/jquery-1.12.4.min.js"></script>
<script src="../../green-horizotal/js/bootstrap.min.js"></script>
<script src="../../green-horizotal/js/wave/waves.min.js"></script>
<script src="../../green-horizotal/js/wave/wave-active.js"></script>

<script>
function searchFlights() {
    var origin      = document.getElementById('search_origin').value.trim().toUpperCase();
    var destination = document.getElementById('search_destination').value.trim().toUpperCase();
    var date        = document.getElementById('search_date').value;

    if (!origin || !destination) {
        Swal.fire({ icon: 'warning', title: 'Missing Fields', text: 'Please enter both origin and destination.' });
        return;
    }

    $.ajax({
        url:  '../controllers/flightController.php',
        type: 'POST',
        data: { action: 'searchFlights', origin: origin, destination: destination, date: date },
        success: function(response) {
            var flights = (typeof response === 'string') ? JSON.parse(response) : response;
            renderFlightResults(flights);
        },
        error: function(xhr) {
            Swal.fire({ icon: 'error', title: 'Search Failed', text: 'Could not reach the flights API.' });
        }
    });
}

function renderFlightResults(flights) {
    var container = document.getElementById('flightResultsTable');
    document.getElementById('results-section').style.display = 'block';

    if (!flights || flights.length === 0) {
        container.innerHTML = '<div class="empty-state"><i class="fa fa-plane"></i><p>No flights found for that route.</p></div>';
        return;
    }

    var rows = flights.map(function(f) {
        return '<tr>' +
            '<td><strong>' + escHtml(f.flightsNum) + '</strong></td>' +
            '<td>' + escHtml(f.flights_originCode) + ' → ' + escHtml(f.flights_destinationCode) + '</td>' +
            '<td>' + escHtml(f.flights_departureTime) + '</td>' +
            '<td>' + escHtml(f.flights_arrivalTime) + '</td>' +
            '<td>' + escHtml(f.flights_availSeats) + '</td>' +
            '<td>₱' + parseFloat(f.flights_basePrice).toLocaleString('en-PH', {minimumFractionDigits:2}) + '</td>' +
            '<td><button class="btn-book" onclick="bookFlight(' + f.flightsID + ')">Book</button></td>' +
        '</tr>';
    }).join('');

    container.innerHTML =
        '<table class="skyzen-table">' +
        '<thead><tr><th>Flight No.</th><th>Route</th><th>Departure</th><th>Arrival</th><th>Seats</th><th>Price</th><th></th></tr></thead>' +
        '<tbody>' + rows + '</tbody>' +
        '</table>';

    document.getElementById('results-section').scrollIntoView({ behavior: 'smooth' });
}

function bookFlight(flightID) {
    Swal.fire({
        icon:  'info',
        title: 'Booking Coming Soon',
        text:  'Full booking flow for flight #' + flightID + ' will be available in the next sprint.'
    });
}

// HTML escape helper — always sanitize before injecting into the DOM
function escHtml(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(String(str)));
    return d.innerHTML;
}
</script>
</body>
</html>