<?php

namespace Tests\Feature;

use Tado\Lyrc\Test\TestCase;
use org\bovigo\vfs\vfsStreamWrapper;

class CompileRoutesCommandTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        vfsStreamWrapper::register();
    }
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

    /** @test */
    public function it_uses_yaml_file_specified_as_command_option()
    {
        $yamlFilePath = __DIR__ . "/../routes.yml";
        $this->exec('tado:lyrc', ['-y' => $yamlFilePath]);

        $this->assertEquals($this->app['config']['lyrc']['source_file_path'], $yamlFilePath);
    }

    /** @test */
    public function it_compiles_file_to_the_given_path_specified_as_command_option()
    {
        $this->exec('tado:lyrc', ['-r' => 'custom_path/compiled_file.php', '-N' => true]);
        $configFilePath = $this->app['config']['lyrc']['compiled_file_path'];
        $this->assertEquals($configFilePath, 'custom_path/compiled_file.php');
//        $configFilePath = date('Y_m_d_His') . "_" . $configFilePath;
//        $this->assertFileExists($configFilePath);
    }
}
