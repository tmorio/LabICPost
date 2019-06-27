<?php
session_start();

if(empty($_SESSION['userNo'])){
	header("Location: login.php");
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

$query = "SELECT * FROM Settings WHERE ID = 1";
$stmt = $dbh->prepare($query);
$stmt->execute();
$result = $stmt->fetch();

$query = "SELECT * FROM AdminUsers WHERE ID = :UserID";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':UserID', $_SESSION['userNo'], PDO::PARAM_STR);
$stmt->execute();
$userResult = $stmt->fetch();

?>
<!doctype html>
<html style="background:#fff;">
	<head>
		<meta charset="UTF-8">
		<title>LabTimes - Admin</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="css/materialize.min.css">
		<link rel="stylesheet" type="text/css" href="css/style.css?">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="js/materialize.min.js"></script>

	</head>
	<body style="background:#fff;">

	<div class="serviceHeader navbar-fixed">
		<nav>
			<div class="nav-wrapper black-text">
				<img class="logo-image" src="img/logo.png">
				<ul class="right">
					<a class="btn waves-effect waves-light" href="version.php?from=2"><i class="material-icons left">info_outline</i>バージョン情報</a>
					<a class="btn waves-effect waves-light" href="logout.php"><i class="material-icons left">exit_to_app</i>ログアウト</a>
					&thinsp;
				</ul>
			</div>
		</nav>
	</div>

	<div class="settingBoard">
		<!-- 設定分類一覧表示 -->
		<div class="collection with-header settingList">
			<div class="collection-header"><h5>システム設定</h5></div>
			<a href="?page=account" class="collection-item blue-grey-text text-darken-4"><i class="material-icons left">account_circle</i>一般設定</a>
			<a href="?page=members" class="collection-item blue-grey-text text-darken-4"><i class="material-icons left">group</i>研究生管理</a>
		</div>
		<!-- 設定表示 -->
		<div class="settingInfo">
<?php
switch ($_GET['mes']) {
	case 1:
		echo '<div class="row"><div class="col s12 m12 pink lighten-5"><h5 class="valign-wrapper"><i style="font-size: 2.5rem;" class="material-icons orange-text text-darken-5">warning</i><span styke="color:#fff;">';
		echo '&nbsp現在のパスワードが違います。</span></h5></div></div>';
		break;
	case 2:
		echo '<div class="row"><div class="col s12 m12 light-blue accent-4"><h5 class="valign-wrapper"><i style="font-size: 2.5rem;color:#fff;" class="material-icons">check</i><span style="color:#fff;">';
		echo '&nbsp設定を更新しました。</span></h5></div></div>';
		break;
	case 3:
		echo '<div class="row"><div class="col s12 m12 light-blue accent-4"><h5 class="valign-wrapper"><i style="font-size: 2.5rem;color:#fff;" class="material-icons">check</i><span style="color:#fff;">';
		echo '&nbsp研究生を追加しました。</span></h5></div></div>';
		break;
	case 4:
		echo '<div class="row"><div class="col s12 m12 light-blue accent-4"><h5 class="valign-wrapper"><i style="font-size: 2.5rem;color:#fff;" class="material-icons">check</i><span style="color:#fff;">';
		echo '&nbspデータベースから削除しました。</span></h5></div></div>';
                break;
}

