$('#form_agregar').submit(function(e){
    e.preventDefault();
    const datos = {
        sensor : $('#sensor').val(),
        frecuencia : $('#frecuencia').val(),
        cada : $('#cada').val(),
        fecha : $('#fecha').val(),
        valor : $('#valor').val(),
        correo_sino : $('#correo_sino').val()
    }
    $.post('agregar_evento.php', datos, (response)=>{
        console.log(response);
        vissualizar();
    });
    $("#exampleModal").modal("hide");
    limpiar_campos();
});
$('#cancelar_evento').click(() =>{
    limpiar_campos();
});
$('#cerrar_evento').click(() =>{
    limpiar_campos();
});

let limpiar_campos = () =>{
    $('#sensor').val("Dimmer");
    $('#frecuencia').val("Unica");
    $('#cada').val("");
    $('#fecha').val("");
    $('#valor').val("");
}