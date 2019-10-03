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
    return Config::loadPath('config/'.$file.'.php');
}