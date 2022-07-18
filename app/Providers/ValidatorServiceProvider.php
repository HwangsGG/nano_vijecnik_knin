<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extendDependent('custom_extension', function($attribute, $value, $parameters, $validator) {
            $ext = $value->getClientOriginalExtension();

            Log::warning($ext);
            if($ext==$parameters[0]){

                    return true;
            }else{
                return false;
            }

        });
    }
}
