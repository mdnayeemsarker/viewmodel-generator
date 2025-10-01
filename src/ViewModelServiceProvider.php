<?php

namespace Mdnayeemsarker\ViewModelGenerator;

use Illuminate\Support\ServiceProvider;
use Mdnayeemsarker\ViewModelGenerator\Commands\MakeViewModel;

class ViewModelServiceProvider extends ServiceProvider
{
    public function register()
    {
        // no bindings required
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // register command
            $this->commands([
                MakeViewModel::class,
            ]);

            // offer stubs for publish
            $this->publishes([
                __DIR__ . '/../stubs/viewmodel.stub' => base_path('stubs/viewmodel.stub'),
                __DIR__ . '/../stubs/viewmodel-with-models.stub' => base_path('stubs/viewmodel-with-models.stub'),
            ], 'viewmodel-stubs');
        }
    }
}