switch ($_GET['page']) {
	default:
		echo '
			<h3>一般設定</h3><br />
			<form action="doSetting.php?Setup=updateLab" method="POST">
				研究室名<br />
				<input type="text" name="newLabName" id="newLabName" value="' . htmlspecialchars($result['LabName'], ENT_QUOTES, 'UTF-8') . '" required>
				管理者ID (変更を希望する場合、お問い合わせ下さい。)<br />
				<input type="text"  value="' . htmlspecialchars($userResult['UserID'], ENT_QUOTES, 'UTF-8') . '" disabled>
				新しいパスワード (変更する場合は入力して下さい)<br />
				<input type="password" name="newPassword" id="newPassword">
				<br /><br /><br />
				現在のパスワード (必須)<br />
				<input type="password" name="nowPassword" id="nowPassword" required><br><br>
				<button class="btn waves-effect waves-light" type="submit"><i class="material-icons right">check</i>変更を適用する</button>
			</form>
			';
		break;

	case members:

		echo '<h3>研究生管理</h3>';
		echo '<p>研究生の追加や登録情報の編集ができます。</p>';
		echo '<a class="waves-effect waves-light btn modal-trigger" href="#addUser" onclick=""><i class="material-icons left">group_add</i>研究生の追加</a>&nbsp';
		echo '<a class="btn waves-effect waves-light modal-trigger blue" href="#allDownload"><i class="material-icons left">cloud_download</i>全研究生の記録書き出し</a><br>';
		$query = "SELECT * FROM Users";
		$stmt = $dbh->prepare($query);
		$stmt->execute();
		$researchUsers = $stmt->fetchAll();

		if(count($researchUsers) != 0){
			echo '<ul class="collection">';

			foreach($researchUsers as $data){
				echo '<li class="collection-item avatar">';

				if(!empty($data['PhotoName'])){
					echo '<img src="img/users/' . htmlspecialchars($data['PhotoName'], ENT_QUOTES, 'UTF-8') . '.jpg" alt="" class="circle">';
				}else{
					echo '<img src="img/default.jpg" alt="" class="circle">';
				}

				echo '<span class="title">' . htmlspecialchars($data['Name'], ENT_QUOTES, 'UTF-8') . '</span>';

				echo '<span class="right">';
				echo '<a class="waves-effect waves-light btn modal-trigger" href="?page=info&user=' . $data['ID'] . '"><i class="material-icons left">assessment</i>詳細・設定</a>';
				echo '</span>';
				echo '<br><br>';
				echo '</li>';

			}

			echo '</ul>';
		}else{
			echo '<br><div class="row"><div class="col s12 m12 pink lighten-5"><h5 class="valign-wrapper"><i style="font-size: 2.5rem;" class="material-icons orange-text text-darken-5">warning</i><span styke="color:#fff;">';
			echo '&nbsp研究生が登録されていません。</span></h5></div></div>';
		}
		break;
	case info:
		$query = "SELECT * FROM Users WHERE ID = :userID";
		$stmt = $dbh->prepare($query);
		$stmt->bindParam(':userID', $_GET['user'], PDO::PARAM_INT);
		$stmt->execute();
		$userInfo = $stmt->fetch();

		$query = "SELECT * FROM History WHERE UserID = :userID ORDER BY Date DESC LIMIT 7";
		$stmt = $dbh->prepare($query);
		$stmt->bindParam(':userID', $_GET['user'], PDO::PARAM_INT);
		$stmt->execute();
		$userHistory = $stmt->fetchAll();

		if(empty($userInfo['ID'])){
			echo '値の受け渡しに失敗しました。';
			exit(1);
		}
		echo '<a class="waves-effect waves-light btn modal-trigger" href="?page=members"><i class="material-icons left">arrow_back</i>戻る</a><br><br>';
		echo '<h3>研究生情報</h3>';
		echo '<div style="display: flex;">';
		if(empty($userInfo['PhotoName'])){
			echo '<img src="img/default.jpg" class="iconBox">';
		}else{
			echo '<img src="img/users/' . htmlspecialchars($userInfo['PhotoName'], ENT_QUOTES, 'UTF-8') . '.jpg" class="iconBox">';
		}
		echo '<div class="infoName"><span style="font-size:2.5rem;">' . htmlspecialchars($userInfo['Name'], ENT_QUOTES, 'UTF-8') . '</span><br><span style="font-size:1.5rem;">学籍番号 : ' . htmlspecialchars($userInfo['studentID'], ENT_QUOTES, 'UTF-8') . '&nbsp;';

		if(strpos($userInfo['studentID'],'S') == true){
			echo '(専攻科)';
		}else{
			echo '(本科)';
		}

		echo '</span></div>';
		echo '</div><br>';
		echo '<a class="waves-effect waves-light btn modal-trigger" href="#nowAccount" onclick="selectUser(' . $userInfo['ID'] . ')"><i class="material-icons left">get_app</i>記録書き出し</a>';
		echo '&nbsp;';
		echo '<a class="waves-effect waves-light btn modal-trigger blue" href="#editUser" onclick="editUser(' . $userInfo['ID'] . ',\'' . $userInfo['Name'] . '\',\'' . $userInfo['studentID'] . '\')"><i class="material-icons left">edit</i>編集</a>';
		echo '&nbsp;';
		echo '<a class="waves-effect waves-light btn modal-trigger red right" href="#delUser" onclick="delUser(' . $userInfo['ID'] . ')"><i class="material-icons left">close</i>削除</a><br><br>';
		echo '<h5>直近7件の入退室情報</h5><br>';

		if(count($userHistory) != 0){

			echo '<table class="striped"><thead><tr><th>日付</th><th>入室時刻</th><th>退室時刻</th><th>取組時間</th><th>取組内容</th></tr></thead>';
			echo '<tbody>';

			$week = array( "日", "月", "火", "水", "木", "金", "土" );

			foreach($userHistory as $data){
				$weekJP =  $week[date("w", strtotime($data['Date']))];
				$inputDate = str_replace("-", "/", $data['Date']);
				$inDateFile = substr($data['InTime'], 0,5);
				$outDateFile = substr($data['OutTime'], 0,5);
				$workTimeFile = substr($data['WorkTime'], 0,5);
				echo '<tr>';
				echo '<td>' . $inputDate . ' (' . $weekJP . ')</td><td>' . $inDateFile . '</td><td>' . $outDateFile . '</td><td>' . $workTimeFile . '</td><td>' . htmlspecialchars($data['WorkType'], ENT_QUOTES, 'UTF-8') . '</td>';
				echo '</tr>';
			}
			echo '</tbody>';
			echo '</table>';

		}else{
			echo '入退室記録がありません。';
		}
		break;
}

