<!DOCTYPE html>

<?php
require_once "pdo.php";
session_start();


if( ! isset($_SESSION['name']) ){
    die("Not logged in");
    header('Location: index.php');
    return;
}

if( isset($_POST['cancel']) ){
	header('Location: index.php');
	return;
}

$stmt = $pdo->query( 'SELECT * FROM Profile WHERE profile_id = '.$_GET['profile_id'] );
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' );
    return;
}

$profile_id = $row['profile_id'];
$first_name = htmlentities( $row['first_name']);
$last_name = htmlentities( $row['last_name']);
$email = htmlentities( $row['email']);
$headline = htmlentities( $row['headline']);
$summary = htmlentities( $row['summary']);

if (isset($_POST['update'])) {
    // $_SESSION['error'] = false;
    $_SESSION['msg'] = false;

    if( strlen($_POST['email']) < 1
    || strlen($_POST['first_name']) < 1
    || strlen($_POST['last_name']) < 1
    || strlen($_POST['headline']) < 1
    || strlen($_POST['summary']) < 1 ){
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=".$_POST["profile_id"]);
        return;
    }

    $count = 0;
    $em = $_POST['email'];
    for( $i=0; $i<strlen($em); $i++ ){
        if( $em[$i] == '@' ){
            $count++;
        }
    }
    
    if( $count !== 1 ){
        $_SESSION['error'] = "Email address must contain @";
        header("Location: edit.php?profile_id=".$_POST["profile_id"]);
        return;
    }

    try {
        $stmt = $pdo->prepare( 'UPDATE Profile SET
                                    first_name = :first_name,
                                    last_name = :last_name,
                                    email = :email,
                                    headline = :headline,
                                    summary = :summary
                                WHERE profile_id = :profile_id' );
        $stmt->execute(array(
            ':profile_id' => $_POST['profile_id'],
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':email' => $_POST['email'],
            ':headline' => $_POST['headline'],
            ':summary' => $_POST['summary'])
        );

    } catch( Exception $ex ){
        echo("Internal error, please contact support");
        return;
    }
    $_SESSION['msg'] = 'Profile Updated';
    header('Location: index.php');
    return;

}
?>


<html>
<head>
<title>Edit Profile</title>
<!-- bootstrap.php - this is HTML -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

</head>
<body>
<div class="container">
<h1>Edit Profile</h1>
<form method="post">
<?php 
    if( isset($_SESSION['error']) && $_SESSION['error'] != false ){
        echo '<p style="color:red">';
        echo    $_SESSION['error'];
        echo '</p>';
    }
    unset($_SESSION['error']);
?>
<input type='hidden' name='profile_id' value='<?= $profile_id?>'>
<p>First Name:
<input type="text" name="first_name" size="60" value='<?= $first_name?>'/></p>
<p>Last Name:
<input type="text" name="last_name" size="60" value='<?= $last_name?>'/></p>
<p>Email:
<input type="text" name="email" size="30" value='<?= $email?>'/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80" value='<?= $headline?>'/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"><?= $summary ?></textarea>
<p>
<input type="submit" name="update" id="update" value="Save">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
</div>
</body>
</html>


