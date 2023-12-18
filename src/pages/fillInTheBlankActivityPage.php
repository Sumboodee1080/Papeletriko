<?php
// Get activityId from the URL parameter
if (isset($_GET['activityId'])) {
    $activityId = $_GET['activityId'];

    // Query to retrieve activity details by ID
    $query = "SELECT `ID`, `ACTIVITY_TYPE`, `START_DTTM`, `END_DTTM`, `MODULE_HEADER`, `PROPERTY_1`, `PROPERTY_2`, `PROPERTY_3` FROM `ci_activity` WHERE `ID` = :activityId";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':activityId', $activityId, PDO::PARAM_INT);
    $stmt->execute();
    $activity = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($activity) {
        $id = $activity['ID'];
        $activityType = $activity['ACTIVITY_TYPE'];
        $moduleHeader = $activity['MODULE_HEADER'];
        $property1 = $activity['PROPERTY_1'];
        $property2 = $activity['PROPERTY_2'];
        $property3 = $activity['PROPERTY_3'];
?>
        <h4>Title: <?= $property1 ?></h4>
        <div class="mt-3">
            <p><strong>Instructions:</strong> <?= $property2 ?></p>
        </div>
        <form id="activityDetailsForm" method="post" enctype="multipart/form-data">
            <input type="hidden" id="activityId" value="<?= $activityId ?>">
            <input type="hidden" id="property3_Orig" value="<?= $property3 ?>">
            <div>
                <?php
                $property3 = str_replace('_', '<input type="text" style="border: none; border-bottom: 1px solid #000; width: 100px;" class="answer" required>', $property3);
                echo $property3 ?>
            </div>
            <div style="display: flex; flex-direction:row-reverse; margin-top:10px; margin-bottom:10px;">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
<?php
    } else {
        echo "Activity not found.";
    }
} else {
    echo "Activity ID not provided.";
}
?>
<script>
    var activityDetailsForm = $("#activityDetailsForm");
    var activityId = $("#activityId").val();

    activityDetailsForm.on("submit", function(event) {
        event.preventDefault();

        if (window.confirm("Are you sure you want to submit your activity?")) {
            // Get the original property3 text
            var originalText = $('#property3_Orig').val();

            // Create a copy of the original text
            var finalText = originalText;

            // Loop through all input fields
            $("input.answer").each(function(index) {
                var $input = $(this);
                var inputText = '<b>' + $input.val() + '</b>';

                // Replace the first occurrence of "_" in finalText with the inputText
                finalText = finalText.replace("_", inputText);

                // Find the corresponding answer input (adjust the selector if needed)
                var $answerInput = $input.siblings(".answers");
                var answerText = $answerInput.val();

                // Replace the first occurrence of "*" in finalText with the answerText
                finalText = finalText.replace("*", answerText);
            });

            var data = {
                activityId: activityId,
                property_1: finalText,
                action: "submitActivity"
            };

            $.ajax({
                type: "POST",
                url: "./src/api/activity_handler.php",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        alert("Submitted Succesfully");
                        var baseUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                        window.history.replaceState({}, document.title, baseUrl);
                        window.location.reload(); // Reloads the current page
                    } else if (response.status === "error") {
                        alert("Error Submitting Activity")
                        return;
                    }

                },
                error: function(xhr, status, error) {
                    alert("Error Submitting Activity")
                    return;
                }
            });
        } else {
            return;
        }
    })
</script>