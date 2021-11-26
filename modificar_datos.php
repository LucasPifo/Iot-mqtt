<?php
if($_POST['foco'] || $_POST['dimmer']){
    $archivo = "datos.txt";
    $foco = $_POST['foco'];
    $dimmer = $_POST['dimmer'];
    
    $estatus = array("foco"=>$foco, "dimmer"=>$dimmer);
    $JSON = json_encode($estatus);
    $f = fopen($archivo, "w");
    if($f){
        fwrite($f, $JSON);
    }
    echo $JSON;
}
?>