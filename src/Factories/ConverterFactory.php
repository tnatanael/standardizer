<?php namespace Standardizer\Factories;

use Stringy\Stringy as Str;

use Standardizer\Exporter;
use Standardizer\Interfaces\ConverterInterface;

/**
 * Exporter factory
 */
class ConverterFactory
{
    public static function create(Exporter $exporter) : ConverterInterface
    {
        // Get the converter type by parsing the exporter inputFilePath
        foreach(config('converters')->get('available') as $type) {
            if (Str::create($exporter->getInputFilePath())->contains($type)) {
                $converterType = $type;
            }
        }

        // Check if a valid converter was found
        if (!isset($converterType)) {
            throw new \Exception('Conversor não implementado!');
        }

        // Create the parser for injection
        $parser = ParserFactory::create($exporter);

        // Define the converter class by type
        $class = 'Standardizer\\Converters\\'.ucfirst($converterType).'Converter';

        // Instantiate new converter
        return new $class($parser);
    }
}