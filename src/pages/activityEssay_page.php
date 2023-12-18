<h5>Create Essay Activity</h5>
<hr>
<form id="essayActivityForm" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">
            <h6>Activity Properties</h6>
            <div class="mb-3">
                <label for="module" class="form-label">Module</label>
                <select id="module" class="form-select">
                    <?php
                    $query = "SELECT `ID`, `MODULE_TITLE` FROM `ci_modules_headers` ";
                    $results = runQuery($query, $params);
                    if (!empty($results)) {
                        foreach ($results as $row) {
                            // Output an option for each row in the table
                            echo '<option value="' . $row['ID'] . '">' . $row['MODULE_TITLE'] . '</option>';
                        }
                    } else {
                        echo '<option selected></option>';
                    }
                    ?>

                </select>
            </div>
            <div class="mb-3">
                <label for="startDttm" class="form-label">Start Date and Time</label>
                <input type="date" class="form-control mb-3" id="startDate" name="startDate" required>
                <input type="time" class="form-control" id="startTime" name="startTime" required>
            </div>
            <div class="mb-3">
                <label for="endDttm" class="form-label">End Date and Time</label>
                <input type="date" class="form-control mb-3" id="endDate" name="endDate" required>
                <input type="time" class="form-control" id="endTime" name="endTime" required>
            </div>
            <p><strong>Note:</strong>This activity will only be visible to the students within the given Start and End Date Time.</p>
        </div>
        <div class="col-md-6">
            <h6>Activity Details</h6>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="instruction" class="form-label">Instruction</label>
                <textarea class="form-control" id="instruction" name="instruction" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label for="activityContent" class="form-label">Activity Content</label>
                <textarea class="form-control" id="activityContent" name="activityContent" rows="8" required></textarea>
                <p><strong>Note:</strong> Seperate each Essay using the symbol **. For example;<br>Write a summary of the first Topic.**<br>Write a summary of the second Topic.**<br>It will then be rendered as two different cards asking for inputs after each instruction.</p>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>
<script>
    var essayActivityForm = $("#essayActivityForm");

    essayActivityForm.on("submit", function(event) {
        event.preventDefault();

        var startDate = new Date($("#startDate").val());
        var startTime = $("#startTime").val();
        var endDate = new Date($("#endDate").val());
        var endTime = $("#endTime").val();

        var startDateTime = new Date(startDate.toDateString() + " " + startTime);
        var endDateTime = new Date(endDate.toDateString() + " " + endTime);

        // Check if startDateTime is a future date
        var now = new Date();
        if (startDateTime < now) {
            alert("Start Date and Time must be a future date and time.");
            return;
        }

        // Check if endDateTime is sooner than startDateTime
        if (endDateTime <= startDateTime) {
            alert("End Date and Time cannot be sooner than Start Date and Time.");
            return;
        }

        var startYear = startDateTime.getFullYear();
        var startMonth = startDateTime.getMonth() + 1; // Months are 0-indexed, so add 1
        var startDay = startDateTime.getDate();
        var startHours = startDateTime.getHours();
        var startMinutes = startDateTime.getMinutes();
        var startSeconds = startDateTime.getSeconds();

        var mysqlFormattedStartDateTime = startYear + '-' +
            (startMonth < 10 ? '0' : '') + startMonth + '-' +
            (startDay < 10 ? '0' : '') + startDay + ' ' +
            (startHours < 10 ? '0' : '') + startHours + ':' +
            (startMinutes < 10 ? '0' : '') + startMinutes + ':' +
            (startSeconds < 10 ? '0' : '') + startSeconds;

        // Get the individual components for endDateTime
        var endYear = endDateTime.getFullYear();
        var endMonth = endDateTime.getMonth() + 1; // Months are 0-indexed, so add 1
        var endDay = endDateTime.getDate();
        var endHours = endDateTime.getHours();
        var endMinutes = endDateTime.getMinutes();
        var endSeconds = endDateTime.getSeconds();

        // Create a MySQL-formatted date and time string for endDateTime
        var mysqlFormattedEndDateTime = endYear + '-' +
            (endMonth < 10 ? '0' : '') + endMonth + '-' +
            (endDay < 10 ? '0' : '') + endDay + ' ' +
            (endHours < 10 ? '0' : '') + endHours + ':' +
            (endMinutes < 10 ? '0' : '') + endMinutes + ':' +
            (endSeconds < 10 ? '0' : '') + endSeconds;


        var data = {
            activityType: "Essay",
            moduleId: $("#module").val(),
            startDttm: mysqlFormattedStartDateTime,
            endDttm: mysqlFormattedEndDateTime,
            title: $("#title").val(),
            instruction: $("#instruction").val(),
            activityContent: $("#activityContent").val(),
            action: "insertActivity"
        };

        $.ajax({
            type: "POST",
            url: "./src/api/activity_handler.php",
            data: data,
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    alert("Activity Successfully Submitted")
                    essayActivityForm[0].reset();
                    location.reload();
                } else if (response.status === "error") {
                    alert("Error During Activity Creation")
                    return;
                }

            },
            error: function(xhr, status, error) {
                alert("Error During Activity Creation")
                return;
            }
        });
    });
</script>