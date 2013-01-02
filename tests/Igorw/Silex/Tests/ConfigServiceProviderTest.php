<?php

/*
 * This file is part of ConfigServiceProvider.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Igorw\Silex\Tests;

use Silex\Application;
use Igorw\Silex\ConfigServiceProvider;

/**
 * ConfigServiceProvider test cases.
 *
 * @author Jérôme Macias <jerome.macias@gmail.com>
 */
class ConfigServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterWithoutReplacement()
    {
        $app = new Application();

        $app->register(new ConfigServiceProvider(__DIR__."/Fixtures/config.json"));

        $this->assertSame(true, $app['debug']);
        $this->assertSame('%data%', $app['data']);
    }

    public function testRegisterWithReplacement()
    {
        $app = new Application();

        $app->register(new ConfigServiceProvider(__DIR__."/Fixtures/config.json", array(
            'data' => 'test-replacement'
        )));

        $this->assertSame(true, $app['debug']);
        $this->assertSame('test-replacement', $app['data']);
    }

    public function testRegisterYamlWithoutReplacement()
    {
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            $this->markTestIncomplete();
        }

        $app = new Application();

        $app->register(new ConfigServiceProvider(__DIR__."/Fixtures/config.yml"));

        $this->assertSame(true, $app['debug']);
        $this->assertSame('%data%', $app['data']);
    }

    public function testRegisterYamlWithReplacement()
    {
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            $this->markTestIncomplete();
        }

        $app = new Application();

        $app->register(new ConfigServiceProvider(__DIR__."/Fixtures/config.yml", array(
            'data' => 'test-replacement'
        )));

        $this->assertSame('test-replacement', $app['data']);
    }

    /**
     * @dataProvider provideFilenames
     */
    public function testGetFileFormat($expectedFormat, $filename)
    {
        $configServiceProvider = new ConfigServiceProvider($filename);
        $this->assertSame($expectedFormat, $configServiceProvider->getFileFormat());
    }

    public function provideFilenames()
    {
        return array(
            'yaml'      => array('yaml', __DIR__."/Fixtures/config.yaml"),
            'yml'       => array('yaml', __DIR__."/Fixtures/config.yml"),
            'yaml.dist' => array('yaml', __DIR__."/Fixtures/config.yaml.dist"),
            'json'      => array('json', __DIR__."/Fixtures/config.json"),
            'json.dist' => array('json', __DIR__."/Fixtures/config.json.dist"),
        );
    }

    public function testEmptyJsonConfigs()
    {
        $readConfigMethod = new \ReflectionMethod('Igorw\Silex\ConfigServiceProvider', 'readConfig');
        $readConfigMethod->setAccessible(true);

        $this->assertEquals(
            array(),
            $readConfigMethod->invoke(new ConfigServiceProvider(__DIR__."/Fixtures/empty_config.json"))
        );
    }

    public function testEmptyYamlConfigs()
    {
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            $this->markTestIncomplete();
        }

        $readConfigMethod = new \ReflectionMethod('Igorw\Silex\ConfigServiceProvider', 'readConfig');
        $readConfigMethod->setAccessible(true);

        $this->assertEquals(
            array(),
            $readConfigMethod->invoke(new ConfigServiceProvider(__DIR__."/Fixtures/empty_config.yml"))
        );
    }

    public function testRegisterJsonWithReplacementInJsonFile()
    {
        $app = new Application();

        $app->register(new ConfigServiceProvider(__DIR__."/Fixtures/config_replacement.json"));

        $this->assertSame( '/var/www', $app['%path%']);
        $this->assertSame( '/var/www/web/images', $app['path.images']);
        $this->assertSame( '/var/www/upload', $app[ 'path.upload' ]);
        $this->assertSame( 'http://example.com', $app['%url%']);
        $this->assertSame( 'http://example.com/images', $app['url.images']);
    }

    public function testRegisterYamlWithReplacementInYamlFile()
    {
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            $this->markTestIncomplete();
        }

        $app = new Application();

        $app->register(new ConfigServiceProvider(__DIR__."/Fixtures/config_replacement.yml"));

        $this->assertSame( '/var/www', $app['%path%']);
        $this->assertSame( '/var/www/web/images', $app['path.images']);
        $this->assertSame( '/var/www/upload', $app[ 'path.upload' ]);
        $this->assertSame( 'http://example.com', $app['%url%']);
        $this->assertSame( 'http://example.com/images', $app['url.images']);
    }
}
