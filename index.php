<?php

require_once('./includes/dbconnect.php');
require_once('./includes/utils.php');

$username = new input("Username", trim(mysqli_real_escape_string($dbc, $_POST['username'])));
$password = new input("Password", trim(mysqli_real_escape_string($dbc, $_POST['password'])));

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  foreach(array($username, $password) as $input) {
    if(trim($input->getValue() == '')) {
      array_push($errors, $input->getName() . " was left blank");
    }
  }

  if (count($errors) == 0) {
    $query = "SELECT * FROM user_login WHERE u_username='$username->value' AND u_password=SHA1('$password->value');";

    $checkCreds = mysqli_query($dbc, $query);

    if(mysqli_num_rows($checkCreds) > 0) {
      while($row = mysqli_fetch_array($checkCreds)) {
        $uuid = $row['u_uuid'];
      }
      setcookie('userid', "$uuid", null, '/');
      header('Location: http://cs334l.educationvault.com/collectiblesweb/browsecollectible.php');
    } else {
      array_push($errors, "Your user name or password were invalid");
    }
  }
}

require_once('./includes/header.html'); ?>

<div class="container">
  <div class="row">
    <div class="col-xs-12 col-sm-8">
      <div class="jumbotron">
        <h1>Collectibles Web</h1>
        <p>Join a website all about sharing your favorite thing -- your collection!</p>
        <p><a href="register.php" class="btn btn-primary btn-lg" role="button">Register</a></p>
      </div>
    </div>
    <div class="col-xs-12 col-sm-4">
      <h2>Login</h2>
      <?php
      if(count($errors) > 0) {
        echo "<div class=\"alert alert-danger\"><h3 style='margin-top: 0px;'>Errors:</h3><ul>";
        foreach($errors as $error) {
          echo "<li>" . $error . "</li>";
        }
        echo "</ul></div>";
      }
      ?>
      <form method="POST" action="" role="form">
        <div class="form-group">
          <input type="text" name="username" class="form-control"
            placeholder="User Name (firstlastname)">
        </div>
        <div class="form-group">
          <input type="password" name="password" class="form-control"
            placeholder="Password">
        </div>
        <div class="form-group">
          <input type="submit" class="btn btn-primary">
        </div>
      </form>
    </div>
  </div><!-- end class row -->
</div> <!-- end class container -->

<!-- end of body content -->
<?php require_once('./includes/footer.php'); ?>