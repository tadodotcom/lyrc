<?php

namespace Tests\Feature;

use Tado\Lyrc\Test\TestCase;

class CompileRoutesCommandTest extends TestCase
{

    /** @test */
    public function itHasCorrectHelpMessage()
    {
        $output = $this->exec('tado:lyrc', ['-h']);

        $this->assertContains('tado:lyrc', $output);
    }

    /** @test */
    public function itGetsFileGivenInPath()
    {
        $output = $this->exec('tado:lyrc');
        $this->assertContains('Compiling your yaml file...', $output);
    }

    /** @test */
    public function itThrowsAnErrorIfNoRoutesFileDefined()
    {
        $this->app['config']->set('lyrc.source_file_path', null);

        $this->expectException(\Exception::class);

        $this->exec('tado:lyrc');
    }

    /** @test */
    public function itThrowsAnErrorIfDefinedRoutesFileDoesNotExist()
    {
        $this->app['config']->set('lyrc.source_file_path', 'non_existent_file.yml');

        $this->expectException(\Exception::class);

        $this->exec('tado:lyrc');
    }

    /** @test */
    public function itGetsTheFileAndCompilesRoutes()
    {

        $this->app['config']->set('lyrc.source_file_path', 'routes.yml');

        $this->exec('tado:lyrc');

        $filePath = 'routes/yaml_compiled_routes.php';

        $this->assertFileExists($filePath);
    }
}
