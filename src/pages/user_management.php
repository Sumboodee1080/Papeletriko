<div class="container mt-5">
    <div class="row">
        <!-- Registration Form -->
        <div class="col-md-6">
            <h2>User Registration</h2>
            <form id="userDetailsForm" method="post">
                <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                </div>
                <div class="mb-3">
                    <label for="middleName" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="middleName" name="middleName">
                </div>
                <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName">
                </div>
                <div class="mb-3">
                    <label for="section" class="form-label">Section</label>
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
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3">
                    <label for="userGroup" class="form-label">User Group</label>
                    <select id="userGroup" class="form-select">
                        <option value="Admin">Admin</option>
                        <option value="Regular">Regular</option>
                    </select>
                </div>
                <button type="submit" id="submitBtn" class="btn btn-primary">Register / Update</button>
            </form>
        </div>

        <!-- User List -->
        <div class="col-md-6">
            <h2>User List</h2>
            <div style="overflow-x: auto;">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Section</th>
                            <th>User Group</th>
                            <th>Action</th>
                            <!-- Add columns for other user information -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT `ID`, `FIRST_NAME`, `MIDDLE_NAME`, `LAST_NAME`, `SECTION`, `USERNAME`, `USER_GROUP` FROM `ci_user`";
                        $params = [];
                        $users = runQuery($query, $params);

                        foreach ($users as $user) {
                            echo "<tr>";
                            echo "<td><a href='javascript:void(0);' onclick='getProfileInfo(" . $user['ID'] . ");'>" . $user['FIRST_NAME'] . " " . $user['MIDDLE_NAME'] . " " . $user['LAST_NAME'] . "</a></td>";
                            echo "<td>" . $user['SECTION'] . "</td>";
                            echo "<td>" . $user['USER_GROUP'] . "</td>";
                            echo '<td><i class="bi bi-x-circle-fill" style="color: red; cursor: pointer;" onclick="deleteUser(' . $user['ID'] . ');"></i></td>';
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<script>
    $(document).ready(function() {
        var userDetailsForm = $("#userDetailsForm");
        userDetailsForm.on("submit", function(event) {
            event.preventDefault();

            var data = {
                firstName: $("#firstName").val(),
                middleName: $("#middleName").val(),
                lastName: $("#lastName").val(),
                section: $("#section").val(),
                username: $("#username").val(),
                password: $("#password").val(),
                userGroup: $("#userGroup").val(),
                action: "addNewUser"
            };

            $.ajax({
                type: "POST",
                url: "./src/api/login_handler.php",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        // Re-enable the username and password fields
                        $("#username").prop("disabled", false);
                        $("#password").prop("disabled", false);
                        alert("Registration / Update Success")
                        userDetailsForm[0].reset();
                        location.reload();
                    } else if (response.status === "error") {
                        alert("Error During Registration")
                    }

                },
                error: function(xhr, status, error) {
                    alert("Error During Registration")
                }
            });
        });
    });

    function getProfileInfo(userID) {
        var data = {
            userId: userID,
            action: "getUserInfo"
        };

        $.ajax({
            type: "POST",
            url: "./src/api/login_handler.php",
            data: data,
            dataType: "json",
            success: function(response) {
                if (response.error) {
                    // Handle the error case
                    alert("User not found or an error occurred");
                } else {
                    // Populate placeholders from the data in response
                    $("#firstName").val(response.FIRST_NAME);
                    $("#middleName").val(response.MIDDLE_NAME);
                    $("#lastName").val(response.LAST_NAME);
                    $("#section").val(response.SECTION);
                    $("#username").val(response.USERNAME).prop("disabled", true);
                    $("#password").val(response.PASSWORD).prop("disabled", true);
                    $("#userGroup").val(response.USER_GROUP);
                }
            },
            error: function(xhr, status, error) {
                alert("Error During Registration")
            }
        });
    }

    function deleteUser(userId) {
        var confirmation = confirm("Are you sure you want to delete this user?");
        if (confirmation) {
            var data = {
                userId: userId,
                action: "deleteUser"
            };

            $.ajax({
                type: "POST",
                url: "./src/api/login_handler.php",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.error) {
                        // Handle the error case
                        alert("Delete Failed");
                    } else {
                        alert("User Deleted Successfully");
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