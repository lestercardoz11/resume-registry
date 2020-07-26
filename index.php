<?php
require_once('pdo.php');
require_once('header.php');
session_start();
?>

<body>
	<div class="container">
		<header>
			<h1> Resume Registry <?= isset($_SESSION['name']) ? '(Logged with '.htmlentities($_SESSION['email']).')' : '' ?> </h1>
			<?php
            if( isset($_SESSION['msg']) && $_SESSION['msg'] != false ){
                echo '<p style="color:green">';
                echo    $_SESSION['msg'];
                echo '</p>';
            }
            unset($_SESSION['msg']);

            
            if( ! isset($_SESSION['name']) ){
                echo '<p class="loginout"> <a href="login.php">Please log in</a> </p>'; 
            } else {
                echo '<p class="loginout"> <a href="logout.php">Log out</a> </p>';
            }
            ?>
		</header>
        
        <?php
        echo "<div class='view_items'>";
        echo "</div>";
        ?>

        <div class='view_items'>
			<?php
			$stmt = $pdo->query( 'SELECT profile_id, user_id, first_name, last_name, headline FROM Profile ORDER BY profile_id' );
            if( $stmt->rowCount() == 0 ){
                echo '<p> No profiles found </p>';
            } else {
                echo '<table border="1">
                        <thead><tr>
                            <th> Name </th>
                            <th> Headline </th>';
                if( isset($_SESSION['user_id']) ){
                    echo "  <th> Action </th>";
                }
                echo "  </tr></thead>
                        <tbody>";

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr><td>';
                    echo '<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a>';
                    echo '</td><td>';
                    echo htmlentities($row['headline']);
                    echo '</td>';
                    if (isset($_SESSION['user_id'])) {
                        if ($_SESSION['user_id'] == $row['user_id']) {
                            echo '<td><a href="edit.php?profile_id='.$row['profile_id'].'"> Edit </a> / <a href="delete.php?profile_id='.$row['profile_id'].'"> Delete </a></td>';
                        } else {
                            echo '<td></td>';
                        }
                    }
                    echo '</tr>';
                }
                echo "	</tbody>
                    </table>";
            }
			?>
		</div>
        <br>
        <?php
        if( isset($_SESSION['name']) ){
            echo '<a href="add.php"> Add New Entry </a>'; 
        }
        ?>
	</div>
</body>
<?php
include_once('footer.php');
?>