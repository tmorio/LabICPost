<?php require_once('./myid.php'); ?>

<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>LabTimes - Export</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="css/materialize.min.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="js/materialize.min.js"></script>
		<script type="text/javascript" src="js/footerFixed.js"></script>
	</head>
	<body>
		<div class="serviceHeader navbar-fixed">
        		<nav>
                		<div class="nav-wrapper black-text">
                        		<img class="logo-image" src="img/logo.png">
                        		<ul class="right">
                                		<a class="btn waves-effect waves-light" href="export.php"><i class="material-icons left">keyboard_backspace</i>戻る</a>
                                		&thinsp;
                        		</ul>
                		</div>
        		</nav>
		</div>
		<div class="container">
			<div class="wizardInfoA">
				<h3>Version Infomation</h3>
				<div class="valign-wrapper">
					<img class="logo-image" src="img/logo.png"><br>
					<h4>Ver1.5.2</h4>
				</div>
				<b>
					Release: 2019/06/20<br>
					Host Type: Install<br><br>
					Organization Name: <?php echo LAB_NAME; ?><br>
				</b>
				<h3>License</h3>
					Copyright 2019 Takenori Morio<br>

					<p>Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), 
					to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, 
					and to permit persons to whom the Software is furnished to do so, subject to the following conditions:</p>

					<p>The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.</p>

					<p>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
					FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. <br>
					IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, 
					ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.</p>
				<h3>Third Party Lisence</h3>
					<h5>Materialize CSS</h5>
					<p>The MIT License (MIT)</p>

					<p>Copyright (c) 2014-2019 Materialize</p>

					<p>Permission is hereby granted, free of charge, to any person obtaining a copy
					of this software and associated documentation files (the "Software"), to deal
					in the Software without restriction, including without limitation the rights
					to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
					copies of the Software, and to permit persons to whom the Software is
					furnished to do so, subject to the following conditions:</p>

					<p>The above copyright notice and this permission notice shall be included in all
					copies or substantial portions of the Software.</p>

					<p>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
					IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
					FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
					AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
					LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
					OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
					SOFTWARE.</p>
			</div>
		</div>
	</body>
</html>
