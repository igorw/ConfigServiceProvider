<?php

namespace Igorw\Silex;

interface ConfigDriver
{
    public function load($filename);
    public function supports($filename);
}
