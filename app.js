$(document).on('input', '#intencidad', function() {
    $('#estatusdimmer').html($(this).val());
});

$(document).ready(() => {
    let usuario = '';
    let contrasena = '';
    let puerto = 0;
    let clientId = "ws" + Math.random();
    let client = new Paho.MQTT.Client("", puerto, clientId);
    let topic_general = `/${usuario}/Instruccion`;
    let comando;
    let mensaje;
    let argumento;
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

    $('#on').click(() => {
        encender_apagar('1');
    });
    $('#off').click(() => {
        encender_apagar('0');
    });
    $('#intencidad').change(() => {
        dimmer($('#estatusdimmer').text());
    });
    $('#C_on_off').click(() => {
        encender_apagar_clima('0');
    });
    $('#C_mas').click(() => {
        subir_clima('1');
    });
    $('#C_menos').click(() => {
        bajar_clima('2');
    });
    $('#C_modo').click(() => {
        modo_clima('3');
    });
    $('#C_aspas').click(() => {
        aspas_clima('4');
    });
    
    let onConnect = () => {
        console.log("onConnect");
        client.subscribe("#");
    }
    
    let onConnectionLost = (responseObject) => {
        if(responseObject.errorCode !== 0){
            console.log("onConnectionLost:", responseObject.errorMessage);
            setTimeout(() => {client.connect()}, 5000);
        }
    } 
    
    let onMessageArrived = (message) => {
        if(message.destinationName == topic_general){
            let array_message = message.payloadString.split(',');
            if(array_message[0] == "0"){
                let respuesta = array_message[1] == '0' ? 'Apagado' : 'Encendido';
                $("#estatusfoco").text(respuesta);
                on_of(respuesta);
            }
            if(array_message[0] == "1"){
                $("#estatusdimmer").text(array_message[1]);
                $('#intencidad').val(array_message[1]);
                intencidad_dimmer(array_message[1]);
            }
            $.ajax({
                url : 'modificar_datos.php',
                type : 'POST',
                data : {foco : $('#estatusfoco').text(), dimmer : $('#estatusdimmer').text()},
                success : (response) => {
                    console.log("datos.txt se modifico")
                }
            });
            vissualizar();
        }        
    }
    
    let onFailure = (invocationContext, errorCode, errorMessage) => {
        console.log = `Error en la conexion del puerto :${puerto}`;
    }  
    
    client.onConnectionLost = onConnectionLost;
    client.onMessageArrived = onMessageArrived;
    client.connect({
        useSSL: true,
        userName: usuario,
        password: contrasena,
        onSuccess: onConnect,
        onFailure: onFailure
    }); 

    let encender_apagar = (dato) =>{
        argumento = dato;
        comando = "0";
        mensaje = `${comando},${argumento},`;
        message = new Paho.MQTT.Message(mensaje);
        message.destinationName = topic_general;
        client.send(message);
    };
    let dimmer = (dato) => {
        argumento = dato;
        comando = "1";
        mensaje = `${comando},${argumento},`;
        message = new Paho.MQTT.Message(mensaje);
        message.destinationName = topic_general;
        client.send(message);
    }; 
    
    let encender_apagar_clima = (dato) =>{
        argumento = dato;
        comando = "2";
        mensaje = `${comando},${argumento},`;
        message = new Paho.MQTT.Message(mensaje);
        message.destinationName = topic_general;
        client.send(message);
    };
    
    let subir_clima = (dato) =>{
        argumento = dato;
        comando = "2";
        mensaje = `${comando},${argumento},`;
        message = new Paho.MQTT.Message(mensaje);
        message.destinationName = topic_general;
        client.send(message);
    };
    
    let bajar_clima = (dato) =>{
        argumento = dato;
        comando = "2";
        mensaje = `${comando},${argumento},`;
        message = new Paho.MQTT.Message(mensaje);
        message.destinationName = topic_general;
        client.send(message);
    };
    
    let modo_clima = (dato) =>{
        argumento = dato;
        comando = "2";
        mensaje = `${comando},${argumento},`;
        message = new Paho.MQTT.Message(mensaje);
        message.destinationName = topic_general;
        client.send(message);
    };
    
    let aspas_clima = (dato) =>{
        argumento = dato;
        comando = "2";
        mensaje = `${comando},${argumento},`;
        message = new Paho.MQTT.Message(mensaje);
        message.destinationName = topic_general;
        client.send(message);
    };    
    
    let obtener_datos = () => $.ajax({
        url : 'obtener_datos.php',
        type : 'GET',
        success : (response) => {
            let estatus = JSON.parse(response);
            $("#estatusfoco").text(estatus.foco);
            $("#estatusdimmer").text(estatus.dimmer);
            $('#intencidad').val(estatus.dimmer);
            on_of(estatus.foco);
            intencidad_dimmer(estatus.dimmer);
        }
    });
    obtener_datos();
    
    let on_of = (valor) => {
        if(valor == "Encendido"){
            $('#on').attr("disabled", true);
            $('#off').attr("disabled", false);
            $('#icono_foco').css('color', 'rgb(255,210,0)');
        }else{
            $('#off').attr("disabled", true);
            $('#on').attr("disabled", false);            
            $('#icono_foco').css('color', 'rgb(0,0,0)');
        }
    };
    
    let intencidad_dimmer = (valor) => {
        let r = 30;
        let g = 25;
        let b = 0;
        r = r + parseInt(valor*2.25);
        g = g + parseInt(valor*1.85);        
        let bgcolor = `rgb(${r},${g},${b})`;
        $('#icono_dimmer').css('color', bgcolor);
    };
    
    $('#activar_reconocimiento').click(() => {
        $('#activar_reconocimiento').removeClass("d-block").addClass("d-none");
        $('#desactivar_reconocimiento').addClass("d-block");
        startArtyom();
    });
    
    $('#desactivar_reconocimiento').click(() => {
        $('#desactivar_reconocimiento').removeClass("d-block").addClass("d-none");
        $('#activar_reconocimiento').addClass("d-block");
        stopArtyom();
    });
    
    artyom.addCommands([{
        indexes:["0%","10%",'20%','30%',"40%","50%","60%","70%","80%","90%","100%","Prende el foco","Apaga el foco","Prende el clima","Apaga el clima"],
        action: function(i){
            if(i==0){
                var mensaje = "Dimmer al 0% mi señor";
                var intancidad = "0";
                eleccionComando(mensaje, intancidad, 'Dimmer');
            }
            if(i==1){
                var mensaje = "Dimmer al 10% mi señor";
                var intancidad = "10";
                eleccionComando(mensaje, intancidad, 'Dimmer');
            }
            if(i==2){
                var mensaje = "Dimmer al 20% mi señor";
                var intancidad = "20";
                eleccionComando(mensaje, intancidad, 'Dimmer');
            }
            if(i==3){
                var mensaje = "Dimmer al 30% mi señor";
                var intancidad = "30";
                eleccionComando(mensaje, intancidad, 'Dimmer');
            }
            if(i==4){
                var mensaje = "Dimmer al 40% mi señor";
                var intancidad = "40";
                eleccionComando(mensaje, intancidad, 'Dimmer');
            }
            if(i==5){
                var mensaje = "Dimmer al 50% mi señor";
                var intancidad = "50";
                eleccionComando(mensaje, intancidad, 'Dimmer');
            }
            if(i==6){
                var mensaje = "Dimmer al 60% mi señor";
                var intancidad = "60";
                eleccionComando(mensaje, intancidad, 'Dimmer');
            }
            if(i==7){
                var mensaje = "Dimmer al 70% mi señor";
                var intancidad = "70";
                eleccionComando(mensaje, intancidad, 'Dimmer');
            }
            if(i==8){
                var mensaje = "Dimmer al 80% mi señor";
                var intancidad = "80";
                eleccionComando(mensaje, intancidad, 'Dimmer');
            }
            if(i==9){
                var mensaje = "Dimmer al 90% mi señor";
                var intancidad = "90";
                eleccionComando(mensaje, intancidad, 'Dimmer');
            }
            if(i==10){
                var mensaje = "Dimmer al 100% mi señor";
                var intancidad = "100";
                eleccionComando(mensaje, intancidad, 'Dimmer');
            }
            if(i==11){
                var mensaje = "Foco encendido mi señor";
                var intancidad = "1";
                eleccionComando(mensaje, intancidad, 'Foco');
            }
            if(i==12){
                var mensaje = "Foco apagado mi señor";
                var intancidad = "0";
                eleccionComando(mensaje, intancidad, 'Foco');
            }
            if(i==13){
                var mensaje = "Clima encendido mi señor";
                var intancidad = "0";
                eleccionComando(mensaje, intancidad, 'Clima');
            }
            if(i==14){
                var mensaje = "Clima apagado";
                var intancidad = "0";
                eleccionComando(mensaje, intancidad, 'Clima');
            }
        }
    }]);

    let startArtyom = () =>{
        alert("Iniciando reconocimento!");
        artyom.initialize({
            lang: "es-ES",
            continuous:true,
            listen:true, 
            debug:true,
            speed:0.9 

        });
        $('#intencidad').attr("disabled", true);
    };

    let stopArtyom = () =>{
        alert("Deteniendo reconocimento!");
        artyom.fatality();
        $('#intencidad').attr("disabled", false);
    };

    function eleccionComando(mensaje, intencidad, sensor){
        if(sensor == "Dimmer"){
            artyom.say(mensaje);
            dimmer(intencidad);
        }
        if(sensor == "Foco"){
            artyom.say(mensaje);
            encender_apagar(intencidad);
        }
        if(sensor == "Clima"){
           artyom.say(mensaje);
            encender_apagar_clima(intencidad);
        }
    };
});

window.onload = () => {
    let el = document.querySelector('[alt="www.000webhost.com"]').parentNode.parentNode;
    el.parentNode.removeChild(el);
}