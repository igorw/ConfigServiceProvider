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
    /**
     * filename
     *
     * @var string
     */
    private $filename;

    /**
     * format (json|yaml)
     *
     * @var string
     */
    private $format;

    /**
     * accepted file formats
     *
     * @var string
     */
    protected $validFormats = array('json', 'yaml');

    /**
     * array of tokens with replacement values
     *
     * @var array
     */
    private $replacements = array();

    /**
     * pass configuration file and (optional) replacements
     *
     * @param string $filename
     * @param array $replacements
     * @return void
     */
    public function __construct($filename, array $replacements = array())
    {
        $this->filename = $filename;
        $this->setFormatByFilename();

        if ($replacements) {
            foreach ($replacements as $key => $value) {
                $this->replacements['%'.$key.'%'] = $value;
            }
        }
    }

    /**
     * register the ConfigServiceProvider in Silex application
     *
     * @param Application $app
     * @return void
     */
    public function register(Application $app)
    {
        if (null === ($config = $this->readConfig())) {
            throw new \InvalidArgumentException(
                sprintf("The config file '%s' appears to be invalid.", $this->filename));
        }

        foreach ($config as $name => $value) {
            $app[$name] = $this->doReplacements($value);
        }
    }

    /**
     * replace tokens within a single or an array of config values
     *
     * @param string|array $value
     * @return string|array
     */
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

        return strtr($value, $this->replacements);
    }

    /**
     * set format by filename extension, ensure it's supported
     *
     * @param string $filename (optional / $this->filename used by default)
     * @return void
     */
    private function setFormatByFilename($filename = null)
    {
        $filename = $filename ? $filename : $this->filename;
        $this->format = str_replace('yml', 'yaml', pathinfo($this->filename, PATHINFO_EXTENSION));
        if (!in_array($this->format, $this->validFormats)) {
            throw new \InvalidArgumentException(
                sprintf("The '%s' format is not supported, try json or yaml and ensure the config file has the right file ending.", $this->format));
        }
    }

    /**
     * read config from file passed with __construct()
     */
    private function readConfig()
    {
        $config = null;
        if (!$this->filename || !$this->format) {
            throw new \RuntimeException('A valid configuration file must be passed before reading the config.');
        }
        if (!file_exists($this->filename)) {
            throw new \InvalidArgumentException(
                sprintf("The config file '%s' does not exist.", $this->filename));
        }
        switch ($this->format) {
            case 'yaml':
                if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
                    throw new \RuntimeException('Unable to read yaml as the Symfony Yaml Component is not installed.');
                }
                $config = Yaml::parse($this->filename);
                break;
            case 'json':
            default:
                $config = json_decode(file_get_contents($this->filename), true);
                break;
        }

        return $config;
    }

}
