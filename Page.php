<?
   //Обозначает что ответ сервера будет в формате json и кодировке UTF-8
   //header('Content-Type: application/json; charset=UTF-8');
   
require_once "inc/database.php";
// загрузить функции.
require_once "inc/functions.php";

//echo " page = ".$_GET['page']."<br>";
$ThisPage = $_GET['page'];
switch($ThisPage):
	case 'ip':

	$ip=$HTTP_SERVER_VARS["REMOTE_ADDR"];
	$matches=array();
	if ($_SERVER["OS"] =='Windows_NT')
	{
	   exec("arp -a", $rgResult);
	   $mac_template="/[\d|A-F]{2}\-[\d|A-F]{2}\-[\d|A-F]{2}\-[\d|A-F]{2}\-[\d|A-F]{2}\-[\d|A-F]{2}/i";

	   foreach($rgResult as $key=>$value)
	   {
		 
		 $num_match = preg_match_all($mac_template, $value ,$matches);
		 for ($i=0; $i < $num_match; $i++) echo "Совпадение $i: ".$matches[0][$i]."<br>";
		 
	   };
	}
	else
	{
	   exec("arp -a | grep $ip", $rgResult);
	   $mac_template="/[\d|A-F]{2}\:[\d|A-F]{2}\:[\d|A-F]{2}\:[\d|A-F]{2}\:[\d|A-F]{2}\:[\d|A-F]{2}/i";
	   
	   preg_match($mac_template, $rgResult[0], $matches);
	}
	
	$mac = $matches[0][1];// собственно, Ваш MAC-адрес.
	echo $mac;

	break;
	case 'Payment':
?>

	<div id="Res<? echo $ThisPage; ?>" class='Res'>

	<label>Оплаты</label><br>
	<input id='contract_number' list='contract23' class='work form-control' placeholder="Введите номер договора" type='text' page='"<? echo $ThisPage; ?>"' size='40' maxlength='40'>
	<datalist id="contract23">
	 <option value="Z731083667401"></option>
	</datalist>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
</div>
<?
	break;
	case 'Payout':
?>
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
	<label>Выплаты</label><br>
	<input id='contract_payment' class='work form-control' type='text' size='40' maxlength='40'>
	<label>Дата платежа</label>
	<input id='date1_payment' type='date'>
	<label>Дата оплаты</label>
	<input id='date2_payment' type='date'>
	<!--
		<label for="donation">Размер пожертвования (USD):</label>
	<input type="range" name="donation" id="donation" list="donation_list"
	  step="5" min="5" max="200">
	<datalist id="donation_list">
	  <option>25</option>
	  <option>50</option>
	  <option>100</option>
	  <option>200</option>
	</datalist>-->
	
	</div>
<?
	break;
	case 'Client':
?>
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Фамилия Имя Отчество</label><br>
		<input id='fio' name='fio' class='work form-control' type='text' size='40' maxlength='40'>
	</div>
<?
	break;
	case 'LoanToActive':
?>
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
	
		<label>Активировать</label><br>
		<input id='contract1' page='"<? echo $ThisPage; ?>"' class='work form-control' type='text' size='40' maxlength='40' >
	</div>
<?
	break;
	case 'CustomerChange':
