<?php
    const CodeSuccess = 0;
    const CodeUnautorized = 401;
    const CodeExpired = 440;
    const CodeNotFound = 404;
    const CodeError = 99;

    const EstadoEnProceso = 1;
    const EstadoActivo = 2;
    const EstadoInactivo = 3;
    const OpcionesPorDefecto = 2;

    const RutaImagenDefault = "/imagenes/Seleccionar imagen.png";
    const votacion = "votacion";
    const idsOpcionEliminar = "idsOpcionEliminar";
    const opcionSeleccionada = "opcionSeleccionada";
    const votacionCreate = "/votacion/create";
    const votacionEdit = "/votacion/%d/edit";
    const votacionIndex = "/votacion";
    const votanteIndex = "/votante";
    const votanteVotar = "/votante/%d/votar";
    
    const destinyCreate = 'create';
    const destinyEdit= 'edit';
    const destiny = 'destiny';

    const EnProceso = 1;
    const Activo = 2;
    const Inactivo = 3;

    const FormatoFechaHoraDB = 'Y-m-d\TH:i';
    const FormatoFechaHoraCR = 'd-m-y H:i';
    const CostaRicaTimeZone = 'America/Costa_Rica';

    const Si = "Si";
    const No = "No";
?>