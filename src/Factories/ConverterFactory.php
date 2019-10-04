<?php namespace Standardizer\Factories;

use Stringy\Stringy as Str;

use Standardizer\Exporter;
use Standardizer\Parser;
use Standardizer\Converter;

/**
 * Exporter factory
 */
class ConverterFactory
{
    public static function create(Parser $parser, Exporter $exporter) : Converter
    {
        // Instantiate new converter
        return new Converter($parser, $exporter);
    }
}