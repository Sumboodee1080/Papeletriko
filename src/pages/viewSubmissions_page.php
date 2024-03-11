<?php
if (isset($_GET['activityId'])) {
    $activityId = $_GET['activityId'];

    $query = "SELECT 
                CONCAT(ci_user.FIRST_NAME, ' ', ci_user.LAST_NAME) AS Name,
                `SUBMISSION_DTTM`,
                `LATESUBMISSION_SW`,
                `PROPERTY_1`
            FROM 
                `ci_activity_submission` AS actSub
            INNER JOIN
                ci_user ON ci_user.ID = actSub.USER_ID
            WHERE
                actSub.ACTIVITY_ID = :activityId;";

    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':activityId', $activityId, PDO::PARAM_INT);
    $stmt->execute();
    $activitySubmissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($activitySubmissions) {
        foreach ($activitySubmissions as $index => $submission) {
            $name = $submission['Name'];
            $submissionDttm = date('Y-m-d h:i a', strtotime($submission['SUBMISSION_DTTM'])); // Format date and time
            $lateSubmissionSw = $submission['LATESUBMISSION_SW'];
            $property1 = $submission['PROPERTY_1'];

            // Process property1 to replace |> with <p><strong> and *_ with </strong></p>
            $property1 = str_replace('|>', '</strong></p><p><strong>', $property1);
            $property1 = str_replace('*_', '</strong></p><p>', $property1);
?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="submissionHeading<?= $index ?>">
                    <button class="accordion-button<?= ($index === 0) ? '' : ' collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#submissionCollapse<?= $index ?>" aria-expanded="<?= ($index === 0) ? 'true' : 'false' ?>" aria-controls="submissionCollapse<?= $index ?>">
                        Isinumite ni: <?= $name ?>
                    </button>
                </h2>
                <div id="submissionCollapse<?= $index ?>" class="accordion-collapse collapse<?= ($index === 0) ? ' show' : '' ?>" aria-labelledby="submissionHeading<?= $index ?>" data-bs-parent="#activitySubmissionsAccordion">
                    <div class="accordion-body">
                        <p><strong>Petsa ng Pagsumite: </strong><?= $submissionDttm ?></p>
                        <p><strong>Huling Pagsusumite: </strong><?= ($lateSubmissionSw == 1) ? 'Yes' : 'No' ?></p>
                        <?= $property1 ?>
                    </div>
                </div>
            </div>
        <?php
        }
    } else {
        ?>
        <div style="text-align: center;">
            <p>Wala pang isinumite.</p>
        </div>
    <?php
    }
} else {
    ?>
    <div style="text-align: center;">
        <p>Walang Tinukoy na Activity ID.</p>
    </div>
<?php
}
?>