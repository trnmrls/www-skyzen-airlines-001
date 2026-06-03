<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['rolesID'] != 1) { header('Location: skyzenLoginPage.php'); exit; }
require_once "../model/database_airlines.php";

$db = (new Database())->connect();
$aircrafts = $db->query("SELECT * FROM tbl_aircrafts")->fetchAll(PDO::FETCH_ASSOC);
$flights = $db->query("SELECT f.*, a.aircraftsModel FROM tbl_flights f LEFT JOIN tbl_aircrafts a ON f.aircraftsID = a.aircraftsID ORDER BY f.flights_departureTime DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Flights | SkyZen Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body style="background: #F6F8FA;">

    <div class="header-top-area" style="background: #111827; padding: 15px 0;">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12"><a href="skyzenAdminDash.php" style="color: #F59E0B; font-size: 24px; font-weight: bold; text-decoration: none;"><i class="fa-solid fa-plane"></i> SKYZEN ADMIN</a></div>
                <div class="col-lg-6 col-md-6 col-sm-12 text-right"><a href="../controllers/userController.php?logout=1" class="btn btn-danger"><i class="fa-solid fa-power-off"></i> Logout</a></div>
            </div>
        </div>
    </div>
    <div class="main-menu-area mg-tb-40" style="background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 40px;">
        <div class="container">
            <ul class="nav nav-tabs notika-menu-wrap" style="border: none; padding: 15px 0;">
                <li><a href="skyzenAdminDash.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
                <li class="active"><a href="skyzenAdminFlights.php" style="color: #008C4A; font-weight: 600; padding: 10px 20px; border-bottom: 2px solid #008C4A;"><i class="fa-solid fa-plane-departure"></i> Manage Flights</a></li>
                <li><a href="skyzenAdminBookings.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-ticket"></i> Manage Bookings</a></li>
                <li><a href="skyzenAdminFleet.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-jet-fighter-up"></i> Manage Fleet</a></li>
                <li><a href="skyzenAdminUsers.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-users"></i> Manage Users</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <button id="btnShowAddFlight" class="btn btn-success" style="background: #008C4A; margin-bottom: 20px;"><i class="fa fa-plus"></i> Add New Flight</button>
        
        <div id="addFlightWrapper" style="display:none; background: #fff; padding: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; border-top: 4px solid #008C4A;">
            <h4 style="margin-top:0; margin-bottom: 20px;">Create New Flight</h4>
            <div class="row">
                <div class="col-md-3 form-group"><label>Flight Number</label><input type="text" id="f_num" class="form-control" placeholder="e.g. SZ-101"></div>
                <div class="col-md-3 form-group">
                    <label>Aircraft</label>
                    <select id="f_aircraft" class="form-control">
                        <?php foreach($aircrafts as $a): ?><option value="<?= $a['aircraftsID'] ?>"><?= htmlspecialchars($a['aircraftsModel']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 form-group"><label>Origin Code (IATA)</label><input type="text" id="f_origin" class="form-control" placeholder="e.g. MNL" maxlength="3" style="text-transform:uppercase;"></div>
                <div class="col-md-3 form-group"><label>Destination Code (IATA)</label><input type="text" id="f_dest" class="form-control" placeholder="e.g. NRT" maxlength="3" style="text-transform:uppercase;"></div>
                <div class="col-md-3 form-group"><label>Departure Time</label><input type="datetime-local" id="f_dep" class="form-control"></div>
                <div class="col-md-3 form-group"><label>Arrival Time</label><input type="datetime-local" id="f_arr" class="form-control"></div>
                <div class="col-md-3 form-group"><label>Base Price (₱)</label><input type="number" id="f_price" class="form-control" placeholder="15000"></div>
                <div class="col-md-3 form-group"><label>Available Seats</label><input type="number" id="f_seats" class="form-control" placeholder="180"></div>
            </div>
            <div class="text-right"><button class="btn btn-default" id="btnCancelFlight">Cancel</button> <button class="btn btn-success" id="btnSaveFlight" style="background: #008C4A;">Save Flight</button></div>
        </div>

        <div class="data-table-list" style="background: #fff; padding: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <div class="basic-tb-hd"><h2>Active Flights</h2><p>Overview of all scheduled operations.</p></div>
            <div class="table-responsive">
                <table id="data-table-basic" class="table table-striped">
                    <thead><tr><th>Flight</th><th>Route</th><th>Departure</th><th>Arrival</th><th>Price</th><th>Seats</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach($flights as $f): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($f['flightsNum']) ?></strong><br><small class="text-muted"><?= htmlspecialchars($f['aircraftsModel']) ?></small></td>
                                <td><?= htmlspecialchars($f['flights_originCode']) ?> <i class="fa-solid fa-arrow-right text-muted"></i> <?= htmlspecialchars($f['flights_destinationCode']) ?></td>
                                <td><?= date('M d, Y H:i', strtotime($f['flights_departureTime'])) ?></td>
                                <td><?= date('M d, Y H:i', strtotime($f['flights_arrivalTime'])) ?></td>
                                <td>₱<?= number_format($f['flights_basePrice'] ?? 0, 2) ?></td>
                                <td><?= $f['flights_availSeats'] ?></td>
                                <td><button class="btn btn-danger btn-sm trigger-del-flight" data-id="<?= $f['flightsID'] ?>"><i class="fa-solid fa-trash"></i></button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../scripts/service.js"></script>
</body>
</html>