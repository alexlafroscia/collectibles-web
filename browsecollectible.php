<?php
require_once('./includes/dbconnect.php');
require_once('./includes/utils.php');
require_once('./includes/checkLogin.php');
require_once('./includes/header.html');

if(isset($_GET['search'])) {
  $search = trim(mysqli_real_escape_string($dbc, $_GET['search']));
  $searchString = "SELECT DISTINCT collectible.* "
    . "FROM collectible, category, subcategory "
    . "WHERE"
    . " (collectible.collectible_name LIKE '%$search%') "
    . "OR"
    . " (category.cat_name LIKE '%$search%' AND collectible.cat_id = category.cat_id) "
    . "OR"
    . " (subcategory.subcat_name LIKE '%$search%' AND collectible.subcat_id = subcategory.subcat_id)";
  $searchQuery = mysqli_query($dbc, $searchString);
} else {
  $searchString = "SELECT * FROM collectible ORDER BY collectible_name ASC;";
  $searchQuery = mysqli_query($dbc, $searchString);
}

$data = array();

while ($row = mysqli_fetch_array($searchQuery)) {
  array_push($data, $row);
}

?>

<div class="container">
  <div class="row">
    <br />
    <form method="GET" action="" role="form" class="form-horizontal">
      <div class="col-xs-11">
        <input id="search" class="form-control" type="text"
          placeholder="Search" name="search" value="<?php echo $search; ?>">
      </div>
      <div class="col-xs-1">
        <input type="submit" value="Search" class="btn btn-primary">
      </div>
    </form>
      <hr />
  </div>
  <?php foreach ($data as $entry) {
    if($entry['collectible_image']) { $mainContClass = "col-xs-8"; } else { $mainContClass = "col-xs-12"; } ?>
  <div class="row entry">
    <div class="col-xs-12">
      <div class="container-fluid">
        <div class="row panel">
          <div class="<?php echo $mainContClass; ?>">

            <h3>
              <?php echo $entry['collectible_name']; ?>
              <?php if (0.00 != (float) $entry['collectible_value']) { echo "<small>" .
                money_format('$%i', $entry['collectible_value']) . "</small>"; } ?>
            </h3>

            <p><?php echo $entry['collectible_description']; ?></p>
          </div>
          <?php if ($entry['collectible_image']) { ?>
          <div class="col-xs-4">
            <img src="<?php echo $entry['collectible_image']; ?>" />
          </div>
          <?php } ?>
          <hr />
        </div>
      </div>
    </div>
  </div>
  <?php } // end for each?>
</div>



<?php
require_once('./includes/footer.php');
?>