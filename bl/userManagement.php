<?php
require_once "../model/database_airlines.php";
require_once "../model/registrationModel.php";

class UserManagement {
    private $regsModel;
    
    public function __construct() {
        $database = new Database();
        $db = $database->connect();
        $this->regsModel = new RegistrationModel($db);
    }

public function addUserFunc($email, $firstName, $middleName, $lastName, $phoneNum, $birthday, $username, $password, $rolesID = 2) {
        try { 
            if ($this->regsModel->getUserByEmail($email)) {
                http_response_code(400); 
                echo "This email is already registered. Please use a different email.";
                return;
            }

            $existingUser = $this->regsModel->getUserByEmailOrUsername($username);
            if ($existingUser && $existingUser['users_username'] === $username) {
                http_response_code(400); 
                echo "This username is already taken. Please choose another.";
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_ARGON2ID); // HASHING TASK 1
            if ($this->regsModel->createRegistration($email, $firstName, $middleName, $lastName, $phoneNum, $birthday, $username, $hashedPassword, $rolesID)) {
                http_response_code(200);
                echo "User Added Successfully!";
            } else {
                http_response_code(500);
                echo "Error: There was an issue adding the user. Please try again.";
            }
        } catch (Exception $ex) {
            http_response_code(500);
            echo "Error: " . $ex->getMessage();
            exit();
        }
    }
    
