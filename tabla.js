let seleccionado = 0;

function fnProcesaPaciente(comp){
  seleccionado = comp.id;
  console.log(seleccionado);
}

let vissualizar = () =>{
    $.ajax({
        url: 'visualizar.php',
        type: 'GET',
        success: function(response){
            let eventos = JSON.parse(response);
            let datos = '';
            let i;
            for(i in eventos){
                console.log(eventos[i].estatus);
                datos += `<tr>
                    <td>${eventos[i].sensor}</td>
                    <td>${eventos[i].frecuencia}</td>
                    <td>${eventos[i].fecha}</td>
                    <td>${eventos[i].valor}</td>`;
                if(eventos[i].estatus == "Habilitado"){
                    datos += `<td><button type='button' class='btn btn-info borrar-evento fuente' id='${eventos[i].id}' data-toggle="modal" data-target="#modalcancelar" data-whatever="@getbootstrap" onclick='fnProcesaPaciente(this)'>Editar</button></td></tr>`;                    
                }else{
                    datos += `<td><button type='button' class='btn btn-danger borrar-evento fuente' id='${eventos[i].id}' data-toggle="modal" data-target="#modalcancelar" data-whatever="@getbootstrap" onclick='fnProcesaPaciente(this)'>Editar</button></td></tr>`;
                }
            }
            $('#filas_eventos').html(datos);
        }
    });
}
vissualizar();

$('#form_inhabilitar').submit(function(e){
    console.log(seleccionado);
    e.preventDefault();
    const datos = {
        id : seleccionado
    }
    $.post('inhabilitar.php', datos, (response)=>{
        console.log(response);
        vissualizar();
        $("#modalcancelar").modal("hide");
    });
});

$('#form_borrar').submit(function(e){
    console.log(seleccionado);
    e.preventDefault();
    const datos = {
        id : seleccionado
    }
    $.post('borrar.php', datos, (response)=>{
        console.log(response);
        vissualizar();
        $("#modalcancelar").modal("hide");
    });
});
                         
$('#form_habilitar').submit(function(e){
    console.log(seleccionado);
    e.preventDefault();
    const datos = {
        id : seleccionado
    }
    $.post('habilitar.php', datos, (response)=>{
        console.log(response);
        vissualizar();
        $("#modalcancelar").modal("hide");
    });
});