<!-- A navigation bar for the website, included in all pages -->
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="/content/login.php">Rolsa Technologies</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link active" aria-current="page" href="/index.php">Home</a>
        <a class="nav-link" href="/content/carbon-calculator.php">Carbon Calculator</a>
        <a class="nav-link" href="/content/learn-more.php">Learn More</a>

        <?php if (isset($_SESSION["username"])) { ?>
            <a class="nav-link" href="/logout.php">Log out</a>
        <?php } ?>

      </div>
      <div class="d-flex ms-auto">
      <button id="theme-toggle-btn" class="btn btn-link nav-link">Light</button>
      </div>
    </div>
</nav>