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

    Route::dispatch();
?>
