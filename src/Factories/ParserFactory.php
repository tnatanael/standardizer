<?php namespace Standardizer\Factories;

use Standardizer\Objects\ParserOptions;
use Standardizer\Interfaces\ParserInterface;

/**
 * Parser factory
 */
class ParserFactory
{
    public static function create(string $parserName): ParserInterface
    {
        // Get the parser configuration by name
        $parserConfig = config('parsers')->get($parserName);

        // Attempt to create a parser options object from parser config
        $options = new ParserOptions($parserConfig);

        // Get the right parser instance based on config
        $parserClass = 'Standardizer\\Parsers\\'.ucfirst($options->get('mode')).'Parser';

        // Create the parser object
        return new $parserClass($options);
    }
}