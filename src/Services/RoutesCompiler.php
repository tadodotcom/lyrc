<?php
namespace Tado\Lyrc\Services;

use Exception;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Filesystem\Filesystem;

class RoutesCompiler
{

    protected $parser;
    protected $files;
    protected $routesBuilder;

    protected $config;

    public function __construct(Yaml $parser, Filesystem $files, YamlRoutesToLaravel $routesBuilder)
    {
        $this->parser = $parser;
        $this->files = $files;
        $this->routesBuilder = $routesBuilder;

        $this->loadConfiguration();
    }

    public function compile($versioned)
    {
        return $this
            ->parseRoutes()
            ->writeFiles($versioned);
    }

    protected function writeFiles($versioned = false)
    {
        // get stub
        $stub = $this->files->get(__DIR__.'/stubs/routesFile.stub');

        //replace on stub
        $stub = str_replace(
            '{{routes_content}}',
            $this->routesBuilder->getLaravelRoutes($this->config, $this->routes),
            $stub
        );

         return $this->createFile($stub, $versioned);
    }

    public function createFile($stub, $versioned)
    {
        if (!$this->files->exists('routes')) {
            $this->files->makeDirectory('routes');
        }

        $fileName = $versioned
                    ? date('Y_m_d_His') . '_yaml_compiled_routes.php'
                    : 'yaml_compiled_routes.php';

        $filePath = 'routes/' . $fileName;

        if ($this->files->put($filePath, $stub)) {
            return $filePath;
        }
    }

    protected function parseRoutes()
    {
        $this->routes = Yaml::parseFile($this->config['source_file_path']);

        return $this;
    }

    protected function loadConfiguration()
    {
        $this->config = config('lyrc');

        if (!isset($this->config['source_file_path'])) {
            throw new Exception("Please specify the routes file in the config path", 1);
        }

        if (!file_exists($this->config['source_file_path'])) {
            throw new Exception("Please make sure the routes file actually exists", 1);
        }
    }
}
