<?php

require_once('vendor/autoload.php');

use Configula\ConfigFactory as Config;

/**
 * Configuration getter helper
 *
 * @param string $file File to get configuration values from
 * @return Config
 */
function config($file) {
    $path = 'config/';

    // Load configuration from tests for testing 
    if (defined('TESTING')) {
        $path = 'tests/config/';
    }
    return Config::loadPath($path.$file.'.php');
}