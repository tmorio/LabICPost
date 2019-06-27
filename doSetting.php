<?php

session_start();
require_once('./myid.php');

$strcode = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8mb4'");
try{
	$dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_ID, DB_PASS, $strcode);
	$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
	echo $e->getMessage();
	exit;
}

$query = "SELECT * FROM AdminUsers WHERE ID = :adminID";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':adminID', $_SESSION['userNo'], PDO::PARAM_INT);
$stmt->execute();
$permissionCheck = $stmt->fetch();

if(empty($permissionCheck['ID'])){
	echo '権限がありません。';
	exit(1);
}

unset($permissionCheck);

switch($_GET['Setup']){
	default:
		exit(0);
		break;
	case updateLab:
		$query = "SELECT * FROM AdminUsers WHERE ID = :UserID";
		$stmt = $dbh->prepare($query);
		$stmt->bindParam(':UserID', $_SESSION['userNo'], PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch();
		
		if (password_verify($_POST['nowPassword'], $result['Password'])){
			if(!empty($_POST['newPassword'])){
				$stmt = $dbh->prepare("UPDATE AdminUsers SET Password = ? WHERE ID = ?");
				$stmt->execute(array(password_hash($_POST['newPassword'], PASSWORD_DEFAULT), $_SESSION['userNo']));
			}

			$query = "UPDATE Settings SET LabName = :newLabName WHERE ID = 1";
			$stmt = $dbh->prepare($query);
			$stmt->bindParam(':newLabName', $_POST['newLabName'], PDO::PARAM_STR);
			$stmt->execute();
			header("Location: ./settings.php?mes=2");
			exit(0);
		}else{
			header("Location: ./settings.php?mes=1");
			exit(1);
		}
		break;
	case addUser:
		$query = "INSERT INTO Users (Name, Status, studentID) VALUES (:studentName, 0, :studentID)";
		$stmt = $dbh->prepare($query);
		$stmt->bindParam(':studentName', $_POST['studentName'], PDO::PARAM_STR);
		$stmt->bindParam(':studentID', $_POST['studentID'], PDO::PARAM_STR);
		$stmt->execute();
		header("Location: ./settings.php?page=members&mes=3");
		exit(0);
		break;
	case updateUser:
		$query = "UPDATE Users SET Name = :newName, studentID = :newID WHERE ID = :userID";
		$stmt = $dbh->prepare($query);
		$stmt->bindParam(':newName', $_POST['newStudentName'], PDO::PARAM_STR);
		$stmt->bindParam(':newID', $_POST['newStudentID'], PDO::PARAM_STR);
		$stmt->bindParam(':userID', $_POST['updateUserID'], PDO::PARAM_INT);
		$stmt->execute();
		header("Location: ./settings.php?page=members&mes=2");
		exit(0);
		break;
	case delUser:
		$query = "DELETE FROM History WHERE UserID = :userID";
		$stmt = $dbh->prepare($query);
		$stmt->bindParam(':userID', $_POST['deluserid'], PDO::PARAM_STR);
		$stmt->execute();
		$query = "DELETE FROM Users WHERE ID = :userID";
		$stmt = $dbh->prepare($query);
		$stmt->bindParam(':userID', $_POST['deluserid'], PDO::PARAM_STR);
		$stmt->execute();
		header("Location: ./settings.php?page=members&mes=4");
		exit(0);
		break;
}

