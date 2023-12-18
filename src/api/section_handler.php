<?php
require_once 'dbconn.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'insertSection':
            $sectionName = $_POST['sectionName'];

            $insertSuccess = insertSection($sectionName);
            if ($insertSuccess) {
                echo json_encode(array('status' => 'success', 'message' => 'Section Created Successfully'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Failed'));
            }
            break;
        case 'deleteSection':
            $sectionId = $_POST["sectionId"];
            $sectionName = $_POST["sectionName"];

            $deleteSection = deleteSection($sectionId, $sectionName);
            if ($deleteSection) {
                echo json_encode(array('status' => 'success', 'message' => 'Section Deleted Successfully'));
            } else {
                echo json_encode(array('status' => 'error'));
            }
            break;
        default:
            echo json_encode(array('status' => 'error', 'message' => 'Invalid action'));
            break;
    }
}

function insertSection($sectionName)
{
    global $dbConn;

    // Check if the SECTION_NAME already exists
    $checkQuery = "SELECT COUNT(*) FROM ci_section WHERE SECTION_NAME = :sectionName";
    $stmtCheck = $dbConn->prepare($checkQuery);
    $stmtCheck->bindParam(':sectionName', $sectionName, PDO::PARAM_STR);
    $stmtCheck->execute();
    $existingCount = $stmtCheck->fetchColumn();

    if ($existingCount > 0) {
        return false; // SECTION_NAME already exists, return false to indicate failure
    }

    // If SECTION_NAME doesn't exist, insert the new record
    $insertQuery = "INSERT INTO ci_section(SECTION_NAME) VALUES (:sectionName)";
    $stmtInsert = $dbConn->prepare($insertQuery);
    $stmtInsert->bindParam(':sectionName', $sectionName, PDO::PARAM_STR);

    return $stmtInsert->execute();
}


function deleteSection($sectionId, $sectionName)
{
    global $dbConn;
    
    // Check if the section is referenced in ci_user table
    $queryCheckUser = "SELECT COUNT(*) FROM `ci_user` WHERE `SECTION` = :sectionName";
    $stmtCheckUser = $dbConn->prepare($queryCheckUser);
    $stmtCheckUser->bindParam(':sectionName', $sectionName, PDO::PARAM_STR);
    $stmtCheckUser->execute();
    $userReferences = $stmtCheckUser->fetchColumn();

    // Check if the section is referenced in ci_modules_headers table
    $queryCheckModules = "SELECT COUNT(*) FROM `ci_modules_headers` WHERE `SECTION` = :sectionName";
    $stmtCheckModules = $dbConn->prepare($queryCheckModules);
    $stmtCheckModules->bindParam(':sectionName', $sectionName, PDO::PARAM_STR);
    $stmtCheckModules->execute();
    $moduleReferences = $stmtCheckModules->fetchColumn();

    // If the section is referenced in either table, do not delete
    if ($userReferences > 0 || $moduleReferences > 0) {
        return false; // You can return false to indicate that the deletion was not successful
    }

    // If not referenced, delete the section
    $queryDelete = "DELETE FROM `ci_section` WHERE `ID` = :sectionId";
    $stmtDelete = $dbConn->prepare($queryDelete);
    $stmtDelete->bindParam(':sectionId', $sectionId, PDO::PARAM_INT);

    return $stmtDelete->execute();
}

