<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['rolesID'] != 1) { 
    header('Location: skyzenLoginPage.php'); 
    exit; 
}
require_once "../model/database_airlines.php";

$db = (new Database())->connect();
$stmt = $db->query("SELECT * FROM tbl_users WHERE rolesID = 2 ORDER BY users_createdAt DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users | SkyZen Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body>

    <div class="header-top-area" style="padding: 15px 0;">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="logo-area">
                        <a href="skyzenAdminDash.php" style="font-size: 24px; font-weight: bold; text-decoration: none;">
                            <i class="fa-solid fa-plane"></i> SKYZEN ADMIN
                        </a>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 text-right">
                    <a href="../controllers/userController.php?logout=1" class="btn btn-danger"><i class="fa-solid fa-power-off"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-menu-area mg-tb-40" style="background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 40px;">
        <div class="container">
            <ul class="nav nav-tabs notika-menu-wrap" style="border: none; padding: 15px 0;">
                <li><a href="skyzenAdminDash.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="skyzenAdminFlights.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-plane-departure"></i> Manage Flights</a></li>
                <li><a href="skyzenAdminBookings.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-ticket"></i> Manage Bookings</a></li>
                <li><a href="skyzenAdminFleet.php" style="color: #333; font-weight: 600; padding: 10px 20px;"><i class="fa-solid fa-jet-fighter-up"></i> Manage Fleet</a></li>
                <li class="active"><a href="skyzenAdminUsers.php" style="color: #008C4A; font-weight: 600; padding: 10px 20px; border-bottom: 2px solid #008C4A;"><i class="fa-solid fa-users"></i> Manage Users</a></li>
            </ul>
        </div>
    </div>

    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    
                    <div id="editFormWrapper">
                        <div class="modal-header">
                            <button type="button" class="close cancel-edit-btn">&times;</button>
                            <h4 class="modal-title">Editing: <span id="editDisplayLabel"></span></h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" id="edit_id">
                                <div class="col-md-6 form-group"><label>First Name</label><input type="text" id="edit_fname" class="form-control"></div>
                                <div class="col-md-6 form-group"><label>Last Name</label><input type="text" id="edit_lname" class="form-control"></div>
                                <div class="col-md-6 form-group"><label>Email Address</label><input type="email" id="edit_email" class="form-control"></div>
                                <div class="col-md-6 form-group"><label>Phone Number</label><input type="text" id="edit_phone" class="form-control"></div>
                                <div class="col-md-6 form-group"><label>Birthday</label><input type="date" id="edit_birthday" class="form-control"></div>
                                <div class="col-md-6 form-group"><label>Username</label><input type="text" id="edit_username" class="form-control"></div>
                                <div class="col-md-6 form-group"><label>New Password</label><input type="password" id="edit_password" class="form-control" placeholder="Leave blank to keep current"></div>
                                <div class="col-md-6 form-group"><label>Confirm Password</label><input type="password" id="regConfirmPassword" class="form-control" placeholder="Required if changing password"></div>
                            </div>
                            <div class="text-right" style="margin-top: 15px;">
                                <button class="btn btn-default cancel-edit-btn">Cancel</button>
                                <button class="btn btn-success save-edit-btn" style="background: #008C4A;">Save Changes</button>
                            </div>
                        </div>
                    </div>

                    <div class="data-table-list">
                        <div class="basic-tb-hd">
                            <h2>Manage Registered Users</h2>
                            <p>View, edit, or remove customer accounts from the system.</p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Username</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($users as $u): ?>
                                        <tr>
                                            <td><?= $u['usersID'] ?></td>
                                            <td><?= htmlspecialchars($u['users_firstName'] . ' ' . $u['users_lastName']) ?></td>
                                            <td><?= htmlspecialchars($u['users_email']) ?></td>
                                            <td><?= htmlspecialchars($u['users_phoneNum']) ?></td>
                                            <td><?= htmlspecialchars($u['users_username']) ?></td>
                                            <td>
                                                <button class="btn btn-info btn-sm trigger-edit-btn" 
                                                    data-id="<?= $u['usersID'] ?>"
                                                    data-fname="<?= htmlspecialchars($u['users_firstName']) ?>"
                                                    data-lname="<?= htmlspecialchars($u['users_lastName']) ?>"
                                                    data-phone="<?= htmlspecialchars($u['users_phoneNum']) ?>"
                                                    data-email="<?= htmlspecialchars($u['users_email']) ?>"
                                                    data-bday="<?= $u['users_birthday'] ?>"
                                                    data-uname="<?= htmlspecialchars($u['users_username']) ?>">
                                                    <i class="fa-solid fa-pen"></i> Edit
                                                </button>
                                                <button class="btn btn-danger btn-sm trigger-del-btn" data-id="<?= $u['usersID'] ?>">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
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