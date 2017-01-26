<?php
/**
 * Created by PhpStorm.
 * User: maryan.espinoza
 * Date: 25/01/2017
 * Time: 16:10
 */
header('Access-Control-Allow-Origin: *');
require_once 'Class.Main.php';
Vistas::ExecuteSQL("INSERT INTO LOG VALUES". $_REQUEST['SQL']);
