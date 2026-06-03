<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['rolesID'] != 1) { header('Location: skyzenLoginPage.php'); exit; }
require_once "../model/database_airlines.php";

$db = (new Database())->connect();
$fleet = $db->query("SELECT * FROM tbl_aircrafts ORDER BY aircraftsID DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Fleet | SkyZen Admin</title>
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
                <li><a href="skyzenAdminFlights.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-plane-departure"></i> Manage Flights</a></li>
                <li><a href="skyzenAdminBookings.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-ticket"></i> Manage Bookings</a></li>
                <li class="active"><a href="skyzenAdminFleet.php" style="color: #008C4A; font-weight: 600; padding: 10px 20px; border-bottom: 2px solid #008C4A;"><i class="fa-solid fa-jet-fighter-up"></i> Manage Fleet</a></li>
                <li><a href="skyzenAdminUsers.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-users"></i> Manage Users</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <button id="btnShowAddFleet" class="btn btn-success" style="background: #008C4A; margin-bottom: 20px;"><i class="fa fa-plus"></i> Add New Aircraft</button>
        
        <div id="addFleetWrapper" style="display:none; background: #fff; padding: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; border-top: 4px solid #008C4A;">
            <h4 style="margin-top:0; margin-bottom: 20px;">Register New Aircraft</h4>
            <div class="row">
                <div class="col-md-6 form-group"><label>Model (e.g. Airbus A320neo)</label><input type="text" id="a_model" class="form-control"></div>
                <div class="col-md-6 form-group"><label>Max Capacity</label><input type="number" id="a_cap" class="form-control" placeholder="180"></div>
            </div>
            <div class="text-right"><button class="btn btn-default" id="btnCancelFleet">Cancel</button> <button class="btn btn-success" id="btnSaveFleet" style="background: #008C4A;">Save Aircraft</button></div>
        </div>

        <div class="data-table-list" style="background: #fff; padding: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <div class="basic-tb-hd"><h2>SkyZen Fleet</h2><p>Manage the airplanes assigned to operations.</p></div>
            <div class="table-responsive">
                <table id="data-table-basic" class="table table-striped">
                    <thead><tr><th>ID</th><th>Aircraft Model</th><th>Capacity</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach($fleet as $a): 
                            // BULLETPROOF VARIABLES (Checks all possible column names in your database)
                            $id = $a['aircraftsID'] ?? $a['id'] ?? 'N/A';
                            $model = $a['aircraftsModel'] ?? $a['aircraft_model'] ?? $a['model'] ?? $a['aircrafts_model'] ?? 'Unknown Model';
                            $cap = $a['aircrafts_capacity'] ?? $a['aircraft_capacity'] ?? $a['capacity'] ?? '0';
                            $status = $a['aircrafts_status'] ?? $a['aircraft_status'] ?? $a['status'] ?? 'Active';
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($id) ?></td>
                                <td><strong><?= htmlspecialchars($model) ?></strong></td>
                                <td><?= htmlspecialchars($cap) ?> Seats</td>
                                <td><span class="label label-success"><?= htmlspecialchars($status) ?></span></td>
                                <td><button class="btn btn-danger btn-sm trigger-del-fleet" data-id="<?= htmlspecialchars($id) ?>"><i class="fa-solid fa-trash"></i></button></td>
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