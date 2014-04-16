<?php
require_once('./includes/dbconnect.php');
require_once('./includes/utils.php');
require_once('./includes/checkLogin.php');


// If they chose to delete something
if(isset($_POST['delete'])) {
  $deleteId = $_POST['delete'];
  $deleteQuery = "DELETE FROM collectible WHERE collectible_id=$deleteId";
  $deleteQuery = mysqli_query($dbc, $deleteQuery);
  if(!$deleteQuery){
    $mysqlError = mysqli_error($dbc);
  }
}

// If they chose to edit an item
if(isset($_GET['edit'])) {
  $editId = $_GET['edit'];
  $editQuery = "SELECT * FROM collectible WHERE collectible_id=$editId";
  $editQuery = mysqli_query($dbc, $editQuery);
  while($row = mysqli_fetch_array($editQuery)){
    $editItem    = true;
    $id          = $row['collectible_id'];
    $name        = $row['collectible_name'];
    $value       = $row['collectible_value'];
    $photo       = $row['collectible_image'];
    $description = $row['collectible_description'];
    $catId       = $row['cat_id'];
    $subCatId    = $row['subcat_id'];
  }
  if(!$editQuery) {
    $mysqlError = mysqli_error($dbc);
  }
}

if(isset($_POST['save-form'])) {
  $name        = new input("Name", trim(mysqli_real_escape_string($dbc, $_POST['name'])));
  $value       = new input("Value", trim(mysqli_real_escape_string($dbc, $_POST['value'])));
  $photo       = new input("Photo", trim(mysqli_real_escape_string($dbc, $_POST['photo'])));
  $description = new input("Description", trim(mysqli_real_escape_string($dbc, $_POST['description'])));
  $catId       = new input("Category", trim(mysqli_real_escape_string($dbc, $_POST['category'])));
  $subCatId    = new input("Subcategory", trim(mysqli_real_escape_string($dbc, $_POST['subcategory'])));

  $errors = array();

  foreach(array($name, $description, $catId, $subCatId) as $input) {
    if(trim($input->getValue() == '')) {
      array_push($errors, $input->getName() . " was left blank");
    }
  }

  if($value->value != "" && !filter_var($value->value, FILTER_VALIDATE_FLOAT)) {
    array_push($errors, "Value format incorrect, please try again. Value should omit the comma and dollar sign.");
  }

  if($photo->value != "" && !filter_var($photo->value, FILTER_VALIDATE_URL)) {
    array_push($errors, "Invalid URL: Submitted value is not a valid URL.");
  } else {
    $img_formats = array("png", "jpg", "jpeg", "gif", "tiff"); //Etc. . .
    $path_info = pathinfo($photo->value);
    if ($photo->value != "" && !in_array(strtolower($path_info['extension']), $img_formats)) {
      array_push($errors, "Invalid URL: URL must point to an image file.");
    }
  }

  if(count($errors) == 0) {
    $updateCollectible = "UPDATE collectible
                          SET
                            cat_id=$catId->value,
                            subcat_id=$subCatId->value,
                            up_id=$user->id,
                            collectible_name='$name->value',
                            collectible_value='$value->value',
                            collectible_description='$description->value',
                            collectible_image='$photo->value'
                          WHERE
                            collectible_id = $id";
    $updateQuery = mysqli_query($dbc, $updateCollectible);

    if($updateQuery){
      $editId = $_GET['edit'];
      $editQuery = "SELECT * FROM collectible WHERE collectible_id=$editId";
      $editQuery = mysqli_query($dbc, $editQuery);
      while($row = mysqli_fetch_array($editQuery)){
        $name        = $row['collectible_name'];
        $value       = $row['collectible_value'];
        $photo       = $row['collectible_image'];
        $description = $row['collectible_description'];
        $catId       = $row['cat_id'];
        $subCatId    = $row['subcat_id'];
      }
    } else {
      $name        = $name->value;
      $value       = $value->value;
      $photo       = $photo->value;
      $description = $description->value;
      $catId       = $catId->value;
      $subCatId    = $subCatId->value;
    }

  } else {
    $name        = $name->value;
    $value       = $value->value;
    $photo       = $photo->value;
    $description = $description->value;
    $catId       = $catId->value;
    $subCatId    = $subCatId->value;
  }
}

// Run on every page load
$userItemsString = "SELECT * FROM collectible WHERE up_id=$user->id";
$userItemsQuery = mysqli_query($dbc, $userItemsString);
$userItems = array();

while($row = mysqli_fetch_array($userItemsQuery)){
  array_push($userItems, $row);
}

require_once('./includes/header.html');
?>

