<?php namespace Standardizer\Factories;

use Standardizer\Exporter;
use Standardizer\Parser;

/**
 * Exporter factory
 */
class ExporterFactory
{
    public static function create(Parser $parser, string $inputFilePath)
    {
        if (!file_exists($inputFilePath)) {
            throw new \Exception('Arquivo de input não encontrado!');
        }

        return new Exporter($parser, $inputFilePath);
    }
}