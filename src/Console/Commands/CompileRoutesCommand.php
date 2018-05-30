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
    protected $signature = 'tado:lyrc {--N|versioned}';

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
        // $path = config('lyrc.filePath');

        // if (empty($path)) {
        //     return $this->error('Please define the routes yml file in config/lyrc.php.');
        // }

        // file existence should be responsibility of the RoutesCompiler class
        // if (!file_exists($path)) {
        //     return $this->error("Please verify the file $path exist.");
        // }

        // ToDo - validate the format of the yml file for compability with the compiler
        // if (!$this->routesCompiler->isValid($path)) {
        //     return $this->error("Please verify the file $path is a valid routes file.");
        // }

        $versioned = $this->option('versioned');

        $this->info('Compiling your yaml file...');

        try {
            if ($filePath = $this->routesCompiler->compile($versioned)) {
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
}
