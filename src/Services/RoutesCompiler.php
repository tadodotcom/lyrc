<?php

namespace Tado\Lyrc\Services;

use Exception;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Filesystem\Filesystem;

/**
 * Parses the yaml file, set configurations and creates routes files
 *
 * Class RoutesCompiler
 * @property mixed routes
 * @package Tado\Lyrc\Services
 */
class RoutesCompiler
{
    /**
     * The yaml parser
     *
     * @var Yaml
     */
    protected $parser;

    /**
     * The Illuminate\Filesystem manager
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The routes builder
     *
     * @var YamlRoutesToLaravel
     */
    protected $routesBuilder;

    /**
     * Holds the array in config/lyrc.php
     *
     * @var array
     */
    protected $config;

    /**
     * RoutesCompiler constructor. Initializes attributes by dependency injection.
     *
     * @param Yaml $parser
     * @param Filesystem $files
     * @param YamlRoutesToLaravel $routesBuilder
     * @throws Exception
     */
    public function __construct(Yaml $parser, Filesystem $files, YamlRoutesToLaravel $routesBuilder)
    {
        $this->parser = $parser;
        $this->files = $files;
        $this->routesBuilder = $routesBuilder;

        $this->loadConfiguration();
    }

    /**
     * Compiles given the specified configurations
     *
     * @return string File path of the originated php compiled file
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function compile()
    {
        return $this
            ->parseRoutes()
            ->writeFiles();
    }

    /**
     * Gets content from stub, replace space holders by
     * the compile routes and write them on the file
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function writeFiles()
    {
        // get stub
        $stub = $this->files->get(__DIR__.'/stubs/routesFile.stub');

        //replace on stub
        $stub = str_replace(
            '{{routes_content}}',
            $this->routesBuilder->getLaravelRoutes($this->config, $this->routes),
            $stub
        );

         return $this->createFile($stub);
    }

    /**
     * Creates a file with the compiled content in a specific directory
     *
     * @param $stub String
     * @return string File path
     */
    public function createFile($stub)
    {
        // refactor this to get the dir path from config
        if (!$this->files->exists('routes')) {
            $this->files->makeDirectory('routes');
        }

        $filePath = $this->getFilePath();

        if ($this->files->put($filePath, $stub)) {
            return $filePath;
        }
    }

    /**
     * Fluent method to add parsed yaml routes to $this->routes
     *
     * @return $this
     */
    protected function parseRoutes()
    {
        $this->routes = Yaml::parseFile($this->config['source_file_path']);

        return $this;
    }

    /**
     * loads configuration all needed configuration in array $this->config
     *
     * @throws Exception
     */
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

    private function getFilePath()
    {
        $versioned = config('lyrc.versioned');
        $configFilePath = config('lyrc.compiled_file_path');

        $filePathArray = explode('/', $configFilePath);
        $fileName = array_pop($filePathArray);

        $fileName = $versioned
                    ? date('Y_m_d_His') . "_" . $fileName
                    : $fileName;

        $dirPath = implode('/', $filePathArray);

        if (! empty($dirPath)) {
            if (! $this->files->exists($dirPath)) {
                $this->files->makeDirectory($dirPath);
            }
            $dirPath .= '/';
        }

        return $dirPath . $fileName;
    }
}