    public function loginAdminFunc($loginIdentifier, $password) {
        try { 
            $hashedPassword = password_hash($password, PASSWORD_ARGON2ID); // HASHING
            $selectedQuery = $this->regsModel->getLoginUser($loginIdentifier);
            
            if ($selectedQuery) {
                $password_check = password_verify($password, $selectedQuery['users_password']);
                
                if($password_check) {
                    http_response_code(200);
                    $_SESSION['user'] = $selectedQuery;
                    echo json_encode([
                            'success' => true,
                            'rolesID' => $selectedQuery['rolesID'],
                            'message' => 'Login successful!'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Error: There was an issue entering your credentials. Please try again.'
                        ]);
                    }
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Error: User not found. Please check your email or username.'
                    ]);
                }
            } catch (Exception $ex) {
                http_response_code(500);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error: ' . $ex->getMessage()
                ]);
                exit();
            }
        }
        
    public function deleteUserFunc(int $usersID): void {
        try {
            $db   = (new Database())->connect();
            $stmt = $db->prepare("DELETE FROM tbl_users WHERE usersID = :id");
            $stmt->execute([':id' => $usersID]);
 
            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $ex->getMessage()]);
        }
    }
 
    public function updateUserFunc($usersID, $firstName, $lastName, $phoneNum, $email, $birthday, $username, $password) { // or $firstName || $lastName || $phoneNum || $email || $birthday || $username || $password
        try {
            $db   = (new Database())->connect();
            
            $checkStmt = $db->prepare("SELECT usersID FROM tbl_users WHERE (users_email = :email OR users_username = :username) AND usersID != :id LIMIT 1");
            $checkStmt->execute([':email' => $email, ':username' => $username, ':id' => $usersID]);
            if ($checkStmt->fetch()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Email or Username is already in use by another account.']);
                return;
            }

            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
                $stmt = $db->prepare("
                    UPDATE tbl_users
                    SET    users_firstName = :fName, users_lastName = :lName, users_phoneNum = :phoneNum,
                           users_email = :email, users_birthday = :birthday, users_username = :username,
                           users_password = :password, users_updatedAt = NOW()
                    WHERE  usersID = :id
                ");
                $stmt->execute([':fName' => $firstName, ':lName' => $lastName, ':phoneNum' => $phoneNum, ':email' => $email, ':birthday' => $birthday, ':username' => $username, ':password' => $hashedPassword, ':id' => $usersID]);
            } else {
                $stmt = $db->prepare("
                    UPDATE tbl_users
                    SET    users_firstName = :fName, users_lastName = :lName, users_phoneNum = :phoneNum,
                           users_email = :email, users_birthday = :birthday, users_username = :username,
                           users_updatedAt = NOW()
                    WHERE  usersID = :id
                ");
                $stmt->execute([':fName' => $firstName, ':lName' => $lastName, ':phoneNum' => $phoneNum, ':email' => $email, ':birthday' => $birthday, ':username' => $username, ':id' => $usersID]);
            }
 
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'User updated successfully.']);

        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $ex->getMessage()]);
        }
    }
    
    public function getUser() {
        try {
            $db   = (new Database())->connect();
            $stmt = $db->query("
                SELECT  u.usersID,
                        u.users_firstName,
                        u.users_middleName,
                        u.users_lastName,
                        CONCAT(u.users_firstName, ' ', u.users_lastName) AS full_name,
                        u.users_username,
                        u.users_email,
                        u.users_phoneNum,
                        u.users_birthday,
                        u.rolesID,
                        r.roles_name,
                        u.users_createdAt
                FROM    tbl_users  u
                JOIN    tbl_roles  r ON u.rolesID = r.rolesID
                ORDER   BY u.users_createdAt DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            return []; 
        }
    }

    public function getDashboardStats() {
        try {
            $db = (new Database())->connect();
            $stats = [];

            $stats['customers'] = $db->query("SELECT COUNT(*) FROM tbl_users WHERE rolesID = 2")->fetchColumn();

            $stats['admins'] = $db->query("SELECT COUNT(*) FROM tbl_users WHERE rolesID = 1")->fetchColumn();

            $rev = $db->query("SELECT SUM(payments_amount) FROM tbl_payments WHERE payments_status = 'Confirmed'")->fetchColumn();
            $stats['revenue'] = $rev ? $rev : 0; 

            $stats['tickets'] = $db->query("SELECT COUNT(*) FROM tbl_tickets")->fetchColumn();

            $stats['flights'] = $db->query("SELECT COUNT(*) FROM tbl_flights WHERE flights_departureTime >= NOW()")->fetchColumn();

            $stats['flight'] = $db->query("SELECT COUNT(*) FROM tbl_aircrafts WHERE aircrafts_status = 'Active'")->fetchColumn();

            $stats['pending_bookings'] = $db->query("SELECT COUNT(*) FROM tbl_bookings WHERE bookings_status = 'Pending'")->fetchColumn();

            $stats['pending_payments'] = $db->query("SELECT COUNT(*) FROM tbl_payments WHERE payments_status = 'Pending'")->fetchColumn();
            return $stats;
        } catch (Exception $ex) {
            return ['customers'=>0, 'revenue'=>0, 'tickets'=>0, 'flights'=>0, 'flight'=>0, 'pending_bookings'=>0, 'pending_payments'=>0, 'destinations'=>0];
        }
    }

    public function getChartData() {
        try {
            $db = (new Database())->connect();
            
            $roles = $db->query("SELECT r.roles_name as label, COUNT(u.usersID) as total FROM tbl_roles r LEFT JOIN tbl_users u ON u.rolesID = r.rolesID GROUP BY r.roles_name")->fetchAll(PDO::FETCH_ASSOC);
            
            $bookings = $db->query("SELECT bookings_status as label, COUNT(bookingsID) as total FROM tbl_bookings GROUP BY bookings_status")->fetchAll(PDO::FETCH_ASSOC);
            if(empty($bookings)) $bookings = [['label'=>'Pending', 'total'=>0], ['label'=>'Confirmed', 'total'=>0]];

            $revenue = $db->query("
                SELECT DATE_FORMAT(payments_createdAt, '%b %Y') as label, SUM(payments_amount) as total 
                FROM tbl_payments 
                WHERE payments_status = 'Confirmed' 
                GROUP BY YEAR(payments_createdAt), MONTH(payments_createdAt) 
                ORDER BY payments_createdAt ASC LIMIT 6
            ")->fetchAll(PDO::FETCH_ASSOC);
            if(empty($revenue)) $revenue = [['label'=>date('M Y'), 'total'=>0]];

            $flightStatus = $db->query("SELECT aircrafts_status as label, COUNT(aircraftsID) as total FROM tbl_aircrafts GROUP BY aircrafts_status")->fetchAll(PDO::FETCH_ASSOC);
            if(empty($flightStatus)) $flightStatus = [['label'=>'Active', 'total'=>0]];

            return [
                'roles' => $roles,
                'bookings' => $bookings,
                'revenue' => $revenue,
                'flightStatus' => $flightStatus
            ];
        } catch (Exception $e) {
            return ['roles'=>[], 'bookings'=>[], 'revenue'=>[], 'destinations'=>[], 'paymentMethods'=>[], 'flightStatus'=>[]];
        }
    }
}

// MAIN PAGE - user side
    function getAirports() {
        try {
            $db = (new Database())->connect();
            $stmt = $db->query("SELECT * FROM tbl_airports ORDER BY airportsName ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            return [];
        }
    }

    // =======================================================
    // CLIENT BOOKING ENGINE: FLIGHT SEARCH
    // =======================================================
    function searchAvailableFlights($origin, $dest, $date, $pax) {
        try {
            $db = (new Database())->connect();
            
            // We use DATE() to ignore the exact time and match only the calendar day.
            // We also verify that flights_availSeats is greater than or equal to the requested passenger count.
            $sql = "SELECT f.*, 
                           a.aircraftsModel,
                           orig.airportsName AS originName,
                           dest.airportsName AS destName
                    FROM tbl_flights f
                    LEFT JOIN tbl_aircrafts a ON f.aircraftsID = a.aircraftsID
                    LEFT JOIN tbl_airports orig ON f.flights_originCode = orig.airportsCode
                    LEFT JOIN tbl_airports dest ON f.flights_destinationCode = dest.airportsCode
                    WHERE f.flights_originCode = :origin 
                      AND f.flights_destinationCode = :dest 
                      AND DATE(f.flights_departureTime) = :dep_date
                      AND f.flights_availSeats >= :pax
                    ORDER BY f.flights_basePrice ASC"; // Order by cheapest flight first
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':origin' => $origin,
                ':dest' => $dest,
                ':dep_date' => $date,
                ':pax' => $pax
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            // In a production environment, you might log $ex->getMessage() here.
            return [];
        }
    }
?>