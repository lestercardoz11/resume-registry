<!DOCTYPE HTML>

<?php
require_once "pdo.php";
session_start();

if( ! isset($_SESSION['name']) ){
  die("Not logged in");
}

if( isset($_POST['cancel']) ){
header('Location: index.php');
return;
}

if( isset($_POST['delete']) ){
  $_SESSION['msg'] = false;
  $_error = false;

  try {
      $sql = "DELETE FROM Profile WHERE profile_id = :profile_id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array( ':profile_id' => $_POST['profile_id'] ));
  } catch( Exception $ex ){
      echo("Internal error, please contact support");
      return;
  }
  $_SESSION['msg'] = 'Profile deleted';
  header('Location: index.php');
  return;
}

if( !isset($_GET['profile_id']) ) {
  $_SESSION['msg'] = "Missing profile_id";
  $_SESSION['error'] = true;
  header( 'Location: index.php' );
  return;
}

$stmt = $pdo->prepare('SELECT profile_id, first_name, last_name FROM Profile WHERE profile_id = :profile_id');
$stmt->execute(array( ':profile_id' => $_GET['profile_id'] ));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if( $row === false ) {
  $_SESSION['msg'] = 'Bad value for profile_id';
  $_SESSION['error'] = true;
  header( 'Location: index.php' );
  return;
}

$profile_id = $row['profile_id'];
$first_name = htmlentities( $row['first_name']);
$last_name = htmlentities( $row['last_name']);

?>

<html>
<head>
<title>Lester Cardoz's Profile Delete</title>
<!-- bootstrap.php - this is HTML -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

</head>
<body>
<div class="container">
<h1>Delete Profile</h1>

<p>Confirm: Deleting <?=$first_name.' '.$last_name?> ? </p>

<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<input type="submit" value="Delete" name="delete">
<input type="submit" value="Cancel" name="cancel">
</form>

</div>
</body>
</html>
