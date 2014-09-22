<?php
$title = "Главная";
include "_header.php"; 



// загрузить файл, если не было загружено ранее.
require_once "inc/database.php";
db_connect();

// загрузить авторизацию пользователей
require_once "inc/auth.php";

$current_user = current_user();



// загрузить функции.
require_once "inc/functions.php";

?>
<div class="wrapper" align=center>
	<table>
	<tr><td colspan=2 align=center><h2><?php echo $title; ?></h2><hr></td></tr>
	<tr><td>
	<p>Данный сервис специально разработан для АСУЗ.<p>
	<p>Для получения доступа к сервису обращаться по адресу vladimir.burundukov@vivadengi.ru.<p>
	<hr>
	<a href="service.php">Сервис</a>
	</td>
	</tr>
</table>
</td>
<td>
</div>
<?php include "_footer.php"; ?>	