?>	
	<div id='ResCustomerChange' class='Res'>
	<table>
	<tr><td colspan=2 align=center>Изменяем</td></tr>
	<tr><td>
	<label>borrower_key/номер клиента</label></td><td>
	<input id='borrower_key_change' class='form-control' page="<? echo $ThisPage."Change"; ?>"' type="text"></td></tr>
	<tr><td>
	<label>last_name/Фамилия</label></td><td>
	<input id='last_name' class='form-control' type='text'></td></tr>
	<tr><td>
	<label>name/Имя</label></td><td>
	<input id='second_name' class='form-control' type='text'></td></tr>
	<tr><td>
	<label>patronymic/Отчество</label></td><td>
	<input id='patronimic' class='form-control' type='text'></td></tr>
	<tr><td>
	<label>birthday/Дата рождения </label></td><td>
	<input id='birthday' class='form-control' type='date'></td></tr>
	<tr><td>
	<label>pass_serial/Серия паспорта </label></td><td>
	<input id='pass_serial' class='form-control' type='text'></td></tr>
	<tr><td>
	<label>pass_number/Номер паспорта </label></td><td>
	<input id='pass_number' class='form-control' type='text'></td></tr>
	<tr><td>
	<label>pass_issue/Дата выдачи </label></td><td>
	<input id='pass_date' class='form-control' type='date'></td></tr>
	<tr><td>
	<label>who_issue/Кем выдан</label></td><td>
	<input id='who_issue' class='form-control' type='text' size=100></td></tr>
	<tr><td>
	<label>pass_code/Код подразделения</label></td><td>
	<input id='pass_code' class='form-control' type='text'></td></tr>
	<tr><td>
	<label>birthplace/Место рождения</label></td><td>
	<input id='birthplace' class='form-control' type='text' size=100></td></tr>
	</table>
</div>
<script>

	$("input#borrower_key_change").on('keyup',function(e){
		if(e.keyCode==13){
		//alert(typeof $(this).val());
		$.ajax({
		   type: "POST",
		   url: "work.php",
		   data: "ResVal=Customer"+"&text1="+$(this).val(),
		   success: function(html){
					$("#Reswork").removeData().empty();
					$("#Reswork").append(html);
					stopLoadingAnimation();
					if (seek == '1'){
					$("input#last_name").val(last_name);
					$("input#second_name").val(second_name);
					$("input#patronimic").val(patronimic);
					$("input#birthday").val(birthday);
					$("input#pass_serial").val(pass_series);
					$("input#pass_number").val(pass_number);
					$("input#pass_date").val(pass_date);
					$("input#who_issue").val(pass_issue);
					$("input#pass_code").val(pass_code);
					$("input#birthplace").val(birthplace);
					} else {
						$("input#last_name").val(null);
						$("input#second_name").val(null);
						$("input#patronimic").val(null);
						$("input#birthday").val(null);
						$("input#pass_serial").val(null);
						$("input#pass_number").val(null);
						$("input#pass_date").val(null);
						$("input#who_issue").val(null);
						$("input#pass_code").val(null);
						$("input#birthplace").val(null);
					}
			}
		});
		}
	});

</script>
<?

	break;
	case 'RemittanceCancel':
?>

<div id="Res<? echo $ThisPage; ?>" class='Res'>
	<label>Отменить перевод через CONTACT</label><br>
	<input id='contract2' page='"<? echo $ThisPage; ?>"' class='work form-control' type='text' size='40' maxlength='40' >
</div>
<?
	break;
	case 'ProcessingHistory':
?>
<div id="Res<? echo $ThisPage; ?>" class='Res'>
	<label>История обработки ...</label><br>
	<input id='history_loan_key' page='"<? echo $ThisPage; ?>"' class='work form-control' type='text' size='40' maxlength='40'>
</div>
<?
	break;
	case 'ListOfExceptions':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Удалить из списка лояльных клиентов</label><br>
		<label>borrower_key</label>
		<input id='borrower_key' page='"<? echo $ThisPage; ?>"' type='text' size='9' maxlength='9'>
		<select id='status_key'>
		<option value='1'>Отказ</option>
		<option value='2' selected>Черный список</option>
		<option value='3'>Умер</option>
		<option value='4'>Приходили</option>
		<option value='5'>Номер не активен</option>
		</select><br>
		<label>Ссылка задачи redmine</label><br>
		<input id='redmine' type='text' class='work form-control' size='60' maxlength='60'>
	</div>	
<?
	break;	
	case 'Discount':
?>
<div id="Res<? echo $ThisPage; ?>" class='Res'>
	<label>Добавить скидку клиенту</label><br>
	<label>borrower_key</label>
	<input id='borrower_key_discount' type='text' size='9' maxlength='9'>
	<select id='type_discount'>
	<option value='400' selected>Приведи друга</option>
	<option value='2000'>День рождения</option>
	<option value='1000'>Бонус 1000</option>
	</select><br>
	<label>Ссылка задачи redmine</label><br>
	<input name='_redmine' id='redmine_discount'  page='"<? echo $ThisPage; ?>"' class='work form-control' type='text' size='60' maxlength='60'>
