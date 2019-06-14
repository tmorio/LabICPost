<?php
if(empty($_POST['userid']) || empty($_POST['month'])){
	echo "値が正しく選択されていません。";
	exit(1);
}
require_once('./myid.php');

$strcode = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8mb4'");
try {
		$dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_ID, DB_PASS, $strcode);
		$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	} catch (PDOException $e) {
		echo $e->getMessage();
		exit;
}

$query = "SELECT * FROM Users WHERE ID = :userID";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':userID', $_POST['userid'], PDO::PARAM_STR);
$stmt->execute();
$userInfo = $stmt->fetch();

$query = "SELECT * FROM History WHERE UserID = :userID AND Month = :selectMonth";

$stmt = $dbh->prepare($query);
$stmt->bindParam(':userID', $_POST['userid'], PDO::PARAM_STR);
$stmt->bindParam(':selectMonth', $_POST['month'], PDO::PARAM_STR);
$stmt->execute();

$filenameData = $userInfo['Name'] . '('  . date("YmdHis") . ')';

$fileinput = "取組日,曜日,入室時刻,退室時刻,取組時間,取組内容\r\n";
$week = array( "日", "月", "火", "水", "木", "金", "土" );
foreach($stmt as $data){
	$weekJP =  $week[date("w", strtotime($data['Date']))];
	$inputDate = str_replace("-", "/", $data['Date']);
	$inDateFile = substr($data['InTime'], 0,5);
	$outDateFile = substr($data['OutTime'], 0,5);
	$workTimeFile = substr($data['WorkTime'], 0,5);

	$fileinput = $fileinput . $inputDate . "," . $weekJP . "," . $inDateFile . "," . $outDateFile . "," . $workTimeFile  . ","  . $data['WorkType']  . "\r\n";
}

$fileinput = mb_convert_encoding($fileinput, "SJIS", "UTF-8");

$fpath = './export/' . $filenameData . '.csv';
file_put_contents($fpath, $fileinput);

$fname = $userInfo['Name']  . $_POST['month'] . '月.csv';

header('Content-Type: application/force-download');
header('Content-Length: '.filesize($fpath));
header('Content-disposition: attachment; filename="'.$fname.'"');
readfile($fpath);
unlink($fpath);

?>
