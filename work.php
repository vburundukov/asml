<?php

// загрузить функции.
require_once "inc/functions.php";
	//echo "+".$_POST['text1']."+".$_POST['ResVal']."+";
	if(isset($_POST['ResVal']) && isset($_POST['text1'])){
		if ($_POST['text1'] == ""){
			exit;
		}
		
		require_once "inc/database.php";
		//$unit= mb_strtoupper($_POST['text1']);
		$unit= $_POST['text1'];
		$unit = mb_convert_encoding($unit, "UTF-8","auto");
		// защищаем от спец символов
		$_POST['text1'] = pg_escape_string($unit);
		//echo $_POST['text1'];
		db_connect();
		/*
		if ($_POST['ResVal']=="Payments")
			echo "Верно";
		else 
			echo "Не верно";
			*/
		$thevalue = $_POST['ResVal'];
	switch($thevalue) {
		case 'Users':
			echo $_POST['ResVal'];
		break;
		case '':
			$query = 'SELECT *
					FROM "public"."users"
					LIMIT 50';
			$result = pg_query($query);
			
			if(pg_num_rows($result)){
				$user = pg_fetch_assoc($result);
				echo $user['id'];
			}	
			$unit = str_replace(array('_', '-', '—'), ' ', $_POST['text1']);
			$unit=preg_replace('/\s+/', ' ', trim($unit));
			
			//проверяем наличие слов в тексте 
			if (empty($unit))
				$tunit = 'Пусто';
			else {
				// преобразуем в массив
				$names = explode(" ", $unit);
				$names = array_values($names);
			}
			echo $names[0];
			echo "\n вернул на стадию рассмотрения, проведите расчет по новой и выберите нужный продукт.";
		break;
		case 'Payment':
			$myparam = trim($_POST['text1']);
			//echo $_GET['text1'];
			$result = pg_query_params("select * from public.loan_c2key(UPPER($1));", array($myparam)) or die(pg_last_error());
			if(pg_num_rows($result)){
				$user = pg_fetch_assoc($result);
				// Заголовок таблицы
				echo "<table class='table table-condensed' border=1><tr>
				<th>loan_key</th><th>borrower_key</th><th>last_name</th><th>first_name</th><th>pantonimic</th><th>subdivition_name</th><th>subdivition_key</th>
				<th>Статус</th></tr><tr><th>".$user['loan_c2key']."</th><th>";
				$key_ = $user['loan_c2key'];
				
				$result = pg_query_params("select tb.borrower_key,tb.last_name,tb.name,tb.patronimic,ts.subdivision_name,ts.subdivision_key from public.t_borrowers tb inner join public.loans l on tb.borrower_key=l.borrower_key left join public.t_subdivisions ts on ts.subdivision_key=l.subdivision_key where l.loan_key = $1", array($key_)) or die(pg_last_error());
				$username = pg_fetch_assoc($result);
				echo "".$username['borrower_key']."</th><th>".$username['last_name']."</th>
				<th>".$username['name']."</th><th>".$username['patronimic']."</th>
				<th>".$username['subdivision_name']."</th><th>".$username['subdivision_key']."</th><th>";

				$result = pg_query_params("Select returned::boolean,issued::boolean
				from public.loans l
				WHERE l.loan_key in ($1);", array($key_)) or die(pg_last_error());
				
				$loan = pg_fetch_row($result);
				$color ='red';
				if ($loan[1]=="t"){
					if ($loan[0]!=="t"){
						$rescolor = ' Не закрыт';
					}else{
						$color = 'green';
						$rescolor = ' Закрыт';
					}
					echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				} else {echo "<label style='color:".$color."'> Не выдан!  </label><p>";
				return false;}
				echo "</th></tr></table>";
				
				$result_new = pg_query_params("
				SELECT SUM(amount)
				FROM public.borrower_discount
				WHERE borrower_key in ($1);", array($username['borrower_key'])) or die(pg_last_error());
				$borrower_discount = pg_fetch_row($result_new);
				echo "<label style='color:darkblue'> Скидка :".$borrower_discount[0]." </label><br>";
				
				
				$result_new_sum = pg_query_params(" Select sum(payment_sum),count(*)
				from asuz.received_payment_by_loan rp
				WHERE rp.loan_key in ($1);", array($key_)) or die(pg_last_error());
				$payment_sum = pg_fetch_row($result_new_sum);
				echo "<label style='color:darkblue'> Сумма :".$payment_sum[0]." Кол-во Платежей: ".$payment_sum[1]."</label><br>";
				
				$result_new_overpayment = pg_query_params(" Select *
				from loan_debt_overpayment($1);", array($key_)) or die(pg_last_error());
				$payment_overpayment = pg_fetch_row($result_new_overpayment);
				echo "<label style='color:darkblue'> Можно списать проценты :".$payment_overpayment[0]."</label><br>";
				$result_new_fine = pg_query_params(" Select *
				from loan_debt_fine($1);", array($key_)) or die(pg_last_error());
				$payment_fine = pg_fetch_row($result_new_fine);
				echo "<label style='color:darkblue'> Можно списать пени :".$payment_fine[0]."</label><br>";
				
				$result = pg_query_params(" Select *
				from asuz.received_payment_by_loan rp
				WHERE rp.loan_key in ($1) order by payment_date;", array($key_)) or die(pg_last_error());
				
				
				
				//$user = pg_fetch_assoc($result,10);
				//$user = pg_fetch_array($result, NULL, PGSQL_ASSOC);
				

				if(pg_num_rows($result)){
					$user = pg_fetch_all($result);
					// выводим имена полей в заголовок таблицы(массив колонок во вложенном массиве)
					$namecolumns= array_keys($user[0]);
					//print_r($user);
					 echo "<table class='table table-condensed'>
							<tr><th> $namecolumns[0] </th><th> $namecolumns[1] </th><th> $namecolumns[2] </th><th> $namecolumns[3] </th><th> $namecolumns[4] </th><th> $namecolumns[5] </th></tr>";
					 foreach ($user as $k => $val)
					  {

					  // Начало строки
						echo "<tr>";
						// count($val); кол-во елементов в массиве $val
						foreach ($val as $key)
						{
							// начало столбца
							echo "<td>".$key."</td>";
						}
						// конце строки
						echo "</tr>";
						  
					  }
					echo "</td></tr></table>";
				} else
				echo "Нет платежей!";
			} else
				echo "Договор не найден";
		break;
		// Перепроводим платеж
		case 'Payout':
			// Номер процесса
			$key_ = trim($_POST['text1']);
			$date_1 = trim($_POST['text2']);
			$date_2 = trim($_POST['text3']);
			echo "-".$key_.$date_1 .$date_2."-<br>";
			// Возвращаем процесс на принудительный отказ
			$result = pg_query_params("SELECT payments.re_execution(loan_contract2key(UPPER($1)),$2,$3);", array($key_,$date_1,$date_2)) or die(pg_last_error());
			// заносим результат в массив
			$user = pg_fetch_assoc($result);
			//проверяем результат 
			if ($user['re_execution'] == '1'){
				$color = 'green';
				$rescolor = 'Платеж перепроведен!';	
				log_text('payments.re_execution','SET',"UPPER($key_) $date_1 $date_2");
				
			}else{
				$color ='red';
				$rescolor = $user['re_execution'];
			}
			//и выводим его
			echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
		break;
		// Активировать договор
		case 'LoanToActive':
			// номер договора
			$myparam = trim($_POST['text1']);
			// ищем ключ договора
			$result31 = pg_query_params("select * from public.loan_c2key(UPPER($1));", array($myparam)) or die(pg_last_error());
			$user = pg_fetch_assoc($result31);
			// если нашли, то 

			//echo $_POST['text1']."'".$user['loan_c2key']."'";
			if($user['loan_c2key']!=''){
			
				// выводим результат выполнения
				$key_ = $user['loan_c2key'];
				// ищем ФИО по ключу
				//$result = pg_query_params("select tb.borrower_key,tb.last_name,tb.name,tb.patronimic from public.t_borrowers tb inner join public.loans l on tb.borrower_key=l.borrower_key where l.loan_key = $1", array($key_)) or die(pg_last_error());
				//$username = pg_fetch_assoc($result);
				//echo "-".$username['borrower_key']."-".$username['last_name']."-".$username['name']."-".$username['patronimic']."-";
				
				// Активируем заем
				$result = pg_query_params(" Select *
				from public.loan2notreturned($1);", array($key_)) or die(pg_last_error());
				// выводим результат
				$loan = pg_fetch_row($result);
				//echo  $loan[0] ;
				if ($loan[0]==1){
					$color = 'green';
					$rescolor = 'Займ активировался!';
					log_text('payments.re_execution','SET',"UPPER($key_) $date_1 $date_2");
				}else{
					$color ='red';
					$rescolor = 'Ошибка, займ не активировался';
				}
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";

			} else
				echo "Договор не найден";
		break;
		case 'Client':
		
			$unit=preg_replace('/\s+/', ' ', trim($_POST['text1']));
			//$myparam = $_POST['text1'];
			$names = explode(" ", $unit);

			if ($names[0]=='' or $names[0]==' '){
				echo "Введите ФИО";
				break;
			}
			$params = array("%".$names[0]."%","%".$names[1]."%","%".$names[2]."%");
			echo $names[0]."#".$names[1]."#".$names[2]."<br>";
			$result = pg_query_params("select borrower_key from public.t_borrowers where upper(last_name) like upper($1) and upper(name) like upper($2) and upper(patronimic) like upper($3);", $params ) or die(pg_last_error());
			if(pg_num_rows($result)){
				$user = pg_fetch_all($result);
				echo '<table class="table table-condensed">
				<tr><th> loan_key </th><th> borrower_key </th><th> contract_number </th><th> process_key </th></tr>';
				 foreach ($user as $k => $val)
				  {
					//echo '';
					foreach ($val as $k1 => $valval)
					{
						// Выводим имя поля
						$result2 = pg_query_params("select  COALESCE(l.contract,'') as contract,l.loan_key, COALESCE(a.process_key,0) as process_key from public.loans l left join loan_issue.application a on a.borrower_key = l.borrower_key where l.borrower_key = $1", array($valval)) or die(pg_last_error());
						$username2 = pg_fetch_all($result2);
						// Выводим все значения выборки
						//print_r($username2);
						if ($username2[0]<>'')
						foreach ($username2 as $key2 => $val23)
								echo "<tr><td>".$val23['loan_key']."</td><td>".$valval."</td><td>".$val23['contract']."</td><td>".$val23['process_key']."</td></tr>";
					}
					//echo "";
				  }
				  echo "</td></tr></table>";
			} else
				echo "не найден";
				
		break;
		// История по процессу обработки клиента звонков
		case 'ProcessingHistory':
			$myparam = trim($_POST['text1']);
			//echo $myparam;
			$result = pg_query_params("select * from public.loan_c2key(UPPER($1));", array($myparam)) or die(pg_last_error());
			if(pg_num_rows($result)){
			
				$user = pg_fetch_assoc($result);
				echo $user['loan_c2key']." ";
				$key_ = $user['loan_c2key'];
				
				$result = pg_query_params("select tb.borrower_key,tb.last_name,tb.name,tb.patronimic from public.t_borrowers tb inner join public.loans l on tb.borrower_key=l.borrower_key where l.loan_key = $1", array($key_)) or die(pg_last_error());
				$username = pg_fetch_assoc($result);
				echo $username['borrower_key']." ".$username['last_name']." ".$username['name']." ".$username['patronimic']."<br>";
				$result = pg_query_params("SELECT * from loan_event_actions_history_($1) Order by action_date", array($key_) ) or die(pg_last_error());
				if(pg_num_rows($result)){
					$user = pg_fetch_all($result);
					// выводим имена полей в заголовок таблицы(массив колонок во вложенном массиве)
					$namecolumns= array_keys($user[0]);
					//print_r($user);
					 echo "<table class='table table-condensed'>
							<tr><th> $namecolumns[0] </th><th> $namecolumns[1] </th><th> $namecolumns[2] </th><th> $namecolumns[3] </th><th> $namecolumns[4] </th></tr>";
					 foreach ($user as $k => $val)
					  {

					  // Начало строки
						echo "<tr>";
						// count($val); кол-во елементов в массиве $val
						foreach ($val as $key)
						{
							// начало столбца
							echo "<td>".$key."</td>";
						}
						// конце строки
						echo "</tr>";
						  
					  }
					echo "</td></tr></table>";
				} else
					echo "процесс не найден";
			} else
				echo "Договор не найден";
		break;
		case 'ListOfExceptions':
			$borower_key =  trim($_POST['text1']);
			$status_key = trim($_POST['text2']);
			$description = trim($_POST['text3']);

			//echo $borower_key."-".$status_key."-".$description."<br>";
				
			$query = "SELECT * FROM  borrowers.add_borrower_marketing_status (
			".$borower_key.",
			'".-1*$status_key."','".$description."');";
			//echo $query."<br>";
			$result2 = pg_query($query) or die(pg_last_error());
			$m_array = pg_fetch_row($result2);
			
			if( $m_array[0]<>0){
				$color = 'green';
				$rescolor = 'Клиент добавлен в список исключения!'.' Номер № '.$m_array[0];
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				log_text('add_borrower_marketing_status','INSERT',"$borower_key");
			} else 
				echo 'Клиент не добавлен';
		break;
		case 'Discount':
			$borower_key =  trim($_POST['text1']);
			$summ_key = trim($_POST['text2']);
			$description = trim($_POST['text3']);
			

			echo $borower_key."-".$summ_key."-".$description."<br>";
				
			$query = "SELECT * FROM public.borrowers_discount_add (
			".$borower_key.",
			".$summ_key.");";
			
			/*$query = 
			 "INSERT INTO borrower_discount (operation_type, object_type, borrower_key, amount, reason_key) 
				VALUES(1, null, '".$borower_key."', '".$summ_key."', 11);";
	
			*/
			echo $query."<br>";
			$result2 = pg_query($query) or die(pg_last_error());
			$m_array = pg_fetch_row($result2);
			
			if( $m_array[0]<>0){
				$color = 'green';
				$rescolor = 'Скидка клиенту  добавлена!'.' Номер № '.$m_array[0];
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				log_text("borrower_discount $summ_key key $borower_key",'INSERT',"$description");
			} else 
				echo 'Скидка не прошла';

		break;
		case 'ReplacementCard':
			$ean1=trim($_POST['text1']);
			$ean2=trim($_POST['text2']);
			//echo $ean1."-".$ean2."_";
			$params = array($ean1,$ean2);
			$result = pg_query_params("SELECT * FROM card_replacement($1, $2);", $params) or die(pg_last_error());
			if(pg_num_rows($result)){
				$user = pg_fetch_assoc($result);

				if ($user['result']==-1){
					$color ='red';
				}else{
					$color = 'green';
					log_text('card_replacement','SET',"$ean1 $ean2");
				}
				$rescolor = $user['result_str'];
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				
			} else
				echo "не найден";
			
		break;
		case 'UserOffice':
		
			$unit=preg_replace('/\s+/', ' ', trim($_POST['text1']));
			//$myparam = $_POST['text1'];
			$names = explode(" ", $unit);

			if ($names[0]=='' or $names[0]==' '){
				echo "Введите ФИО";
				break;
			}
			$params = array("%".$names[0]."%");
			echo $params[0];
			$result = pg_query_params("select tup.phone,tup.email from public.t_users_phones tup where upper(email) like upper($1);", $params ) or die(pg_last_error());
			if(pg_num_rows($result)){
				$user = pg_fetch_all($result);
				// выводим имена полей в заголовок таблицы(массив колонок во вложенном массиве)
				$namecolumns= array_keys($user[0]);
				 echo "<table class='table table-condensed'>
						<tr><th> $namecolumns[0] </th><th> $namecolumns[1] </th></tr>";
				 foreach ($user as $k => $val)
				  {

				  // Начало строки
					echo "<tr>";
					// count($val); кол-во елементов в массиве $val
					foreach ($val as $key)
					{
						// начало столбца
						echo "<td>".$key."</td>";
					}
					// конце строки
					echo "</tr>";
					  
				  }
				echo "</td></tr></table>";
			} else
				echo "не найден";
				
		break;		
		case 'Geography':
			//Название НП
			$name=trim($_POST['text1']);
			$params = array($name);
			log_text(' kladr_objects_by_name',"select","SELECT * FROM kladr.kladr_objects_by_name($name)");
			$result = pg_query_params("SELECT * FROM kladr.kladr_objects_by_name($1)", $params) or die(pg_last_error());
			if(pg_num_rows($result)){
				$user = pg_fetch_all($result);
				// выводим имена полей в заголовок таблицы(массив колонок во вложенном массиве)
				$namecolumns= array_keys($user[0]);
				//print_r($user);
				 echo "<table class='table table-condensed'>
						<tr><th> $namecolumns[0] </th><th> $namecolumns[1] </th><th> $namecolumns[2] </th><th> $namecolumns[3] </th></tr>";
				 foreach ($user as $k => $val)
				  {

				  // Начало строки
					echo "<tr>";
					// count($val); кол-во елементов в массиве $val
					foreach ($val as $key)
					{
						// начало столбца
						echo "<td>".$key."</td>";
					}
					// конце строки
					echo "</tr>";
					  
				  }
				echo "</td></tr></table>";
				/*
				 foreach ($user as $k => $val)
				  {
					foreach ($val as $k1 => $valval)
					{
						//Выводим все НП
						echo "$valval"."#";
					}
				
					echo "<br>";
				  }*/
			} else 
			echo "Не найден";
			
		break;
		case 'AddStreet':
			// Название улицы
			$street=trim($_POST['text1']);
			// тип улицы
			$kod=trim($_POST['text2']);
			// Ключ НП
			$num=trim($_POST['text3']);
			// Выводим предварительные данные
			//echo $street."-".$kod."-".$num."<br>";
			$params = array($street,$kod,$num);
			$result1 = pg_query_params("SELECT code FROM kladr.street_asuz Where name like $1 and socr = $2 and owner_key = $3 ", $params) or die(pg_last_error());
			$user = pg_fetch_all($result1);
			if(pg_num_rows($result1)){
				$color = 'red';
				$rescolor = 'улица уже есть';
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				break;
			}

			// Добавление новой улицы
			$result = pg_query_params("SELECT * FROM kladr.add_street($1, $2, $3)", $params) or die(pg_last_error());
			$user = pg_fetch_all($result);
			if(pg_num_rows($result)){
				log_text('kladr.add_street',"SET","$street, $kod, $num");
				$color = 'green';
				$rescolor = 'улица добавлена';
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				 foreach ($user as $k => $val)
				  {
					 foreach ($val as $k1 => $valval)
					{
						// Выводим все значения выборки
						echo "$valval\n";
					}
					echo "<br>";
				  }
			} else 
			echo "не найден";	
		break;
		case 'AddLocality':
			// Название населенного пункта
			$street=trim($_POST['text1']);
			// Тип улицы (поселок,город)
			$kod=trim($_POST['text2']);
			// Код (owner_key) куда привязывается населенный пункт
			$num=trim($_POST['text3']);
			
			$params = array($street,$kod,$num);
			// Проверяем если есть такие населенные пункты
			$result1 = pg_query_params("Select key from public.kladr_addition_test where name like $1 and socr like $2 and owner_key=$3", $params) or die(pg_last_error());
			$user = pg_fetch_all($result1);
			if(pg_num_rows($result1)){
				$color = 'red';
				$rescolor = 'Населенный пункт уже есть во временной таблице';
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				exit;
			}

			// Новый ключ для нового НП
			$result2 = pg_query("Select max(key) from public.kladr_addition_test") or die(pg_last_error());
			$user = pg_fetch_row($result2);
			if(pg_num_rows($result2)){
				$new_key = $user[0]+1;
				//echo $new_key."<p>";
			} else 
				$new_key = 1;
			// если ключа к кому отнести НП, то вводим null
			if ($num==''){
				echo "Нет owner_key вводим null<br>";
				$num=null;
			}
			
			// Добавляем новую запись во временную таблицу
			$params = array($street,$kod,$new_key,$num);
			$result3 = pg_query_params("INSERT INTO public.kladr_addition_test (name, socr, key, owner_key) 
				VALUES($1, $2, $3, $4);", $params) or die(pg_last_error());
			$m_array = pg_fetch_row($result3);
			if(pg_affected_rows($result3)){
				$color = 'green';
				$rescolor = 'Населенный пункт добавлен во временную таблицу';
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				log_text('public.kladr_addition_test',"Insert","$street $kod $new_key $num");
			}
				//Проверяем еще раз по другим параметрам
				$params = array($street,$kod,$num);
				$result5 = pg_query_params("Select key from kladr.kladr where name like $1 and socr like $2 and owner_key = $3;", $params) or die(pg_last_error());
				$user = pg_fetch_all($result5);			
				if(pg_num_rows($result5)){
					$color = 'red';
					$rescolor = 'Населенный пункт уже есть в основной таблице';
					echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
					break;
				} else {
					// Добавляем новый НП
					$params1 = array($new_key);
					$result6 = pg_query_params("
						 INSERT INTO  kladr.kladr
						Select * from public.kladr_addition_test ka where  ka.key = $1", $params1) or die(pg_last_error());
					$m_array = pg_fetch_row($result6);
					if(pg_affected_rows($result6)){
						$color = 'green';
						$rescolor = 'Населенный пункт добавлен!';
						echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
						log_text('kladr.kladr','Insert',"$new_key");
					}

				}
		
		break;	
		//06.03.03.000870
		case 'Doc_nko':
			$name=trim($_POST['text1']);
			echo $name."<br>";
			$params = array($name);

			$result12 = pg_query_params("select order_key, loan_key, payment_sum, payment_date, borrower_name from public.credit_cash_orders where 
				order_number like ($1);", $params) or die(pg_last_error());
			$user = pg_fetch_all($result12);

			if(pg_num_rows($result12)){
				echo '<table class="table table-condensed">
				<tr><th> order_key </th><th> loan_key </th><th> payment_sum </th><th> payment_date </th><th> borrower_name </th></tr>';
				 foreach ($user as $k => $val)
				  {
					echo '<tr>';
					foreach ($val as $k1 => $valval)
					{
						// Выводим имя поля
						//echo $k1."<br>";
						// Выводим все значения выборки
						echo "<td>$valval</td>";
					}
					echo "</tr>";
				  }
				  echo "</td></tr></table>";
			} else 
			echo "Не найден";
			
		break;
		case 'change_nko':
			// Ключ ПКО
			if ($_POST['text1']==''){
				$order_key = null;
			}else{
				$order_key = trim($_POST['text1']);
			}		
			// Номер ПКО
			if ($_POST['text2']==''){
				$order_number = null;
			}else{
				$order_number=trim($_POST['text2']);
			}
			// Сумма платежа
			if ($_POST['text3']==''){
				$payment_sum = null;
			}else{
				$payment_sum=trim($_POST['text3']);
			}
			// Дата платежа
			if ($_POST['text4']==''){
				$payment_date = null;
			}else{
				$payment_date=trim($_POST['text4']);
			}
			//  ключ нового договора
			if ($_POST['text5']==''){
				$new_loan_key = null;
			}else{
				$new_loan_key=trim($_POST['text5']);
			}
			echo $order_key."-".$order_number."-".$payment_sum."-".$payment_date."-".$new_loan_key."<br>";
 
			
			
			//Проверяем еще раз по другим параметрам
			$params1 = array($order_key,$order_number,$payment_sum,$payment_date,$new_loan_key);
			$result5 = pg_query_params("Select order_key from public.credit_cash_orders where order_key = $1 and ((order_number = $2)
			or (payment_sum = $3) or (payment_date = $4)  or (loan_key = $5));", $params1) or die(pg_last_error());
			$user = pg_fetch_all($result5);			
			if(pg_num_rows($result5)){
					$color = 'red';
					$rescolor = 'ПКО уже изменен!';
					echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
					break;
			} else {
			
				// Изменяем ПКО новый НП
				$result13 = pg_query_params("select * from 
					public.credit_cash_order_change($1, $2, $3, $4, $5);", $params1) or die(pg_last_error());
				$m_array = pg_fetch_row($result13);
				
				if( $m_array[0]==1){
					$color = 'green';
					$rescolor = 'ПКО исправлен!';
					echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
					log_text('credit_cash_order_change','SET',"$params1[0], $params1[1], $params1[2], $params1[3], $params1[4]");
				}
			}
		break;
		
		// Изменить ФИО данные
		case 'CustomerChange':
			echo intval($_POST['text1']);
			$borrower_key_change = intval($_POST['text1']);
			if ($borrower_key_change==0){
				echo "Введите номер клиента";
				exit;
			}
			$last_name = $_POST['text2'];
			$second_name = $_POST['text3'];
			$patronimic = $_POST['text4'];
			$birthday = $_POST['text5'];
			$pass_serial = $_POST['text6'];
			$pass_number = $_POST['text7'];
			$pass_date = $_POST['text8'];
			$pass_issue = $_POST['text9'];
			$pass_code = $_POST['text10'];
			$birthplace = $_POST['text11'];
			$params22 = array(
			$borrower_key_change,
			$last_name,
			$second_name,
			$patronimic,
			$birthday,
			$pass_serial,
			$pass_number,
			$pass_date,
			$pass_issue,
			$pass_code,$birthplace);
			echo $_POST['text3']."-". $_POST['text2'];
			// Изменяем данные в Анкете-заемщика
			$result22 = pg_query_params("SELECT	* FROM public.change_borrowers(
				$1,$2,$3,$4,$5,$6,$7,$8,$9,$10,UPPER($11));", $params22) or die(pg_last_error());
			$m_array = pg_fetch_row($result22);
		
			if( $m_array[0]==1){
				$color = 'green';
				$rescolor = 'Заемщик исправлен!';
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				log_text('change borrowers and tmp_app_pass','UPDATE',"$params22[5]"."$params22[6]");
			} elseif ($m_array[0]==-2) {
				$color = 'darkgreen';
				$rescolor = 'Заемщик исправлен в АСУЗе, в процессе не найден!';
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				log_text('change borrowers','UPDATE',"$params22[5]"."$params22[6]");
			} else {
				echo  $m_array[0]."Пусто";
			}	
		break;
		//Оплаты из реестра
		case 'DocReestr':
			// номер договора
			$date_of_transfer_begin = trim($_POST['text1']);
			$date_of_transfer_end = trim($_POST['text2']);
			$Bank_key = $_POST['text3'];
			$myparam = array($date_of_transfer_begin , $date_of_transfer_end, $Bank_key);
			echo $date_of_transfer_begin." ".$date_of_transfer_end."-".$Bank_key."-<br>";
			// ищем ключ договора
			$result15 = pg_query_params("select t.register_key,sum(tt.payment_sum),date_trunc('day', COALESCE(t.date_of_transfer,t.date_receipt)) as paadd, b.bank_name
			 from registers.registers t
			 inner join  registers.register_records tt
			 ON tt.register_key = t.register_key
			 inner join registers.banks b
			 on t.bank_key=b.bank_key
			 where date_trunc('day', COALESCE(t.date_of_transfer,t.date_receipt)) 
			 BETWEEN $1 AND $2 and (t.bank_key =$3) GROUP BY t.date_receipt,t.date_of_transfer,t.register_key,b.bank_name Order by paadd;", $myparam) or die(pg_last_error());
			// если нашли, то 
			
			//echo $_POST['text1']."'".$user['loan_c2key']."'";
			if(pg_num_rows($result15)){
				$user = pg_fetch_all($result15);
				echo '<table class="table table-condensed">
				<tr><th> register_key </th><th> sum </th><th> date </th><th> bank_name </th></tr>';
				 foreach ($user as $k => $val)
				  {
					//echo '';
					//foreach ($val as $k1 => $valval)
					//{
						/*// Выводим имя поля
						$result2 = pg_query_params("select  COALESCE(l.contract,'') as contract,l.loan_key, COALESCE(a.process_key,0) as process_key from public.loans l left join loan_issue.application a on a.borrower_key = l.borrower_key where l.borrower_key = $1", array($valval)) or die(pg_last_error());
						$username2 = pg_fetch_all($result2);
						// Выводим все значения выборки
						//print_r($username2);
						if ($username2[0]<>'')
						foreach ($username2 as $key2 => $val23)*/
						
						echo "<tr><td>".$val['register_key']."</td><td>".$val[sum]."</td><td>".$val['paadd']."</td><td>".$val['bank_name']."</td></tr>";
					//}
					//echo "";
				  }
				  echo "</td></tr></table>";
			} else
				echo "реестро за данный период не найдено!";
		break;
		case 'Doc_rko':
			$name=trim($_POST['text1']);
			echo $name."<br>";
			$params = array($name);

			$result12 = pg_query_params("select order_key, loan_key, loan_sum, date_issue, borrower_name from public.expenditure_cash_orders where 
				order_number like ($1);", $params) or die(pg_last_error());
			$user = pg_fetch_all($result12);

			if(pg_num_rows($result12)){
				echo '<table class="table table-condensed">
				<tr><th> order_key </th><th> loan_key </th><th> loan_sum </th><th> date_issue </th><th> borrower_name </th></tr>';
				 foreach ($user as $k => $val)
				  {
					echo '<tr>';
					foreach ($val as $k1 => $valval)
					{
						// Выводим все значения выборки
						echo "<td>$valval</td>";
					}
					echo "</tr>";
				  }
				  echo "</td></tr></table>";
			} else 
			echo "Не найден";
			
		break;
		case 'change_rko':
			// Ключ РКО
			if ($_POST['text1']==''){
				$order_key = null;
			}else{
				$order_key = trim($_POST['text1']);
			}		
			// ФИО Клиента
			if ($_POST['text2']==''){
				$borrower_name = null;
			}else{
				$borrower_name=trim($_POST['text2']);
			}
			
			echo $order_key."-".mb_convert_case($borrower_name, MB_CASE_TITLE, 'UTF-8')."<br>";
			
			
			//Проверяем еще раз по другим параметрам
			$params1 = array($order_key, mb_convert_case($borrower_name, MB_CASE_TITLE, 'UTF-8'));
			$result5 = pg_query_params("Select order_key from public.expenditure_cash_orders where order_key = $1 and (borrower_name like $2);", $params1) or die(pg_last_error());
			$user = pg_fetch_all($result5);			
			if(pg_num_rows($result5)){
					$color = 'red';
					$rescolor = 'РКО уже изменен!';
					echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
					break;
			} else {
			
				// Изменяем РКО 
				$result13 = pg_query_params("Select * From 
						expenditure_cash_order_change($1, $2);", $params1) or die(pg_last_error());
					
					
				$m_array = pg_fetch_row($result13);
				
				if( $m_array[0]==1){
					$color = 'green';
					$rescolor = 'РКО исправлен!';
					echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
					log_text('expenditure_cash_order_change','UPDATE',"$order_key");
				}
			}
		break;
		case 'change_RegRec':
			// Ключ order_key 
			if ($_POST['text1']==''){
				$order_key = null;
			}else{
				$order_key = trim($_POST['text1']);
			}		
			// ФИО Клиента
			if ($_POST['text2']==''){
				$new_contract = null;
				$color = 'red';
				$rescolor = 'Введите новый номер договора!';
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				exit;
			}else{
				$new_contract=trim($_POST['text2']);
			}
			
			$result = pg_query_params("select * from public.loan_c2key(UPPER($1));", array($new_contract)) or die(pg_last_error());
			
			$user1 = pg_fetch_assoc($result);
			$key_ = $user1['loan_c2key'];
			if ($key_==''){
				$color = 'red';
				$rescolor = 'Договор не найден!';
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				exit;
			}
			
			echo $order_key."-".mb_convert_case($new_contract, MB_CASE_TITLE, 'UTF-8')."<br>";
			
			
			//Проверяем еще раз по другим параметрам
			$params1 = array($order_key, mb_convert_case($new_contract, MB_CASE_TITLE, 'UTF-8'));
			$result5 = pg_query_params("Select register_records_key from registers.register_records where register_records_key = $1 and (contract_number like UPPER($2));", $params1) or die(pg_last_error());
			$user = pg_fetch_all($result5);			
			if(pg_num_rows($result5)){
					$color = 'red';
					$rescolor = 'register_record уже изменен!';
					echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
					break;
			} else {
			
				// Изменяем РКО 
				$result13 = pg_query_params("SELECT * FROM registers.register_record_change_contract($1,UPPER($2));", $params1) or die(pg_last_error());
					
					
				$m_array = pg_fetch_row($result13);
				
				if( $m_array[0]==1){
					$color = 'green';
					$rescolor = 'оплата исправлена!';
					echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
					log_text('registers.register_record_change_con','UPDATE',"$order_key");
				} else {
					echo "<label style='color:blue'> ".$m_array[0]."</label><p>";
				}
			}
		break;
		case 'Customer':
			//$sval1 = 0;
			$sval1 = intval($_POST['text1']);
			//echo $sval1;
			if ($sval1 == 0){
				echo "Введите номер клиента";
				exit;
			}
			
			$params = array($sval1);
			//echo $sval1;
			$result30 = pg_query_params("SELECT UPPER(tb.last_name) as last_name,UPPER(tb.name) as second_name,UPPER(tb.patronimic) as patronimic,tb.birthday,tb.pass_series,tb.pass_number,tb.pass_date ,UPPER(tb.pass_issue) as pass_issue,pass_code,
					UPPER(tap.birthplace) as birthplace
				FROM t_borrowers tb left join loan_issue.tmp_application_passport tap on tb.pass_series=tap.serial and tb.pass_number = tap.number
				WHERE tb.borrower_key = $1;", $params) or die(pg_last_error());
			if(pg_num_rows($result30)){
				echo '<script> var seek="1"; </script>';
				$user = pg_fetch_all($result30);
				//echo "<script> var ";
				 foreach ($user as $k => $val)
				  {
					echo "<script> var ";
					foreach ($val as $k1 => $valval)
					{
						
						if ($k1 == 'birthplace') {
							echo "$k1 = '".$valval."';";
						} else 	echo "$k1 = '".$valval."',";
					}
					echo "</script>";
				  }
			} else {
				echo "не найден1";
				echo '<script> var seek="0"; </script>';
				}
			
		break;

		// Отменить перевод через контакт CONTACT 32
		case 'RemittanceCancel':
			// Номер договора
			$key_ = trim($_POST['text1']);
			// Отменяем перевод
			$result = pg_query_params(" Select *
			from loan_issue.application_issue_via_contact_to_201(UPPER($1));", array($key_)) or die(pg_last_error());
			// заносим результат в массив
			$user = pg_fetch_assoc($result);
			//проверяем результат 
			if ($user['application_issue_via_contact_to_201'] == 'OK'){
				$color = 'green';
				$rescolor = 'Перевод отменен!';	
			}else{
				$color ='red';
				$rescolor = $user['application_issue_via_contact_to_201'];
			}
			echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
			log_text('application_issue_via_contact_to_201','UPDATE',"$key_ $rescolor");
		break;
		// Черный список
		case 'BlackList':
			$borower_key =  trim($_POST['text1']);

			//echo $borower_key."-".$status_key."-".$description."<br>";
				
			$query = "SELECT * FROM  borrowers.add_borrower_to_blacklist(-1000,
			".$borower_key.");";
			
			//echo $query."<br>";
			$result2 = pg_query($query) or die(pg_last_error());
			$m_array = pg_fetch_row($result2);
			
			if( $m_array[0]<>0){
				$color = 'green';
				$rescolor = 'Клиент добавлен в черный список!';
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				log_text('add_borrower_to_blacklist','INSERT',"$borower_key");
			} else 
				echo 'Клиент не добавлен';

		break;
		// Полное закрытие займа
		case 'LoanAllClose':
			// Номер договора
			$loan_key =  trim($_POST['text1']);
			// Ссылка на задачу в портале redmine
			$redmine_key = trim($_POST['text2']);
			// Находим ключ займа и проверяем результат
			$result = pg_query_params("select * from public.loan_c2key(UPPER($1));", array($loan_key)) or die(pg_last_error());
			// результат заносим в переменную в виде массива
			$user = pg_fetch_assoc($result);
			// получаем ключ заема
			$key_ = $user['loan_c2key'];
			// Ключ есть, то выводим данные по договору 
			if ($key_<>0){
				// Заголовок таблицы
				echo "<table class='table table-condensed' border=1><tr>
				<th>loan_key</th><th>borrower_key</th><th>last_name</th><th>first_name</th><th>pantonimic</th><th>subdivition_name</th>
				</tr><tr><th>".$user['loan_c2key']."</th><th>";
				$result = pg_query_params("select tb.borrower_key,tb.last_name,tb.name,tb.patronimic,ts.subdivision_name,l.returned from public.t_borrowers tb inner join public.loans l on tb.borrower_key=l.borrower_key left join public.t_subdivisions ts on ts.subdivision_key=l.subdivision_key where l.loan_key = $1", array($key_)) or die(pg_last_error());
				$username = pg_fetch_assoc($result);
				echo $username['borrower_key']."</th><th>".$username['last_name']."</th>
				<th>".$username['name']."</th><th>".$username['patronimic']."</th>
				<th>".$username['subdivision_name']."</th></tr></table>";
				//не закрыт?
				if ($username['returned']<>'t'){
					$myparam = array($key_,$redmine_key);
					// закрываем
					$result2 = pg_query_params("SELECT loan_close_all($1,$2);", $myparam) or die(pg_last_error());
					$m_array = pg_fetch_row($result2);
					// результат 1?
					if($m_array[0]==1){
						// Договор закрыт
						$color = 'green';
						$rescolor = 'Заем закрыт! '.$m_array[0];
						// запись в лог
						log_text('loan_close_all','SET',"$myparam[0] $myparam[1]");
					} else {
						$color = 'red';
						$rescolor = 'Ошибка закрытия! '.$m_array[0];
					}
				} else {
					$color = 'darkblue';
					$rescolor = 'Договор закрыт! '.$m_array[0];
				}
			} else {
				$color = 'red';
				$rescolor = 'Договор не найден! '.$m_array[0];
			}
			// выводим результат
			echo "<label style='color:".$color."'> ".$rescolor."</label><p>";	
		break;
		// Ручная коррекция списания
		case 'LoanСorrectionClose':
			// Номер договора
			$loan_key =  trim($_POST['text1']);
			
			// Сумма комиссии
			$Overpayment_ = str_replace(',','.', $_POST['text2']);
			$Overpayment_ =   floatval(number_format((double)$Overpayment_,2, '.', ''));
			if ($Overpayment_=='')
				$Overpayment_ = 0;
			
			// Сумма пени
			$Fine_ = str_replace(',','.', $_POST['text3']);
			$Fine_ = floatval(number_format((double)$Fine_,2, '.', ''));
			if ($Fine_=='')
				$Fine_ = 0;
			// кол-во платежей
			$Num_rows_ =  trim($_POST['text4']);
			if ($Num_rows_== '' or $Num_rows_<=0){
				echo "Введите кол-во платежей больше 0";
				exit;
			}
			// Ссылка на задачу в портале redmine
			$redmine_key = trim($_POST['text5']);
			if ($redmine_key== ''){
				echo "Не указанна задача!";
				exit;
			}
			echo "Сумма комиссии: $Overpayment_  Сумма пени: $Fine_";
			// Находим ключ займа и проверяем результат
			$result = pg_query_params("
			select * from public.loan_c2key(UPPER($1));", array($loan_key)) or die(pg_last_error());
			// результат заносим в переменную в виде массива
			$user = pg_fetch_assoc($result);
			// получаем ключ заема
			$key_ = $user['loan_c2key'];
			// Ключ есть, то выводим данные по договору 
			if ($key_<>0){
				// Заголовок таблицы
				echo "<table class='table table-condensed' border=1><tr>
				<th>loan_key</th><th>borrower_key</th><th>last_name</th><th>first_name</th><th>pantonimic</th><th>subdivition_name</th>
				</tr><tr><th>".$user['loan_c2key']."</th><th>";
				$result = pg_query_params("select tb.borrower_key,tb.last_name,tb.name,tb.patronimic,ts.subdivision_name,l.returned from public.t_borrowers tb inner join public.loans l on tb.borrower_key=l.borrower_key left join public.t_subdivisions ts on ts.subdivision_key=l.subdivision_key where l.loan_key = $1", array($key_)) or die(pg_last_error());
				$username = pg_fetch_assoc($result);
				echo $username['borrower_key']."</th><th>".$username['last_name']."</th>
				<th>".$username['name']."</th><th>".$username['patronimic']."</th>
				<th>".$username['subdivision_name']."</th></tr></table>";
				//не закрыт?
				if ($username['returned']<>'t'){
					$myparam = array($key_,$Overpayment_,$Fine_,$Num_rows_,$redmine_key);
					// закрываем

					$query = 'SELECT * From
					public.loan_manual_correction_add('.$key_.',
					-1*0,-1*'.$Overpayment_.',-1*'.$Fine_.',-1*0,'."'".$redmine_key."'".',null,'.$Num_rows_.',true)';
					
					$result2 = pg_query($query);
					$m_array = pg_fetch_row($result2);
					// результат 1?
					if($m_array[0]>0){
						// Договор закрыт
						$color = 'green';
						$rescolor = 'Коррекция добавлена! '.$m_array[0];
						// запись в лог
						log_text('loan_manual_correction_add','SET',"$myparam[0] ");
					} else {
						$color = 'red';
						$rescolor = 'Ошибка закрытия! '.$m_array[0];
					}
				} else {
					$color = 'darkblue';
					$rescolor = 'Договор закрыт! '.$m_array[0];
				}
			} else {
				$color = 'red';
				$rescolor = 'Договор не найден! '.$m_array[0];
			}
			// выводим результат
			echo "<label style='color:".$color."'> ".$rescolor."</label><p>";	
		break;
		case 33:
			// Номер договора
			$key_ = trim($_POST['text1']);
			$comment_ = trim($_POST['text2']);
			// выполняем 
			$comment_ = "'".$comment_."'";
			$params = array($key_, $comment_);
			//echo $key_;
			$result = pg_query_params("Select *
			from public.remove_fine_and_bl(loan_c2key(UPPER($1)),$2);", $params) or die(pg_last_error());
		
			// заносим результат в массив
			$user = pg_fetch_assoc($result);
			//проверяем результат 
			if ($user['remove_fine_and_bl'] = 2){
				$color = 'green';
				$rescolor = 'Пеня списана и клиент добавлен в черный список!'.$user['remove_fine_and_bl'];	
				log_text('remove_fine_and_bl','SET',"$key_  $comment_");
			}else if($user['remove_fine_and_bl'] = 1){
				$color ='green';
				$rescolor = 'Пеня списана!'.$user['remove_fine_and_bl'];
			}else{
				$color ='red';
				$rescolor = $user['remove_fine_and_bl'];
			}
			
			echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
		break;
		
		case 'Process':
			$process_key = trim($_POST['text1']);
			//echo $myparam;
			$result = pg_query_params("Select application_key, borrower_key,application_status from loan_issue.application where process_key = $1;", array($process_key)) or die(pg_last_error());
			if(pg_num_rows($result)){
			
				$user = pg_fetch_assoc($result);
				echo "Анкета-заявка №".$user['application_key'];
				if ($user['application_status'] == 'opened'){
					$color = 'green';
					$rescolor = "Открыт";
				}else if ($user['application_status'] == 'closed'){
					$color ='red';
					$rescolor = "Закрыт";
				}	
				echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
				
				$result1 = pg_query_params("select tb.borrower_key,tb.last_name,tb.name,tb.patronimic from public.t_borrowers tb inner join public.loans l on tb.borrower_key=l.borrower_key where tb.borrower_key = $1;", array($user['borrower_key'])) or die(pg_last_error());
				$username = pg_fetch_assoc($result1);
				echo "borrower_key = ".$username['borrower_key']."<br>ФИО: ".$username['last_name']."-".$username['name']."-".$username['patronimic'];
				log_console('application');

			} else
				echo "Процесс не найден";
		break;
		// К выбору суммы
		case 'ProcessTo200':
			// Номер процесса
			$key_ = trim($_POST['text1']);
			// Возвращаем процесс на шаг выбора суммы
			$result = pg_query_params(" Select *
			from loan_issue.application_to_200($1);", array($key_)) or die(pg_last_error());
			// заносим результат в массив
			$user = pg_fetch_assoc($result);
			//проверяем результат 
			if ($user['application_to_200'] == 'OK'){
				$color = 'green';
				$rescolor = 'Процесс вернулся на шаг выбора суммы!';	
				log_text('application_to_200','SET',"$key_");
			}else{
				$color ='red';
				$rescolor = $user['application_to_200'];
			}
			//и выводим его
			echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
		break;
		case 'ProcessTo100':
			// Номер процесса
			$key_ = trim($_POST['text1']);
			// Возвращаем процесс на проверку паспорта
			$result = pg_query_params(" Select *
			from loan_issue.application_to_100($1);", array($key_)) or die(pg_last_error());
			// заносим результат в массив
			$user = pg_fetch_assoc($result);
			//проверяем результат 
			if ($user['application_to_100'] == 'OK'){
				$color = 'green';
				$rescolor = 'Процесс вернулся на шаг проверки паспортных данных!';	
				log_text('application_to_100','SET',"$key_");
			}else{
				$color ='red';
				$rescolor = $user['application_to_100'];
			}
			//и выводим его
			echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
		break;
		case 'ProcessTo103':
			// Номер процесса
			$key_ = trim($_POST['text1']);
			// Возвращаем процесс на шаг проверки наличия исполнительных делопроизводств
			$result = pg_query_params(" Select *
			from loan_issue.application_to_103($1);", array($key_)) or die(pg_last_error());
			// заносим результат в массив
			$user = pg_fetch_assoc($result);
			//проверяем результат 
			if ($user['application_to_103'] == 'OK'){
				$color = 'green';
				$rescolor = 'Процесс вернулся на шаг проверки наличия исполнительных делопроизводств!';	
				log_text('application_to_103','SET',"$key_");
			}else{
				$color ='red';
				$rescolor = $user['application_to_103'];
			}
			//и выводим его
			echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
		break;
		case 'ProcessTo14':
			// Номер процесса
			$key_ = trim($_POST['text1']);
			// Возвращаем процесс на шаг сканирования, отказ по географии(добавить нп)
			$result = pg_query_params(" Select *
			from loan_issue.application_99to14($1);", array($key_)) or die(pg_last_error());
			// заносим результат в массив
			$user = pg_fetch_assoc($result);
			//проверяем результат 
			if ($user['application_99to14'] == 'OK'){
				$color = 'green';
				$rescolor = 'Процесс вернуля на шаг сканирования!';	
				log_text('application_99to14','SET',"$key_");
			}else{
				$color ='red';
				$rescolor = $user['application_99to14'];
			}
			//и выводим его
			echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
		break;
		case 'ProcessTo99':
			// Номер процесса
			$key_ = trim($_POST['text1']);
			// Возвращаем процесс на принудительный отказ
			$result = pg_query_params(" Select *
			from loan_issue.application_to_99($1);", array($key_)) or die(pg_last_error());
			// заносим результат в массив
			$user = pg_fetch_assoc($result);
			//проверяем результат 
			if ($user['application_to_99'] == 'OK'){
				$color = 'green';
				$rescolor = 'Процесс принудительно переведен в отказ!';	
				log_text('application_to_99','SET',"$key_");
			}else{
				$color ='red';
				$rescolor = $user['application_to_99'];
			}
			//и выводим его
			echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
		break;
		case 'ProcessToCancel':
			// Номер процесса
			$key_ = trim($_POST['text1']);
			$key_2 = trim($_POST['text2']);
			// Возвращаем процесс на принудительный отказ
			$result = pg_query_params(" Select loan_issue.application_cancel($1,3,$2);", array($key_,$key_2)) or die(pg_last_error());
			// заносим результат в массив
			$user = pg_fetch_assoc($result);
			//проверяем результат 
			if ($user['application_cancel'] == '1'){
				$color = 'green';
				$rescolor = 'Процесс полностью отменен!';	
				log_text('application_cancel','SET',"$key_");
			}else{
				$color ='red';
				$rescolor = $user['application_cancel'];
			}
			//и выводим его
			echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
		break;
		// Удаление процесса
		case 'ProcessToDelete':
			// Номер процесса
			$key_ = trim($_POST['text1']);
			// Возвращаем процесс на принудительный отказ
			$result = pg_query_params("Select
			b_processes.process_cancel($1);
			", array($key_)) or die(pg_last_error());
			// заносим результат в массив
			$user = pg_fetch_assoc($result);
			//проверяем результат 
			if ($user['process_cancel'] == '1'){
				$color = 'green';
				$rescolor = 'Процесс полностью отменен!';	
				log_text('process_cancel','SET',"$key_");
			}else{
				$color ='red';
				$rescolor = $user['process_cancel'];
			}
			//и выводим его
			echo "<label style='color:".$color."'> ".$rescolor."</label><p>";
		break;
		default:
			echo "Пусто!<br>";
		break; 
		
	};
	
	} else {
	
		echo 'text1 is empty';
	};


?>