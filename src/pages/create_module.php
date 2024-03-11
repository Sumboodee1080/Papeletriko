<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <h5>Paglikha ng Aralin</h5>
            <form id="moduleDetailsForm" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Pamagat</label>
                    <input type="text" class="form-control" id="title" name="title" maxlength="51" required>
                </div>
                <div class="mb-3">
                    <label for="shortDescription" class="form-label">Maikling Paglalarawan</label>
                    <textarea class="form-control" id="shortDescription" name="shortDescription" rows="2" maxlength="75" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="longDescription" class="form-label">Layunin</label>
                    <textarea class="form-control" id="longDescription" name="longDescription" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="section" class="form-label">Seksyon</label>
                    <select id="section" class="form-select">
                        <?php
                        $query = "SELECT `ID`, `SECTION_NAME` FROM `ci_section`";
                        $results = runQuery($query, $params);
                        if (!empty($results)) {
                            foreach ($results as $row) {
                                // Output an option for each row in the table
                                echo '<option value="' . $row['SECTION_NAME'] . '">' . $row['SECTION_NAME'] . '</option>';
                            }
                        } else {
                            echo '<option selected></option>';
                        }
                        ?>

                    </select>
                </div>
                <div class="mb-3">
                    <label for="fileInput" class="form-label">Mag-upload ng Cover ng Module</label>
                    <input type="file" class="form-control" id="fileInput" name="fileInput">
                    <span id="coverFileNameDisplay"></span>
                </div>
                <button type="submit" id="submitBtn" class="btn btn-primary">Magdagdag / Mag-update</button>
            </form>

        </div>

        <!-- User List -->
        <div class="col-md-6 userList">
            <h5>Listahan ng mga Aralin</h5>
            <div style="margin-top: 10px;" class="accordion" id="modulesAccordion">
                <?php
                // Execute the query to fetch data from the database
                $query = "SELECT `ID`, `MODULE_TITLE`, `DESCRIPTION`, `LONG_DESCRIPTION`, `SECTION` FROM `ci_modules_headers`";
                $modules = runQuery($query, $params);

                foreach ($modules as $key => $module) {
                    $ID = $module['ID'];
                    $moduleTitle = $module['MODULE_TITLE'];
                    $moduleDescription = $module['DESCRIPTION'];
                    $moduleLongDescription = $module['LONG_DESCRIPTION'];
                ?>
                    <div class="card">
                        <div class="card-body" id="module<?= $key + 1 ?>Heading" data-bs-toggle="collapse" data-bs-target="#module<?= $key + 1 ?>Collapse" aria-expanded="true" aria-controls="module<?= $key + 1 ?>Collapse">
                            <div style="display: flex; justify-content:space-between; align-items:center">
                                <h6><?= $moduleTitle ?></h6>
                                <div>
                                    <i class="bi bi-pencil-square" style="cursor: pointer; margin-right:10px;" onclick="fetchModuleDetails(<?= $ID ?>)"></i>
                                    <i class="bi bi-x-circle-fill" style="color: red; cursor: pointer;" onclick="deleteModule(<?= $ID ?>)"></i>
                                </div>
                            </div>
                        </div>
                        <div id="module<?= $key + 1 ?>Collapse" class="collapse" aria-labelledby="module<?= $key + 1 ?>Heading" data-parent="#modulesAccordion">
                            <div style="max-height: 180px; overflow-y:auto;" class="card-body">
                                <?= $moduleDescription ?>
                                <div style="height: 10px;"></div>
                                <?= $moduleLongDescription ?>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>

    </div>
</div>
<script>
    var moduleDetailsForm = $("#moduleDetailsForm");
    var fileInput = $("#fileInput");

    moduleDetailsForm.on("submit", function(event) {
        event.preventDefault();

        var file = fileInput[0].files[0];

        var data = {
            title: $("#title").val(),
            shortDescription: $("#shortDescription").val(),
            longDescription: $("#longDescription").val(),
            section: $("#section").val(),
            fileName: file.name,
            action: "insertHeader"
        };

        $.ajax({
            type: "POST",
            url: "./src/api/modules_handler.php",
            data: data,
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    uploadCover();
                    moduleDetailsForm[0].reset();
                    alert("Module Update/Insert Successfully.")
                    location.reload();
                } else if (response.status === "error") {
                    alert("Error During Module Creation")
                    return;
                }

            },
            error: function(xhr, status, error) {
                alert("Error During Module Creation")
                return;
            }
        });
    });

    function uploadCover() {
        var formData = new FormData();
        var file = fileInput[0].files[0];

        if (file) {
            formData.append("fileInput", file);
            formData.append("fileName", file.name);
            formData.append("action", "uploadCover");
            $.ajax({
                type: "POST",
                url: "./src/api/modules_handler.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log("Cover Upload Success");
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

    function fetchModuleDetails(id) {
        var data = {
            moduleId: id,
            action: "getModuleDetails"
        };

        $.ajax({
            type: "POST",
            url: "./src/api/modules_handler.php",
            data: data,
            dataType: "json",
            success: function(response) {
                if (response.error) {
                    // Handle the error case
                    alert("Module not found or an error occurred");
                } else {
                    // Populate placeholders from the data in response
                    $("#title").val(response.MODULE_TITLE);
                    $("#shortDescription").val(response.DESCRIPTION);
                    $("#longDescription").val(response.LONG_DESCRIPTION);
                    $("#section").val(response.SECTION);
                    $("#coverFileNameDisplay").text(response.COVER_FILENAME);
                    moduleDetailsForm.focus();
                    $("#title").prop("disabled", true);
                }
            },
            error: function(xhr, status, error) {
                alert("Error Displaying Module Details")
            }
        });
    }

    function deleteModule(id) {
        var confirmation = confirm("Are you sure you want to delete this module?");
        if (confirmation) {
            var data = {
                moduleId: id,
                action: "deleteModule"
            };

            $.ajax({
                type: "POST",
                url: "./src/api/modules_handler.php",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.error) {
                        // Handle the error case
                        alert("Delete Failed");
                    } else {
                        alert("Module Deleted Successfully");
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    alert("Delete Failed")
                }
            });
        }
    }
</script>