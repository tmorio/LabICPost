<?php
require_once('./myid.php');

?>
<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>LabTimes</title>

		<link rel="stylesheet" type="text/css" href="css/materialize.min.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="js/materialize.min.js"></script>
		<script type="text/javascript" src="js/footerFixed.js"></script>
		<script>
			$(document).ready(function(){
				$('.modal').modal();
			});
		</script>
	</head>
	<body>
	<?php require_once('./header.php'); ?>
	<div class="deviceAdd">
		<!-- 設定分類一覧表示 -->
		<div class="wizardInfo">
	<?php
		switch($_GET['step']){
			default:
				echo '
					<h3>Welcome to IIJLab!</h3>
					研究者名を選択して下さい。<br>
				';
				$strcode = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8mb4'");
				try {
					$dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_ID, DB_PASS, $strcode);
					$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				} catch (PDOException $e) {
					echo $e->getMessage();
				}
				$query = "SELECT * FROM Users";
				$stmt = $dbh->prepare($query);
				$stmt->execute();
				foreach($stmt as $data){
					echo '<a class="modal-trigger" href="#nowAccount" onclick="selectUser(' . $data['ID'] .', ' . $data['Status'] . ')">';
					if($data['Status'] == 0){
						echo '<div class="cardProfileOn"><div class="boxProfile">';
					}else{
						echo '<div class="cardProfile"><div class="boxProfile">';
					}
					if(empty($data['PhotoName'])){
						echo '<img src="img/default.jpg" class="iconBox">';
					}else{
						echo '<img src="img/users/' . $data['PhotoName'] . '.jpg" class="iconBox">';
					}
					echo '<div><p>&nbsp;&nbsp;' . $data['Name'] . '</p></div>';
					echo '</div></div></a>';
				}

				break;
			case 2:
				if(empty($_POST['userID']) && empty($_POST['userStatus'])){
				header("Location: index.php");
				}
				$strcode = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8mb4'");
				try {
					$dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_ID, DB_PASS, $strcode);
					$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				} catch (PDOException $e) {
					echo $e->getMessage();
					exit;
				}
				if($_POST['userStatus'] == 0){
					$query = "UPDATE Users SET Status = 1, InTime = :nowTime WHERE ID = :userID";
					$stmt = $dbh->prepare($query);
					$stmt->bindParam(':nowTime', date('H:i') , PDO::PARAM_STR);
					$stmt->bindParam(':userID', $_POST['userID'], PDO::PARAM_INT);
					$stmt->execute();
				}else{
					$query = "SELECT * FROM Users WHERE ID = :userID";
					$stmt = $dbh->prepare($query);
					$stmt->bindParam(':userID', $_POST['userID'], PDO::PARAM_INT);
					$stmt->execute();

					$infoData = $stmt->fetch();
					$InTime = $infoData['InTime'];

					$query = "UPDATE Users SET Status = 0, InTime = NULL WHERE ID = :userID";
					$stmt = $dbh->prepare($query);
					$stmt->bindParam(':userID', $_POST['userID'], PDO::PARAM_INT);
					$stmt->execute();

					$query = "INSERT INTO History (UserID, Date, InTime, OutTime, WorkTime, WorkType, Month) VALUES (:userID, :date, :inTime, :outTime, :workTime, :workType, :nowMonth)";
					$stmt = $dbh->prepare($query);
					$stmt->bindParam(':userID', $_POST['userID'], PDO::PARAM_INT);
					$stmt->bindParam(':date', date('Y/m/d'), PDO::PARAM_STR);
					$stmt->bindParam(':inTime', $InTime, PDO::PARAM_STR);
					$stmt->bindParam(':outTime', date('H:i'), PDO::PARAM_STR);
					$stmt->bindParam(':workTime', $_POST['workTime'], PDO::PARAM_STR);
					$stmt->bindParam(':nowMonth', date('m'), PDO::PARAM_STR);

					$insertData = "";
					$typeCounter = count($_POST['workType']);
					$prCounter = 1;
					foreach($_POST['workType'] as $typeData){
						$insertData = $insertData . $typeData;
						if($typeCounter != $prCounter){
							$insertData = $insertData . "・";
							$prCounter++;
						}
					}
					$stmt->bindParam(':workType', $insertData, PDO::PARAM_STR);
					$stmt->execute();

				}

				echo '<br><div class="center">';
				echo '<i class="large material-icons checkColor">check</i>';
				echo '<h2>完了</h2><br>';

				if($_POST['userStatus'] == 0){
					echo '<h4>入室情報の登録が完了しました。</h4>';
				}else{
					echo '<h4>退室情報の登録が完了しました。本日もお疲れ様でした。</h4>';
				}
				echo '<br><h5>3秒後に最初の画面に戻ります。</h5>';
				echo '<META http-equiv="Refresh" content="3;URL=index.php"></div>';
				break;
		}
	?>

		</div>
	</div>
	<div id="nowAccount" class="modal">
		<form action="?step=2" method="POST">
			<div class="modal-content">
				<div id="messageTitle"></div>
				<div id="messageLine"></div>
				<div class="row">
					<input type="hidden" id="userID" name="userID" value="" required>
					<input type="hidden" id="userStatus" name="userStatus" value="" required>
				</div>
				<a class="waves-effect waves-light modal-close btn red left"><i class="material-icons left">close</i>キャンセル</a>
				<button class="btn waves-effect waves-light right" type="submit" id="login" name="login"><i class="material-icons left">check</i>登録</button><br>
			</div>
		</form>
	</div>
		<footer>
			<script>
                        function selectUser(userID, statusCode){
                                document.getElementById( "userID" ).value = userID;
				document.getElementById( "userStatus" ).value = statusCode;
				var messageTitle = document.getElementById('messageTitle');
				var messageLine = document.getElementById('messageLine');
				if(statusCode == 0){
					messageTitle.innerHTML = '<h5>入室登録</h5>';
					messageLine.innerHTML = '<p>入室登録を行います。よろしいですか?</p>';
				}else{
					messageTitle.innerHTML = '<h5>退室登録</h5>';
					messageLine.innerHTML = '<div class="input-field col s12"><select name="workTime" required><option value="" disabled selected>取り組み時間の選択</option><option value="2:00">2時間（1コマ）</option><option value="4:00">4時間（2コマ）</option><option value="6:00">6時間（3コマ）</option><option value="8:00">8時間（4コマ）</option></select></div><div class="typeSelect"><p><label><input type="checkbox" class="filled-in" name="workType[]" value="文献調査"/><span>文献調査</span></label></p><p><label><input type="checkbox" checked="checked" class="filled-in" name="workType[]" value="作業"/><span>作業</span></label></p><p><label><input type="checkbox" class="filled-in" name="workType[]" value="実験"/><span>実験</span></label></p><p><label><input type="checkbox" class="filled-in"  name="workType[]" value="データ整理"/><span>データ整理</span></label></p><p><label><input type="checkbox" class="filled-in" name="workType[]" value="資料作成"/><span>資料作成</span></label></p><p><label><input type="checkbox" class="filled-in" name="workType[]" value="ディスカッション"/><span>ディスカッション</span></label></p><p><label><input type="checkbox" class="filled-in" name="workType[]" value="ゼミ"/><span>ゼミ</span></label></p></div>';
					$(document).ready(function(){
						$('select').formSelect();
					});
				}
                        }
			</script>
		</footer>
	</body>
</html>

