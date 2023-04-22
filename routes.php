<?php
    
    Route::get('/', 'SitioPublicidadController@index');
    Route::get('/seguridad/register', 'SeguridadController@register');
    Route::get('/seguridad/login', 'SeguridadController@login');

    Route::dispatch();
?>
