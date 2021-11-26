artyom.addCommands([{
    indexes:["10%",'20%','30%',"40%","50%","60%","70%","80%","90%","100%","Prende el foco","apágalo","esclavo"],
    action: function(i){
        if(i==0){
            var mensaje = "Foco al 10% mi señor";
            var intancidad = "10";
            eleccionComando(mensaje, intancidad);
        }
        if(i==1){
            var mensaje = "Foco al 20% mi señor";
            var intancidad = "20";
            eleccionComando(mensaje, intancidad);
        }
        if(i==2){
            var mensaje = "Foco al 30% mi señor";
            var intancidad = "30";
            eleccionComando(mensaje, intancidad);
        }
        if(i==3){
            var mensaje = "Foco al 40% mi señor";
            var intancidad = "40";
            eleccionComando(mensaje, intancidad);
        }
        if(i==4){
            var mensaje = "Foco al 50% mi señor";
            var intancidad = "50";
            eleccionComando(mensaje, intancidad);
        }
        if(i==5){
            var mensaje = "Foco al 60% mi señor";
            var intancidad = "60";
            eleccionComando(mensaje, intancidad);
        }
        if(i==6){
            var mensaje = "Foco al 70% mi señor";
            var intancidad = "70";
            eleccionComando(mensaje, intancidad);
        }
        if(i==7){
            var mensaje = "Foco al 80% mi señor";
            var intancidad = "80";
            eleccionComando(mensaje, intancidad);
        }
        if(i==8){
            var mensaje = "Foco al 90% mi señor";
            var intancidad = "90";
            eleccionComando(mensaje, intancidad);
        }
        if(i==9){
            var mensaje = "Foco al 100% mi señor";
            var intancidad = "100";
            eleccionComando(mensaje, intancidad);
        }
        if(i==10){
            var mensaje = "Foco encendido mi señor";
            var intancidad = "100";
            eleccionComando(mensaje, intancidad);
        }
        if(i==11){
            var mensaje = "Foco apagado mi señor";
            var intancidad = "0";
            eleccionComando(mensaje, intancidad);
        }
        if(i==12){
           artyom.say("Que es lo que desea mi señor?");
        }
    }
}]); 

function startArtyom(){
    alert("Iniciando reconocimento!");
    artyom.initialize({
        lang: "es-ES",
        continuous:true,// Reconoce 1 solo comando y para de escuchar}
        listen:true, // Iniciar !
        debug:true, // Muestra un informe en la consola
        speed:0.9 // Habla normalmente

    });
    document.getElementById('slider').disabled = true;
};
		
function stopArtyom(){
    alert("Deteniendo reconocimento!");
    artyom.fatality();// Detener cualquier instancia previa
    document.getElementById('slider').disabled = false;
};

function eleccionComando(mensaje, intencidad){
    artyom.say(mensaje);
    slider.value = intencidad;
    porcentaje.innerHTML = intencidad;
    mandarValor();
}
/*
function eleccionComando(mensaje, funcion){
    artyom.say(mensaje);
    output.innerHTML = parseInt(funcion);
    ocultoEstatus.value = parseInt(funcion);
    nuevoValor.innerHTML = parseInt(funcion);
    slider.value = parseInt(valorActual.textContent);
    slider.oninput = parseInt(valorActual.textContent);
        
    var actual = parseInt(valorActual.textContent);
    var nuevo = parseInt(output.textContent);
    var comandoDimmer = "0,"+actual+","+nuevo;
    enviarDimmer(comandoDimmer);
}*/