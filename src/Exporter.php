<?php namespace Standardizer;

use Standardizer\Factories\WriterFactory;
use Standardizer\Factories\ReaderFactory;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use Standardizer\Interfaces\ParserInterface;

/**
 * Create a new standardizer exporter object
 */
class Exporter
{
    // Properties
    protected $inputFilePath;
    protected $inputFileInfo = [];

    protected $rawFilePath;

    protected $parser;

    /**
     * Class constructor.
     */
    public function __construct(ParserInterface $parser, string $inputFilePath)
    {
        // Input file path
        $this->inputFilePath = $inputFilePath;
        // Get file info from file path
        $this->inputFileInfo = Filesystem::getInfo($inputFilePath);
        // Bind parser
        $this->parser = $parser;
        // Create the temp file path
        $this->tempFilePath = config('global')->get('temp_folder').'raw.csv';
    }

    /**
     * Return inputFileInfo
     *
     * @return array
     **/
    public function getInputFileInfo() : array
    {
        return $this->inputFileInfo;
    }

    /**
     * Run execute the exporter instance and generates output
     *
     * @return void
     */
    public function run() : void
    {
        // Get the input extension
        $inputExtension = $this->inputFileInfo['extension'];

        // In this case use csv
        if ($inputExtension == 'txt') {
            $inputExtension = 'csv';
        }

        // Get the input delimiter if set
        $delimiter = $this->parser->options->get('delimiter');

        // Create the reader
        $reader = ReaderFactory::create($inputExtension, $delimiter);

        //$reader->setReadDataOnly(true);

        // Load imput file to reader
        $spreadsheet = $reader->load($this->inputFilePath);

        // Create the writer factory instance
        $writer = WriterFactory::create($spreadsheet);

        // Ensure the folder output exists
        Filesystem::makeFolder(dirname($this->tempFilePath));

        // Save temp conversion output
        $writer->save($this->tempFilePath);
    }

    /**
     * Get the input file path
     *
     * @return string The path to input file informed
     **/
    public function getInputFilePath()
    {
        return $this->inputFilePath;
    }

    /**
     * Get the temp file path
     *
     * @return string The path to the temp raw file that exporter generates
     **/
    public function getTempFilePath()
    {
        return $this->tempFilePath;
    }

    /**
     * Decode a column letter to integer
     *
     * @param string $column Column to get index from
     * @return int
     **/
    public static function columnIndex(string $column)
    {
        return Coordinate::columnIndexFromString($column);
    }
}
