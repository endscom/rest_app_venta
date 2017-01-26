<?php
header('Access-Control-Allow-Origin: *');
require_once 'Class.Main.php';
$obj = new Vistas;
$obj ->Facturas3($_REQUEST['V'],$_REQUEST['P']);
