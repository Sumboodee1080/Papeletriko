<?php
// Get moduleId from the URL parameter
if (isset($_GET['moduleId'])) {
    $moduleId = $_GET['moduleId'];
    $sectionFilter = $_SESSION['SECTION'];

    // Query to retrieve module details by ID and SECTION
    $query = "SELECT `ID`, `MODULE_TITLE`, `DESCRIPTION`, `LONG_DESCRIPTION`, `COVER_FILENAME` FROM `ci_modules_headers` WHERE `ID` = :moduleId AND `SECTION` = :section";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
    $stmt->bindParam(':section', $sectionFilter, PDO::PARAM_STR);
    $stmt->execute();
    $module = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($module) {
        $moduleTitle = $module['MODULE_TITLE'];
        $moduleDescription = $module['DESCRIPTION'];
        $longDescription = $module['LONG_DESCRIPTION'];
        $coverFilename = $module['COVER_FILENAME'];
?>
        <div>
            <div style="display: flex; justify-content: space-between">
                <h5><?= $moduleTitle ?></h5>
                <div>
                    <?php if ($_SESSION['USER_GROUP'] == 'Admin') { ?>
                        <a href="?page=viewSubmissions&moduleId=<?= $moduleId ?>" class="btn btn-primary">View Submissions <i class="bi bi-paperclip"></i></a>
                    <?php } elseif ($_SESSION['USER_GROUP'] == 'Regular') { ?>
                        <a href="?page=viewActivity&moduleId=<?= $moduleId ?>" class="btn btn-primary">Activities <i class="bi bi-paperclip"></i></a>
                    <?php } ?>
                </div>

            </div>
            <div>
                <pre><?= $moduleDescription ?></pre>
            </div>
            <div>
                <pre><?= $longDescription ?></pre>
            </div>
        </div>

        <?php
        if (isset($_GET['moduleId'])) {
            $moduleId = $_GET['moduleId'];

            // Query to retrieve topics for the specified moduleId
            $query = "SELECT `ID`, `TITLE`, `TEXT_CONTENT`, `IMG_CONTENT` FROM `ci_modules_topics` WHERE `HEADER_ID` = :moduleId";
            $stmt = $dbConn->prepare($query);
            $stmt->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
            $stmt->execute();
            $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($topics) {
                foreach ($topics as $index => $topic) {
                    $imgPattern = '/\[\[(.*?)\]\]/';
                    $linkPattern = '/\[(.*?)\]/';

                    $title = $topic['TITLE'];
                    $textContent = $topic['TEXT_CONTENT'];
                    $imgContent = $topic['IMG_CONTENT'];
                    $accordionId = 'collapse' . ($index + 1);
                    $ariaExpanded = ($index === 0) ? 'true' : 'false';

                    $textContent = preg_replace($imgPattern, '<div style="width: 100%;"><img src="../../media/img/topicImages/$1" alt="Topic Img" style="width: 100%;"></div>', $textContent);
                    $textContent = preg_replace($linkPattern, '<a href="$1" target="_blank">$1</a>', $textContent);
        ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= ($index + 1) ?>">
                            <button class="accordion-button<?= ($index === 0) ? '' : ' collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $accordionId ?>" aria-expanded="<?= $ariaExpanded ?>" aria-controls="<?= $accordionId ?>">
                                <strong><?= $title ?></strong>
                            </button>
                        </h2>
                        <div id="<?= $accordionId ?>" class="accordion-collapse collapse<?= ($index === 0) ? ' show' : '' ?>" aria-labelledby="heading<?= ($index + 1) ?>" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <pre><?= $textContent ?></pre>
                            </div>
                        </div>
                    </div>
        <?php
                }
            } else {
                echo '<div class="col">No topics available for this module</div>';
            }
        } else {
            echo '<div class="col">Module ID not provided in the URL</div>';
        }
        ?>

<?php
    } else {
        echo '<div class="col">Module not found</div>';
    }
} else {
    echo '<div class="col">Module ID not provided in the URL</div>';
}
?>