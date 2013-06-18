<?php

namespace Igorw\Silex;

use Symfony\Component\Yaml\Yaml;

class YamlConfigDriver implements ConfigDriver
{
    public function load($filename)
    {
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            throw new \RuntimeException('Unable to read yaml as the Symfony Yaml Component is not installed.');
        }
        $config = Yaml::parse($filename);
        $config = $this->loadImports($config, $filename);

        return $config ?: array();
    }

    public function supports($filename)
    {
        return (bool) preg_match('#\.ya?ml(\.dist)?$#', $filename);
    }

    public function loadImports($config, $filename)
    {
        if (isset($config['imports']) && is_array($config['imports'])) {
            $path = pathinfo($filename);
            $imports = $config['imports'];
            unset($config['imports']);

            $importedConfig = array();
            foreach ($imports as $import) {
                $file = $path['dirname'] . '/' . $import['resource'];

                if (is_file($file)) {
                    $imported = $this->load($file);
                    $importedConfig = array_merge($importedConfig, $imported);
                }
            }
            $config = array_merge($importedConfig, $config);
        }

        return $config;
    }
}
