<?php
header('Access-Control-Allow-Origin: *');
require_once 'Class.Main.php';
$obj = new Vistas;
$obj ->PorRecuperar($_REQUEST['V'],$_REQUEST['P']);
