<?php

// Connect to the database
require_once('./includes/dbconnect.php');
require_once('./includes/utils.php');


$firstName    = new input("First Name", trim(mysqli_real_escape_string($dbc, $_POST['first-name'])));
$lastName     = new input("Last Name", trim(mysqli_real_escape_string($dbc, $_POST['last-name'])));
$username     = new input("Username", trim(mysqli_real_escape_string($dbc, $_POST['username'])));
$emailAddress = new input("Email Address", trim(mysqli_real_escape_string($dbc, $_POST['email'])));
$password     = new input("Password", trim(mysqli_real_escape_string($dbc, $_POST['password'])));
$passConfirm  = new input("Password Confirmation", trim(mysqli_real_escape_string($dbc, $_POST['password-confirm'])));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $errors = array();

  foreach(array($firstName, $lastName, $username, $emailAddress, $password, $passConfirm) as $input) {
    if(trim($input->getValue() == '')) {
      array_push($errors, $input->getName() . " was left blank");
    }
  }

  if($password->getValue() != $passConfirm->getValue())
  {
    array_push($errors, "Password and password confirmation don't match!");
  }

  $checkEmail = mysqli_query($dbc, "SELECT * FROM user_profile WHERE up_email='$emailAddress->value';");
  $checkUserName = mysqli_query($dbc, "SELECT * FROM user_login WHERE u_username='$username->value';");

  if(mysqli_num_rows($checkEmail) > 0) {
    array_push($errors, "Email address already exists in this system!");
    $emailAddress->value = "";
  }

  if(mysqli_num_rows($checkUserName) > 0) {
    array_push($errors, "Username already exists in this system!");
    $username->value = "";
  }

}

if (count($errors) == 0 && $_SERVER['REQUEST_METHOD'] == 'POST')
{
  $insertPassword = $password->getValue();
  $uuid = getGUID();
  $query    = "INSERT INTO user_login (u_username, u_password, u_uuid) VALUES ('$username->value', SHA1('$insertPassword'), '$uuid');";
  $addProfile = mysqli_query($dbc, $query);

  if ($addProfile) {
    $selectUser = "SELECT * FROM user_login WHERE u_username=\"$username->value\";";
    $selectUserQuery = mysqli_query($dbc, $selectUser);

    while($row = mysqli_fetch_array($selectUserQuery)) {
      $id = $row['u_id'];
    }

    $query2   = "INSERT INTO user_profile
                  (u_id, up_email, up_first_name, up_last_name, up_join_date)
                VALUES ($id,
                        '$emailAddress->value',
                        '$firstName->value',
                        '$lastName->value',
                        CURRENT_DATE());";

    $insertProfile = mysqli_query($dbc, $query2);

    if($insertProfile){
      setcookie('userid', "$uuid", null, '/');
      header('Location: http://cs334l.educationvault.com/collectiblesweb/browsecollectible.php');
    }
    else
    {
      echo mysqli_error($dbc);
    }

  }
  else
  {
    echo mysqli_error($dbc);
  }

}
else
{
  $title = "Register | Collectibles Web";
  require_once('./includes/header.html'); ?>

<div class="container">
  <div class="row">
    <div class="col-xs-12">
      <h1>Make an Account</h1>
        <?php
        if(count($errors) > 0) {
          echo "<div class=\"alert alert-danger\"><h3 style='margin-top: 0px;'>Errors:</h3><ul>";
          foreach($errors as $error) {
            echo "<li>" . $error . "</li>";
          }
          echo "</ul></div>";
        }
        ?>
        <form role="form" action="" method="POST">
          <div class="form-group">
            <input type="text" name="first-name" class="form-control"
              placeholder="First Name" value="<?php echo $firstName->getValue() ?>">
          </div>
          <div class="form-group">
            <input type="text" name="last-name" class="form-control"
              placeholder="Last Name" value="<?php echo $lastName->getValue() ?>">
          </div>
          <div class="form-group">
            <input type="text" name="username" class="form-control"
              placeholder="Username (ex: alexlafroscia)" value="<?php echo $username->getValue() ?>">
          </div>
          <div class="form-group">
            <input type="text" name="email" class="form-control"
              placeholder="Email Address" value="<?php echo $emailAddress->getValue() ?>">
          </div>
          <div class="form-group">
            <input type="password" name="password" class="form-control"
              placeholder="Password">
          </div>
          <div class="form-group">
            <input type="password" name="password-confirm" class="form-control"
              placeholder="Password (Repeat)">
          </div>
          <div class="form-group">
            <input type="submit" class="btn btn-primary">
          </div>
        </form>
    </div>
  </div>
</div>

<?php
  require_once('./includes/footer.php');
} // end if/else statement
?>
