<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function check_db_errors($pdo) {
	$error_code = $pdo->errorInfo()[0];
	if ($error_code != 0) {
		$_SESSION['error'] = "Errore Database, codice: $error_info";
		header('Location: index.php'); 
	}
}