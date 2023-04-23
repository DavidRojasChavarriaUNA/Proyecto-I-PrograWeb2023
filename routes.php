<?php
    
    Route::get('/', 'SitioPublicidadController@index');
    Route::get('/seguridad/register', 'SeguridadController@register');
    Route::get('/seguridad/showLoginForm', 'SeguridadController@showLoginForm');
    Route::post('/seguridad/autenticate', 'SeguridadController@autenticate');
    Route::get('/seguridad/loginNotAuthorized', 'SeguridadController@loginNotAuthorized');
    Route::get('/seguridad/logout', 'SeguridadController@logout');
    Route::get('/sitioInterno', 'SitioInternoController@Home');
    Route::dispatch();
?>
