<?php

namespace Tado\Lyrc\Test;

use Tado\Lyrc\LyrcServiceProvider;
use \Illuminate\Foundation\Console\Kernel;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->cmd = $this->app[Kernel::class];

        $this->app['config']->set('lyrc', require 'src/config/lyrc.php');
    }

    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return Tado\Lyrc\LyrcServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [LyrcServiceProvider::class];
    }

    protected function exec($command, array $options = [])
    {
        $this->cmd->call($command, $options);

        return $this->cmd->output();
    }
}
