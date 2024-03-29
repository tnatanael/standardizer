<?php

require_once('vendor/autoload.php');

use Configula\ConfigFactory as Config;
use Stringy\Stringy as Str;
use Standardizer\Exporter;

/**
 * Configuration getter helper
 *
 * @param string $file File to get configuration values from
 * @return Config
 */
function config($file)
{
    $path = 'config/';

    // Load configuration from tests for testing
    if (defined('TESTING')) {
        $path = 'tests/config/';
    }
    return Config::loadPath($path.$file.'.php');
}

function contains($needle, $haystack)
{
    return Str::create($haystack)->contains($needle);
}

function split($explode, $string)
{
    return explode($explode, $string);
}

function position($column, $line)
{
    //Get the column index
    $index = Exporter::columnIndex($column);
    return @explode(',', $line)[$index-1];
}