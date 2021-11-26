<?php
    require "conexion.php";
    if(isset($_POST['valor'])){
        $sensor = $_POST['sensor'];
        $frecuencia = $_POST['frecuencia'];
        $cada = $_POST['cada'];
        $fecha = $_POST['fecha'];
        $valor = $_POST['valor'];
        $correo_sino = $_POST['correo_sino'];
        
        $query = "INSERT INTO eventos(SENSOR, FRECUENCIA, CADA, FECHA, VALOR, ESTATUS, CORREO_SINO)VALUES('$sensor', '$frecuencia', $cada, '$fecha', '$valor', 'Habilitado','$correo_sino')";
        $resultado = mysqli_query($conexion, $query);
        if(!$resultado){
            echo "Error al insertar";
        }else{
            echo "Agregado con exito";
        }
    }
?>