?>

		</div>
	</div>
	<div id="nowAccount" class="modal">
		<form action="makesheet.php" method="POST">
			<div class="modal-content textBlack">
				<h3>ダウンロードする範囲の選択</h3>
				<p>月範囲を以下から選択して下さい。</p>
				<div class="row">
					<input type="hidden" id="userid" name="userid" value="" required>
					<div class="input-field col s12">
						<select class="browser-default" name="month" required>
							<option value="" disabled selected>ここをクリックして選択して下さい。</option>
							<option value="4">2019 / 4</option>
							<option value="5">2019 / 5</option>
							<option value="6">2019 / 6</option>
						</select>
					</div>
				</div>
				<a class="waves-effect waves-light modal-close btn red left"><i class="material-icons left">close</i>キャンセル</a>
				<button class="btn waves-effect waves-light right" type="submit" id="login" name="login"><i class="material-icons left">check</i>ダウンロード</button><br>
			</div>
		</form>
	</div>

	<div id="delUser" class="modal">
		<form action="doSetting.php?Setup=delUser" method="POST">
			<div class="modal-content textBlack">
				<input type="hidden" id="deluserid" name="deluserid" value="" required>
				<h3>研究生の削除</h3>
				<p>削除を行うと今まで記録した入退室時間や取組時間などが全て削除されます。<br>本当に削除をしますか?</p>
				<a class="waves-effect waves-light modal-close btn blue left"><i class="material-icons left">close</i>キャンセル</a>
				<button class="btn waves-effect waves-light right red" type="submit" id="login" name="login"><i class="material-icons left">delete</i>削除する</button><br>
			</div>
		</form>
	</div>
	<div id="addUser" class="modal">
		<form action="doSetting.php?Setup=addUser" method="POST">
			<div class="modal-content textBlack">
				<h3>研究生の追加</h3>
				<p>研究生の名前と学籍番号を入力してください。</p>
				<div class="row">
					<div class="input-field col s12">
						<i class="material-icons prefix">account_circle</i>
						<input name="studentName" id="studentName" type="text" class="validate" required>
						<label for="studentName">名前（必須）</label>
					</div>
					<div class="input-field col s12">
						<i class="material-icons prefix">school</i>
						<input name="studentID" id="studentID" type="text" class="validate" required>
						<label for="studentID">学籍番号（必須）</label>
					</div>
				</div>
				<a class="waves-effect waves-light modal-close btn red left"><i class="material-icons left">close</i>キャンセル</a>
				<button class="btn waves-effect waves-light right" type="submit" id="login" name="login"><i class="material-icons left">check</i>追加する</button><br>
			</div>
		</form>
	</div>
	<div id="editUser" class="modal">
		<form action="doSetting.php?Setup=updateUser" method="POST">
			<div class="modal-content textBlack">
				<h3>研究生情報の編集</h3>
				<input type="hidden" id="updateUserID" name="updateUserID" value="" required>
				<div class="row">
					<div class="input-field col s12">
						<i class="material-icons prefix">account_circle</i>
						<input name="newStudentName" id="newStudentName" type="text" class="validate" required>
						<label class="active" for="newStudentName">名前（必須）</label>
					</div>
					<div class="input-field col s12">
						<i class="material-icons prefix">school</i>
						<input  name="newStudentID" id="newStudentID" type="text" class="validate" required>
						<label class="active" for="newStudentID">学籍番号（必須）</label>
					</div>
				</div>
				<a class="waves-effect waves-light modal-close btn red left"><i class="material-icons left">close</i>キャンセル</a>
				<button class="btn waves-effect waves-light right" type="submit" id="login" name="login"><i class="material-icons left">check</i>変更する</button><br>
			</div>
		</form>
	</div>
	<div id="allDownload" class="modal">
		<form action="makeallsheet.php" method="POST">
			<div class="modal-content textBlack">
				<h3>ダウンロードする範囲の選択</h3>
				<p>月範囲を以下から選択して下さい。</p>
				<div class="row">
					<div class="input-field col s12">
						<select class="browser-default" name="month" required>
							<option value="" disabled selected>ここをクリックして選択して下さい。</option>
							<option value="4">2019 / 4</option>
							<option value="5">2019 / 5</option>
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
				$(document).ready(function(){
					$('.modal').modal();
				});
				function selectUser(userID){
					document.getElementById( "userid" ).value = userID;
				}
				function editUser(userID, Name, studentID){
					document.getElementById( "updateUserID" ).value = userID;
					document.getElementById( "newStudentName" ).value = Name;
					document.getElementById( "newStudentID" ).value = studentID;
					$(document).ready(function() {
						M.updateTextFields();
					});
				}
				function delUser(userID){
					document.getElementById( "deluserid" ).value = userID;
	                        }
				$(document).ready(function(){
					$('select').formSelect();
				});
			</script>
		</footer>
</body>

