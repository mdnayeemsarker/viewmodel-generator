<?php

namespace Mdnayeemsarker\ViewModelGenerator\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeViewModel extends GeneratorCommand
{
    protected $name = 'make:viewmodel';
    protected $description = 'Create a new ViewModel class';
    protected $type = 'ViewModel';

    protected function getStub()
    {
        if ($this->option('model')) {
            return __DIR__ . '/../../stubs/viewmodel-with-models.stub';
        }

        return __DIR__ . '/../../stubs/viewmodel.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\ViewModels';
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'One or multiple models (comma separated) to inject'],
            ['collection', 'c', InputOption::VALUE_NONE, 'Inject models as Collection(s)'],
        ];
    }

    /**
     * Build the class using the parent builder then inject model imports and constructor content.
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $modelsOption = $this->option('model');

        // if no models, just clean placeholders
        if (empty($modelsOption)) {
            $stub = str_replace(['// DummyImports', '// DummyConstructorParams'], ['', ''], $stub);
            return $stub;
        }

        $models = array_map('trim', explode(',', $modelsOption));
        $isCollection = (bool) $this->option('collection');

        $imports = [];
        $constructorParams = [];

        foreach ($models as $model) {
            // handle namespaced models like Admin\User
            $modelClass = ltrim(str_replace('/', '\\', $model), '\\');
            $baseModel = class_basename($modelClass);

            // import statement
            if (Str::contains($modelClass, '\\')) {
                $imports[] = "use {$modelClass};";
            } else {
                $imports[] = "use App\\Models\\{$baseModel};";
            }

            // variable name & type
            if ($isCollection) {
                $varName = Str::camel(Str::plural($baseModel));
                $constructorParams[] = "public \\Illuminate\\Support\\Collection \${$varName}";
            } else {
                $varName = Str::camel($baseModel);
                $constructorParams[] = "public {$baseModel} \${$varName}";
            }
        }

        // add Collection import if using collections
        if ($isCollection) {
            array_unshift($imports, 'use Illuminate\\Support\\Collection;');
        }

        // unique imports
        $importsText = implode("\n", array_unique($imports));

        // constructor signature with real newlines
        $constructorText = implode(",\n        ", $constructorParams);

        // replace placeholders in stub
        $stub = str_replace('// DummyImports', $importsText, $stub);
        $stub = str_replace('// DummyConstructorParams', $constructorText, $stub);

        return $stub;
    }
}