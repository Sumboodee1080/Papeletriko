<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <h5>Pamamahala ng Seksyon</h5>
            <form id="sectionDetailsForm" method="post" enctype="multipart/form-data" class="mt-4">
                <div class="mb-3">
                    <label for="sectionName" class="form-label">Pangalan ng Seksyon</label>
                    <input type="text" class="form-control" id="sectionName" name="sectionName" maxlength="30" required>
                </div>
                <button type="submit" id="submitBtn" class="btn btn-primary">Idagdag</button>
            </form>

        </div>

        <!-- User List -->
        <div class="col-md-6">
            <h5>Umiiral na Listahan ng Seksyon</h5>
            <div class="accordion mt-3" id="sectionAccordion">
                <?php
                // Execute the query to fetch section data from the database
                $sectionQuery = "SELECT `ID`, `SECTION_NAME` FROM `ci_section`";
                $sections = runQuery($sectionQuery, $params);

                foreach ($sections as $section) {
                    $sectionID = $section['ID'];
                    $sectionName = $section['SECTION_NAME'];
                ?>
                    <div class="card">
                        <div class="card-body" id="section<?= $sectionID ?>Heading">
                            <div style="display: flex; justify-content:space-between; align-items:center">
                                <h6><?= $sectionName ?></h6>
                                <div>
                                    <i class="bi bi-x-circle-fill" style="color: red; cursor: pointer;" onclick="deleteSection(<?= $sectionID ?>, '<?= $sectionName ?>')"></i>
                                </div>
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
    var sectionDetailsForm = $("#sectionDetailsForm");

    sectionDetailsForm.on("submit", function(event) {
        event.preventDefault();

        var data = {
            action: "insertSection",
            sectionName: $("#sectionName").val()
        };

        $.ajax({
            type: "POST",
            url: "./src/api/section_handler.php",
            data: data,
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    alert("Section Insert Successfully.")
                    sectionDetailsForm[0].reset();
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

    function deleteSection(sectionId, sectionName) {
        var confirmation = confirm("Are you sure you want to delete this section?");
        if (confirmation) {
            var data = {
                action: "deleteSection",
                sectionId: sectionId,
                sectionName: sectionName
            };

            $.ajax({
                type: "POST",
                url: "./src/api/section_handler.php",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        alert("Section Deleted Successfully.")
                        sectionDetailsForm[0].reset();
                        location.reload();
                    } else if (response.status === "error") {
                        alert("Error deleting Section.")
                        return;
                    }

                },
                error: function(xhr, status, error) {
                    alert("Error deleting Section.")
                    return;
                }
            });
        }
    }
</script>