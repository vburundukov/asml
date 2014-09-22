<?php
//Записать имя пользователя в сессию
function log_in($user_id){
 $_SESSION['user_id'] = $user_id;
}
// Проверка имени с паролем 
function credentials_valid($email,$password){
	$email = pg_escape_string($email);
	$query = ' SELECT "id",
	"salt","password"
			FROM "users_asml"
			WHERE active and "email" = '."'".$email."'";
	
	$result = pg_query($query);
	if(pg_num_rows($result)){
	 $user = pg_fetch_assoc($result);
	 $password_requested = sha1($user['salt']. $password);
	 if($password_requested === $user['password']){
	  return $user['id'];
	 }
	}
	return false;
}
// Проверка пользователя
function current_user(){
 static $current_user;
 if(!$current_user){
  if($_SESSION['user_id']){
   $user_id = intval($_SESSION['user_id']);
   $query = ' SELECT *
			FROM "users_asml"
			WHERE active and "id" = '."'".$user_id."'";
	$result = pg_query($query);
	if(pg_num_rows($result)){
	
	 $current_user = @pg_fetch_assoc($result);
	 return $current_user;
	}
	
  } 
 }
 return $current_user;
}
//Проверка правильности пользователя
function require_login(){	
 if(!current_user()){
 $_SESSION['redirect_to'] = $_SERVER["REQUEST_URI"];
	header("Location: login.php?login_required=1");
 exit("Вам нужно <a href='login.php'>Войти</a>");

 }
}

?>