<div class="container">
  <div class="row">
    <div class="col-xs-12">

      <?php // Handle showing a success or error message about item deletion
        if (isset($deleteQuery)) { if ($deleteQuery) { ?>
        <div class="alert alert-success">Item successfully deleted!</div>
      <?php } else { ?>
        <div class="alert alert-danger">
          There was a problem deleting your item: <?php print($mysqlError); ?>
        </div>
      <?php } } ?>

      <?php // Handle showing a success or error message about updating an item
        if (isset($updateQuery)) { if ($updateQuery) { ?>
        <div class="alert alert-success">Item successfully editted!</div>
      <?php } else { ?>
        <div class="alert alert-danger">
          There was a problem updating your item: <?php print($mysqlError); ?>
        </div>
      <?php } } ?>

      <h1>Edit Collectibles</h1>

      <table class="table table-striped">
        <tr>
          <th>ID</th>
          <th>Item Name</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>

        <?php foreach ($userItems as $item) {
          $idEach = $item['collectible_id'];
          $nameEach = $item['collectible_name'];      ?>

        <tr>
          <td><?php echo $idEach; ?></td>
          <td><?php echo $nameEach; ?></td>
          <td>
            <form method="GET" action="">
              <input type='hidden' name='edit' value='<?php echo $idEach; ?>'>
              <input type='submit' value='Edit' class="btn btn-primary">
            </form>
          </td>
          <td>
            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this item?');">
              <input type='hidden' name='delete' value='<?php echo $idEach; ?>'>
              <input type='submit' value='Delete' class='btn btn-danger'>
            </form>
          </td>
        </tr>

        <?php } ?>
      </table>

    </div>
  </div><!-- end row -->

  <?php // If the item to edit has been defined
        if(isset($editItem)) { ?>

  <div class="row">
    <div class="col-xs-12">
      <?php
      if(count($errors) > 0) {
        echo "<div class=\"alert alert-danger\"><h3 style='margin-top: 0px;'>Errors:</h3><ul>";
        foreach($errors as $error) {
          echo "<li>" . $error . "</li>";
        }
        echo "</ul></div>";
      }
      ?>
      <form method="POST" action="" class="form-horizontal" role="form">
        <div class="form-group">
          <label for="input-name" class="col-sm-2 control-label">Name</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="input-name"
              placeholder="ex: Signed Baseball Card" name="name" value="<?php echo $name; ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="input-value" class="col-sm-2 control-label">Estimated Value</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="input-value"
              placeholder="ex: 1000.00" name="value" value="<?php if($value != '0.00'){ echo $value; } ?>">
            <span class="help-block"><em>Optional Field</em></span>
          </div>
        </div>
        <div class="form-group">
          <label for="input-photo" class="col-sm-2 control-label">Photo URL</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="input-photo"
              placeholder="ex: http://www.example.com/photo.png"
              name="photo" value="<?php echo $photo; ?>">
            <span class="help-block"><em>Optional Field</em></span>
          </div>
        </div>
        <div class="form-group">
          <label for="input-description" class="col-sm-2 control-label">Description</label>
          <div class="col-sm-10">
            <textarea class="form-control" rows="5"
              placeholder="ex: A baseball card signed by someone famous.
                           Originally printed in 1943, this card...."
              name="description"><?php echo $description; ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="input-category" class="col-sm-2 control-label">Category</label>
          <div class="col-sm-3">
            <select id="input-category" class="form-control" name="category">
              <option value=""></option>
              <option value="1" <?php if($catId == 1) { echo "selected"; } ?>>Sports</option>
              <option value="2" <?php if($catId == 2) { echo "selected"; } ?>>Music</option>
              <option value="3" <?php if($catId == 3) { echo "selected"; } ?>>Stamps</option>
              <option value="4" <?php if($catId == 4) { echo "selected"; } ?>>Toys</option>
            </select>
          </div>
          <label for="input-subcategory" class="col-sm-2 control-label">Sub Category</label>
          <div class="col-sm-3">
            <select id="input-subcategory" class="form-control" name="subcategory">
            <?php if($catId == 1) { ?>

            <option value="1" <?php if($subCatId == 1) { echo "selected"; } ?>>Cards</option>
            <option value="2" <?php if($subCatId == 2) { echo "selected"; } ?>>Autographs</option>
            <option value="3" <?php if($subCatId == 3) { echo "selected"; } ?>>Sports Equipment</option>

            <?php } elseif ($catId == 2) { ?>

            <option value="4" <?php if($subCatId == 4) { echo "selected"; } ?>>Autographs</option>
            <option value="5" <?php if($subCatId == 5) { echo "selected"; } ?>>Records</option>
            <option value="6" <?php if($subCatId == 6) { echo "selected"; } ?>>Toys</option>

            <?php } elseif ($catId == 3) { ?>

            <option value="7" <?php if($subCatId == 7) { echo "selected"; } ?>>U.S.</option>
            <option value="8" <?php if($subCatId == 8) { echo "selected"; } ?>>International</option>

            <?php } elseif ($catId == 4) { ?>

            <option value="9"  <?php if($subCatId == 9)  { echo "selected"; } ?>>Antique</option>
            <option value="10" <?php if($subCatId == 10) { echo "selected"; } ?>>1980s</option>
            <option value="11" <?php if($subCatId == 11) { echo "selected"; } ?>>Current Day</option>
            <option value="12" <?php if($subCatId == 12) { echo "selected"; } ?>>Metal</option>

            <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <input type="hidden" name="save-form" value="true">
            <input type="submit" class="btn btn-primary" value="Save">
            <a href="editcollectible.php" class="btn">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php } ?>
</div>


<?php
require_once('./includes/footer.php');
?>