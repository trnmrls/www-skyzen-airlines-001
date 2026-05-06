<?php
    session_start();
    require_once "../bl/userManagement.php";
    
    if (!isset($_SESSION['user']) || (int)$_SESSION['user']['rolesID'] !== 1) {
        header("Location: skyzenLoginPage.php");
        exit;
    }

    $userManagement = new UserManagement();
    $usersList = $userManagement->getUser(); 
     
    $dashStats = $userManagement->getDashboardStats();

    $chartData = $userManagement->getChartData();
    
    $pieLabels = array_column($chartData['roles'], 'label');
    $pieData = array_column($chartData['roles'], 'total');
    
    $barLabels = array_column($chartData['bookings'], 'label');
    $barData = array_column($chartData['bookings'], 'total');
    
    $lineLabels = array_column($chartData['revenue'], 'label');
    $lineData = array_column($chartData['revenue'], 'total');

    $flightLabels = array_column($chartData['flightStatus'], 'label');
    $flightData = array_column($chartData['flightStatus'], 'total');
    
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Admin Dashboard | SkyZen Airlines</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700,300" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/responsive.css">
    
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
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="notika-status-area">
        <div class="container">
            <div class="row">            
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="dash-card" style="background: var(--card-bg); border-left: 5px solid var(--theme-green);">
                    <p style="color: #777;"><i class="fa fa-users" style="color: var(--theme-green);"></i> Total Customers</p>
                    <h3 style="color: var(--text-dark);"><?= $dashStats['customers'] ?></h3>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="dash-card" style="background: var(--card-bg); border-left: 5px solid var(--theme-green);">
                    <p style="color: #777;"><i class="fa fa-users" style="color: var(--theme-green);"></i> Total Admins</p>
                    <h3 style="color: var(--text-dark);"><?= $dashStats['admins'] ?></h3>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="dash-card">
                    <p style="color: #777;"><i class="fa fa-money" style="color: var(--theme-green);"></i> Total Revenue</p>
                    <h3 style="color: var(--text-dark);">₱<?= number_format($dashStats['revenue'], 2) ?></h3>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="dash-card">
                    <p style="color: #777;"><i class="fa fa-ticket" style="color: var(--theme-green);"></i> Tickets Sold</p>
                    <h3 style="color: var(--text-dark);"><?= number_format($dashStats['tickets']) ?></h3>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="dash-card" style="border-left: 5px solid var(--theme-red);">
                    <p style="color: #777;"><i class="fa fa-plane" style="color: var(--theme-red);"></i> Scheduled Flights</p>
                    <h3 style="color: var(--text-dark);"><?= number_format($dashStats['flights']) ?></h3>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="dash-card" style="border-left: 5px solid var(--theme-red);">
                    <p style="color: #777;"><i class="fa fa-clock-o" style="color: var(--theme-red);"></i> Pending Bookings</p>
                    <h3 style="color: var(--text-dark);"><?= number_format($dashStats['pending_bookings']) ?></h3>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="dash-card" style="border-left: 5px solid var(--theme-red);">
                    <p style="color: #777;"><i class="fa fa-credit-card" style="color: var(--theme-red);"></i> Pending Payments</p>
                    <h3 style="color: var(--text-dark);"><?= number_format($dashStats['pending_payments']) ?></h3>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="dash-card" style="border-left: 5px solid var(--theme-red);">
                    <p style="color: #777;"><i class="fa fa-fighter-jet" style="color: var(--theme-red);"></i> Active Flights</p>
                    <h3 style="color: var(--text-dark);"><?= number_format($dashStats['flights']) ?></h3>
                </div>
            </div>

        </div>
    
    <div class="row">
            <div class="col-md-8 mb-4">
                <div class="dash-card" style="border-left: 5px solid var(--nav-dark);">
                    <div class="dash-card-header"><h4><i class="fa fa-line-chart"></i> Monthly Revenue Trend</h4></div>
                    <div class="chart-box-line"><canvas id="lineChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="dash-card" style="border-left: 5px solid var(--nav-dark);">
                    <div class="dash-card-header"><h4><i class="fa fa-fighter-jet"></i> Flight Status</h4></div>
                    <div class="chart-box-pie"><canvas id="flightChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mb-4">
                <div class="dash-card" style="border-left: 5px solid var(--nav-dark);">
                    <div class="dash-card-header"><h4><i class="fa fa-ticket"></i> Booking Statuses</h4></div>
                    <div class="chart-box-bar"><canvas id="barChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="dash-card" style="border-left: 5px solid var(--nav-dark);">
                    <div class="dash-card-header"><h4><i class="fa fa-users"></i> Role Distribution</h4></div>
                    <div class="chart-box-doughnut"><canvas id="pieChart"></canvas></div>
                </div>
            </div>
        </div>


    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div id="editFormWrapper">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin: 0; color: #333;"><i class="fa fa-edit"></i> Edit User Information</h3>
                            <p style="margin: 5px 0 0; color: #777;">Editing records for: <strong id="editDisplayLabel" style="color: #00c292;"></strong></p>
                        </div>
                        <button class="btn btn-default btn-sm" onclick="closeEditForm()"><i class="fa fa-times"></i> Close</button>
                    </div>

                    <form id="updateForm" onsubmit="event.preventDefault(); submitUpdate();">
                        <input type="hidden" id="edit_id" name="usersID">
                        
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>First Name</label>
                                <input type="text" class="form-control" id="edit_fname" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control" id="edit_lname" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Phone Number</label>
                                <input type="text" class="form-control" id="edit_phone" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Email Address</label>
                                <input type="email" class="form-control" id="edit_email" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Birthday</label>
                                <input type="date" class="form-control" id="edit_birthday" required>
                            </div>
                            <div class="col-md-4 form-group">  
                                <label>Username</label>
                                <input type="text" class="form-control" id="edit_username" required>
                            </div>
                            
                        </div>

                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success" style="background: #00c292; border: none; padding: 8px 25px;">
                                    <i class="fa fa-save"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="data-table-area" style="margin-bottom: 60px;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="basic-tb-hd">
                            <h2>SkyZen User List</h2>
                            <p>Registered Users from SkyZen Airlines.</p>
                        </div>
                        
                        <?php if(empty($usersList)): ?>
                            <div class="alert alert-warning">
                                <strong><i class="fa fa-exclamation-triangle"></i> Notice:</strong> 
                                There are currently no users in the database.
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Full Name</th>
                                        <th>Email Address</th>
                                        <th>Phone Number</th>
                                        <th>Birthday</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($usersList)) : ?>
                                        <?php foreach($usersList as $user) : ?>
                                            <?php 
                                                $userID = $user["usersID"];
                                                $fName = $user["users_firstName"];
                                                $lName = $user["users_lastName"];
                                                $email = $user["users_email"];
                                                $phone = $user["users_phoneNum"];
                                                $birthday = $user["users_birthday"];
                                                $username = $user["users_username"];
                                                
                                                $roleID = (int)$user["rolesID"];
                                                $roleName = ($roleID === 1) ? 'Admin' : 'Customer';
                                                $roleColor = ($roleID === 1) ? '#F44336' : '#00c292';
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($userID); ?></td>
                                                <td><?= htmlspecialchars($fName . ' ' . $lName); ?></td>
                                                <td><?= htmlspecialchars($email); ?></td>
                                                <td><?= htmlspecialchars($phone); ?></td>
                                                <td><?= date('Y-m-d', strtotime($birthday)); ?></td>
                                                <td><?= htmlspecialchars($username); ?></td>
                                                <td>
                                                    <span style="background: <?= $roleColor ?>; color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                                        <?= $roleName ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-info btn-sm" style="background: #00BCD4; border:none; margin-right: 5px;"
                                                        onclick="openEditForm('<?= htmlspecialchars(addslashes($userID)) ?>', '<?= htmlspecialchars(addslashes($fName)) ?>', '<?= htmlspecialchars(addslashes($lName)) ?>', '<?= htmlspecialchars(addslashes($phone)) ?>')">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </button>
                                                    
                                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete('<?= htmlspecialchars(addslashes($userID)) ?>')" style="background: #F44336; border:none;">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?> 
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    
    <script>
    window.lineChartData = {
        labels: <?php echo json_encode($lineLabels); ?>, 
        data: <?php echo json_encode($lineData); ?>
    };

    window.flightChartData = {
        labels: <?php echo json_encode($flightLabels); ?>,
        data: <?php echo json_encode($flightData); ?>
    };

    window.barChartData = {
        labels: <?php echo json_encode($barLabels); ?>,
        data: <?php echo json_encode($barData); ?>
    };

    window.pieChartData = {
        labels: <?php echo json_encode($pieLabels); ?>,
        data: <?php echo json_encode($pieData); ?>
    };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../scripts/service.js"></script>
    </body>
</html>