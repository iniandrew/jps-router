<?php

namespace Jalameta\Router\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Make Command.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class MakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new JPS route';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Route';

    /**
     * Get the default namespace for the class.
     *
     * @param $rootNameSpace
     *
     * @return string
     */
    public function getDefaultNamespace($rootNameSpace)
    {
        return $rootNameSpace.'\Http\Routes';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = null;

        if ($this->option('controller')) {
            $stub = '/../../stubs/route.controller.stub';
        } else {
            $stub = '/../../stubs/route.stub';
        }

        return __DIR__.$stub;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        if ($this->option('controller')) {
            $this->buildController();

            return str_replace(
                [
                    'DummyRootNamespace',
                    'DummyControllerName',
                    'DummyController',
                ], [
                    $this->rootNamespace(),
                    str_replace($this->type, 'Controller', $this->getNameInput()),
                    $this->getControllerClassname(),
                ], parent::buildClass($name)
            );
        }

        return parent::buildClass($name);
    }

    /**
     * Generate new controller class.
     *
     * @return void
     */
    protected function buildController()
    {
        $this->call('make:controller', [
            'name' => str_replace($this->type, 'Controller', $this->getNameInput()),
        ]);
    }

    /**
     * Get Controller class name without namespace.
     *
     * @return string
     */
    protected function getControllerClassname()
    {
        $class = str_replace($this->getNamespace($this->getNameInput()).'\\', '', $this->getNameInput());

        return str_replace($this->type, 'Controller', $class);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['controller', 'c', InputOption::VALUE_NONE, 'Generate controller accompanying route class.'],
            ['inject', 'j', InputOption::VALUE_OPTIONAL, 'Automatically inject route into registered array.']
        ];
    }
}
