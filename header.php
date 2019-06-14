<div class="serviceHeader navbar-fixed">
	<nav>
		<div class="nav-wrapper black-text">
			<!-- ロゴ -->
			<img class="logo-image" src="img/logo.png">
			<ul class="right">
					<?php
					if(!empty($_SESSION['userName'])){
						echo '<div class="chip">';
						if(!empty($_SESSION['PhotoName'])){
							echo '<img src="img/users/' . $_SESSION['PhotoName'] . '.jpg" alt="Contact Person">';
						}else{
							echo '<img src="img/default.jpg" alt="Contact Person">';
						}
					}
					?>
					<?php
						if(!empty($_SESSION['userName'])){
							print htmlspecialchars($_SESSION['userName'], ENT_QUOTES, 'UTF-8');
							echo '</div>';
						}
					?>
					&nbsp;&nbsp;
				&thinsp;
			</ul>
		</div>
	</nav>
</div>
<script>
	$('.dropdown-trigger').dropdown();
</script>
