<?php
class RegistrationModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createRegistration($email, $fName, $middleName, $lName, $phoneNum, $birthday, $username, $hashedPassword, $rolesID = 2): bool {
        $query = "INSERT INTO tbl_users
                    (rolesID, users_email, users_firstName, users_middleName,
                     users_lastName, users_phoneNum, users_birthday,
                     users_username, users_password)
                  VALUES
                    (:rolesID, :email, :firstName, :middleName,
                     :lastName, :phoneNum, :birthday,
                     :username, :password)";
 
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':rolesID'   => $rolesID,
            ':email'     => $email,
            ':firstName' => $fName,
            ':middleName'  => $middleName,
            ':lastName'  => $lName,
            ':phoneNum'  => $phoneNum,
            ':birthday'  => $birthday,
            ':username'  => $username,
            ':password'  => $hashedPassword
        ]);
    }

    public function getLoginUser($loginIdentifier) {
    $selectQuery = "SELECT * FROM tbl_users WHERE users_username = :username OR users_email = :email";
    $stmt = $this->conn->prepare($selectQuery);
    $stmt->execute([
        ':username' => $loginIdentifier, 
        ':email' => $loginIdentifier
        ]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
    public function getUserByEmail($email): array|false {
        $query = "SELECT * FROM tbl_users WHERE users_email = :email LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmailOrUsername($login): array|false {
        $query = "SELECT * FROM tbl_users WHERE users_email = :login OR users_username = :login LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute([':login' => $login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers(): array {
        $stmt = $this->conn->query("SELECT * FROM tbl_users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>