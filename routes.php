<?php
    
    Route::get('/', 'SitioPublicidadController@index');
    Route::get('/seguridad/showLoginForm', 'SeguridadController@showLoginForm');
    Route::post('/seguridad/autenticate', 'SeguridadController@autenticate');
    Route::get('/seguridad/loginNotAuthorized', 'SeguridadController@loginNotAuthorized');
    Route::get('/seguridad/logout', 'SeguridadController@logout');
    Route::get('/sitioInterno', 'SitioInternoController@Home');
    Route::get('/seguridad/showRegistrationForm', 'SeguridadController@showRegistrationForm');
    Route::post('/seguridad/register','SeguridadController@register'); 
    Route::get('/seguridad/registrationError', 'SeguridadController@registrationError');
    
    Route::resource('/votacion', 'VotacionController');
    Route::post('/votacion/newOption', 'VotacionController@newOption');
    Route::post('/votacion/removeOption','VotacionController@removeOption');

    Route::get('/votacion/edit(:number)','VotacionController@edit');  
    Route::get('/votacion/(:number)/delete','VotacionController@destroy');
    Route::get('/votacion/estado/(:number)/(:number)','VotacionController@cambiarEstado');
    Route::get('/votacion/(:number)/desactivar','VotacionController@cambiarEstado');
    
    Route::get('/votante', 'VotanteController@index');
    Route::get('/votante/(:number)/votar', 'VotanteController@votar');

    Route::get('/resultados', 'ResultadosController@index');
    Route::get('/resultados/(:number)/votar', 'ResultadosController@votar');

    Route::dispatch();
?>
