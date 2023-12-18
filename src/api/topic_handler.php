<?php
require_once 'dbconn.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'addTopic':
            $headerId = $_POST["moduleHeaderId"];
            $title = $_POST["title"];
            $textContent = $_POST["content"];
            $imgContent = $_POST["fileName"];

            $insertSuccess = insertTopic($headerId, $title, $textContent, $imgContent);
            if ($insertSuccess) {
                echo json_encode(array('status' => 'success', 'message' => 'Topic Created Successfully'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Failed'));
            }
            break;
        case 'getTopicDetails':
            $topicId = $_POST["topicId"];

            $topicDetails = getTopicDetails($topicId);
            if ($topicDetails) {
                // User details found, send them as a JSON response
                header('Content-Type: application/json');
                echo json_encode($topicDetails);
            } else {
                echo json_encode(array("error" => "Error fetching topic details"));
            }
            break;
        case 'getTopicDetailsByHeaderId':
            $headerId = $_POST["moduleHeaderId"];

            $topicListsByHeaderId = getTopicDetailsByHeaderId($headerId);
            if ($topicListsByHeaderId) {
                header('Content-Type: application/json');
                echo json_encode($topicListsByHeaderId);
            } else {
                echo json_encode(array("error" => "Error fetching topic details"));
            }
            break;
        case 'deleteTopic':
            $topicId = $_POST["topicId"];

            $topicDeleted = deleteTopic($topicId);
            if ($topicDeleted) {
                echo json_encode(array('status' => 'success', 'message' => 'Topic Deleted Successfully'));
            } else {
                echo json_encode(array("error" => "Error Deleting Module"));
            }
            break;
        case 'uploadImageContent':
            $fileName = $_POST["fileName"];
            $uploadedFile = $_FILES["fileInput"];

            $fileTmpName = $uploadedFile["tmp_name"];
            $fileSize = $uploadedFile["size"];
            $fileType = $uploadedFile["type"];
            $fileError = $uploadedFile["error"];

            $targetDirectory = "../../media/img/topicImages";
            $targetFilePath = $targetDirectory . '/' . $fileName;

            $maxFileSize = 5 * 1024 * 1024;
            if ($fileSize > $maxFileSize) {
                echo json_encode(["status" => "error", "message" => "File size exceeds 5MB limit."]);
                exit;
            }

            $allowedFileTypes = ['image/png', 'image/jpeg'];
            if (!in_array($fileType, $allowedFileTypes)) {
                echo json_encode(["status" => "error", "message" => "Invalid file type. Only PNG and JPEG are allowed."]);
                exit;
            }

            if ($fileError !== UPLOAD_ERR_OK) {
                echo json_encode(["status" => "error", "message" => "File upload error."]);
                exit;
            }

            // Check if the file already exists in the target directory
            if (file_exists($targetFilePath)) {
                echo json_encode(["status" => "success", "message" => "File uploaded successfully."]);
            }

            if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                echo json_encode(["status" => "success", "message" => "File uploaded successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error moving the file."]);
            }
            break;
        default:
            echo json_encode(array('status' => 'error', 'message' => 'Invalid action'));
            break;
    }
}

function insertTopic($headerId, $title, $textContent, $imgContent)
{
    global $dbConn;

    // Check if the topic with the given headerId already exists
    $query = "SELECT COUNT(*) FROM ci_modules_topics WHERE TITLE = :title";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Topic with the given headerId already exists, update its information
        $query = "UPDATE ci_modules_topics 
                  SET TEXT_CONTENT = :textContent, IMG_CONTENT = :imgContent
                  WHERE TITLE = :title";
        $stmt = $dbConn->prepare($query);

        $params = [
            ':title' => $title,
            ':textContent' => $textContent,
            ':imgContent' => $imgContent
        ];
    } else {
        // Topic with the given headerId doesn't exist, perform an insert
        $query = "INSERT INTO ci_modules_topics (HEADER_ID, TITLE, TEXT_CONTENT, IMG_CONTENT) 
                  VALUES (:headerId, :title, :textContent, :imgContent)";
        $stmt = $dbConn->prepare($query);

        $params = [
            ':headerId' => $headerId,
            ':title' => $title,
            ':textContent' => $textContent,
            ':imgContent' => $imgContent
        ];
    }
    return $stmt->execute($params);
}

function getTopicDetailsByHeaderId($headerId)
{
    global $dbConn;

    $query = "SELECT `ID`, `HEADER_ID`, `TITLE`, `TEXT_CONTENT`, `IMG_CONTENT` FROM `ci_modules_topics` WHERE `HEADER_ID` = :headerId";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':headerId', $headerId, PDO::PARAM_INT);
    $stmt->execute();
    $topicDetailsByHeaderId = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $topicDetailsByHeaderId;
}

function getTopicDetails($topicId)
{
    global $dbConn;

    $query = "SELECT `ID`, `HEADER_ID`, `TITLE`, `TEXT_CONTENT`, `IMG_CONTENT` FROM `ci_modules_topics` WHERE `ID` = :topicId";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':topicId', $topicId, PDO::PARAM_INT);
    $stmt->execute();
    $topicDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    return $topicDetails;
}

function deleteTopic($topicId)
{
    global $dbConn;

    // Get the filename associated with the topic being deleted
    $query = "SELECT `IMG_CONTENT` FROM `ci_modules_topics` WHERE `ID` = :topicId";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':topicId', $topicId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // Topic not found, return false
        return false;
    }

    // Delete the file associated with the topic (if it exists)
    $imgContent = $row['IMG_CONTENT'];
    $targetFilePath = "../../media/img/topicImages/" . $imgContent;

    if (file_exists($targetFilePath)) {
        unlink($targetFilePath); // Delete the file
    }

    // Delete the topic from the database
    $query = "DELETE FROM `ci_modules_topics` WHERE `ID` = :topicId";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':topicId', $topicId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
