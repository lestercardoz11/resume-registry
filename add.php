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

    $count = 0;
    $em = $_POST['email'];
    for( $i=0; $i<strlen($em); $i++ ){
        if( $em[$i] == '@' ){
            $count++;
        }
    }

    if( $count !== 1 ){
        $_SESSION['error'] = "Email address must contain @";
        echo $_SESSION['error'];
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
    } catch( Exception $ex ){
        echo("Internal error, please contact support");
        return;
    }
    $_SESSION['msg'] = 'Profile added';
    header('Location: index.php');
    return;
}

?>


<html>
<head>
<title>Add Profile</title>
<!-- bootstrap.php - this is HTML -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

</head>
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
<input type="submit" name="add" id="add" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
</div>
</body>
</html>
