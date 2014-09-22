<?php
// Выгрузка данных по списку повторных клиентов
function Get_list_call() {
	$array_list=1;
	$array_list="Тут данные по переключению ".$array_list;

	Return $array_list;

}
// Записываем логи  для добавления, изменения и удаления данных
function log_text($tab,$command,$log){
$resul99 = pg_query_params("INSERT INTO public.log_serv (tab, command, date_time, log)VALUES($1, $2, $3, $4);", array($tab,$command,date('Y-m-d H:i'),$log)) or die(pg_last_error());
};


function log_console($command){
echo "<script> console.log('".$command."');</script>";
};


function Users() {


};


?>