</div>
<?
	break;
	case 'ReplacementCard':
?>
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label> Старый номер карты</label><br>
		<input id='ean_old' class='work form-control' type='text' size='13' maxlength='13'><br>
		<label> Новый номер карты</label><br>
		<input id='ean_new' class='work form-control' page='"<? echo $ThisPage; ?>"' type='text' size='13' maxlength='13'><br>
	</div>
<?
	break;
	case 'BlackList':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Добавить в черный список</label><br>
		<label>borrower_key</label><br>
		<input id='borrower_key45' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='9' maxlength='9'>
	</div>
<?
	break;		
	case 'UserService':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
	<? 
			db_connect();
			$unit="tatya";
			//$myparam = $_GET['text1'];
			$names = explode(" ", $unit);

			if ($names[0]=='' or $names[0]==' '){
				echo "Введите ФИО";
				break;
			}
			$params = array("%".$names[0]."%");
			echo $params[0];
			$result = pg_query_params("select first_name,last_name,active from public.users_asml where last_name not like($1);", $params ) or die(pg_last_error());
			if(pg_num_rows($result)){
				$user = pg_fetch_all($result);
				// выводим имена полей в заголовок таблицы(массив колонок во вложенном массиве)
				$namecolumns= array_keys($user[0]);
				//print_r($user);
				 echo "<table border=1 id='datatable'>
						<tr><th> $namecolumns[0] </th><th> $namecolumns[1] </th><th> $namecolumns[2] </th></tr>";
				 foreach ($user as $k => $val)
				  {

				  // Начало строки
					echo "<tr>";
					// count($val); кол-во елементов в массиве $val
					foreach ($val as $key => $val2)
					{
						//$temp1 = array_keys($val);
						//echo $temp1[2];
						if ($key=="active"){
							if ($val2 == "t")
								echo '<td><input class=works1 type="checkbox" checked="checked"/><button type="button" class="close" aria-hidden="true">&times;</button></td>';
							else
								echo '<td><input class=works1 type="checkbox"/><button type="button" class="close" aria-hidden="true">&times;</button></td>';	
						}else 
							echo "<td>".$val2."</td>";
					}
					// конце строки
					echo "</tr>";
				  }
				echo "</td></tr></table>";
			} else
				echo "не найден";
	?>
	</div>
<?
	break;		
	case 'Process':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Поиск процесса</label><br>
		<label>process_key</label><br>
		<input id='process_key' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='9' maxlength='9'>
	</div>
<?
	break;	
	case 'ProcessTo200':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Вернуть процесс на шаг выбора суммы(другого тарифа)</br>Введите process_key</label><br>
		<label>process_key</label><br>
		<input id='process_key1' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='9' maxlength='9'>
	</div>
<?
	break;
	case 'ProcessTo100':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Возвращаем процесс на проверку паспорта</label><br>
		<label>process_key</label><br>
		<input id='process_key2' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='9' maxlength='9'>
	</div>
<?
	break;
	case 'ProcessTo103':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Возвращаем процесс на шаг проверки наличия исполнительных делопроизводств</label><br>
		<label>process_key</label><br>
		<input id='process_key3' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='9' maxlength='9'>
	</div>
<?
	break;
	case 'ProcessTo14':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Возвращаем процесс на шаг сканирования, отказ по географии(добавить нп)</label><br>
		<label>process_key</label><br>
		<input id='process_key4' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='9' maxlength='9'>
	</div>
<?
	break;
	case 'ProcessTo99':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Возвращаем процесс на принудительный отказ</label><br>
		<label>process_key</label><br>
		<input id='process_key5' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='9' maxlength='9'>
	</div>
