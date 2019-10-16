<?php namespace Standardizer\Factories;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Reader factory
 */
class ReaderFactory
{
    public static function create(string $extension, string $delimiter = ',')
    {
        // Determine the reader format by the file extension
        if (!in_array($extension, config('global')->get('supported_extensions'))) {
            throw new \Exception('Arquivo n�o suportado!');
        }

        // For csv or txt file we need to read the input delimiter config
        if (!in_array($extension, ['txt', 'csv'])) {
            // Read the file using PhpSpreadsheet
            return IOFactory::createReader(ucfirst($extension));
        }

        // Read the file using PhpSpreadsheet
        return IOFactory::createReader(ucfirst($extension))
        ->setDelimiter($delimiter);
    }
}