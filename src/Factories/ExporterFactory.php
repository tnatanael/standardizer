<?php namespace Standardizer\Factories;

use Standardizer\Exporter;
use Standardizer\Interfaces\ParserInterface;

/**
 * Exporter factory
 */
class ExporterFactory
{
    public static function create(ParserInterface $parser, string $inputFilePath)
    {
        if (!file_exists($inputFilePath)) {
            throw new \Exception('Arquivo de input n�o encontrado!');
        }

        return new Exporter($parser, $inputFilePath);
    }
}