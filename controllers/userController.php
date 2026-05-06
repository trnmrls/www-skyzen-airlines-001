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
}
?>