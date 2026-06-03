<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['rolesID'] != 1) { header('Location: skyzenLoginPage.php'); exit; }
require_once "../model/database_airlines.php";

$db = (new Database())->connect();
// Fetch only standard Customers
$stmt = $db->query("SELECT * FROM tbl_users WHERE rolesID = 2 ORDER BY users_createdAt DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users | Admin</title>
    <link rel="stylesheet" href="../views/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="header-top-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="logo-area">
                        <h2><i class="fa fa-plane"></i> SkyZen Admin</h2>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div class="header-top-menu text-right" style="margin-top: 5px;">
                        <span style="color: white; font-size: 14px; margin-right: 15px;">
                            <i class="fa fa-user-circle"></i> Welcome, <?= htmlspecialchars($_SESSION['user']['users_firstName'] ?? 'Admin') ?>
                        </span>
                        <a href="../controllers/userController.php?logout=1" class="nav-link">
                            <i class="fa fa-sign-out"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-menu-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#"><i class="fa fa-home"></i> Dashboard</a></li>
                        <li><a href="skyzenAdminFlights.php"><i class="fa fa-plane"></i> Flights</a></li>
                        <li><a href="skyzenAdminBookings.php"><i class="fa fa-ticket"></i> Bookings</a></li>
                        <li><a href="skyzenAdminUsers.php"><i class="fa fa-users"></i> Users</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <main class="admin-main">
        
        <div class="basic-tb-hd">
            <h2>Manage Registered Users</h2>
            <p>View, edit, or remove customer accounts.</p>
        </div>

        <div id="editFormWrapper">
            <div class="modal-header" style="display:flex; justify-content:space-between; padding:15px; align-items:center;">
                <span class="modal-title">Editing User: <span id="editDisplayLabel"></span></span>
                <span class="close" id="btnCloseEdit" style="cursor: pointer;"><i class="fa-solid fa-xmark"></i></span>
            </div>
            
            <div class="form-grid">
                <input type="hidden" id="edit_id">
                <div class="form-group"><label>First Name</label><input type="text" id="edit_fname" class="form-control"></div>
                <div class="form-group"><label>Last Name</label><input type="text" id="edit_lname" class="form-control"></div>
                <div class="form-group"><label>Email Address</label><input type="email" id="edit_email" class="form-control"></div>
                <div class="form-group"><label>Phone Number</label><input type="text" id="edit_phone" class="form-control"></div>
                <div class="form-group"><label>Birthday</label><input type="date" id="edit_birthday" class="form-control"></div>
                <div class="form-group"><label>Username</label><input type="text" id="edit_username" class="form-control"></div>
                <div class="form-group"><label>New Password</label><input type="password" id="edit_password" class="form-control" placeholder="Leave blank to keep current"></div>
                <div class="form-group"><label>Confirm Password</label><input type="password" id="regConfirmPassword" class="form-control" placeholder="Required if changing"></div>
            </div>
            
            <div class="form-actions">
                <button class="btn-cancel" id="btnCancelEdit">Cancel</button>
                <button class="btn-save" id="btnSaveEdit">Save Updates</button>
            </div>
        </div>

        <div class="data-table-list">
            <table id="data-table-basic" class="modern-table display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Username</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                        <tr>
                            <td><span class="text-muted">#<?= $u['usersID'] ?></span></td>
                            <td class="font-bold"><?= htmlspecialchars($u['users_firstName'] . ' ' . $u['users_lastName']) ?></td>
                            <td><?= htmlspecialchars($u['users_email']) ?></td>
                            <td><?= htmlspecialchars($u['users_phoneNum']) ?></td>
                            <td><span class="badge-blue"><?= htmlspecialchars($u['users_username']) ?></span></td>
                            <td class="action-cells text-center">
                                <button class="btn-action edit-user-btn" 
                                    data-id="<?= $u['usersID'] ?>"
                                    data-fname="<?= htmlspecialchars($u['users_firstName']) ?>"
                                    data-lname="<?= htmlspecialchars($u['users_lastName']) ?>"
                                    data-phone="<?= htmlspecialchars($u['users_phoneNum']) ?>"
                                    data-email="<?= htmlspecialchars($u['users_email']) ?>"
                                    data-bday="<?= $u['users_birthday'] ?>"
                                    data-uname="<?= htmlspecialchars($u['users_username']) ?>"
                                    title="Edit User">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button class="btn-action delete-user-btn" 
                                    data-id="<?= $u['usersID'] ?>"
                                    title="Delete User">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../scripts/service.js"></script>
</body>
</html>