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

        // Split PROPERTY_3 into individual topics
        $topics = explode("**", $property3);
?>
        <h4>Title: <?= $property1 ?></h4>
        <div class="mt-3">
            <pre><strong>Instructions:</strong> <?= $property2 ?></pre>
        </div>
        <form id="activityDetailsForm" method="post" enctype="multipart/form-data">
            <input type="hidden" id="activityId" value="<?= $activityId ?>">
            <div class="mt-4">
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    <?php
                    // Loop through topics and create cards
                    foreach ($topics as $index => $topic) {
                        $topic = trim($topic);
                        if (!empty($topic)) {
                    ?>
                            <div class="col">
                                <div class="card">
                                    <div class="card-header text-center">
                                        <p class="instructions"><?= $topic ?></p>
                                    </div>
                                    <div class="card-body">
                                        <textarea class="form-control answers" id="answer<?= $index + 1 ?>" name="answer<?= $index + 1 ?>" rows="8" required></textarea>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div style="display: flex; flex-direction:row-reverse; margin-top:10px; margin-bottom:10px;">
                <button type="submit" class="btn btn-primary">Ipasa</button>
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
            // Create an array to store formatted topic and answer pairs
            var formattedData = [];

            // Loop through all card elements
            $(".card").each(function(index) {
                var $card = $(this);
                var topic = $card.find(".instructions").text();
                var answer = $card.find(".answers").val();

                // Create the topic and answer pair
                var topicText = "|>" + topic + "*_" + answer;
                formattedData.push(topicText);
            });

            // Join the formattedData array into a single string
            var finalText = formattedData.join('\n');

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