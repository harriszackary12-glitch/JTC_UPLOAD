<!-- Index Page -->
<!DOCTYPE html>
<html lang="en" class="html">
<?php
require_once "includes/head.php";
require_once "includes/password.php";
require_once "includes/db.php";

$error = "";
$success = "";
$username = $_SESSION["username"] ?? '';

if (!empty($username)) {
    $user_query = "select username from rolsa_users where username = \"{$username}\"";

    $user_result = $conn->query($user_query);

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();

        $_SESSION["username"] = $user["username"];
    } else {
        $error = "Error fetching user data";
    }
}
?>
<body>
<?php include "includes/navbar.php"; ?>
<div class="container-fluid mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow">
        <div class="login-container card-body">
            <?php if (isset($_SESSION["username"])) { ?>
                <h2 class="card-title text-center">Hello, <?php echo $_SESSION["username"]; ?>!</h2>

                <?php if ($error) { ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php } ?>

            <?php } else { ?>
                <h2 class="card-title text-center mb-4">You're not signed in.</h2>
                <p class="text-center small">Please <a href="/content/register.php">make an account</a> or <a href="/content/login.php">log in.</a></p>
            <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <?php if (isset($_SESSION["username"])) { ?>
  <div class="row justify-content-center mt-5">
    <div class="col-md-10 col-lg-8">
      <?php require_once "includes/span.php"; ?>
    </div>
  </div>
  <?php } ?>
</div>
</body>
</html>