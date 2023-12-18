<?php
require_once 'dbconn.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'insertHeader':
            $moduleTitle = $_POST["title"];
            $description = $_POST["shortDescription"];
            $longDescription = $_POST["longDescription"];
            $section = $_POST["section"];
            $fileName = $_POST["fileName"];

            $insertSuccess = insertModuleHeader($moduleTitle, $description, $longDescription, $section, $fileName);
            if ($insertSuccess) {
                echo json_encode(array('status' => 'success', 'message' => 'Module Created Successfully'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Failed'));
            }
            break;
        case 'uploadCover':
            $fileName = $_POST["fileName"];
            $uploadedFile = $_FILES["fileInput"];

            $fileTmpName = $uploadedFile["tmp_name"];
            $fileSize = $uploadedFile["size"];
            $fileType = $uploadedFile["type"];
            $fileError = $uploadedFile["error"];

            $targetDirectory = "../../media/img/moduleCovers";
            $targetFilePath = $targetDirectory . '/' . $fileName;

            $maxFileSize = 5 * 1024 * 1024; 
            if ($fileSize > $maxFileSize) {
                echo json_encode(array('status' => 'error', 'message' => 'File size exceeds 5MB limit.'));
                exit;
            }

            $allowedFileTypes = ['image/png', 'image/jpeg'];
            if (!in_array($fileType, $allowedFileTypes)) {
                echo json_encode(array('status' => 'error', 'message' => 'Invalid file type. Only PNG and JPEG are allowed.'));
                exit;
            }

            if ($fileError !== UPLOAD_ERR_OK) {
                echo json_encode(array('status' => 'error', 'message' => 'File upload error.'));
                exit;
            }

            if (file_exists($targetFilePath)) {
                echo json_encode(["status" => "success", "message" => "File uploaded successfully."]);
            }

            if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                echo json_encode(array('status' => 'success', 'message' => 'File uploaded successfully.'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Error moving the file.'));
            }

            break;
        case 'getModuleDetails':
            $moduleId = $_POST["moduleId"];

            $moduleDetails = getModuleDetails($moduleId);
            if ($moduleDetails) {
                // User details found, send them as a JSON response
                header('Content-Type: application/json');
                echo json_encode($moduleDetails);
            } else {
                echo json_encode(array("error" => "Error fetching module details"));
            }
            break;
        case 'deleteModule':
            $moduleId = $_POST["moduleId"];

            $deleteModule = deleteModuleById($moduleId);
            if ($deleteModule) {
                echo json_encode(array('status' => 'success', 'message' => 'Module Deleted Successfully'));
            } else {
                echo json_encode(array("error" => "Error Deleting Module"));
            }
            break;
        default:
            echo json_encode(array('status' => 'error', 'message' => 'Invalid action'));
            break;
    }
}

function insertModuleHeader($moduleTitle, $description, $longDescription, $section, $fileName)
{
    global $dbConn;

    // Check if the module with the given moduleTitle already exists
    $query = "SELECT COUNT(*) FROM ci_modules_headers WHERE MODULE_TITLE = :moduleTitle";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':moduleTitle', $moduleTitle);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Module with the given moduleTitle already exists, update its information
        $query = "UPDATE ci_modules_headers 
                  SET DESCRIPTION = :description, LONG_DESCRIPTION = :longDescription, SECTION = :section, COVER_FILENAME = :fileName
                  WHERE MODULE_TITLE = :moduleTitle";
        $stmt = $dbConn->prepare($query);

        $params = [
            ':description' => $description,
            ':longDescription' => $longDescription,
            ':section' => $section,
            ':moduleTitle' => $moduleTitle,
            ':fileName' => $fileName
        ];
    } else {
        // Module with the given moduleTitle doesn't exist, perform an insert
        $query = "INSERT INTO ci_modules_headers (MODULE_TITLE, DESCRIPTION, LONG_DESCRIPTION, SECTION, COVER_FILENAME) 
                  VALUES (:moduleTitle, :description, :longDescription, :section, :fileName)";
        $stmt = $dbConn->prepare($query);

        $params = [
            ':moduleTitle' => $moduleTitle,
            ':description' => $description,
            ':longDescription' => $longDescription,
            ':section' => $section,
            ':fileName' => $fileName
        ];
    }
    return $stmt->execute($params);
}

function getModuleDetails($moduleId)
{
    global $dbConn;

    $query = "SELECT `ID`, `MODULE_TITLE`, `DESCRIPTION`, `LONG_DESCRIPTION`, `SECTION`, `COVER_FILENAME` FROM `ci_modules_headers` WHERE `ID` = :moduleId";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
    $stmt->execute();
    $moduleDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    return $moduleDetails;
}

function deleteModuleById($moduleId)
{
    global $dbConn;

    // Get the filename associated with the module being deleted
    $query = "SELECT `COVER_FILENAME` FROM `ci_modules_headers` WHERE `ID` = :moduleId";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // Module not found, return false
        return false;
    }

    // Delete the file associated with the module (if it exists)
    $coverFilename = $row['COVER_FILENAME'];
    $targetFilePath = "../../media/img/moduleCovers/" . $coverFilename;
    
    if (file_exists($targetFilePath)) {
        unlink($targetFilePath); // Delete the file
    }

    // Check if the module is referenced in ci_modules_topics
    $query = "SELECT COUNT(*) FROM `ci_modules_topics` WHERE `HEADER_ID` = :moduleId";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Module is referenced in ci_modules_topics, do not delete
        return false;
    }

    // If not referenced and file deleted, delete the module
    $query = "DELETE FROM `ci_modules_headers` WHERE `ID` = :moduleId";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}


