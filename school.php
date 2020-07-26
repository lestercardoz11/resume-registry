<?php


if (!isset($_REQUEST['term'])) {
    die('Missing required parameter');
}
if (!isset($_COOKIE[session_name()])) {
    die('Must be logged in');
}

session_start();

if (!isset($_SESSION['user_id'])) {
    die('ACCESS DENIED');
}

require 'pdo.php';

header('Content-Type: application/json; charset=utf-8');

$sql = 'SELECT name FROM Institution WHERE name LIKE :prefix';
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':prefix' => $_REQUEST['term'] . "%"
]);
$retval = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $retval[] = $row['name'];
}

echo (json_encode($retval, JSON_PRETTY_PRINT));