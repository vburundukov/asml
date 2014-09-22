<div id="header">
<div class="session">
<?php if(current_user()): ?>
<strong><? echo $current_user['first_name']." "; ?>

<a href="logout.php">Выход</a>
		</strong>
<?php else: ?>
		<a href="login.php">Войти</a
<?php endif;?>
</div>
</div>