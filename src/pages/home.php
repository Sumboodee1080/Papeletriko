<?php
if (isset($_GET['logout'])) {
  session_destroy();
  echo '<script>window.location.reload();</script>';
  exit();
}
?>
<!-- NAV BAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="/">PAPELETRIKO</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (hasPrivilege('modules')) : ?>
          <li class="nav-item">
            <a class="nav-link <?php if (isset($_GET['page']) && $_GET['page'] === 'modules') echo 'active'; ?>" href="?page=modules">Modules</a>
          </li>
        <?php endif; ?>

        <?php if (hasPrivilege('profile')) : ?>
          <li class="nav-item">
            <a class="nav-link <?php if (isset($_GET['page']) && $_GET['page'] === 'profile') echo 'active'; ?>" href="?page=profile">Profile</a>
          </li>
        <?php endif; ?>

        <?php if (hasPrivilege('management')) : ?>
          <li class="nav-item">
            <a class="nav-link <?php if (isset($_GET['page']) && $_GET['page'] === 'management') echo 'active'; ?>" href="?page=management">Management</a>
          </li>
        <?php endif; ?>

        <li class="nav-item">
          <a href="?logout=true" class="btn btn-danger">Log out <i class="bi bi-box-arrow-right"></i></a>
        </li>
      </ul>
    </div>

  </div>
</nav>

<div class="container mt-4">
  <?php
  $page = isset($_GET['page']) ? $_GET['page'] : 'modules';
  if (hasPrivilege($page)) {
    switch ($page) {
      case 'profile':
        include 'profile.php';
        break;
      case 'management':
        include 'management.php';
        break;
      case 'modules':
        include 'modules.php';
        break;
      case 'userManagement':
        include 'user_management.php';
        break;
      case 'moduleManagement':
        include 'module_management.php';
        break;
      case 'createModule':
        include 'create_module.php';
        break;
      case 'createTopic':
        include 'create_topic.php';
        break;
      case 'sectionManagement':
        include 'create_section.php';
        break;
      case 'viewModule';
        include 'module_page.php';
        break;
      case 'activityType';
        include 'activityType_page.php';
        break;
      case 'essay';
        include 'activityEssay_page.php';
        break;
      case 'fillInTheBlank';
        include 'activityFillInTheBlank_page.php';
        break;
      case 'fillTheBlank';
        include 'fillInTheBlankActivityPage.php';
        break;
      case 'viewActivity';
        include 'viewActivity_page.php';
        break;
      case 'essayAct';
        include 'essayActivityPage.php';
        break;
      case 'viewSubmissions';
        include 'submissions_page.php';
        break;
      case 'viewActivitySubmission';
        include 'viewSubmissions_page.php';
        break;
      default:
        include 'modules.php';
        break;
    }
  } else {
  ?>
    <div style="text-align: center;">
      You dont have enough privilige to access this page.
    </div>
  <?php
  }
  ?>
</div>