<?php
    ini_set('date.timezone','America/Mexico_City');
    require "conexion.php";
    require "phpMQTT.php";
    $host = "";
    $port = 0;
    $username = "";
    $password = "";
    $topic = '/'.$username.'/Instruccion';
    $hora_y_fecha = date("Y-m-d H:i");
    $file = fopen("prueba.txt", "w");
    fwrite($file, "Se ejecuto a las $hora_y_fecha". PHP_EOL);
    
    $query = "SELECT * FROM eventos";
    $resultado = mysqli_query($conexion, $query);
    
    $mail = "Se a ejecutado a las $hora_y_fecha";
    $titulo = "el cron se ejecuta a esta hora";
    $headers = "MIME-Version: 1.0\r\n"; 
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
    $headers .= "From: Control de automatizacion < https://encenderfoco.000webhostapp.com/ >\r\n";
    $bool = mail("betoescobedo34@gmail.com",$titulo,$mail,$headers);
    if($bool){
        fwrite($file, "Primer correo enviado". PHP_EOL);
    }else{
        fwrite($file, "Primer correo no enviado". PHP_EOL);
    }
    while($fila = mysqli_fetch_array($resultado)){
        if(strtotime($fila['FECHA']) == strtotime($hora_y_fecha) && $fila['ESTATUS'] == "Habilitado"){
            echo strtotime($fila['FECHA'])." es igual a".strtotime($hora_y_fecha);
            fwrite($file, "Se va a ejecutar un evento". PHP_EOL);
            $id = $fila['ID'];
            $cada = $fila['CADA'];
            $sensor = $fila['SENSOR'];
            $valor = $fila['VALOR'];
            $fecha = $fila['FECHA'];
            $frecuencia = $fila['FRECUENCIA'];
            if($fila['SENSOR'] == "Dimmer"){
                $message = "1,".$fila['VALOR'].",";
            }else if($fila['SENSOR'] == "Foco"){
                $mayuscula = strtoupper($fila['VALOR']);
                if($mayuscula == "ENCENDER"){
                    $message = "0,1,";
                }else if($mayuscula == "APAGAR"){
                    $message = "0,0,";
                }                
            }
            $mqtt = new bluerhinos\phpMQTT($host, $port, "ClientID".rand());
            if($mqtt->connect(true,NULL,$username,$password)){
                $mqtt->publish($topic, $message, 0);
                $mqtt->close();
                fwrite($file, "Mensaje enviado correctamente". PHP_EOL);
                if($frecuencia == "Unica"){
                    $agregar = "DELETE FROM eventos WHERE ID = '$id'";
                    $respuesta = mysqli_query($conexion, $agregar);
                    if(!$respuesta){
                        echo "Error al actializar";
                        fwrite($file, "Ye respondio el topic de unica". PHP_EOL);
                    }else{
                        echo "Actualizacion con exito";
                        fwrite($file, "Se preseto un error en el topic". PHP_EOL);
                    }
                }elseif($frecuencia == "Diario"){
                    $fecha_agregar = strtotime('+'.$cada.' day',strtotime($hora_y_fecha));	
                    $fecha_agregar = date('Y-m-d H:i', $fecha_agregar);
                    $agregar = "UPDATE eventos Set FECHA = '$fecha_agregar' WHERE ID = '$id'";
                    $respuesta = mysqli_query($conexion, $agregar);
                    if(!$respuesta){
                        echo "Error al actializar";
                        fwrite($file, "Ye respondio el topic de diario". PHP_EOL);
                    }else{
                        echo "Actualizacion con exito";
                        fwrite($file, "Se preseto un error en el topic". PHP_EOL);
                    }                    
                }elseif($frecuencia == "Semanal"){
                    $semanas = $cada * 7;
                    $fecha_agregar = strtotime('+'.$semanas.' day',strtotime($hora_y_fecha));	
                    $fecha_agregar = date('Y-m-d H:i', $fecha_agregar);
                    $agregar = "UPDATE eventos Set FECHA = '$fecha_agregar' WHERE ID = '$id'";
                    $respuesta = mysqli_query($conexion, $agregar);
                    if(!$respuesta){
                        echo "Error al actializar";
                        fwrite($file, "Ye respondio el topic de semanal". PHP_EOL);
                    }else{
                        echo "Actualizacion con exito";
                        fwrite($file, "Se preseto un error en el topic". PHP_EOL);
                    }                    
                }elseif($frecuencia == "4 Veces Por Mes"){
                    $diasdmes = date( "t", strtotime($hora_y_fecha));
                    $fecha_det1 = explode("-",$hora_y_fecha);
                    $visitames = $fecha_det1[1];              
                    $cont = 0;
                    $dia = date("N", strtotime($hora_y_fecha));                        
                    $visitadia2 = $fecha_det1[2];                                            
                    $visitadia = ($visitadia2) - (1);
                    $fecha_resta = strtotime('-'.$visitadia.'day',strtotime($hora_y_fecha));
                    $fecha_resta = date('Y-m-j',$fecha_resta);                        
                    for ($i = 1; $i <= $diasdmes; $i++){
                        $diadelasemana = date("N", strtotime($fecha_resta));
                        if($diadelasemana == $dia){
                            $cont++;
                        }
                        $fecha_resta = strtotime( '+1 day' , strtotime($fecha_resta));
                        $fecha_resta = date( 'Y-m-j' , $fecha_resta );            	        
                    }
                    $numerodia=$cont;
                    if($diasdmes >= 29){
                        if($numerodia == 5){			
                            if($fecha_det1[2] <= 21){	                 
                                $hora_y_fecha=strtotime('+7 day',strtotime($hora_y_fecha));	
                                $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                                break;
                            }else if($fecha_det1[2] >= 22 && $fecha_det1[2] <= 28){                    
                                $hora_y_fecha=strtotime('+14 day',strtotime($hora_y_fecha));	
                                $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                            break;
                            }else if($fecha_det1[2] >= 29){
                                $hora_y_fecha=strtotime('+7 day',strtotime($hora_y_fecha));	
                                $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                                break;
                            }
                        }else if($numerodia == 4){		            
                            $hora_y_fecha=strtotime('+7 day',strtotime($hora_y_fecha));	
                            $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                            break;
                        }  
                    }else{
                        $hora_y_fecha=strtotime('+7 day',strtotime($hora_y_fecha));	
                        $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                        break;
                    }
                    $agregar = "UPDATE eventos Set FECHA = '$hora_y_fecha' WHERE ID = '$id'";
                    $respuesta = mysqli_query($conexion, $agregar);
                    if(!$respuesta){
                        echo "Error al actializar";
                        fwrite($file, "Ye respondio el topic de 4 veces". PHP_EOL);
                    }else{
                        echo "Actualizacion con exito";
                        fwrite($file, "Se preseto un error en el topic". PHP_EOL);
                    }                    
                }elseif($frecuencia == "Catorcenal"){
                    $fecha_agregar = strtotime('+14 day',strtotime($hora_y_fecha));	
                    $fecha_agregar = date('Y-m-d H:i', $fecha_agregar);
                    $agregar = "UPDATE eventos Set FECHA = '$fecha_agregar' WHERE ID = '$id'";
                    $respuesta = mysqli_query($conexion, $agregar);
                    if(!$respuesta){
                        echo "Error al actializar";
                        fwrite($file, "Ye respondio el topic de catorcenal". PHP_EOL);
                    }else{
                        echo "Actualizacion con exito";
                        fwrite($file, "Se preseto un error en el topic". PHP_EOL);
                    }                    
                }elseif($frecuencia == "2 Veces Por Mes"){
                    $diasdmes = date( "t", strtotime($hora_y_fecha));
                    $fecha_det1 = explode("-",$hora_y_fecha);
                    $visitames = $fecha_det1[1];              
                    $cont = 0;
                    $dia = date("N", strtotime($hora_y_fecha));                        
                    $visitadia2 = $fecha_det1[2];                                               
                    $visitadia = ($visitadia2) - (1);
                    $fecha_resta = strtotime('-'.$visitadia.'day',strtotime($hora_y_fecha));
                    $fecha_resta = date('Y-m-j',$fecha_resta);                        
                    for ($i = 1; $i <= $diasdmes; $i++){
                        $diadelasemana = date("N", strtotime($fecha_resta));
                        if($diadelasemana == $dia){
                            $cont++;
                        }
                        $fecha_resta = strtotime( '+1 day' , strtotime($fecha_resta));
                        $fecha_resta = date( 'Y-m-j' , $fecha_resta );            	        
                    }
                    $numerodia=$cont;
                    if($diasdmes>= 29){
                        if($numerodia == 5){
                            if($fecha_det1[2] <= 10){
                                $hora_y_fecha = strtotime('+14 day',strtotime($hora_y_fecha));	
                                $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                                break;
                            }else if($fecha_det1[2] >= 15 && $fecha_det1[2] <= 28){
                                $hora_y_fecha = strtotime('+21 day',strtotime($hora_y_fecha));	
                                $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                                break;
                            }else if($fecha_det1[2] >= 29){
                                $hora_y_fecha = strtotime('+14 day',strtotime($hora_y_fecha));	
                                $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                                break;
                            }
                        }else if($numerodia == 4){
                            $hora_y_fecha=strtotime('+14 day',strtotime($hora_y_fecha));	
                            $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                            break;
                        }  
                    }else{
                        $hora_y_fecha=strtotime('+14 day',strtotime($hora_y_fecha));	
                        $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                        break;
                    }
                    $agregar = "UPDATE eventos Set FECHA = '$hora_y_fecha' WHERE ID = '$id'";
                    $respuesta = mysqli_query($conexion, $agregar);
                    if(!$respuesta){
                        echo "Error al actializar";
                        fwrite($file, "Ye respondio el topic de 2 veces". PHP_EOL);
                    }else{
                        echo "Actualizacion con exito";
                        fwrite($file, "Se preseto un error en el topic". PHP_EOL);
                    }                  
                }elseif($frecuencia == "Mensual"){
                    $diasdmes = date( "t", strtotime($hora_y_fecha));
                    $fecha_det1 = explode("-",$hora_y_fecha);
                    $visitames = $fecha_det1[1];              
                    $cont = 0;
                    $dia = date("N", strtotime($hora_y_fecha));                        
                    $visitadia2 = $fecha_det1[2];                                               
                    $visitadia = ($visitadia2) - (1);
                    $fecha_resta = strtotime('-'.$visitadia.'day',strtotime($hora_y_fecha));
                    $fecha_resta = date('Y-m-j',$fecha_resta);                        
                    for ($i = 1; $i <= $diasdmes; $i++){
                        $diadelasemana = date("N", strtotime($fecha_resta));
                        if($diadelasemana == $dia){
                            $cont++;
                        }
                        $fecha_resta = strtotime( '+1 day' , strtotime($fecha_resta));
                        $fecha_resta = date( 'Y-m-j' , $fecha_resta );            	        
                    }
                    $numerodia=$cont;
                    if($numerodia == 5){
                        if($fecha_det1[2] <=28){
                            $hora_y_fecha = strtotime('+35 day',strtotime($hora_y_fecha));	
                            $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                            break;
                        }else if($fecha_det1[2] >=29){
                            $hora_y_fecha=strtotime('+28 day',strtotime($hora_y_fecha));	
                            $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                            break;
                        }
                    }else if($numerodia == 4){
                        $hora_y_fecha=strtotime('+28 day',strtotime($hora_y_fecha));	
                        $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                        break;
                    }
                    $agregar = "UPDATE eventos Set FECHA = '$hora_y_fecha' WHERE ID = '$id'";
                    $respuesta = mysqli_query($conexion, $agregar);
                    if(!$respuesta){
                        echo "Error al actializar";
                        fwrite($file, "Ye respondio el topic de mensual". PHP_EOL);
                    }else{
                        echo "Actualizacion con exito";
                        fwrite($file, "Se preseto un error en el topic". PHP_EOL);
                    }                    
                }elseif($frecuencia == "Anual"){
                    $fecharelpica=strtotime('+364 day',strtotime($hora_y_fecha));	
                    $fecharelpica=date('Y-m-j',$fecharelpica);
                    $fecha_repl = explode("-",$fecharelpica);
                    $mesreplica = $fecha_repl[1];
		            $cont=0;
                    $contrepli=0;
                    $dia = date("N",strtotime($hora_y_fecha));
                    $diareplica = date("N",strtotime($fecharelpica));
                    $fecha_det = explode("-",$hora_y_fecha);
                    $diaactual = $fecha_det[2]; 
                    $diaactualreplica = $fecha_repl[2];
                    $mes = $fecha_det[1];
                    $visitadia = ($diaactual) - (1);
                    $viadiareplica = ($diaactualreplica) - (1);
                    $fecha_resta = strtotime('-'.$visitadia.'day',strtotime($hora_y_fecha));
                    $fecha_resta = date('Y-m-j',$fecha_resta);
                    $fresrepl = strtotime('-'.$viadiareplica.'day',strtotime($fecharelpica));
                    $fresrepl = date('Y-m-j',$fresrepl);
                    for ($j = 1; $j <= $diaactual; $j++){
                        $diadelasemana = date("N", strtotime($fecha_resta));
                        if($diadelasemana == $dia){
                            $cont++;				
                        }
                        $fecha_resta = strtotime('+1 day',strtotime($fecha_resta));	
                        $fecha_resta = date('Y-m-j',$fecha_resta);			
                    }
                    for ($p = 1; $p <= $diaactualreplica; $p++){
                        $diadelasemanareplica = date("N", strtotime($fresrepl));
                        if($diadelasemanareplica == $diareplica){
                            $contrepli++;				
                        }
                        $fresrepl = strtotime('+1 day',strtotime($fresrepl));	
                        $fresrepl = date('Y-m-j',$fresrepl);			
                    }
                    if($cont == 1){
                        $diaresta = $diaactual - 1;
                        if($diaresta <= 0){
                            $hora_y_fecha=strtotime('+371 day',strtotime($hora_y_fecha));	
                            $hora_y_fecha = date('Y-m-j',$hora_y_fecha );		
                        }else{
                            if($mesreplica == $mes){
                                $hora_y_fecha=strtotime('+364 day',strtotime($hora_y_fecha));	
                                $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                            }else{
                                $hora_y_fecha=strtotime('+371 day',strtotime($hora_y_fecha));	
                                $hora_y_fecha = date('Y-m-j',$hora_y_fecha );						
                            }
                        }
                    }else if($cont == 5){
                        $hora_y_fecha=strtotime('+357 day',strtotime($hora_y_fecha));	
                        $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                    }else if($cont == $contrepli){
                        $hora_y_fecha=strtotime('+364 day',strtotime($hora_y_fecha));	
                        $hora_y_fecha = date('Y-m-j',$hora_y_fecha );
                    }
                    else{
                        $hora_y_fecha=strtotime('+371 day',strtotime($hora_y_fecha));	
                        $hora_y_fecha = date('Y-m-j',$hora_y_fecha );	
                    }
                    $agregar = "UPDATE eventos Set FECHA = '$hora_y_fecha' WHERE ID = '$id'";
                    $respuesta = mysqli_query($conexion, $agregar);
                    if(!$respuesta){
                        echo "Error al actializar";
                        fwrite($file, "Ye respondio el topic de unica". PHP_EOL);
                    }else{
                        echo "Actualizacion con exito";
                        fwrite($file, "Se preseto un error en el topic". PHP_EOL);
                    }                    
                }                          
            }else{
                fwrite($file, "Error al mandar el mensaje!". PHP_EOL);
                echo "Error al mandar el mensaje!!";
            }
            if($fila['CORREO_SINO'] == 'SI'){
                $mail = "Se a cambiado el valor de $sensor a $valor el dia $fecha";
                $titulo = "$valor!";
                $headers = "MIME-Version: 1.0\r\n"; 
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
                $headers .= "From: Control de automatizacion < https://encenderfoco.000webhostapp.com/ >\r\n";
                $bool = mail("betoescobedo34@gmail.com",$titulo,$mail,$headers);
                if($bool){
                    echo "Mensaje enviado";
                    fwrite($file, "Correo enviado del topic". PHP_EOL);
                }else{
                    echo "Mensaje no enviado";
                }
            }
        }
    }
fclose($file);
mysqli_close($conexion);
?>