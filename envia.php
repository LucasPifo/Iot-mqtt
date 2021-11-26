<?php
ini_set('date.timezone','America/Mexico_City');
$hora_actual = date("G:i");
echo "La hora es: ".$hora_actual."<br><br>";
require("phpMQTT.php");
$host = "";
$port = 0;
$username = "";
$password = "";

$fp = fopen("txt/estatusDimmer.txt", "r");
$actual = fgets($fp);
$actual = (int) $actual;
fclose($fp);
if($actual == 0){
    $archivo_estatus = "txt/Intencidad_estatus";
    $valor="Incremento";
    $fila = fopen($archivo_estatus, "w+");
    if($fila){
        fwrite($fila, $valor);
    }
    fclose($archivo_estatus);
}
if($actual == 100){
    $archivo_estatus = "txt/Intencidad_estatus";
    $valor="Decremento";
    $fila = fopen($archivo_estatus, "w+");
    if($fila){
        fwrite($fila, $valor);
    }
    fclose($archivo_estatus);
}

$leer_valor = fopen("txt/Intencidad_estatus", "r");
$esta = fgets($leer_valor);
fclose($leer_valor);
if($esta == "Incremento"){
    $nuevo = $actual + 20;
}else{
    $nuevo = $actual - 20;
}
$message = "0,$actual,$nuevo";
$mqtt = new bluerhinos\phpMQTT($host, $port, "ClientID".rand());
if($mqtt->connect(true,NULL,$username,$password)){
    $mqtt->publish('/'.$username.'/Dimmer',$message, 0);
    $mqtt->close();
    echo "mandando $message<br>";
}else{
  echo "Fail or time out";
}
$archivo = "txt/estatusDimmer.txt";
$f = fopen($archivo, "w+");
if($f){
    fwrite($f, "$nuevo");
}
fclose($archivo);

/*$asunto = "Aviso importante";
$cabeceras = "From: Robertron L500-X <biosinsasistema2@gmail.com>\n";
$cabeceras .= "Content-type: text/html; charset=UTF-8\r\n";
$cabeceras .= "Reply-To: betoescobedo34@gmail.com";
$para = "betoescobedo34@gmail.com";
$mensaje = "<b>Nombre:</b><br> Robertron L500-X";
mail($para,$asunto,$mensaje,$cabeceras);*/
$mail = "Algunos pagos programados no han sido pagados, favor de verificar el porque no han sido pagados.";
$titulo = "ALGUNOS PAGOS NO SE HAN REALIZADO!";
$headers = "MIME-Version: 1.0\r\n"; 
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
$headers .= "From: Control de iluminacion < https://stylesoftware.000webhostapp.com/ >\r\n";
$bool = mail("betoescobedo34@gmail.com",$titulo,$mail,$headers);
if($bool){
    echo "Mensaje enviado";
}else{
    echo "Mensaje no enviado";
}
?>