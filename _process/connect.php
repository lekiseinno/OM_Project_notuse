<?php

	session_start();
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', true);
	
	$serverName	=	"192.168.110.125";
	$connectionInfo = array( "Database"=>"OM_Planning", "UID"=>"innovation", "PWD"=>"Inno20i9","CharacterSet"=>	'UTF-8',"MultipleActiveResultSets"=>true);
	$connect = sqlsrv_connect( $serverName, $connectionInfo);

	if( $connect === false ) {
		die( print_r( sqlsrv_errors(), true));
	}else{ }
	date_default_timezone_set('Asia/Bangkok');






?>