<?php
class UserModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    } 
   
public function createUser ($fName, $middleName, $lName, $email, $phoneNum, $birthday, $username, $password) {
$createQuery = "INSERT INTO tbl_users
                    (usersID, rolesID, users_email, users_firstName, users_middleName,
                     users_lastName, users_phoneNum, users_birthday,
                     users_username, users_password)
                  VALUES
                    (:usersID, :rolesID, :email, :firstName, :middleName,
                     :lastName, :phoneNum, :birthday,
                     :username, :password(hashed))";
$response = $this->conn->prepare($createQuery);
$dateNow = date('Y-m-d H:i:s');
$response->bindParam(":email", $email);
$response->bindParam(":firstName", $fName);
$response->bindParam(":middleName", $middleName);
$response->bindParam(":lastName", $lName);
$response->bindParam(":phoneNum", $phoneNum);
$response->bindParam(":birthday", $birthday);
$response->bindParam(":username", $username);
$response->bindParam(":password", password_hash($password, PASSWORD_ARGON2ID));
$response->bindParam(":createdAt", $dateNow);
return $response->execute();
}

public function readUser() {
$selectQuery = "SELECT * FROM tbl_users";
$response = $this->conn->prepare($selectQuery);
$response->execute();
return $response;
}

public function getUserByUsernameOrEmail($identifier) {
    $query = "SELECT * FROM tbl_users WHERE users_username = :identifier OR users_email = :identifier LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':identifier', $identifier);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

 public function updateUser($uID, $email, $fName, $middleName, $lName, $phoneNum, $birthday, $username): mixed {
        $updateQuery = "UPDATE tbl_users 
                        SET users_email = :email, 
                            users_firstName = :firstName, 
                            users_middleName = :middleName, 
                            users_lastName = :lastName, 
                            users_phoneNum = :phoneNum, 
                            users_birthday = :birthday, 
                            users_username = :username, 
                            updatedAt = :updatedAt 
                        WHERE usersID = :usersID";
                        
        $response = $this->conn->prepare($updateQuery);
        $dateNow = date('Y-m-d H:i:s');
        
        $response->bindValue(":usersID", $uID); 
        $response->bindValue(":email", $email);
        $response->bindValue(":firstName", $fName);
        $response->bindValue(":middleName", $middleName);
        $response->bindValue(":lastName", $lName);
        $response->bindValue(":phoneNum", $phoneNum);
        $response->bindValue(":birthday", $birthday);
        $response->bindValue(":username", $username);
        $response->bindValue(":updatedAt", $dateNow);
        
        $response->execute();
        return $response;
    }

    public function deleteUser($usersID): mixed {
        $deleteQuery = "DELETE FROM tbl_users WHERE usersID = :usersID";
        $response = $this->conn->prepare($deleteQuery);
        $response->bindValue(":usersID", $usersID);
        $response->execute();
        
        return $response;
    }
}