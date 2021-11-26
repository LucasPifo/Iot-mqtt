<?php
    require "conexion.php";

    if(isset($_POST['id'])){
        $id = $_POST['id'];
        $query = "DELETE FROM eventos WHERE ID = $id";
        $resultado = mysqli_query($conexion, $query);
        if(!$resultado){
            echo "Error al borrar";
        }else{
            echo "Borrado con exito";
        }
    }else{
        echo("No hay nada");
    }
?>