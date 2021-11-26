<?php
    $fp = fopen("datos.txt", "r");
    while (!feof($fp)){
        $linea = fgets($fp);
        echo $linea;
    }
    fclose($fp);
?>