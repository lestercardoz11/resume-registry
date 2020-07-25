<!DOCTYPE HTML>

<?php
require_once('pdo.php');
?>

<html lang='en'>

<head>
	<meta charset='UTF-8'>
	
    <link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

	<title>Lester Cardoz - Resume Registry </title>
</head>

<body>
	<div class='container'>
		<header>
			<h1> Profile information </h1>
		</header>

		<div class='box'>
			<?php

            if( isset($_POST['done']) ){
                header('Location: index.php');
                return;
}

            // Get and show profile basic info.

            $stmt = $pdo->query( 
                'SELECT first_name, last_name, email, headline, summary
                FROM Profile
                WHERE profile_id = '.$_GET['profile_id'] );
            if( $stmt->rowCount() === 0 ){
                echo '<p> Wrong profile id </p>';
            } else {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                echo '<p><b> First Name: </b>'.$row['first_name'].'<p>';
                echo '<p><b> Last Name: </b>'.$row['last_name'].'<p>';
                echo '<p><b> Email: </b>'.$row['email'].'<p>';
                echo '<p><b> Headline: </b><br>'.$row['headline'].'<p>';
                echo '<p><b> Summary: </b><br>'.$row['summary'].'<p>';
            }

            $stmt1 = $pdo->prepare( 
                'SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
                $stmt1->execute(array( ':prof' => $_GET['profile_id']));
            
                if( $stmt1->rowCount() === 0 ){
                echo '<p> Wrong profile id </p>';
                } else {
                echo '<p><b> Position: </b><p><ul>';
                while ( $row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){
                    echo '<li>'.$row1['year'].': '.$row1['description'].'</li>';
                }
                echo '</ul>';
                
            }
			?>
        </div>
        <form method="POST">
        <input type="submit" name="done" value="Done">
        </form>
	</div>
</body>
</html>