<?
	break;	
	case 'ProcessToCancel':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Полная отмена процесса</label><br>
		<label>process_key</label><br>
		<input id='process_key6' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='9' maxlength='9'>
		<input id='process_key7' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' >
	</div>
<?
	break;
	case 'ProcessToDelete':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Удаление процесса</label><br>
		<label>process_key</label><br>
		<input id='process_keyDelete' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='9' maxlength='9'>
	</div>
<?
	break;	
	case 'UserOffice':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Введите логин специалиста или email</label><br>
		<input id='fio_1' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='9' maxlength='9'>
	</div>
<?
	break;		
	case 'UserOffice1':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>История обработки ...</label><br>
		<label>borrower_key</label><br>
		<input id='Loan_key1' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='9' maxlength='9'>
	</div>
<?
	break;	
	
	case 'DocReestr':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<table>
		<tr><td colspan=1 align=center>Выгружаем</td></tr>
		<tr><td>
		<label>Дата оплаты с </label><input id='date_of_transfer_begin' type='date' class='form-control'/>
		<label>по </label><input id='date_of_transfer_end' type='date' class='form-control'/></td></tr>
		<tr><td>
		<label>Bank_key/Способ оплаты</label><br>
		<select id='bank_key1' class='form-control' page='"<? echo $ThisPage; ?>"'>
		<option value='1'>КОНТАКТ</option>
		<option value='2'>Поступления из банков(сводный)</option>
		<option value='3'>ЦФТ(город)</option>
		<option value='4' selected>Киберплат</option>
		<option value='5'>SkySend</option>
		</select>
		</td></tr>
		</table>
	</div>
<?
	break;	
		case 'Geography':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Укажите область/район/город/населенный пункт</label><br>
		<input id='locality' class='work form-control' type='text' size='30' maxlength='30' page='"<? echo $ThisPage; ?>"'>
	</div>
<?
	break;	
		case 'AddStreet':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Введите улицу</label><br>
		<input id='street_name' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' placeholder="Введите название улицы" size='30' maxlength='55'>
		<input id='sorc1' class='form-control' type='text' size='5' maxlength='5' placeholder="Сокращенное наз. Улица - 'ул'">
		<input id='owner_key1' class='form-control' type='text' size='20' maxlength='20' placeholder="Введите owner_key НП"><br>
	</div>
<?
	break;	
		case 'AddLocality':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Введите населенный пункт</label><br>
		<input id='locality_name' class='work form-control' type='text' placeholder="Введите название населенного пункта" page='"<? echo $ThisPage; ?>"' size='40' maxlength='40'>
		<input id='sorc2' class='form-control' type='text' size='5' maxlength='5' value='п'>
		<input id='owner_key2' class='form-control' type='text' size='20' maxlength='20' placeholder="Введите owner_key"><br>
	</div>
<?
	break;	
	case 'Doc_nko':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Поиск ПКО/Введите номер ПКО</label><br>
		<input id='nko' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='15' maxlength='15'>
	</div>
<?
	break;	
	case 'change_nko':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<table>
		<tr><td colspan=2>Введите</td></tr>
		<tr><td>
		<label>order_key</label></td><td>
		<input id='order_key' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='7' maxlength='7'></td></tr>
		<tr><td>
		<label>order_number</label></td><td>
		<input id='order_number' class='form-control' type='text' size='15' maxlength='15'></td></tr>
		<tr><td>
		<label>payment_sum</label></td><td>
		<input id='payment_sum' class='form-control' type='text' size='10' maxlength='10'></td></tr>
		<tr><td>
		<label>payment_date</label></td><td>
		<div class="well">
		<input id='payment_date' class='form-control' type='date' class="spen2">
		</div>
		</td>
		</tr>
		<tr><td>
		<label>new_loan_key</label></td><td>
		<input id='new_loan_key' class='form-control' type='text' size='7' maxlength='7'></td></tr>
		</table>
	</div>
<?
	break;	
	case 'Doc_rko':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Поиск выплаты/Введите номер РКО</label><br>
		<input id='rko' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' type='text' size='15' maxlength='15'>
	</div>
