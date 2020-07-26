<?php
require_once('pdo.php');
require_once('header.php');
require('util.php');
session_start();

if( ! isset($_SESSION['user_id']) ){
    die("ACCESS DENIED");
    header('Location: index.php');
    return;
}

if( isset($_POST['cancel']) ){
	header('Location: index.php');
	return;
}

if (isset($_POST['add'])) {
    // $_SESSION['error'] = false;
    $_SESSION['msg'] = false;

    if( strlen($_POST['email']) < 1
    || strlen($_POST['first_name']) < 1
    || strlen($_POST['last_name']) < 1
    || strlen($_POST['headline']) < 1
    || strlen($_POST['summary']) < 1 ){
        $_SESSION['error'] = "All fields are required";
        header( 'Location: add.php' );
        return;
    }

    if( countEmail() !== 1 ){
        $_SESSION['error'] = "Email address must contain @";
        echo $_SESSION['error'];
        header( 'Location: add.php' );
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

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
            VALUES (:user_id, :first_name, :last_name, :email, :headline, :summary)'
        );
        $stmt->execute(array(
            ':user_id' => $_SESSION['user_id'],
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':email' => $_POST['email'],
            ':headline' => $_POST['headline'],
            ':summary' => $_POST['summary'])
        );

        $profile_id = $pdo->lastInsertId();

        //Insert the position entries
        insertPositions($pdo, $profile_id);

        //Insert the education entries
        insertEducations($pdo, $profile_id);
  
    } catch( Exception $ex ){
        echo("Internal error, please contact support");
        return;
    }
    $_SESSION['msg'] = 'Profile added';
    header('Location: index.php');
    return;
}
?>

<body>
<div class="container">
<h1>Adding Profile</h1>
<form method="post">
<?php 
    if( isset($_SESSION['error']) && $_SESSION['error'] != false ){
        echo '<p style="color:red">';
        echo    $_SESSION['error'];
        echo '</p>';
    }
    unset($_SESSION['error']);
?>
<p>First Name:
<input type="text" name="first_name" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"/></p>
<p>Email:
<input type="text" name="email" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea>
<p>
Education: <input type="submit" id="addEdu" value="+">
<div id="edu_fields">
</div>
</p>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
</p>
<p>
<input type="submit" name="add" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script src="script.js"></script>
</div>
</body>
<?php
include_once('footer.php');
?>
