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
    public static function PorRecuperar($V, $P)
    {
        $obj = new Sqlsrv();
        if ($P=="1")
        {
            $Array = $obj->fetchArray("SELECT dcc.DOCUMENTO, dcc.FECHA_DOCUMENTO, dcc.FECHA_VENCE
                                              , dcc.CONDICION_PAGO, cp.DESCRIPCION DESCRIPCION_CP, dcc.MONEDA
                                              , dcc.VENDEDOR, ven.NOMBRE NOMBRE_VENDEDOR, dcc.CLIENTE, cli.NOMBRE NOMBRE_CLIENTE, dcc.TIPO
                                              , dcc.SUBTIPO, sdcc.DESCRIPCION DESC_SUBTIPO, dcc.ANULADO, dcc.MONTO, dcc.SALDO
                                       FROM Softland.umk.DOCUMENTOS_CC dcc INNER JOIN Softland.umk.CONDICION_PAGO cp ON dcc.CONDICION_PAGO = cp.CONDICION_PAGO
                                            INNER JOIN Softland.umk.VENDEDOR ven ON dcc.VENDEDOR = ven.VENDEDOR INNER JOIN Softland.umk.CLIENTE cli on dcc.CLIENTE = cli.CLIENTE
                                            INNER JOIN Softland.umk.SUBTIPO_DOC_CC sdcc ON dcc.TIPO = sdcc.TIPO AND dcc.SUBTIPO=sdcc.SUBTIPO
                                       WHERE dcc.VENDEDOR= 'F06' AND dcc.TIPO IN ('FAC','N/C') AND dcc.SALDO>0 AND dcc.FECHA_VENCE <= '2017-01-17'
                                       ORDER BY dcc.CLIENTE ",SQLSRV_FETCH_ASSOC);
        }
        else
        {
            $Array = $obj->fetchArray("SELECT dcc.DOCUMENTO, CONVERT(NVARCHAR(20),dcc.FECHA_DOCUMENTO, 103) FECHA_DOCUMENTO
                                              , CONVERT(NVARCHAR(20), dcc.FECHA_VENCE, 103) FECHA_VENCE
                                              , dcc.CONDICION_PAGO, cp.DESCRIPCION DESCRIPCION_CP, dcc.MONEDA
                                              , dcc.VENDEDOR, ven.NOMBRE NOMBRE_VENDEDOR, dcc.CLIENTE, cli.NOMBRE NOMBRE_CLIENTE, dcc.TIPO
                                              , dcc.SUBTIPO, sdcc.DESCRIPCION DESC_SUBTIPO, dcc.ANULADO, CAST(dcc.MONTO AS DECIMAL(28,4)) MONTO, CAST(dcc.SALDO AS DECIMAL(28,4)) SALDO
                                       FROM Softland.umk.DOCUMENTOS_CC dcc INNER JOIN Softland.umk.CONDICION_PAGO cp ON dcc.CONDICION_PAGO = cp.CONDICION_PAGO
                                            INNER JOIN Softland.umk.VENDEDOR ven ON dcc.VENDEDOR = ven.VENDEDOR INNER JOIN Softland.umk.CLIENTE cli on dcc.CLIENTE = cli.CLIENTE
                                            INNER JOIN Softland.umk.SUBTIPO_DOC_CC sdcc ON dcc.TIPO = sdcc.TIPO AND dcc.SUBTIPO=sdcc.SUBTIPO
                                       WHERE dcc.VENDEDOR= 'F06' AND dcc.TIPO IN ('FAC','N/C') AND dcc.SALDO>0 AND dcc.FECHA_VENCE <= '20170117'
                                       ORDER BY dcc.CLIENTE ",SQLSRV_FETCH_ASSOC);
        }
        $FACTURAS = "INSERT INTO CC_CLIENTES VALUES ";
        $i=1;
        $TamPag=100;
        $InicPag=1;
        $FinPag=$TamPag;
        $CantPags=1;
        $pila=array();
        foreach ($Array as $fila)
        {
            $FACTURAS .= "('".$fila['DOCUMENTO']."','".$fila['FECHA_DOCUMENTO']."','".$fila['FECHA_VENCE']."','".$fila['CONDICION_PAGO']."', '".$fila['DESCRIPCION_CP']."','".$fila['MONEDA']."','".$fila['VENDEDOR']."','".$fila['NOMBRE_VENDEDOR']."','".$fila['CLIENTE']."','".$fila['NOMBRE_CLIENTE']."','".$fila['TIPO']."','".$fila['SUBTIPO']."','".$fila['DESC_SUBTIPO']."','".$fila['ANULADO']."','".$fila['MONTO']."','".$fila['SALDO']."'),";
            if ($i==$FinPag)
            {
                $pila[$CantPags-1]=substr($FACTURAS,0,strlen($FACTURAS)-1);
                $InicPag+=$TamPag;
                $FinPag+=$TamPag;
                $CantPags+=1;
                $FACTURAS = "INSERT INTO CC_CLIENTES VALUES ";
            }
            $i+=1;
        }

        $pila[$CantPags-1]=substr($FACTURAS,0,strlen($FACTURAS)-1);

        for($x=0;$x<count($pila);$x++){
            $json[0]["PorRecuperar"]["PorRecuperar".$x] = $pila[$x];
        }
        echo json_encode($json);
        $obj->close();

    }
    public static function Facturas3($V, $P)
    {
        $obj = new Sqlsrv;

        if ($P=="1")
        {
            //$Array    = $obj->fetchArray("SELECT * FROM app_ventas_master_detalle_vtas ORDER BY Codigo, FACTURA",SQLSRV_FETCH_ASSOC);
            $Array    = $obj->fetchArray("SELECT * FROM app_ventas_Indicadores3 ORDER BY CODIGO, NOMBRE, CODCLIENTE, NOMBREDELCLIENTE",SQLSRV_FETCH_ASSOC);
        }
        else
        {
            //$Array    = $obj->fetchArray("SELECT * FROM app_ventas_master_detalle_vtas WHERE Codigo='".$V."' ORDER BY Codigo, FACTURA",SQLSRV_FETCH_ASSOC);
            $Array    = $obj->fetchArray("SELECT * FROM app_ventas_Indicadores3 WHERE CODIGO='".$V."' ORDER BY CODIGO, NOMBRE, CODCLIENTE, NOMBREDELCLIENTE",SQLSRV_FETCH_ASSOC);
        }
        //$inCLS    = "( ";
        //$CLIENTES = "INSERT INTO CLIENTES VALUES ";
        $FACTURAS = "INSERT INTO INDICADORES3 VALUES ";
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
            $FACTURAS .= "('".$fila['CODIGO']."','".$fila['NOMBRE']."','".$fila['CODCLIENTE']."','".$fila['NOMBREDELCLIENTE']."', '".$fila['VENTAS_3']."','".$fila['NUM_ART_FAC']."','".$fila['PROMEDIO_ART']."','".$fila['MontoPromXFac']."'),";
            if ($i==$FinPag)
            {

                $pila[$CantPags-1]=substr($FACTURAS,0,strlen($FACTURAS)-1);
                $InicPag+=$TamPag;
                $FinPag+=$TamPag;
                $CantPags+=1;
                $FACTURAS = "INSERT INTO INDICADORES3 VALUES ";

            }
            $i+=1;
        }

        $pila[$CantPags-1]=substr($FACTURAS,0,strlen($FACTURAS)-1);

        for($x=0;$x<count($pila);$x++){
            $json[0]["FACTURASINDICADORES"]["FACTURASINDICADORES".$x] = $pila[$x];
        }
        echo json_encode($json);
        $obj->close();
    }
    public static function Facturas($V, $P)
    {
        $obj = new Sqlsrv;
        
        if ($P=="1")
        {
            $Array    = $obj->fetchArray("SELECT * FROM app_ventas_master_detalle_vtas ORDER BY Codigo, FACTURA",SQLSRV_FETCH_ASSOC);
            //$Array    = $obj->fetchArray("SELECT * FROM app_ventas_Indicadores3 ORDER BY Codigo, FACTURA",SQLSRV_FETCH_ASSOC);
        }
        else
        {
            $Array    = $obj->fetchArray("SELECT * FROM app_ventas_master_detalle_vtas WHERE Codigo='".$V."' ORDER BY Codigo, FACTURA",SQLSRV_FETCH_ASSOC);
            //$Array    = $obj->fetchArray("SELECT * FROM app_ventas_Indicadores3 WHERE CODIGO='".$V."' ORDER BY Codigo, FACTURA",SQLSRV_FETCH_ASSOC);
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
            $Array    = $obj->fetchArray("SELECT * FROM app_ventas_clientes WHERE VENDEDOR='".$V."' ORDER BY NOMBRE",SQLSRV_FETCH_ASSOC);
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
/*__________________________________________________________________________________________________________________________________________________________________*/
        /*Inicio PROMEDIOS*/
        if ($P=="1")
        {
            $ArrayProm    = $obj->fetchArray("SELECT * FROM app_ventas_master_vtas ORDER BY Codigo",SQLSRV_FETCH_ASSOC);
        }
        else
        {
            $ArrayProm    = $obj->fetchArray("SELECT * FROM app_ventas_master_vtas WHERE Codigo='".$V."' ORDER BY Codigo ",SQLSRV_FETCH_ASSOC);
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
/*__________________________________________________________________________________________________________________________________________________________________*/
        /*Inicio PROMEDIOS 3 MESES*/
        if ($P=="1")
        {
            $ArrayProm    = $obj->fetchArray("SELECT * FROM app_ventas_master_vtas_3 ORDER BY Codigo, CLIENTE",SQLSRV_FETCH_ASSOC);
        }
        else
        {
            $ArrayProm    = $obj->fetchArray("SELECT * FROM app_ventas_master_vtas_3 WHERE Codigo='".$V."' ORDER BY Codigo, CLIENTE",SQLSRV_FETCH_ASSOC);
        }
        $PROMEDIOS = "INSERT INTO PROMEDIOS3 VALUES ";
        $pilaPROMEDIOS=array();
        foreach ($ArrayProm as $fila)
        {
            $PROMEDIOS .= "('".$fila['Codigo']."','".$fila['Nombre']."','".$fila['CLIENTE']."','".$fila['NombreCliente']."','".$fila['Prm_art_3']."','".$fila['Prm_vta_3']."'),";
        }
        /*echo($PROMEDIOS);*/
        $pilaPROMEDIOS=substr($PROMEDIOS,0,strlen($PROMEDIOS)-1);
        /*print_r($pilaPROMEDIOS);*/
        /*for($m=0;$m<count($pilaPROMEDIOS);$m++)
            {$json[0]["PROMEDIOS"] = $pilaPROMEDIOS[$m];}
        */
        $json[0]["PROMEDIOS3"] = $pilaPROMEDIOS;

        /*FIN PROMEDIOS 3 MESES*/
/*__________________________________________________________________________________________________________________________________________________________________*/
        /*Inicio METAS*/
        $obj = new Vistas;
        $link = $obj ->open_database_connectionMYSQL();
        $qry= "SELECT * FROM metas ORDER BY CodVendedor, CodCliente";
        $resultado= mysql_query($qry,$link) or die (mysql_error());
        $pP=array();
        $i=1;
        $TamPag=100;
        $InicPag=1;
        $FinPag=$TamPag;
        $CantPags=1;
        $met = "INSERT INTO metas VALUES ";

        while($row = mysql_fetch_assoc($resultado))
        {
            $met .= "('".$row['CodVendedor']."','".$row['NombreVendedor']."','".$row['CodCliente']."','".utf8_encode($row['NombreCliente'])."','".$row['MontoVenta']."','".$row['NumItemFac']."','".$row['MontoXFac']."','".$row['PromItemXFac']."'),";
            if ($i==$FinPag)
            {
                $pP[$CantPags]=substr($met,0,strlen($met)/*-1*/);
                $InicPag+=$TamPag;
                $FinPag+=$TamPag;
                $CantPags+=1;
                $met = "INSERT INTO metas VALUES ";
            }
            $i+=1;
        }

        $pP[$CantPags]=substr($met,0,strlen($met)/*-1*/);

        for($x=0;$x<count($pila);$x++){
            $json[0]["METAS"]["METAS".$x] = $pila[$x];
        }

        //$json[0]["METAS"] = $pP;
        /*FIN METAS*/
       /*__________________________________________________________________________________________________________________________________________________________________*/

        echo json_encode($json);
        //$obj->close();
    }

    public static function ExecuteSQL($SQL){
        $json = Array();
        $json["Execute"]= ((mysql_query($SQL,Vistas::open_database_connectionMYSQL())) ? 1 : 0);
        echo json_encode($json);
    }


}
?>