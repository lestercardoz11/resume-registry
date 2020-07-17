<!DOCTYPE html>

<?php
require_once('pdo.php');

session_start();

// Enters here only in POST request.
if( isset($_POST['cancel']) ){
    session_destroy();
	header('Location: index.php');
	return;
}

function count_atSigns( $email ){
    $count = 0;
    for( $i=0; $i<strlen($email); $i++ ){
        if( $email[$i] == '@' ){
            $count++;
        }
    }
    return $count;
}

// Enters here only in POST request.
if( isset($_POST['email']) ) {
    unset( $_SESSION['user_id'] );
    unset( $_SESSION['name'] );
	$email = ( !empty($_POST['email']) ? $_POST['email'] : '' );
	$pass = ( !empty($_POST['pass']) ? $_POST['pass'] : '' );
	$_SESSION['msg'] = false;

    // Query for user_id and password in the DB.
    $stmt = $pdo->prepare( 'SELECT user_id, name, password FROM users WHERE email = :email' );
    $stmt->execute( array(':email' => $_POST['email']) );
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $hash = $row['password'];

	if (!empty($email) && !empty($pass)) {
		// $error_message = 'Incorrect password';
		$salt = 'XyZzy12*_';
		$_SESSION['wrong_password'] = false;

        $count = count_atSigns( $email );
        if( $count !== 1 ){
            $_SESSION['msg'] = 'Please enter a valid email address';
            header( 'Location: login.php' );
            return;
        }

		if ($count === 1) {
			$check = hash('md5', $salt . $pass);
			if( $check == $hash ){
				error_log( "Login success ".$email );
				// Stores email in SESSION data and redirects to view.php using GET request (without GET parameters this time).
                $_SESSION['name'] = $row['name'];
                $_SESSION['email'] = $_POST['email'];
                $_SESSION['user_id'] = $row['user_id'];
				header( 'Location: index.php' );
				return;
			} else {
				error_log( "Login fail ".$email." $check" );
                $_SESSION['msg'] = "Incorrect login or password";
                $_SESSION['wrong_password'] = true;
                header( "Location: login.php" );
                return;
			}
		}
	} else {
        $_SESSION['msg'] = "Email and password are required";
        header( "Location: login.php" );
        return;
    }
    
	// Error. Execution's not supposed to reach here.
	die( "Should not pass by here" );
}
?>

<html>
<head>
<title>Login Page</title>
<!-- bootstrap.php - this is HTML -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<form method="POST" action="login.php">
<label for="email">Email</label>
<input type="text" name="email" id="email"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<br>
<p>
For a password hint, view source and find an account and password hint
in the HTML comments.
<!-- Hint: 
The account is umsi@umich.edu
The password is the three character name of the 
programming language used in this class (all lower case) 
followed by 123. -->
</p>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>
</div>
</body>