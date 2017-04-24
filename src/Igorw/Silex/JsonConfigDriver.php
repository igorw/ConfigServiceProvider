<?php

namespace Igorw\Silex;

class JsonConfigDriver implements ConfigDriver
{
    public function load($filename)
    {
        $json = file_get_contents($filename);
        $config = $this->parseJson($json);
        // determine if there was an error
        // suppress PHP 7's syntax error for empty JSON strings
        $errorCode = json_last_error();
        if (JSON_ERROR_NONE !== $errorCode && ($errorCode !== JSON_ERROR_SYNTAX || $json !== '')) {
            $jsonError = $this->getJsonError($errorCode);
            throw new \RuntimeException(
                sprintf('Invalid JSON provided "%s" in "%s"', $jsonError, $filename));
        }

        return $config ?: array();
    }

    public function supports($filename)
    {
        return (bool) preg_match('#\.json(\.dist)?$#', $filename);
    }

    private function parseJson($json)
    {
        return json_decode($json, true);
    }

    private function getJsonError($code)
    {
        $errorMessages = array(
            JSON_ERROR_DEPTH            => 'The maximum stack depth has been exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Invalid or malformed JSON',
            JSON_ERROR_CTRL_CHAR        => 'Control character error, possibly incorrectly encoded',
            JSON_ERROR_SYNTAX           => 'Syntax error',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded',
        );

        return isset($errorMessages[$code]) ? $errorMessages[$code] : 'Unknown';
    }
}