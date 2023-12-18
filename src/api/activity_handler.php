<?php
require_once 'dbconn.php';
session_start();

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'insertActivity':
            $activityType = $_POST['activityType'];
            $moduleId = $_POST['moduleId'];
            $startDttm = $_POST['startDttm'];
            $endDttm = $_POST['endDttm'];
            $title = $_POST['title'];
            $instruction = $_POST['instruction'];
            $activityContent = $_POST['activityContent'];

            $insertSuccess = insertActivity($activityType, $moduleId, $startDttm, $endDttm, $title, $instruction, $activityContent);
            if ($insertSuccess) {
                echo json_encode(array('status' => 'success', 'message' => 'Activity Created Successfully'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Failed'));
            }
            break;
        case 'submitActivity':
            $activityId = $_POST['activityId'];
            $property_1 = $_POST['property_1'];

            $submittedSuccessfully = submitActivity($activityId, $property_1);
            if ($submittedSuccessfully) {
                echo json_encode(array('status' => 'success', 'message' => 'Activity Submitted Successfully'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Failed'));
            }
            break;
        default:
            echo json_encode(array('status' => 'error', 'message' => 'Invalid action'));
            break;
    }
}

function insertActivity($activityType, $moduleId, $startDttm, $endDttm, $title, $instruction, $activityContent)
{
    global $dbConn;

    // Define the SQL query
    $query = "INSERT INTO ci_activity (ACTIVITY_TYPE, START_DTTM, END_DTTM, MODULE_HEADER, PROPERTY_1, PROPERTY_2, PROPERTY_3, ACTIVE_SW) 
              VALUES (:activityType, :startDttm, :endDttm, :moduleHeader, :title, :instruction, :activityContent, 1)";

    // Prepare the SQL statement
    $stmt = $dbConn->prepare($query);

    // Bind parameters
    $params = [
        ':activityType' => $activityType,
        ':startDttm' => $startDttm,
        ':endDttm' => $endDttm,
        ':moduleHeader' => $moduleId,
        ':title' => $title,
        ':instruction' => $instruction,
        ':activityContent' => $activityContent
    ];

    // Execute the query and return the result
    return $stmt->execute($params);
}

function submitActivity($activityId, $property_1){
    global $dbConn;
    
    // Get the user ID from the session
    $userId = $_SESSION['ID'];

    // Define the query to retrieve END_DTTM for the given activity
    $endDttmQuery = "SELECT END_DTTM FROM ci_activity WHERE ID = :activityId";
    $endDttmStmt = $dbConn->prepare($endDttmQuery);
    $endDttmStmt->bindParam(':activityId', $activityId, PDO::PARAM_INT);
    $endDttmStmt->execute();
    $endDttmResult = $endDttmStmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if the activity is submitted past the given END_DTTM
    if ($endDttmResult && strtotime($endDttmResult['END_DTTM']) < strtotime(date('Y-m-d H:i:s'))) {
        $lateSubmission = 1; // Set LATESUBMISSION_SW to true
    } else {
        $lateSubmission = 0; // Set LATESUBMISSION_SW to false
    }

    // Define the insert query for ci_activity_submission
    $insertQuery = "INSERT INTO ci_activity_submission (ACTIVITY_ID, USER_ID, LATESUBMISSION_SW, PROPERTY_1) 
                    VALUES (:activityId, :userId, :lateSubmission, :property1)";
    
    // Prepare the insert statement
    $insertStmt = $dbConn->prepare($insertQuery);

    // Bind parameters
    $insertStmt->bindParam(':activityId', $activityId, PDO::PARAM_INT);
    $insertStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $insertStmt->bindParam(':lateSubmission', $lateSubmission, PDO::PARAM_INT);
    $insertStmt->bindParam(':property1', $property_1, PDO::PARAM_STR);
    
    // Execute the insert query
    return $insertStmt->execute();
}
