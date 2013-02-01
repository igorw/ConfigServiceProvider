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
use Symfony\Component\Yaml\Yaml;

class ConfigServiceProvider implements ServiceProviderInterface
{
    private $filename;
    private $replacements = array();

    public function __construct($filename, array $replacements = array())
    {
        $this->filename = $filename;

        if ($replacements) {
            foreach ($replacements as $key => $value) {
                $this->replacements['%'.$key.'%'] = $value;
            }
        }
    }

    public function register(Application $app)
    {
        $config = $this->readConfig();

        foreach ($config as $name => $value)
            if ('%' === substr($name, 0, 1))
                $this->replacements[$name] = (string) $value;

        foreach ($config as $name => $value) {
			if (isset($app[$name])){
				$arr = $app[$name];
				foreach($value as $sub_name => $sub_value) {
					$arr[$sub_name] = $this->doReplacements($sub_value);
				}
				$app[$name] = $arr;
			}else{
				$app[$name] = $this->doReplacements($value);
			}
        }
    }

    public function boot(Application $app)
    {
    }

    private function doReplacements($value)
    {
        if (!$this->replacements) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->doReplacements($v);
            }

            return $value;
        }

        if (is_string($value)) {
            return strtr($value, $this->replacements);
        }

        return $value;
    }

    private function readConfig()
    {
        $format = $this->getFileFormat();

        if (!$this->filename || !$format) {
            throw new \RuntimeException('A valid configuration file must be passed before reading the config.');
        }

        if (!file_exists($this->filename)) {
            throw new \InvalidArgumentException(
                sprintf("The config file '%s' does not exist.", $this->filename));
        }

        if ('php' === $format) {
            $config = require $this->filename;
            $config = (1 === $config) ? array() : $config;
            return $config ?: array();
        }

        if ('yaml' === $format) {
            if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
                throw new \RuntimeException('Unable to read yaml as the Symfony Yaml Component is not installed.');
            }
            $config = Yaml::parse($this->filename);
            return $config ?: array();
        }

        if ('json' === $format) {
            $config = $this->parseJson($this->filename);
            return $config ?: array();
        }

        throw new \InvalidArgumentException(
                sprintf("The config file '%s' appears has invalid format '%s'.", $this->filename, $format));
    }

    private function parseJson($filename)
    {
        $json = file_get_contents($filename);
        $json = $this->processRawJson($json);
        return json_decode($json, true);
    }

    protected function processRawJson($json)
    {
        return $json;
    }

    public function getFileFormat()
    {
        $filename = $this->filename;

        if (preg_match('#.ya?ml(.dist)?$#i', $filename)) {
            return 'yaml';
        }

        if (preg_match('#.json(.dist)?$#i', $filename)) {
            return 'json';
        }

        if (preg_match('#.php(.dist)?$#i', $filename)) {
            return 'php';
        }

        return pathinfo($filename, PATHINFO_EXTENSION);
    }
}
