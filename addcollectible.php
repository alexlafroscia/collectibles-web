<?php
require_once('./includes/dbconnect.php');
require_once('./includes/utils.php');
require_once('./includes/checkLogin.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    $img_formats = array("png", "jpg", "jpeg", "gif", "tiff");//Etc. . .
    $path_info = pathinfo($photo->value);
    if ($photo->value != "" && !in_array(strtolower($path_info['extension']), $img_formats)) {
      array_push($errors, "Invalid URL: URL must point to an image file.");
    }
  }


  if(count($errors) == 0){

    $addCollectible = "INSERT INTO collectible
                        (cat_id, subcat_id, up_id, collectible_name, collectible_value, collectible_description, collectible_image)
                       VALUES
                        ($catId->value,
                         $subCatId->value,
                         $user->id,
                         '$name->value',
                         '$value->value',
                         '$description->value',
                         '$photo->value');";
    $addQuery = mysqli_query($dbc, $addCollectible);
  }

} // end if POST

if($addQuery) {
  require_once('./includes/header.html');
  ?>

  <div class="container">
    <div class="row">
      <div class="col-xs-12"><h2>Thank you for submitting a collectible!</div>
    </div>
  </div>

  <?php
  require_once('./includes/footer.php');
}
else
{
  if(isset($addQuery)) {
    $mysqlError = mysqli_error($dbc);
    array_push($errors, "There was an error submitting your collectible: $mysqlError");
  }

  require_once('./includes/header.html');
  ?>

<div class="container">
  <div class="row">
    <div class="col-xs-12">
      <h1>Add a Collectible</h1>
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
              placeholder="ex: Signed Baseball Card" name="name" value="<?php echo $name->value; ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="input-value" class="col-sm-2 control-label">Estimated Value</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="input-value"
              placeholder="ex: 1000.00" name="value" value="<?php echo $value->value; ?>">
            <span class="help-block"><em>Optional Field</em></span>
          </div>
        </div>
        <div class="form-group">
          <label for="input-photo" class="col-sm-2 control-label">Photo URL</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="input-photo"
              placeholder="ex: http://www.example.com/photo.png" name="photo" value="<?php echo $photo->value; ?>">
            <span class="help-block"><em>Optional Field</em></span>
          </div>
        </div>
        <div class="form-group">
          <label for="input-description" class="col-sm-2 control-label">Description</label>
          <div class="col-sm-10">
            <textarea class="form-control" rows="5"
              placeholder="ex: A baseball card signed by someone famous.
                           Originally printed in 1943, this card...."
              name="description"><?php echo $description->value; ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="input-category" class="col-sm-2 control-label">Category</label>
          <div class="col-sm-3">
            <select id="input-category" class="form-control" name="category">
              <option value=""></option>
              <option value="1" <?php if ($catId->value == 1) { echo "selected"; } ?>>Sports</option>
              <option value="2" <?php if ($catId->value == 2) { echo "selected"; } ?>>Music</option>
              <option value="3" <?php if ($catId->value == 3) { echo "selected"; } ?>>Stamps</option>
              <option value="4" <?php if ($catId->value == 4) { echo "selected"; } ?>>Toys</option>
            </select>
          </div>
          <label for="input-subcategory" class="col-sm-2 control-label">Sub Category</label>
          <div class="col-sm-3">
            <select id="input-subcategory" class="form-control" name="subcategory">

            <?php if($catId->value == 1) { ?>

            <option></option>
            <option value="1" <?php if($subCatId->value == 1) { echo "selected"; } ?>>Cards</option>
            <option value="2" <?php if($subCatId->value == 2) { echo "selected"; } ?>>Autographs</option>
            <option value="3" <?php if($subCatId->value == 3) { echo "selected"; } ?>>Sports Equipment</option>

            <?php } elseif ($catId->value == 2) { ?>

            <option></option>
            <option value="4" <?php if($subCatId->value == 4) { echo "selected"; } ?>>Autographs</option>
            <option value="5" <?php if($subCatId->value == 5) { echo "selected"; } ?>>Records</option>
            <option value="6" <?php if($subCatId->value == 6) { echo "selected"; } ?>>Toys</option>

            <?php } elseif ($catId->value == 3) { ?>

            <option></option>
            <option value="7" <?php if($subCatId->value == 7) { echo "selected"; } ?>>U.S.</option>
            <option value="8" <?php if($subCatId->value == 8) { echo "selected"; } ?>>International</option>

            <?php } elseif ($catId->value == 4) { ?>

            <option></option>
            <option value="9"  <?php if($subCatId->value == 9)  { echo "selected"; } ?>>Antique</option>
            <option value="10" <?php if($subCatId->value == 10) { echo "selected"; } ?>>1980s</option>
            <option value="11" <?php if($subCatId->value == 11) { echo "selected"; } ?>>Current Day</option>
            <option value="12" <?php if($subCatId->value == 12) { echo "selected"; } ?>>Metal</option>

            <?php } ?>

            </select>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <input type="submit" class="btn btn-primary">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


<?php
}
require_once('./includes/footer.php');
?>