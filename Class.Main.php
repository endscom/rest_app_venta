<?php
require_once 'Sqlsrv.php';

class Vistas{
    public static function open_database_connectionMYSQL(){
        $link = mysql_connect('localhost', 'root', 'a7m1425.');
        mysql_select_db('appventa', $link);
        return $link;
    }

    public static function VENDEDORES($V,$P){
        $obj = new Vistas;
        $link = $obj ->open_database_connectionMYSQL();
        //$pwd=md5($P);
        $pwd=$P;
        $consulta= "SELECT * FROM usuario WHERE vendedor='".$V."' AND Password ='".$pwd."' ";
        $resultado= mysql_query($consulta,$link) or die (mysql_error());
        $fila=mysql_fetch_array($resultado);
        $json = array();

        $json['VENDEDOR']         = ($fila['vendedor']==null) ? 0 : $fila['vendedor'];
        $json['NOMBRE']           = ($fila['Nombre']==null) ? 0 : $fila['Nombre'];
        $json['CONTRASENNA']      = ($fila['Password']==null) ? 0 : $fila['Password'];
        $json['ROL'] = ($fila['rol']==null) ? 0 : $fila['rol'];
        echo json_encode($json);
    }
    public static function FACTURAS_PUNTOS($C){
        $obj       = new Vistas;
        $link      = $obj ->open_database_connectionMYSQL();
        $consulta  = "SELECT * FROM app_puntos_facturas WHERE CLIENTE IN ".$C;
        //$consulta  = "SELECT * FROM app_puntos_facturas ";
        $resultado = mysql_query($consulta,$link) or die (mysql_error());
        $vFACTURAS = "";
        $vFACTURAS="INSERT INTO DETALLE_FACTURA_PUNTOS VALUES ";
        $i=1;
        $TamPag=100;
        $InicPag=1;
        $FinPag=$TamPag;
        $CantPags=1;
        $pilax=array();
        while($row=mysql_fetch_array($resultado)){
            $vFACTURAS .= "(
                '".(($row['CLIENTE']==null) ? 0 : $row['CLIENTE'])."',
                '".(($row['FACTURA']==null) ? 0 : $row['FACTURA'])."',
                '".(($row['FECHA']==null) ? 0 : $row['FECHA'])."',
                '".(($row['ACUMULADO']==null) ? 0 : $row['ACUMULADO'])."',
                '".(($row['DISPONIBLE']==null) ? 0 : $row['DISPONIBLE'])."',
                '".(($row['FECHA']==null) ? 0 : $row['FECHA'])."'
            ),";
            if ($i==$FinPag)
            {
            	//echo substr($vFACTURAS,0,strlen($vFACTURAS)-1);
	            $pilax[$CantPags-1]=substr($vFACTURAS,0,strlen($vFACTURAS)-1);
	            $InicPag+=$TamPag; 
	            $FinPag+=$TamPag;
	            $CantPags+=1;
	            $vFACTURAS="INSERT INTO DETALLE_FACTURA_PUNTOS VALUES ";
            }
            $i+=1;
        }
        //echo strlen($vFACTURAS)-1;
        if ($vFACTURAS!="INSERT INTO DETALLE_FACTURA_PUNTOS VALUES ")
           $pilax[$CantPags-1]=substr($vFACTURAS,0,strlen($vFACTURAS)-1);
        //return substr($vFACTURAS,0,-1);
        return $pilax;
    }
    public static function master_vtas()
    {
        
    }
    
    public static function Facturas($V, $P)
    {
        $obj = new Sqlsrv;
        
        if ($P=="1")
        {
            $Array    = $obj->fetchArray("SELECT * FROM app_ventas_master_detalle_vtas ORDER BY Codigo, FACTURA",SQLSRV_FETCH_ASSOC);
        }
        else
        {
            $Array    = $obj->fetchArray("SELECT * FROM app_ventas_master_detalle_vtas WHERE Codigo='".$V."' ORDER BY Codigo, FACTURA",SQLSRV_FETCH_ASSOC);
        }
        //$inCLS    = "( ";
        //$CLIENTES = "INSERT INTO CLIENTES VALUES ";
        $FACTURAS = "INSERT INTO DETALLE_FACTURA_PUNTOS VALUES ";
        $i=1;
        $TamPag=100;
        $InicPag=1;
        $FinPag=$TamPag;
        $CantPags=1;
        $pila=array();
        foreach ($Array as $fila)
        {
            //$inCLS .= "'".$fila['CLIENTE']."',";
            //$CLIENTES .= "('".$fila['CLIENTE']."','".$fila['NOMBRE']."','".$fila['DIRECCION']."','".$fila['TELEFONO1']."','0'),";
            $FACTURAS .= "('".$fila['Codigo']."','".$fila['ARTICULO']."','".$fila['DESCRIPCION']."','".$fila['CANTIDAD']."', '".$fila['P. Unitario']."','".$fila['Venta']."','".$fila['Cod. Cliente']."','".$fila['FACTURA']."'),";
            if ($i==$FinPag)
            {
                
                $pila[$CantPags-1]=substr($FACTURAS,0,strlen($FACTURAS)-1);
                $InicPag+=$TamPag;
                $FinPag+=$TamPag;
                $CantPags+=1;
                $FACTURAS = "INSERT INTO DETALLE_FACTURA_PUNTOS VALUES ";
                
            }
            $i+=1;
        }
        
        $pila[$CantPags-1]=substr($FACTURAS,0,strlen($FACTURAS)-1);
        
        for($x=0;$x<count($pila);$x++){            
            $json[0]["FACTURAS"]["FACTURA".$x] = $pila[$x];
        }
         echo json_encode($json);
        $obj->close();
    }
    public static function CLS($V,$P)

	{
        
        $obj      = new Sqlsrv;

        
        if ($P=="1")
        {
            $Array    = $obj->fetchArray("SELECT * FROM app_ventas_clientes ORDER BY VENDEDOR, CLIENTE",SQLSRV_FETCH_ASSOC);
        }
        else
        {
            $Array    = $obj->fetchArray("SELECT * FROM app_ventas_clientes WHERE VENDEDOR='".$V."' ",SQLSRV_FETCH_ASSOC);
        }

        $inCLS    = "( ";
        $CLIENTES = "INSERT INTO CLIENTES VALUES ";
        $FACTURAS = "INSERT INTO DETALLE_FACTURA_PUNTOS VALUES ";
        $i=1;
        $TamPag=100;
        $InicPag=1;
        $FinPag=$TamPag;
        $CantPags=1;
        $pila=array();
        foreach ($Array as $fila)
        {
            $inCLS .= "'".$fila['CLIENTE']."',";
            $CLIENTES .= "('".$fila['CLIENTE']."','".$fila['NOMBRE']."','".$fila['DIRECCION']."','".$fila['TELEFONO1']."','0'),";
            if ($i==$FinPag)
            {
                
                $pila[$CantPags-1]=substr($CLIENTES,0,strlen($CLIENTES)-1);
                $InicPag+=$TamPag;
                $FinPag+=$TamPag;
                $CantPags+=1;
                $CLIENTES = "INSERT INTO CLIENTES VALUES ";
                
            }
            $i+=1;
        }
        
        $pila[$CantPags-1]=substr($CLIENTES,0,strlen($CLIENTES)-1);

        for($x=0;$x<count($pila);$x++){            
            $json[0]["CLIENTES"]["CLIENTES".$x] = $pila[$x];
        }


        $inCLS = substr($inCLS,0,-1)." )";
        
       /* $pila1 = self::FACTURAS_PUNTOS($inCLS);
        for($y=0;$y<count($pila1);$y++){
            $json[0]["FACTURAS"]["FACTURAS".$y] = $pila1[$y];
        }
       */
        /*Inicio PROMEDIOS*/
        if ($P=="1")
        {
            $ArrayProm    = $obj->fetchArray("SELECT * FROM app_ventas_master_vtas ORDER BY Codigo",SQLSRV_FETCH_ASSOC);
        }
        else
        {
            $ArrayProm    = $obj->fetchArray("SELECT * FROM app_ventas_master_vtas WHERE Codigo='".$V."' ",SQLSRV_FETCH_ASSOC);
        }
        $PROMEDIOS = "INSERT INTO PROMEDIOS VALUES ";
        $pilaPROMEDIOS=array();
        foreach ($ArrayProm as $fila)
        {
            $PROMEDIOS .= "('".$fila['Codigo']."','".$fila['Prm_art']."','".$fila['Prm_vta']."')";
        }
        /*echo($PROMEDIOS);*/
        $pilaPROMEDIOS=substr($PROMEDIOS,0,strlen($PROMEDIOS)/*-1*/);
        /*print_r($pilaPROMEDIOS);*/
        /*for($m=0;$m<count($pilaPROMEDIOS);$m++)            
            {$json[0]["PROMEDIOS"] = $pilaPROMEDIOS[$m];}
        */
        $json[0]["PROMEDIOS"] = $pilaPROMEDIOS;
        /*FIN PROMEDIOS*/
        echo json_encode($json);
        $obj->close();
    }
}
?>