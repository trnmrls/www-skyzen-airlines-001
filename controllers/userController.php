<?php
session_start();
require_once "../bl/userManagement.php";
require_once "../helper/sendEmail.php";
$userManagement = new UserManagement();

$emailTemplate = file_get_contents(__DIR__ . '/../helper/templates/emailTemplate.php');

if (isset($_POST['action']) && $_POST['action'] === 'register') { //create register account
    $email = $_POST['users_email'] ?? '';
    $firstName = $_POST['users_firstName'] ?? '';
    $middleName = $_POST['users_middleName'] ?? '';
    $lastName = $_POST['users_lastName'] ?? '';
    $phoneNum = $_POST['users_phoneNum'] ?? '';
    $birthday = $_POST['users_birthday'] ?? ''; 
    $username = $_POST['users_username'] ?? '';
    $password = $_POST['users_password'] ?? '';
    $rolesID = (int)($_POST['rolesID'] ?? 2);

    if (empty($email) || empty($firstName) || empty($lastName) || empty($phoneNum) || empty($birthday) || empty($username) || empty($password)) {
        http_response_code(500);
        echo 'Please complete all required registration fields.';
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(500);
        echo 'Please enter a valid email address.';
        exit;
    }

    $rolesID = $rolesID === 1 ? 1 : 2; 

    $userManagement->addUserFunc($email, $firstName, $middleName, $lastName, $phoneNum, $birthday, $username, $password, $rolesID);
    
    $name = $name = $firstName . ' ' . $lastName; 
    $email = filter_var($email, FILTER_SANITIZE_EMAIL); 

    if (!$email) {
        http_response_code(400);
        die("Invalid email format.");
    }

    $body ="
        <h3> Hello, $firstName! </h3>
        <p>Thank you for choosing SkyZen Airlines. We are thrilled to have you on board and look forward to providing you with exceptional flight experiences. If you have any questions or need assistance, please don't hesitate to reach out to our customer support team.</p>
        <p>Safe travels and welcome to the SkyZen family!</p>
        <br> 
        $emailTemplate
    ";

    $result = sendEmail(
        $email,
        $name,
        "Registration to SkyZen Airlines",
        $body
    );

    if ($result === true) {
        http_response_code(200);
        echo "Email sent successfully!";
    } else {
        http_response_code(500);
        echo "Failed: $result";
    }
    exit;

} else if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $loginIdentifier = $_POST['users_username'] ?? $_POST['users_email'] ?? '';
    $password = $_POST['users_password'] ?? '';

    $userManagement->loginAdminFunc($loginIdentifier, $password);
    exit;

} else if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_destroy();
    header('Location: ../views/skyzenLoginPage.php');
    exit;

} else if (isset($_POST['action']) && $_POST['action'] === 'update') {
    header('Content-Type: application/json');

    if (!isset($_SESSION['user']) || (int)$_SESSION['user']['rolesID'] !== 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    $userManagement->updateUserFunc(
        (int)$_POST['usersID'],
        trim($_POST['users_firstName'] ?? ''),
        trim($_POST['users_lastName'] ?? ''),
        trim($_POST['users_phoneNum'] ?? ''),
        trim($_POST['users_email'] ?? ''),
        trim($_POST['users_birthday'] ?? ''),
        trim($_POST['users_username'] ?? ''),
        trim($_POST['users_password'] ?? '')
    );
    exit;

} else if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    header('Content-Type: application/json');

    if (!isset($_SESSION['user']) || (int)$_SESSION['user']['rolesID'] !== 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $userManagement->deleteUserFunc((int)$_POST['usersID']);
    exit;
    } else if (isset($_POST['action']) && $_POST['action'] === 'checkout_flight') {
    header('Content-Type: application/json');
    
    // Safety check: Ensure they are actually logged in
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Please log in to book a flight.']);
        exit;
    }
    
    // 1. THIS IS THE LINE THAT WAS MISSING!
    $userID = $_SESSION['user']['usersID']; 
    
    $flightID = $_POST['flightID'];
    $paxCount = $_POST['paxCount'];
    $grandTotal = $_POST['grandTotal']; 
    $seats = $_POST['seats']; 
    $paxNames = $_POST['paxNames'] ?? []; 
    
    $result = $userManagement->createBookingFunc($userID, $flightID, $paxCount, $grandTotal, $seats, $paxNames);
    
    echo json_encode($result);
    exit;
} else if (isset($_POST['action']) && $_POST['action'] === 'web_checkin') {
    header('Content-Type: application/json');
    $pnr = trim($_POST['pnr']);
    $lastName = trim($_POST['lastName']);
    
    $result = $userManagement->processWebCheckIn($pnr, $lastName);
    echo json_encode($result);
    exit;
} else if (isset($_POST['action']) && $_POST['action'] === 'update_passenger') {
    header('Content-Type: application/json');
    try {
        $db = (new Database())->connect();
        
        // Find the passenger ID linked to this ticket
        $stmt = $db->prepare("SELECT tickets_passengersID FROM tbl_tickets WHERE ticketsID = :tid");
        $stmt->execute([':tid' => $_POST['ticketID']]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($ticket) {
            // Update the passenger's name in tbl_passengers!
            $update = $db->prepare("UPDATE tbl_passengers SET passengers_fullName = :name WHERE passengersID = :pid");
            $update->execute([
                ':name' => strtoupper($_POST['newName']), 
                ':pid' => $ticket['tickets_passengersID']
            ]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ticket not found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error.']);
    }
    exit;
}
?>