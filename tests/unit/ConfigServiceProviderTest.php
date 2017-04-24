<?php

/*
 * This file is part of ConfigServiceProvider.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Igorw\Silex;

/**
 * @author Igor Wiedler <igor@wiedler.ch>
 * @author Jérôme Macias <jerome.macias@gmail.com>
 */
class GetFileFormatTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideFilenamesForFormat
     */
    public function testGetFileFormat($filename)
    {
        $driver = new ChainConfigDriver([
            new PhpConfigDriver(),
            new YamlConfigDriver(),
            new JsonConfigDriver(),
            new TomlConfigDriver(),
        ]);

        $this->assertTrue($driver->supports($filename));
    }

    public function provideFilenamesForFormat()
    {
        return [
            'yaml'      => [__DIR__.'/Fixtures/config.yaml'],
            'yml'       => [__DIR__.'/Fixtures/config.yml'],
            'yaml.dist' => [__DIR__.'/Fixtures/config.yaml.dist'],
            'json'      => [__DIR__.'/Fixtures/config.json'],
            'json.dist' => [__DIR__.'/Fixtures/config.json.dist'],
            'php'       => [__DIR__.'/Fixtures/config.php'],
            'php.dist'  => [__DIR__.'/Fixtures/config.php.dist'],
            'toml'      => [__DIR__.'/Fixtures/config.toml'],
            'toml.dist' => [__DIR__.'/Fixtures/config.toml.dist'],
        ];
    }
}
