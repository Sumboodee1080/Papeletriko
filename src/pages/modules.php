<style>
    .module-card:hover {
        cursor: pointer;
    }
</style>
<div class="d-flex justify-content-between section-title">
    <h3 class="mb-4">Mga Aralin</h3>
</div>

<div class="module-wrapper row row-cols-1 row-cols-md-3 g-4">
    <div class="module-row">
        <?php
        $sectionFilter = $_SESSION['SECTION'];

        $query = "SELECT `ID`, `MODULE_TITLE`, `DESCRIPTION`, `LONG_DESCRIPTION`, `SECTION`, `COVER_FILENAME` FROM `ci_modules_headers` WHERE `SECTION` = :section";
        $stmt = $dbConn->prepare($query);
        $stmt->bindParam(':section', $sectionFilter, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            foreach ($result as $module) {
                $moduleId = $module['ID'];
                $moduleTitle = $module['MODULE_TITLE'];
                $moduleDescription = $module['DESCRIPTION'];
                $coverFilename = $module['COVER_FILENAME'];
        ?>
                <div class="col">
                    <div class="card module-card">
                        <div class="module-cover">
                            <img src="<?= SITE_DOMAIN ?>/../media/img/moduleCovers/<?= $coverFilename ?>" class="card-img-top" alt="Module Cover">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $moduleTitle ?></h5>
                            <p class="card-text"><?= $moduleDescription ?></p>


                            <div class="overlink">
                                <a href="?page=viewModule&moduleId=<?= $moduleId ?>"></a>
                            </div>

                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            // Display a "No Available Modules" message if there are no results.
            echo '<div class="col">No Available Modules</div>';
        }
        ?>
    </div>

</div>