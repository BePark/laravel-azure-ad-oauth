<?php

namespace Metrogistics\AzureSocialite;

use SocialiteProviders\Manager\SocialiteWasCalled;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/azure-oath.php' => config_path('azure-oath.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/config/azure-oath.php', 'azure-oath'
        );

        foreach(config('azure-oath.instances') as $name => $instance)
        {
	        $this->app['Laravel\Socialite\Contracts\Factory']->extend($name, function($app) use ($instance) {
		        return $app['Laravel\Socialite\Contracts\Factory']->buildProvider(
			        'Metrogistics\AzureSocialite\AzureOauthProvider',
			        $instance['credentials']
		        );
	        });

	        $this->app['router']->group(['middleware' => $instance['routes']['middleware']], function($router){
		        $router->get($instance['routes']['login'], ($instance['auth_controller'] ?? 'Metrogistics\AzureSocialite\AuthController') . '@redirectToOauthProvider');
		        $router->get($instance['routes']['callback'], ($instance['auth_controller'] ?? 'Metrogistics\AzureSocialite\AuthController') . '@handleOauthResponse');
	        });
        }

    }
}
