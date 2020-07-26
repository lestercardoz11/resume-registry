<?php
require_once('pdo.php');
require_once('header.php');
require('util.php');
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

$msg = validatePos();
if( $msg !== true){
    $_SESSION['error'] = $msg;
    header( 'Location: add.php' );
    return;
} 

$msg1 = validateEdu();
if( $msg1 !== true){
    $_SESSION['error'] = $msg1;
    header( 'Location: add.php' );
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

$positions = loadPos($pdo, $_REQUEST['profile_id']);
$educations = loadEdu($pdo, $_REQUEST['profile_id']);


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
    
    if( countEmail() !== 1 ){
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

        $sql = 'DELETE FROM Position WHERE profile_id = :pid';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pid' => $_REQUEST['profile_id'],
        ]);

        //Insert the position entries
        insertPositions($pdo, $_REQUEST['profile_id']);

        //remove the old education entries
        $sql = 'DELETE FROM Education WHERE profile_id = :pid';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pid' => $_REQUEST['profile_id'],
        ]);

        //Insert the education entries
        insertEducations($pdo, $_REQUEST['profile_id']);



    } catch( Exception $ex ){
        echo("Internal error, please contact support");
        return;
    }

    $_SESSION['msg'] = 'Profile Updated';
    header('Location: index.php');
    return;
}
?>

<script type="text/javascript">
  var countPos = <?php echo json_encode(count($positions)) ?>;
  var countEdu = <?php echo json_encode(count($educations)) ?>;
</script>

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
<div id="edu_fields_database">
        <p> Institution: <input type="submit" id="addEdu" value="+"></p>
        <?php
        foreach ($educations as $education) {
          echo  '<div id="edu' .
            $education['rank'] .
            '"><p>Year: <input type="text" name="edu_year' .
            $education['rank']  .
            '" value="'
            . htmlspecialchars($education['year']) .
            '"/><input type="button" value="-"onclick="$(\'#edu' .
            $education['rank'] .
            '\').remove(); return false;"></p><p>School: <input type="text" size="80" name="edu_school' .
            $education['rank'] .
            '" class="school" value="'
            . htmlspecialchars($education['name']) .
            '"/></p></div>';
        }
        ?>
        <div id="edu_fields">
        </div>
</div>

<div id="position_fields_database">
        <p> Position: <input type="submit" id="addPos" value="+"></p>
        <?php
        foreach ($positions as $position) {
          echo  '<div id="position' .
            $position['rank'] .
            '"><p>Year: <input type="text" name="year' .
            $position['rank']  .
            '" value="'
            . htmlspecialchars($position['year']) .
            '"/><input type="button" value="-"onclick="$(\'#position' .
            $position['rank'] .
            '\').remove();countPos--; return false;"></p><textarea name="desc' .
            $position['rank']  .
            '" rows="8" cols="80">' .
            htmlspecialchars($position['description']) .
            '</textarea></div>';
        }
        ?>
<div id="position_fields"></div>
</div>
</p>
<p>
<input type="submit" name="update" id="update" value="Save">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script src="script.js"></script>
</div>
</body>
<?php
include('footer.php');
?>


