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

use Silex\Application;
use Silex\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function register(Application $app)
    {
        if (!file_exists($this->filename)) {
            throw new \InvalidArgumentException(
                sprintf("The config file '%s' does not exist.", $this->filename));
        }

        $config = json_decode(file_get_contents($this->filename), true);

        if (null === $config) {
            throw new \InvalidArgumentException(
                sprintf("The config file '%s' appears to be invalid JSON.", $this->filename));
        }

        foreach ($config as $name => $value) {
            $app[$name] = $value;
        }
    }
}
