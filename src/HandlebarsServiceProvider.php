<?php namespace ProAI\Handlebars;

use Illuminate\Support\ServiceProvider;
use ProAI\Handlebars\Engines\HandlebarsEngine;
use ProAI\Handlebars\Support\LightnCandy;
use ProAI\Handlebars\Compilers\HandlebarsCompiler;
use ProAI\Handlebars\Compilers\BladeCompiler;

class HandlebarsServiceProvider extends ServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();

        $this->registerEngineResolverExtensions();

        $this->registerFileExtensions();
    }

    /**
     * Register the view engine resolver extension.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $configPath = __DIR__ . '/../config/handlebars.php';

        $this->mergeConfigFrom($configPath, 'handlebars');

        $this->publishes([$configPath => config_path('handlebars.php')], 'config');
    }

    /**
     * Register the view engine resolver extension.
     *
     * @return void
     */
    protected function registerEngineResolverExtensions()
    {
        $this->app->extend('view.engine.resolver', function ($resolver, $app) {
            $this->registerBladeEngine($resolver);

            $this->registerHandlebarsEngine($resolver);

            return $resolver;
        });
    }

    /**
     * Register the Blade engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver $resolver
     * @return void
     */
    public function registerBladeEngine($resolver)
    {
        $app = $this->app;

        // The Compiler engine requires an instance of the CompilerInterface, which in
        // this case will be the Blade compiler, so we'll first create the compiler
        // instance to pass into the engine so it can compile the views properly.
        $app->singleton('blade.compiler', function ($app) {
            $cache = $app['config']['view.compiled'];

            return new BladeCompiler($app['files'], $cache);
        });
    }

    /**
     * Register the mustache engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver $resolver
     * @return void
     */
    public function registerHandlebarsEngine($resolver)
    {
        $app = $this->app;

        // The Compiler engine requires an instance of the CompilerInterface, which in
        // this case will be the Handlebars compiler, so we'll first create the compiler
        // instance to pass into the engine so it can compile the views properly.
        $app->singleton('handlebars.lightncandy', function ($app) {
            return new LightnCandy;
        });

        $app->singleton('handlebars.compiler', function ($app) {
            $cache = $app['config']['view.compiled'];

            return new HandlebarsCompiler($app['files'], $app['handlebars.lightncandy'], $cache);
        });

        $resolver->register('handlebars', function () use ($app) {
            return new HandlebarsEngine($app['handlebars.compiler']);
        });
    }

    /**
     * Register the file extensions.
     *
     * @return void
     */
    protected function registerFileExtensions()
    {
        $this->app->extend('view', function ($env, $app) {
            $fileexts = $app['config']['handlebars.fileext'];

            foreach ($fileexts as $fileext) {
                $env->addExtension(trim($fileext, '.'), 'handlebars');
            }

            return $env;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['handlebars'];
    }

}
