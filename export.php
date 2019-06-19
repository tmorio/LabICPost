<?php
require_once './myid.php'; ?>
<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>LabTimes - Export</title>

		<link rel="stylesheet" type="text/css" href="css/materialize.min.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="js/materialize.min.js"></script>
		<script>
			$(document).ready(function(){
				$('.modal').modal();
			});
		</script>
	</head>
	<body>
	<?php require_once './wbheader.php'; ?>
	<div class="deviceAdd">
		<div class="wizardInfo">
	<?php
		echo '
			<h3>Export to CSV</h3>
			記録ダウンロードする対象ユーザを選択して下さい。<br>
		';
		$strcode = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8mb4'");
		try {
			$dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_ID, DB_PASS, $strcode);
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
		$query = 'SELECT * FROM Users';
		$stmt = $dbh->prepare($query);
		$stmt->execute();
		foreach ($stmt as $data) {
			echo '<a class="modal-trigger" href="#nowAccount" onclick="selectUser('.$data['ID'].')">';
			echo '<div class="cardProfileOn"><div class="boxProfile">';
			if (empty($data['PhotoName'])) {
				echo '<img src="img/default.jpg" class="iconBox">';
			} else {
				echo '<img src="img/users/'.$data['PhotoName'].'.jpg" class="iconBox">';
			}
			echo '<div><p>  '.$data['Name'].'</p></div>';
			echo '</div></div></a>';
		}
	?>

		</div>
	</div>
	<div id="nowAccount" class="modal">
		<form action="makecsv.php" method="POST">
			<div class="modal-content">
				<h3>ダウンロードする範囲の選択</h3>
				<p>月範囲を以下から選択して下さい。</p>
				<div class="row">
					<input type="hidden" id="userid" name="userid" value="" required>
					<div class="input-field col s12">
						<select name="month" required>
							<option value="" disabled selected>ここをクリックして選択して下さい。</option>
							<option value="6">2019 / 6</option>
						</select>
					</div>
				</div>
				<a class="waves-effect waves-light modal-close btn red left"><i class="material-icons left">close</i>キャンセル</a>
				<button class="btn waves-effect waves-light right" type="submit" id="login" name="login"><i class="material-icons left">check</i>ダウンロード</button><br>
			</div>
		</form>
	</div>
	<div id="allDownload" class="modal">
		<form action="makeallcsv.php" method="POST">
			<div class="modal-content">
				<h3>ダウンロードする範囲の選択</h3>
				<p>月範囲を以下から選択して下さい。</p>
				<div class="row">
					<div class="input-field col s12">
						<select name="month" required>
							<option value="" disabled selected>ここをクリックして選択して下さい。</option>
							<option value="6">2019 / 6</option>
						</select>
					</div>
				</div>
				<a class="waves-effect waves-light modal-close btn red left"><i class="material-icons left">close</i>キャンセル</a>
				<button class="btn waves-effect waves-light right" type="submit" id="login" name="login"><i class="material-icons left">check</i>ダウンロード</button><br>
			</div>
		</form>
	</div>
		<footer>
			<script>
				function selectUser(userID){
					document.getElementById( "userid" ).value = userID;
				}
				$(document).ready(function(){
					$('select').formSelect();
				});
			</script>
		</footer>
	</body>
</html>
