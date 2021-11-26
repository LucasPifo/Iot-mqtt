<?php
    require "conexion.php";
    
    $query = "SELECT ID, SENSOR, FRECUENCIA, FECHA, VALOR, ESTATUS FROM eventos ORDER BY FECHA ASC";
    $resultado = mysqli_query($conexion, $query);
    if(!$resultado){
        "Error al mostrar los datos";
    }else{
        $JSON = [];
        while($fila = mysqli_fetch_array($resultado)){
            $JSON[] = array(
                'id' => $fila['ID'],
                'sensor' => $fila['SENSOR'],
                'frecuencia' => $fila['FRECUENCIA'],
                'fecha' => $fila['FECHA'],
                'valor' => $fila['VALOR'],
                'estatus' => $fila['ESTATUS']
            );
        }
        $jsonString = json_encode($JSON);
        echo $jsonString;
    }
?>