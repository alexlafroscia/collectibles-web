<?php

if (isset($_COOKIE['userid'])) {
  $uuid = $_COOKIE['userid'];
  $userConnect = mysqli_query($dbc, "SELECT * FROM user_login WHERE u_uuid='$uuid';");
  if (mysqli_num_rows($userConnect) > 0) {
    while($row = mysqli_fetch_array($userConnect)){
      $user = new user($dbc, $row['u_id'], $row['u_username'], $row['u_uuid']);
    }
  }
}

if(!isset($user)) {
  header('Location: http://cs334l.educationvault.com/collectiblesweb');
}