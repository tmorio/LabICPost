<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'hoge');
define('DB_ID', 'hogehoge');
define('DB_PASS', 'example');
define('MAIN_URL', 'index.php');

try {
	$dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_ID, DB_PASS, $strcode);
	$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
	echo $e->getMessage();
}
$query = "SELECT * FROM Settings WHERE ID = 1";
$stmt = $dbh->prepare($query);
$stmt->execute();

$labInfo = $stmt->fetch();

define('LAB_NAME', $labInfo['LabName']);
