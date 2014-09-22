<?php
// запустить сессию
session_start();
// загрузить auth, если не было загружено ранее.
require_once "inc/auth.php";
// загрузить файл, если не было загружено ранее.
require_once "inc/database.php";
db_connect();


$user_id = credentials_valid($_POST['username'],$_POST['password']);

if($user_id){
 log_in($user_id);
 
 if($_SESSION['redirect_to']){
  header("Location: " . $_SESSION['redirect_to']);
  unset($_SESSION['redirect_to']);
  echo $_SESSION['redirect_to'];
 }else{
  header("Location: products.php");
 }
 
}else{
 header("Location: login.php?error=1");
 exit("You are being redirected");
}
?>