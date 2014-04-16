<?php

class input
{
  function __construct( $name, $value) {
    $this->name = $name;
    $this->value = $value;
  }

  function getName() {
    return $this->name;
  }

  function getValue() {
    return $this->value;
  }
}

class user
{
  function __construct($dbc, $id, $username, $uuid) {
    $this->login_id = $id;
    $this->username = $username;
    $this->uuid = $uuid;

    $profile = mysqli_query($dbc, "SELECT * FROM user_profile WHERE u_id=$id;");
    if(mysqli_num_rows($profile) > 0) {
      while($row = mysqli_fetch_array($profile)) {
        $this->id = $row['up_id'];
        $this->first_name = $row['up_first_name'];
        $this->last_name = $row['up_last_name'];
        $this->email = $row['up_email'];
        $this->photo = $row['up_photo'];
      }
    }
  }
}

function getGUID(){
  if (function_exists('com_create_guid')){
    return com_create_guid();
  }else{
    mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = chr(123)// "{"
      .substr($charid, 0, 8).$hyphen
      .substr($charid, 8, 4).$hyphen
      .substr($charid,12, 4).$hyphen
      .substr($charid,16, 4).$hyphen
      .substr($charid,20,12)
      .chr(125);// "}"
    return $uuid;
  }
}