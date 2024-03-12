<?php
require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../../');
$dotenv->load();

$host = $_ENV["DATABASE_HOSTNAME"];
$dbname = $_ENV["DATABASE_NAME"];
$username = $_ENV["DATABASE_USERNAME"];
$password = $_ENV["DATABASE_PASSWORD"];

try {
    $dbConn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function runQuery($query, $params = [])
{
    global $dbConn;
    $stmt = $dbConn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function hasPrivilege($requiredPrivilege)
{
    if (isset($_SESSION['USER_GROUP']) && !empty($_SESSION['USER_GROUP'])) {
        $memberType = $_SESSION['USER_GROUP'];

        $privileges = array(
            'Admin' => array('modules', 'profile', 'management', 'userManagement', 'moduleManagement', 'createModule', 'createTopic', 'sectionManagement', 'viewModule', 'activityType', 'essay', 'fillInTheBlank', 'viewSubmissions', 'viewActivitySubmission'),
            'Regular' => array('modules', 'profile', 'viewModule', 'viewActivity', 'essayAct', 'fillTheBlank')
        );

        if (isset($privileges[$memberType]) && in_array($requiredPrivilege, $privileges[$memberType])) {
            return true;
        }
    }
    return false;
}
