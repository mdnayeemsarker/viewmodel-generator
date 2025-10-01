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

        // if no models, just return
        $modelsOption = $this->option('model');
        if (empty($modelsOption)) {
            // cleanup possible placeholders
            $stub = str_replace(['// DummyImports', "// DummyConstructorParams"], ['', ''], $stub);
            return $stub;
        }

        $models = array_map('trim', explode(',', $modelsOption));
        $isCollection = (bool) $this->option('collection');

        $imports = [];
        $constructorParams = [];

        foreach ($models as $model) {
            // allow namespaced model like Admin\\User
            $modelClass = ltrim(str_replace('/', '\\', $model), '\\');
            $baseModel = class_basename($modelClass);

            // import App\Models\... unless model already contains a full namespace (contains \\)
            if (Str::contains($modelClass, '\\')) {
                // user provided namespace: use as-is
                $imports[] = "use {$modelClass};";
            } else {
                $imports[] = "use App\\Models\\{$baseModel};";
            }

            if ($isCollection) {
                $constructorParams[] = 'public \Illuminate\Support\Collection $' . Str::camel(Str::plural($baseModel));
            } else {
                $constructorParams[] = 'public ' . $baseModel . ' $' . Str::camel($baseModel);
            }
        }

        // ensure Collection import is present when collection flag used
        if ($isCollection) {
            array_unshift($imports, 'use Illuminate\\Support\\Collection;');
        }

        // unique imports and collapse to string
        $imports = array_unique($imports);
        $importsText = implode("\n", $imports);

        // constructor signature
        $constructorText = implode(', ', $constructorParams);

        // replace placeholders
        $stub = str_replace('// DummyImports', $importsText, $stub);
        $stub = str_replace('// DummyConstructorParams', $constructorText, $stub);

        return $stub;
    }
}