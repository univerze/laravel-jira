<?php

namespace Univerze\Jira;

use Illuminate\Support\ServiceProvider;

class JiraServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes( [
            __DIR__ . '/config/jira.php' => config_path( 'jira.php' ),
        ] );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom( __DIR__ . '/config/jira.php', 'jira' );

        $this->app['jira'] = $this->app->singleton( 'jira', function ( $app )
        {
            return new Jira;
        } );
    }
}
