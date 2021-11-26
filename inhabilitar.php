<?php
    require "conexion.php";

    if(isset($_POST['id'])){
        $id = $_POST['id'];
        $query = "Update eventos Set ESTATUS='Inhabilitado' WHERE ID='$id'";
        $resultado = mysqli_query($conexion, $query);
        if(!$resultado){
            echo "Error al actializar";
        }else{
            echo "Actualizacion con exito";
        }
    }else{
        echo("No hay nada");
    }
?>