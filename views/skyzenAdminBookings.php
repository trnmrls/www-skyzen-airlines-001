<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['rolesID'] != 1) { header('Location: skyzenLoginPage.php'); exit; }
require_once "../model/database_airlines.php";

$db = (new Database())->connect();
$bookings = $db->query("SELECT b.*, u.users_firstName, u.users_lastName FROM tbl_bookings b JOIN tbl_users u ON b.usersID = u.usersID ORDER BY b.bookings_createdAt DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings | SkyZen Admin</title>
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
                <li class="active"><a href="skyzenAdminBookings.php" style="color: #008C4A; font-weight: 600; padding: 10px 20px; border-bottom: 2px solid #008C4A;"><i class="fa-solid fa-ticket"></i> Manage Bookings</a></li>
                <li><a href="skyzenAdminFleet.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-jet-fighter-up"></i> Manage Fleet</a></li>
                <li><a href="skyzenAdminUsers.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-users"></i> Manage Users</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="data-table-list" style="background: #fff; padding: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <div class="basic-tb-hd"><h2>Customer Bookings</h2><p>Manage and process all passenger reservations.</p></div>
            <div class="table-responsive">
                <table id="data-table-basic" class="table table-striped">
                    <thead><tr><th>PNR Ref</th><th>Customer</th><th>Date Booked</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach($bookings as $b): 
                            $statusLabel = $b['bookings_status'] === 'Confirmed' ? 'label-success' : ($b['bookings_status'] === 'Cancelled' ? 'label-danger' : 'label-warning');
                        ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($b['bookings_pnrCode']) ?></strong></td>
                                <td><?= htmlspecialchars($b['users_firstName'] . ' ' . $b['users_lastName']) ?></td>
                                <td><?= date('M d, Y', strtotime($b['bookings_createdAt'])) ?></td>
                                <td>₱<?= number_format($b['bookings_amount'], 2) ?></td>
                                <td><span class="label <?= $statusLabel ?>"><?= htmlspecialchars($b['bookings_status']) ?></span></td>
                                <td>
                                    <?php if($b['bookings_status'] !== 'Confirmed'): ?>
                                        <button class="btn btn-success btn-sm trigger-status-btn" data-id="<?= $b['bookingsID'] ?>" data-status="Confirmed"><i class="fa-solid fa-check"></i></button>
                                    <?php endif; ?>
                                    <?php if($b['bookings_status'] !== 'Cancelled'): ?>
                                        <button class="btn btn-danger btn-sm trigger-status-btn" data-id="<?= $b['bookingsID'] ?>" data-status="Cancelled"><i class="fa-solid fa-xmark"></i></button>
                                    <?php endif; ?>
                                </td>
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