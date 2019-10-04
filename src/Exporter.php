<?php namespace Standardizer;

use Standardizer\Factories\WriterFactory;
use Standardizer\Factories\ReaderFactory;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Create a new standardizer exporter object
 */
class Exporter
{
    // Properties
    protected $inputFilePath;
    protected $inputFileInfo;

    protected $outputFilePath;
    protected $rawFilePath;

    // Local config cache
    protected $config;

    /**
     * Class constructor.
     */
    public function __construct(string $inputFilePath)
    {
        // Load global config to local cache
        $this->config = config('global');
        // Input file path
        $this->inputFilePath = $inputFilePath;
        // Get file info from file path
        $this->inputFileInfo = Filesystem::getInfo($inputFilePath);

        // Create the output file path
        $this->outputFilePath = str_replace(
            $this->inputFileInfo['extension'],
            $this->config->get('output_type'),
            $this->inputFilePath
        );

        // Create the temp file path
        $this->tempFilePath = $this->config->get('temp_folder').'raw.csv';
    }

    /**
     * Run execute the exporter instance and generates output
     *
     * @return void
     */
    public function run() : void
    {
        // Create the reader
        $reader = ReaderFactory::create(
            $this->inputFileInfo['extension']
        );

        // Load imput file to reader
        $spreadsheet = $reader->load($this->inputFilePath);

        // Create the writer factory instance
        $writer = WriterFactory::create($spreadsheet, $this->outputFilePath);

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
