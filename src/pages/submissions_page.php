<?php
$moduleId = $_GET['moduleId']; // Get the moduleId from the URL

// Fetch Subject Name from the database
$query = "SELECT ID, MODULE_TITLE FROM ci_modules_headers WHERE ID = :moduleId";
$stmt = $dbConn->prepare($query);
$stmt->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
$stmt->execute();
$subjectInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch activities for the module
$query = "SELECT * FROM ci_activity WHERE `MODULE_HEADER` = :moduleId AND `ACTIVE_SW` = 1 ORDER BY START_DTTM DESC LIMIT 10";
$stmt = $dbConn->prepare($query);
$stmt->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
$stmt->execute();
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mb-5">
    <h4>Activities For: <?php echo $subjectInfo['MODULE_TITLE']; ?></h4>
    <p><strong>Note:</strong> Only the 10 most recent activities will be displayed.</p>
</div>
<div>
    <?php
    if (empty($activities)) {
    ?>
        <div style="text-align: center;">
            <label>No Activity Available for the given module.</label>
        </div>
        <?php
    } else {
        foreach ($activities as $activity) {
        ?>
            <div class="mb-3">
                <div class="card" style="padding: 15px;">
                    <h5><?php echo $activity['PROPERTY_1']; ?></h5>
                    <label><strong>Instructions: </strong><?php echo $activity['PROPERTY_2']; ?></label>
                    <label><strong>Activity Type:</strong> <?php echo $activity['ACTIVITY_TYPE']; ?></label>
                    <label><strong>Given On: </strong><?php echo date('Y-m-d h:i a', strtotime($activity['START_DTTM'])); ?></label>
                    <label><strong>Deadline: </strong><?php echo date('Y-m-d h:i a', strtotime($activity['END_DTTM'])); ?></label>
                    <div class="mt-4">
                        <a href="?page=viewActivitySubmission&activityId=<?php echo $activity['ID']; ?>" class="btn btn-primary">View Submissions <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
    <?php }
    } ?>
</div>