<?
	break;	
	case 'change_rko':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<table>
		<tr><td colspan=2>Введите</td></tr>
		<tr><td>
		<label>order_key</label></td><td>
		<input id='order_key2' type='text' size='7' maxlength='7'></td></tr>
		<tr><td>
		<label>Новое ФИО</label></td><td>
		<input id='fio_number' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"'></td></tr>
		</table>
	</div>
<?
	break;
	case 'ListOfCallCustomer':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<div class="tree well">
		<ul class="mypadding">
		  <li><span><i class="icon-folder-open"></i>Список по обзвону повторных клиентов</span>
			<ul class="mypadding">
				<li><span><i class="icon-minus-sign"></i>РЦ </span>
				  <ul class="mypadding">
					<li><span><i class="icon-minus-sign"></i>ОО </span><br>
						<input id="text" type="text" class="hitext">
					</li>

				  </ul>
				</li>
			</ul>
		  </li>
		</ul>
		</div>
	</div>
<?
	break;
	case 'change_RegRec':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<table>
		<tr><td colspan=2>Введите</td></tr>
		<tr><td>
		<label>order_key</label></td><td>
		<input id='order_key_Rec' type='text' size='7' maxlength='7'></td></tr>
		<tr><td>
		<label>Укажите новый номер договора Z...</label></td><td>
		<input id='new_contract_Rec' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"'></td></tr>
		</table>
	</div>
<?
	break;	
	case 'LoanAllClose':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>

		<label>Введите номер договора для закрытия займа с 0 кол-вом платежей</label><br>
		<input id='contract_allClose' class='work form-control' type='text' size='40' maxlength='40'><br>
		<label>Ссылка задачи redmine</label><br>
		<input id='redmine2_allClose'class='work form-control' type='text' size='60' maxlength='60'>
	</div>
<?
	break;
	case 'LoanСorrectionClose':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>

		<label>Введите номер договора</label><br>
		<input id='contract_Сorrection' class='work form-control' type='text' size='40' maxlength='40'><br>
		<label>Сумма комиссии </label><br>
		<input id='Overpayment_Сorrection' class='work form-control' type='text' size='10' maxlength='10'><br>		
		<label>Сумма пени</label><br>
		<input id='Fine_Сorrection' class='work form-control' type='text' size='10' maxlength='10'><br>
		<label>Кол-во платежей</label><br>
		<input id='Num_rows_Сorrection' class='work form-control' type='text' size='10' maxlength='10'><br>
		<label>Ссылка задачи redmine</label><br>
		<input id='redmine_Сorrection'class='work form-control' type='text' size='60' maxlength='60'>
	</div>
<?
	break;	
	
	case 'AddLocality4':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>

		<label>Введите номер договора для списания пени</label><br>
		<input name='_key' id='contract3' class='work form-control' type='text' size='40' maxlength='40'><br>
		<label>Ссылка задачи redmine</label><br>
		<input name='_key' id='redmine2' type='text' size='60' maxlength='60'>
		<input val1=33 val2=0 type='submit' class='bwork' value='Списать'/>

		<label>История обработки ...</label><br>
		<label>borrower_key</label><br>
		<input id='Loan_key1' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' size='9' maxlength='9'>
	</div>
<?
	break;
	case 'Doc_rko2':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Поиск выплаты/Введите номер РКО</label><br>
		<input id='rko' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' type='text' size='15' maxlength='15'>
	</div>
<?
	break;	
	case 'Doc_rko3':
?>	
	<div id="Res<? echo $ThisPage; ?>" class='Res'>
		<label>Поиск выплаты/Введите номер РКО</label><br>
		<input id='rko' class='work form-control' type='text' page='"<? echo $ThisPage; ?>"' type='text' size='15' maxlength='15'>
	</div>
<?
	break;	
	default:
?>	
<div id='Res' class='Res'>
	<span>По умолчанию пусто</span>
</div>	
<?
endswitch;
?>
<script>
$("input.work").bind('keypress',function(e){
	if(e.keyCode==13){
		Getphp();
	}
});
</script>