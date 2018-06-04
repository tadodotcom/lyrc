<?php

namespace Tado\Lyrc\Console\Commands;

use Illuminate\Console\Command;
use Tado\Lyrc\Services\RoutesCompiler;

class CompileRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tado:lyrc {--N|versioned} {--r|result_file_path=} {--y|yaml_file_path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile the routes on a YAML file';

    /**
     * Create a new command instance.
     *
     * @param RoutesCompiler $routesCompiler
     */
    public function __construct(RoutesCompiler $routesCompiler)
    {
        parent::__construct();
        ini_set('memory_limit', '2G');

        $this->routesCompiler = $routesCompiler;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Compiling your yaml file...');
        $this->setConfigurations();

        try {
            if ($filePath = $this->routesCompiler->compile()) {
                $this->info($filePath);
            } else {
                $this->error("Some error occurred while creating the file!");
            }
        } catch (\Exception $e) {
            dd($e); // Only for test purpose
            return $this->error(print_r($e));
        }

        $this->info('Routes compiled successfully!');
    }

    private function setConfigurations()
    {
        if ($versioned = $this->option('versioned')) {
            config(['lyrc.versioned' => $versioned]);
        }

        if ($yaml_file_path = $this->option('yaml_file_path')) {
            config(['lyrc.source_file_path' => $yaml_file_path]);
        }

        if ($result_file_path = $this->option('result_file_path')) {
            config(['lyrc.compiled_file_path' => $result_file_path]);
        }
    }
}
