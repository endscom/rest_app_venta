<?php
/**
 * Created by PhpStorm.
 * User: maryan.espinoza
 * Date: 16/11/2016
 * Time: 15:54
 */

header('Access-Control-Allow-Origin: *');
	require_once 'Class.Main.php';
	$obj = new Vistas;
	$obj ->VENDEDORES($_REQUEST['V'],$_REQUEST['P']);
