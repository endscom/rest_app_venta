<?php
header('Access-Control-Allow-Origin: *');
require_once 'Class.Main.php';
$obj = new Vistas;
$obj ->IndicadoresConsolidados($_REQUEST['V'],$_REQUEST['P']);