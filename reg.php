<?php
$title = "Регистрация";
// запустить сессию
session_start();

// загрузить файл, если не было загружено ранее.
require_once "inc/database.php";

db_connect();

// загрузить функции.
require_once "inc/functions.php";

// загрузить авторизацию пользователей
require_once "inc/auth.php";

$current_user = current_user();
require_login();

include "_header.php"; 

// Если запрос отправлен с выполненной формы
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	// очищаем  first,last, and email
	$first_name = pg_escape_string($_POST['first_name']);
	$last_name = pg_escape_string($_POST['last_name']);
	$email = pg_escape_string($_POST['email']); 

	//Генерируем псевдо-число salt
	$salt = sha1(microtime() . $_POST['password']);
	
	//Генерируем пароль с помощью salt
	$password = sha1($salt . $_POST['password']);
	
	// Вставляем пользователя в базу данных
	$query = 'INSERT INTO public.users_asml
			("first_name", "last_name", "email", "salt", "password", "bio", "active")
			VALUES
			('."'".$first_name."'".','."'".$last_name."'".',  '."'".$email."'".', '."'".$salt."'".', '."'".$password."'".', '."'".'регистрация'."'".", TRUE".' );';
	echo 	$query;
	$result = pg_query($query);
	$result_status = pg_result_status($result);
	$mysqli_errno_equivalent = pg_result_error_field($result_status, PGSQL_DIAG_SQLSTATE);
	echo "'".$mysqli_errno_equivalent.$result_status."'";
	
	if($result){
		// PHP 5.4.4. взятие id после вставки данных
		//$user_id =  pg_escape_identifier($result);
		//log_in($user_id);
		//echo $user_id;
		header("Location: index.php");
	}
}

?><table>
<div id="content">

<tr>
<td width="5%">
<div id="leftmenu" align="center">

Меню слева 
</div>

</td><td width="90%">

<div class="wrapper">
<table><tr>
<td>

<tr><td><h2><?php echo $title; ?></h2><hr></td></tr>

    <form action="reg.php" method="POST">
	<table>
	<tr>
	<th><label for="first_name">Имя</label></th>
	<td><input name="first_name" id="first_name" type="text">
	</td>
	</tr>
	
	<tr>
	<th><label for="last_name">Фамилия</label></th>
	<td><input name="last_name" id="last_name" type="text">
	</td>
	</tr>
	<tr>
	<th><label for="email">Еmail Адрес</label></th>
	<td><input name="email" id="email" type="text">
	</td>
	</tr>
	<tr>
	<th><label for="password">Пароль</label></th>
	<td><input name="password" id="password" type="text">
	</td>
	</tr>
	<tr>
	<th><label for="password_confirmation">Подтверждение Пароля</label></th>
	<td><input name="password_confirmation" id="password_confirmation" type="text" >
	</td>
	</tr>
	<tr>
	<th></th>
	<td><input type="submit" value="Добавить"></td>
	</tr>
	</table>
	</form>
	</td></tr>
</table>
</div>

</td></tr>
</div></table>