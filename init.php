<?php
	header("Content-Type:text/html;Charset=utf8");
	$host="localhost";
	$user="root";
	$pwd="gt";
	$db_name="db_ecWeb";
	$conn = mysqli_connect($host,$user,$pwd);
	$sql="CREATE DATABASE IF NOT EXISTS `$db_name`;";
	mysqli_query($conn,$sql);
	mysqli_select_db($conn,$db_name);
	
	include_once("./Install/install_db.php");
	sp_execute_sql($conn,"./Install/ecWeb.sql","tb_");
	mysqli_close($conn);
?>