<?php namespace Standardizer\Factories;

use Standardizer\Parser;
use Standardizer\Objects\ParserOptions;

/**
 * Parser factory
 */
class ParserFactory
{
    public static function create(string $parserName): Parser
    {
        // Get the parser configuration by name
        $parserConfig = config('parsers')->get($parserName);

        // Attempt to create a parser options object from parser config
        $options = new ParserOptions($parserConfig);

        // Create the parser object
        return new Parser($options);
    }
}