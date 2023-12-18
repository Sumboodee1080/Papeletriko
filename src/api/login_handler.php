<?php
require_once 'dbconn.php';
session_start();

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'resetPassword':
            if (isset($_POST['newPassword'])) {
                $newPassword = $_POST['newPassword'];

                $result = updateUserPassword($newPassword);
                if ($result) {
                    echo json_encode(array('status' => 'success', 'message' => 'Password has been updated successfully'));
                } else {
                    echo json_encode(array('status' => 'error', 'message' => 'Password reset failed.'));
                }
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Invalid request'));
            }
            break;
        case 'addNewUser':
            $firstName = $_POST["firstName"];
            $middleName = $_POST["middleName"];
            $lastName = $_POST["lastName"];
            $section = $_POST["section"];
            $username = $_POST["username"];
            $password = $_POST["password"];
            $userGroup = $_POST["userGroup"];

            $insertSuccess = insertOrUpdateUser($firstName, $middleName, $lastName, $section, $username, $password, $userGroup);
            if ($insertSuccess) {
                echo json_encode(array('status' => 'success', 'message' => 'User Registration Success'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Failed'));
            }
            break;
        case 'getUserInfo':
            $userId = $_POST["userId"];
            $userDetails = getUserDetails($userId);
            if ($userDetails) {
                // User details found, send them as a JSON response
                header('Content-Type: application/json');
                echo json_encode($userDetails);
            } else {
                // User not found or an error occurred
                echo json_encode(array("error" => "User not found"));
            }
            break;
        case 'deleteUser';
            $userId = $_POST["userId"];
            $isDeleted = deleteUserById($userId);

            if ($isDeleted) {
                echo json_encode(array('status' => 'success', 'message' => 'User Deleted Successfully'));
            }
            break;
        default:
            echo json_encode(array('status' => 'error', 'message' => 'Invalid action'));
            break;
    }
}

function handleLogin($username, $password)
{
    global $dbConn;

    $query = "SELECT ID, SECTION, USERNAME, PASSWORD, USER_GROUP FROM ci_user WHERE USERNAME = :username";
    $stmt = $dbConn->prepare($query);
    $stmt->execute([':username' => $username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['PASSWORD'])) {
        $_SESSION['ID'] = $user['ID'];
        $_SESSION['USER_GROUP'] = $user['USER_GROUP'];
        $_SESSION['SECTION'] = $user['SECTION'];
        return true;
    } else {
        return false;
    }
}

function insertOrUpdateUser($firstName, $middleName, $lastName, $section, $username, $password, $userGroup)
{
    global $dbConn;

    // Check if the username already exists
    $query = "SELECT COUNT(*) FROM ci_user WHERE USERNAME = :username";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($count > 0) {
        // Username already exists, update the user's information
        $query = "UPDATE ci_user 
                  SET FIRST_NAME = :firstName, MIDDLE_NAME = :middleName, LAST_NAME = :lastName, SECTION = :section, USER_GROUP = :userGroup 
                  WHERE USERNAME = :username";
        $stmt = $dbConn->prepare($query);

        $params = [
            ':firstName' => $firstName,
            ':middleName' => $middleName,
            ':lastName' => $lastName,
            ':section' => $section,
            ':username' => $username,
            ':userGroup' => $userGroup
        ];
    } else {
        // Username doesn't exist, perform an insert
        $query = "INSERT INTO ci_user (FIRST_NAME, MIDDLE_NAME, LAST_NAME, SECTION, USERNAME, PASSWORD, USER_GROUP) 
                  VALUES (:firstName, :middleName, :lastName, :section, :username, :password, :userGroup)";
        $stmt = $dbConn->prepare($query);

        $params = [
            ':firstName' => $firstName,
            ':middleName' => $middleName,
            ':lastName' => $lastName,
            ':section' => $section,
            ':username' => $username,
            ':password' => $hashedPassword,
            ':userGroup' => $userGroup
        ];
    }

    return $stmt->execute($params);
}




function updateUserPassword($newPassword)
{
    global $dbConn;
    $userId = $_SESSION['ID'];

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $query = "UPDATE ci_user SET PASSWORD = :password WHERE ID = :userId";
    $stmt = $dbConn->prepare($query);

    $params = [
        ':password' => $hashedPassword,
        ':userId' => $userId
    ];

    return $stmt->execute($params);
}

function getUserDetails($userId)
{
    global $dbConn;

    $query = "SELECT `ID`, `FIRST_NAME`, `MIDDLE_NAME`, `LAST_NAME`, `SECTION`, `USERNAME`, `USER_GROUP` FROM `ci_user` WHERE `ID` = :userId";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    return $userDetails;
}

function deleteUserById($userId)
{
    global $dbConn;

    if($_SESSION['ID'] == $userId){
        return false;
    }

    // Delete the user
    $query = "DELETE FROM `ci_user` WHERE `ID` = :userId";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
