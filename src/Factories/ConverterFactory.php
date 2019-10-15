<?php namespace Standardizer\Factories;

use Standardizer\Exporter;
use Standardizer\Interfaces\ParserInterface;
use Standardizer\Converter;

/**
 * Exporter factory
 */
class ConverterFactory
{
    public static function create(ParserInterface $parser, Exporter $exporter) : Converter
    {
        // Instantiate new converter
        return new Converter($parser, $exporter);
    }
}