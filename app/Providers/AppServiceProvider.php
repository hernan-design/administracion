<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use Carbon\Carbon;

use App\Models\Param\Institucion;

use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Carbon
        Carbon::setLocale(config('app.locale'));

        // Validate ReCaptcha
        Validator::extend('recaptcha', function ($attribute, $value, $parameters) 
        {
            $institution = Institucion::findOrFail(session('institution')->codigo_institucion);
            $client = new Client();
            $response = $client->post(
                'https://www.google.com/recaptcha/api/siteverify', [
                    'form_params'=> [
                        'secret' => $institution->clave_secreta_captcha ?: config('tuproyecto.grecatcha_secret_key'),
                        'response' => $value
                    ]
                ]
            );        
            $body = json_decode((string)$response->getBody());
            return $body->success;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    
    }
}
