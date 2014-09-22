<?php
if (isset($_POST['password'])){
 //$salt = "some-salty-salt-a9u9jfncicxbzs9";
 $salt = sha1($_POST['password'] . microtime());
 $password = sha1($salt . $_POST['password']);
 //MD5 (no salt)
 //$result = md5($_POST['password']);
 
 //SHA1 (no salt)
 
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Hash Generator</title>
	<style>
		label{margin-top: 10px; display: block;}
	</style>
</head>
</body>
	<h1>Генерация Hash</h1>
	<?php if(isset($salt)):?>
		<h2>salt</h2>
		<h3><?php echo $salt;?></h2>
		<h2>password</h2>
		<h3><?php echo $password;?></h2>
	<hr>
	<?php endif; ?>
	<form action="hash.php" method="POST">
		<label for="password">Password</label><br>
		<input type="text" name="password"/>
		<input type="submit" value="Вперед"/>
	</form>
