<?php
if(empty($_POST['month'])){
	echo "値が正しく選択されていません。";
	exit(1);
}
require_once('./myid.php');
require_once './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('template.xlsx');

$strcode = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8mb4'");
try {
	$dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_ID, DB_PASS, $strcode);
	$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
	echo $e->getMessage();
	exit;
}

$query = "SELECT * FROM Users";
$stmt = $dbh->prepare($query);
$stmt->execute();
$userCounter = $stmt->fetchAll();

$makeZip = new ZipArchive();
$fname = LAB_NAME . '(' . $_POST['month'] . '月).zip';
$zipPath = "export/" . $fname;

$result = $makeZip -> open("export/".$fname, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
if(!$result){
    echo "ファイルの生成に失敗しました。";
    exit(2);
}

$zipFiles = [];
$filesPath = [];
foreach($userCounter as $userIDNum){
	$sheet = $spreadsheet->getActiveSheet();
	$query = "SELECT * FROM History WHERE UserID = :userID AND Month = :selectMonth ORDER BY Date";

	$stmt = $dbh->prepare($query);
	$stmt->bindParam(':userID', $userIDNum['ID'], PDO::PARAM_STR);
	$stmt->bindParam(':selectMonth', $_POST['month'], PDO::PARAM_STR);
	$stmt->execute();

	$filenameData = $userIDNum['Name'];
	$outputName = md5(uniqid(rand(), true));
	$dayCounter = 0;
	$workTimes = 0;
	$colCount = 4;
	$sheetTitle = "卒業研究の記録 (" . $_POST['month'] . "月)";

	$sheet->setCellValue('A1', $sheetTitle);
	$sheet->setCellValue('B2', $userIDNum['studentID']);
	$sheet->setCellValue('F2', $userIDNum['Name']);

	$week = array( "日", "月", "火", "水", "木", "金", "土" );
	foreach($stmt as $data){
		$weekJP =  $week[date("w", strtotime($data['Date']))];
		$inputDate = str_replace("-", "/", $data['Date']);
		$inDateFile = substr($data['InTime'], 0,5);
		$outDateFile = substr($data['OutTime'], 0,5);
		$workTimeFile = substr($data['WorkTime'], 0,5);

		$areaA = "A" . $colCount;
		$areaB = "B" . $colCount;
		$areaC = "C" . $colCount;
		$areaD = "D" . $colCount;
		$areaE = "E" . $colCount;
		$areaF = "F" . $colCount;
		
		$sheet->setCellValue($areaA, $inputDate);
		$sheet->setCellValue($areaB, $weekJP);
		$sheet->setCellValue($areaC, $inDateFile);
		$sheet->setCellValue($areaD, $outDateFile);
		$sheet->setCellValue($areaE, $workTimeFile);
		$sheet->setCellValue($areaF, $data['WorkType']);
		
		$workTimes = $workTimes + substr($data['WorkTime'], 0,2);
		$dayCounter++;
		$colCount++;
	}

	$writeTotalDate = $dayCounter . "日";
	$writeTotalWork = $workTimes . "h";

	$sheet->setCellValue('C32', $writeTotalDate);
	$sheet->setCellValue('E32', $writeTotalWork);

	$fpath = 'export/' . $outputName . '.xlsx';
	array_push($filesPath,  $fpath);

	$writer = new Xlsx($spreadsheet);
	$writer->save($fpath);

	$makeZip->addFile($fpath, $filenameData.".xlsx");
	unset($sheet);
	unset($sheet);
}

$makeZip -> close();

header('Content-Type: application/force-download');
header('Content-Length: '.filesize($fpath));
header('Content-disposition: attachment; filename="'.$fname.'"');
readfile($zipPath);

foreach($filesPath as $files){
	unlink($files);
}

unlink($zipPath);

?>
