<?php
// Запустить сессию
session_start();
$title = "Сервис АСУЗ";
define('__ROOT__', dirname(dirname(__FILE__))); 
// загрузить файл, если не было загружено ранее.
require_once(__ROOT__.'./test/inc/database.php');

db_connect();
// загрузить авторизацию пользователей
require_once "inc/auth.php";

$current_user = current_user();

require_login();
header('Content-Type: text/html; charset=UTF-8');
// Заголовок где содержится описание и параметры web-page
include "_header.php"; 
// загрузить функции.
require_once "inc/functions.php";
?>
<div id="content">
<!--Левая колонка таблицы содержит div с id="leftmenu" т.е форму для отображения навигации Сервиса или Меню -->
<table width=100%><tr>
<td width=15% class="my34">
<div id="leftmenu" align="center">
<?php include "MenuService.php"; ?>
</div>
</td>
<!--Центральная колонка таблицы содержит div с id="center" т.е форму для работы в сервисе АСУЗ -->
<td width=70% class="my34">
	<div id="center" class="my34" align=center>
		<table>
		<tr><td align=center><h2 class="headtext">Сервис</h2></td></tr>
		<tr><td>
			<table>
			 <tr><td valign=top>
			 
				<div id='ResWorks'>
					<? if(isset($_GET['page'])):
						include('Page.php');
						endif;?>
				</div>
				<div class="getData">
				<input type='submit' class='bwork btn btn-default' value='Выполнить' page="<? echo $_GET['page'];?>"/>
				</div>
			
			 </td>
			 </tr>
			 <tr>
			  <td>
			  <label>Результат</label><br> 
			  <img id="loadImg" src="img/load.gif" />
			  <div id='Reswork'></div>
			  </td>
			 </tr>
			</table>
		</td>
		</tr>
	</table>
	</div>
</td>
<!--Крайняя правая колонка таблицы содержит div с id="rightmenu" т.е форму выхода для авторизованных пользователей -->
<td width=15% class="my34">
	<div id="rightmenu">
		<?php include "logged.php"; ?>	
		<div id="StoryName">История:</div>
		<div id="StoryDiv"></div>
	</div>
</td>
</tr>
</table>
</div>

<?php
// Окончание где содержится описание о правах и подгрузка скриптов 
include "_footer.php";
?>