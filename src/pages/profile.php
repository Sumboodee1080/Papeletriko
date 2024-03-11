<?php
// Assuming you have established a database connection and stored it in $dbConn

if (isset($_SESSION['ID'])) {
    $userId = $_SESSION['ID'];

    // Query to retrieve user information by ID
    $query = "SELECT `ID`, `FIRST_NAME`, `MIDDLE_NAME`, `LAST_NAME`, `SECTION`, `USERNAME`, `USER_GROUP` FROM `ci_user` WHERE `ID` = :userId";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userInfo) {
?>
        <div>
            <h3>Aking Profile</h3>
        </div>
        <hr style="margin-top: 20px;">
        <h5>Pangunahing Impormasyon</h5>
        <div style="margin-top: 10px;">
            <p>Unang Pangalan: <?= $userInfo['FIRST_NAME'] ?></p>
            <p>Gitnang Pangalan: <?= $userInfo['MIDDLE_NAME'] ?></p>
            <p>Apelyido: <?= $userInfo['LAST_NAME'] ?></p>
        </div>
        <hr>
        <h5>Pangunahing Impormasyon</h5>
        <div style="margin-top: 10px;">
            <p>Seksyon: <?= $userInfo['SECTION'] ?></p>
            <p>Pangkat: <?= $userInfo['USER_GROUP'] ?></p>
        </div>
        <hr>
        <h5>Mga Kredensyal ng Gumagamit</h5>
        <div style="margin-top: 10px;">
            <p>Username: <?= $userInfo['USERNAME'] ?></p>
            <form id="updatePasswordForm" method="post">
                <div class="mb-3">
                    <label for="newPassword" class="form-label">Bagong Password</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword">
                </div>
                <div class="mb-3">
                    <label for="reEnterNewPassword" class="form-label">Itala ulit ang Bagong Password</label>
                    <input type="password" class="form-control" id="reEnterNewPassword" name="reEnterNewPassword">
                </div>
                <button type="submit" id="submitBtn" class="btn btn-primary">I-update Password</button>
            </form>
        </div>
<?php
    } else {
        echo '<p>User not found.</p>';
    }
} else {
    echo '<p>User ID not provided in the session.</p>';
}
?>
<script>
    var updatePasswordForm = $("#updatePasswordForm");
    var newPasswordInput = $("#newPassword");
    var reEnterNewPasswordInput = $("#reEnterNewPassword");

    updatePasswordForm.on("submit", function(event) {
        event.preventDefault();

        var newPassword = newPasswordInput.val();
        var reEnterNewPassword = reEnterNewPasswordInput.val();

        // Password length validation
        if (newPassword.length < 8) {
            alert("Password should be at least 8 characters long.");
            return;
        }

        // Password contains special character validation
        if (!/[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]/.test(newPassword)) {
            alert("Password should contain at least one special character.");
            return;
        }

        // Password match validation
        if (newPassword !== reEnterNewPassword) {
            alert("Passwords do not match.");
            return;
        }

        var data = {
            newPassword: newPassword,
            action: "resetPassword"
        };

        $.ajax({
            type: "POST",
            url: "./src/api/login_handler.php",
            data: data,
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    alert("Password Update Success.");
                    updatePasswordForm[0].reset();
                    location.reload();
                } else if (response.status === "error") {
                    alert("Password Update Failed.");
                    return;
                }

            },
            error: function(xhr, status, error) {
                alert("Password Update Failed.");
                return;
            }
        });
    })
</script>