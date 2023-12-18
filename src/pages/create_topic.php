<div>
    <div>
        <h5>Topic Management</h5>
    </div>
    <div style="margin-top: 20px;">
        <select id="module" class="form-select">
            <option selected disabled>Select Module</option>
            <?php
            $query = "SELECT `ID`, `MODULE_TITLE` FROM `ci_modules_headers`";
            $results = runQuery($query, $params);
            if (!empty($results)) {
                foreach ($results as $row) {
                    // Output an option for each row in the table
                    echo '<option value="' . $row['ID'] . '">' . $row['MODULE_TITLE'] . '</option>';
                }
            }
            ?>
        </select>
    </div>
    <div class="container mt-5" id="topicFormWrapper">
        <div class="row">
            <div class="col-md-6">
                <form id="topicDetailsForm" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" maxlength="51" required>
                    </div>
                    <div class="mb-1">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                    </div>
                    <p><strong>Note:</strong> To add an image in between text. For example, you are writing for a content about animal; <br>This is a Koala [[koala_image_480.png]]<br>The filename should correspond to the file upload available.<br>To add a link, wrap your link with [www.yourlinkHere.com]</p>
                    <div class="mb-3">
                        <label for="topicImgAttachment" class="form-label">Image Content</label>
                        <input type="file" class="form-control" id="topicImgAttachment" name="topicImgAttachment">
                        <span id="coverFileNameDisplay"></span>
                    </div>
                    <button type="submit" id="submitBtn" class="btn btn-primary">Add / Update</button>
                </form>
            </div>

            <!-- Topic List -->
            <div class="col-md-6">
                <h6>Existing Topic List</h6>
                <div id="topicAccordion" class="mt-4"></div>
            </div>
        </div>
    </div>

    <script>
        var topicDetailsForm = $("#topicDetailsForm");
        var fileInput = $("#topicImgAttachment");
        let selectedModule;
        $("#topicFormWrapper").hide();

        function uploadImageContent() {
            var formData = new FormData();
            var file = fileInput[0].files[0];

            if (file) {
                formData.append("fileInput", file);
                formData.append("fileName", file.name);
                formData.append("action", "uploadImageContent");
                $.ajax({
                    type: "POST",
                    url: "./src/api/topic_handler.php",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log("File Uploaded Success");
                    },
                    error: function(xhr, status, error) {
                        // Handle errors
                        alert(error);
                        return;
                    }
                });
            } else {
                // Handle the case where no file is selected
                alert("No file selected.");
                return
            }
        }

        $("#module").on("change", function() {
            $("#topicFormWrapper").show();
            selectedModule = $(this).val();
            loadModuleTopicList();
        });

        function loadModuleTopicList() {
            var data = {
                moduleHeaderId: selectedModule,
                action: "getTopicDetailsByHeaderId"
            };

            $.ajax({
                type: "POST",
                url: "./src/api/topic_handler.php",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.status === "error") {
                        alert("Topic creation failed.")
                        return;
                    }
                    renderTopics(response);
                },
                error: function(xhr, status, error) {
                    alert("Topic creation failed.")
                    return;
                }
            });
        }

        function fetchTopicDetails(topicId) {
            var data = {
                topicId: topicId,
                action: "getTopicDetails"
            };

            $.ajax({
                type: "POST",
                url: "./src/api/topic_handler.php",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.error) {
                        alert("Topic not found or an error occurred");
                    } else {
                        // Populate placeholders from the data in response
                        $("#title").val(response.TITLE);
                        $("#content").val(response.TEXT_CONTENT);
                        $("#coverFileNameDisplay").text(response.IMG_CONTENT);
                        $("#title").prop("disabled", true);
                    }
                },
                error: function(xhr, status, error) {
                    alert("Error Displaying Topic Details")
                }
            });
        }

        function deleteTopic(topicId) {
            var confirmation = confirm("Are you sure you want to delete this topic?");
            if (confirmation) {
                var data = {
                    topicId: topicId,
                    action: "deleteTopic"
                };

                $.ajax({
                    type: "POST",
                    url: "./src/api/topic_handler.php",
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        if (response.error) {
                            alert("Delete Failed");
                        } else {
                            alert("Topic Deleted Successfully");
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("Delete Failed")
                    }
                });
            }
        }

        function renderTopics(topicData) {
            var topicAccordion = $("#topicAccordion");
            topicAccordion.empty(); // Clear existing content

            // Loop through the topic data and create elements for each topic
            if (topicData.error) {
                var noTopicSpan = $("<span>").text("No Topic Available");
                topicAccordion.append(noTopicSpan);
            } else {
                $.each(topicData, function(index, topic) {
                    var card = $("<div>").addClass("card");
                    var cardBody = $("<div>").addClass("card-body")
                        .attr("id", "topic" + (index + 1) + "Heading")
                        .attr("data-bs-toggle", "collapse")
                        .attr("data-bs-target", "#topic" + (index + 1) + "Collapse")
                        .attr("aria-expanded", "true")
                        .attr("aria-controls", "topic" + (index + 1) + "Collapse");

                    var flexContainer = $("<div>").css({
                        "display": "flex",
                        "justify-content": "space-between",
                        "align-items": "center"
                    });

                    var topicTitle = $("<h6>").text(topic.TITLE);

                    var actionsContainer = $("<div>");
                    var editIcon = $("<i>").addClass("bi bi-pencil-square")
                        .css("cursor", "pointer")
                        .css("margin-right", "10px")
                        .on("click", function() {
                            fetchTopicDetails(topic.ID);
                        });
                    var deleteIcon = $("<i>").addClass("bi bi-x-circle-fill")
                        .css("color", "red")
                        .css("cursor", "pointer")
                        .on("click", function() {
                            deleteTopic(topic.ID);
                        });

                    actionsContainer.append(editIcon, deleteIcon);
                    flexContainer.append(topicTitle, actionsContainer);
                    cardBody.append(flexContainer);

                    var collapseDiv = $("<div>").addClass("collapse")
                        .attr("id", "topic" + (index + 1) + "Collapse")
                        .attr("aria-labelledby", "topic" + (index + 1) + "Heading")
                        .attr("data-parent", "#topicAccordion");
                    var collapseCardBody = $("<div>").addClass("card-body").text(topic.TEXT_CONTENT);

                    collapseDiv.append(collapseCardBody);
                    card.append(cardBody, collapseDiv);

                    // Append the card to the topicAccordion
                    topicAccordion.append(card);
                });
            }
        }

        topicDetailsForm.on("submit", function(event) {
            event.preventDefault();

            var file = fileInput[0].files[0];

            var data = {
                moduleHeaderId: selectedModule,
                title: $("#title").val(),
                content: $("#content").val(),
                fileName: file.name,
                action: "addTopic"
            };

            $.ajax({
                type: "POST",
                url: "./src/api/topic_handler.php",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        uploadImageContent();
                        alert("Topic Update/Insert Success");
                        topicDetailsForm[0].reset();
                        location.reload();
                    } else if (response.status === "error") {
                        alert("Topic creation failed.")
                        return;
                    }

                },
                error: function(xhr, status, error) {
                    alert("Topic creation failed.")
                    return;
                }
            });
        });
    </script>