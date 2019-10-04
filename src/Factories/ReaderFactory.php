<?php namespace Standardizer\Factories;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Reader factory
 */
class ReaderFactory
{
    public static function create($extension)
    {
        //Determine the reader format by the file extension
        if (!in_array($extension, config('global')->get('supported_extensions'))) {
            throw new \Exception('Arquivo não suportado!');
        }

        // Read the file using PhpSpreadsheet
        return IOFactory::createReader(ucfirst($extension));
    }
}