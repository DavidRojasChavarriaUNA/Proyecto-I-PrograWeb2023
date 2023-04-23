SeleccionarArchivo = function(idElemento){
    var elemento = document.getElementById(idElemento);
    elemento.click();
}

MostrarGuardarExitoso = function() {
    var myAlert =document.getElementById('GuardadoExitoso');
    var bsAlert = new bootstrap.Toast(myAlert);
    bsAlert.show();
}

var elementoEliminar = null;

EliminarOpcion = function(boton){
    elementoEliminar = boton.closest(".Opcion");
    var AccionEliminar = document.getElementById("AccionEliminar");
    AccionEliminar.onclick = EjecutarAccionEliminar;
}

EliminarVotacion = function(boton){
    elementoEliminar = boton.closest(".filaVotacion");
    var AccionEliminar = document.getElementById("AccionEliminar");
    AccionEliminar.onclick = EjecutarAccionEliminar;
}

EjecutarAccionEliminar = function(){
    elementoEliminar.remove(); 
    elementoEliminar = null;
}

var elementoActivarDesactivar = null;
var botonMostrar = null;
var botonOcultar = null;

DesactivarVotacion = function(idElemento,idBotonMostrar,idBotonOcultar){
    elementoActivarDesactivar = document.getElementById(idElemento);
    botonMostrar = document.getElementById(idBotonMostrar);
    botonOcultar = document.getElementById(idBotonOcultar);
    var AccionDesactivar = document.getElementById("AccionDesactivar");
    AccionDesactivar.onclick = EjecutarAccionDesactivarVotacion;
}

ActivarVotacion = function(idElemento,idBotonMostrar,idBotonOcultar){
    elementoActivarDesactivar = document.getElementById(idElemento);
    botonMostrar = document.getElementById(idBotonMostrar);
    botonOcultar = document.getElementById(idBotonOcultar);
    var AccionActivar = document.getElementById("AccionActivar");
    AccionActivar.onclick = EjecutarAccionActivarVotacion;
}

EjecutarAccionDesactivarVotacion = function(){
    elementoActivarDesactivar.innerText = "Inactivo"; 
    elementoActivarDesactivar = null;
    botonOcultar.classList.add("display-none");
    botonOcultar = null;
    botonMostrar.classList.remove("display-none");
    botonMostrar = null;
}

EjecutarAccionActivarVotacion = function(){
    elementoActivarDesactivar.innerText = "Activo"; 
    elementoActivarDesactivar = null;
    botonOcultar.classList.add("display-none");
    botonOcultar = null;
    botonMostrar.classList.remove("display-none");
    botonMostrar = null;
}

NuevaOpcion = function(idContenedorLista, boton){
    var elementoAlFinal = boton.closest(".NuevaOpcion");
    var plantilla = document.getElementById("plantilla");
    var nuevo = plantilla.cloneNode(true);
    nuevo.removeAttribute("id");
    var contenedorlista = document.getElementById(idContenedorLista);
    contenedorlista.insertBefore(nuevo,elementoAlFinal);
}