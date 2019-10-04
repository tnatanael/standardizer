<?php namespace Standardizer\Factories;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Reader factory
 */
class WriterFactory
{
    public static function create($spreadsheet)
    {
        // Export to CSV in output
        return IOFactory::createWriter(
            $spreadsheet, ucfirst(config('global')->get('output_type'))
        )
        ->setSheetIndex(0)   // Select which sheet to export.
        ->setDelimiter(',');  // Set delimiter.
    }
}