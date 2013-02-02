<?php

/*
 * This file is part of ConfigServiceProvider.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Silex\Application;
use Igorw\Silex\ConfigServiceProvider;

/**
 * @author Igor Wiedler <igor@wiedler.ch>
 * @author Jérôme Macias <jerome.macias@gmail.com>
 */
class ConfigServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFilenames
     */
    public function testRegisterWithoutReplacement($filename)
    {
        $app = new Application();

        $app->register(new ConfigServiceProvider($filename));

        $this->assertSame(true, $app['debug']);
        $this->assertSame('%data%', $app['data']);
    }

    /**
     * @dataProvider provideFilenames
     */
    public function testRegisterWithReplacement($filename)
    {
        $app = new Application();

        $app->register(new ConfigServiceProvider(__DIR__."/Fixtures/config.json", array(
            'data' => 'test-replacement'
        )));

        $this->assertSame(true, $app['debug']);
        $this->assertSame('test-replacement', $app['data']);
    }

    /**
     * @dataProvider provideEmptyFilenames
     */
    public function testEmptyConfigs($filename)
    {
        $readConfigMethod = new \ReflectionMethod('Igorw\Silex\ConfigServiceProvider', 'readConfig');
        $readConfigMethod->setAccessible(true);

        $this->assertEquals(
            array(),
            $readConfigMethod->invoke(new ConfigServiceProvider($filename))
        );
    }

    /**
     * @dataProvider provideReplacementFilenames
     */
    public function testInFileReplacements($filename)
    {
        $app = new Application();

        $app->register(new ConfigServiceProvider($filename));

        $this->assertSame('/var/www', $app['%path%']);
        $this->assertSame('/var/www/web/images', $app['path.images']);
        $this->assertSame('/var/www/upload', $app['path.upload']);
        $this->assertSame('http://example.com', $app['%url%']);
        $this->assertSame('http://example.com/images', $app['url.images']);
    }

    public function provideFilenames()
    {
        return array(
            array(__DIR__."/Fixtures/config.php"),
            array(__DIR__."/Fixtures/config.json"),
            array(__DIR__."/Fixtures/config.yml"),
        );
    }

    public function provideReplacementFilenames()
    {
        return array(
            array(__DIR__."/Fixtures/config_replacement.php"),
            array(__DIR__."/Fixtures/config_replacement.json"),
            array(__DIR__."/Fixtures/config_replacement.yml"),
        );
    }

    public function provideEmptyFilenames()
    {
        return array(
            array(__DIR__."/Fixtures/config_empty.php"),
            array(__DIR__."/Fixtures/config_empty.json"),
            array(__DIR__."/Fixtures/config_empty.yml"),
        );
    }
}
