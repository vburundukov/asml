<?php
// запустить сессию
session_start();
$title = "Вход";
// загрузить файл, если не было загружено ранее.
require_once "inc/database.php";
db_connect();
// загрузить auth, если не было загружено ранее.
require_once "inc/auth.php";
$current_user = current_user();

include "_header.php"; 
?>

<div class="wrapper">
<h1><?php echo $title; ?></h1>
  
<?php if($_GET['error'] == "1"): ?>
		<h4 class="error">email и/или пароль не верны</h4>
<?php endif ?>

<?php if($_GET['login_required'] == "1"): ?>
		<h3 class="error">Для отображения страницы вам нужно войти.</h3>
<?php else: ?>

<?php endif; ?>

<?php if(!$current_user): ?>
		<form action="authenticate.php" method="POST" role="form" class="form-horizontal">
		 <table class="form">
		  <tr>
			<td>
			<label for="username">E-mail</label>
			<div class="form-group">
			<input type="text" class="form-control" name="username" size="40" id="username" placeholder="Введите Ваш E-mail">
			<div>
			</td>
		  </tr>
		  <tr>
			<td><div class="form-group">
			<label for="password">Пароль</label>
			<input type="password" class="form-control" name="password" id="password" size="40" placeholder="Введите Ваш Пароль"></td><div>
		  </tr>
		  <tr>
		
		  <td colspan="2" >
				<button type="submit" class="btn btn-default">Войти</button>
		  </td>
		  </tr>
		 </table>
		</form>
<?php else: ?>
	<h3 class="error">Вы вошли.</h3>
	<a href="service.php">Сервис</a>
<?php endif; ?>	
</div>


<?php include "_footer.php"; ?>	