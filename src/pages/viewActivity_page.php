<?php
$moduleId = $_GET['moduleId']; // Get the moduleId from the URL

// Fetch Subject Name from the database
$query = "SELECT ID, MODULE_TITLE FROM ci_modules_headers WHERE ID = :moduleId";
$stmt = $dbConn->prepare($query);
$stmt->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
$stmt->execute();
$subjectInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch activities for the module
$currentDateTime = date("Y-m-d H:i:s");
$query = "SELECT a.ID, a.PROPERTY_1 AS ActivityName, a.ACTIVITY_TYPE, a.END_DTTM, s.ID AS SubmissionID
          FROM ci_activity a
          LEFT JOIN ci_activity_submission s ON a.ID = s.ACTIVITY_ID AND s.USER_ID = :userId
          WHERE a.MODULE_HEADER = :moduleId AND :currentDateTime BETWEEN a.START_DTTM AND a.END_DTTM AND a.ACTIVE_SW = 1";
$stmt = $dbConn->prepare($query);
$stmt->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
$stmt->bindParam(':userId', $_SESSION['ID'], PDO::PARAM_INT);
$stmt->bindParam(':currentDateTime', $currentDateTime, PDO::PARAM_STR);
$stmt->execute();
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mb-5">
    <h4>Mga aktibidad para sa: <?php echo $subjectInfo['MODULE_TITLE']; ?></h4>
</div>
<div>
    <?php
    $unsubmittedActivities = false; // Flag to track unsubmitted activities
    foreach ($activities as $activity) {
        if (!$activity['SubmissionID']) {
            $unsubmittedActivities = true;
    ?>
            <div class="mb-3">
                <div class="card" style="padding: 15px;">
                    <h5><?php echo $activity['ActivityName']; ?></h5>
                    <label>Klase ng gawain: <?php echo $activity['ACTIVITY_TYPE']; ?></label>
                    <label>Deadline: <?php echo date('Y-m-d h:i a', strtotime($activity['END_DTTM'])); ?></label>
                    <div class="mt-4">
                        <?php
                        $page = '';
                        if ($activity['ACTIVITY_TYPE'] == 'Fill in the Blank') {
                            $page = 'fillTheBlank';
                        } elseif ($activity['ACTIVITY_TYPE'] == 'Essay') {
                            $page = 'essayAct';
                        }
                        ?>
                        <a href="?page=<?php echo $page; ?>&activityId=<?php echo $activity['ID']; ?>" class="btn btn-primary">Kumuha ng Aktibidad <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>

    <?php if (!$unsubmittedActivities) { ?>
        <div style="text-align: center;">
            <p>Walang Hindi Naisumite na Mga Aktibidad na Ipapakita</p>
        </div>
    <?php } ?>
</div>