<?php
// Подключение к базе данных
function db_connect(){
	$host="asuzdev.vivadengi.ru";
	$port="5432";
	$username="vladimir.burundukov";
	$password="";
	$database="asuz";
	$conn_string = "host=$host port=$port dbname=$database user=$username password=$password";
	//Соединяемся 
	//echo $conn_string;
	$link = pg_connect($conn_string) or die ('Не удается открыть канал связи с базой данных');
	return $link;
}
function db_connect1(){
	$host="asuztest.vivadengi.ru";
	$port="5432";
	$username="vladimir.burundukov";
	$password="";
	$database="asuz";
	$conn_string = "host=$host port=$port dbname=$database user=$username password=$password";
	//Соединяемся 
	//echo $conn_string;
	$link = new ConnectToDataBase();
	//$link = pg_connect($conn_string) or die ('Не удается открыть канал связи с базой данных');
	return $link->Connecting($host,$port,$username,$password,$database);
}

 class ConnectToDataBase {
 /*
	// параметры подключения 
	var $host;
	var $port;
	var $username;
	var $password;
	var $database;*/
	public static $Connect;

	// Конструктор
	public static function Connecting($a,$b,$c,$d,$f){
	/*
		$this->host = $a;
		$this->port = $b;
		$this->username = $c;
		$this->password = $d;
		$this->database = $f;
		
		*/
		self::$Connect = pg_connect("host=$a port=$b dbname=$d user=$c password=$f") or die ('Не удается открыть канал связи с базой данных');
		
		if(!self::$Connect){
		    echo "<p><b>fdsafdas".pg_last_error()."</b></p>";
            exit();
            return false;
		}
		//$this->conn_string = "host=$this->host port=$this->port dbname=$this->database user=$this->username password=$this->password";
		
	}
	
	
	public static function Closing(){
		return ps_close(self::$Connect);
	}
 }



?>