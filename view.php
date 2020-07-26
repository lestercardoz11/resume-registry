<?php
require_once('pdo.php');
require_once('header.php');
require_once('util.php');

//load up the position rows
$positions = loadPos($pdo, $_REQUEST['profile_id']);

//load up the education rows
$educations = loadEdu($pdo, $_REQUEST['profile_id']);

?>

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

            echo '<p><b>Position Year: </b></p><ul>';
            foreach ($positions as $position) {
                echo '<li>'.htmlspecialchars($position['year']).': '.htmlspecialchars($position['description']).'</li>';
            }
            echo '</ul>';

            echo '<p><b>Education Year: </b></p><ul>';
            foreach ($educations as $education) {
                echo '<li>'.htmlspecialchars($education['year']).': '.htmlspecialchars($education['name']).'</li>';
            }
            echo '</ul>';
			?>
        </div>
        <form method="POST">
        <input type="submit" name="done" value="Done">
        </form>
	</div>
</body>
<?php
include('footer.php');
?>