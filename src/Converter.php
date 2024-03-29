<?php namespace Standardizer;

// Thyrd party lib's
use Stringy\Stringy as Str;

use Standardizer\Filesystem;
use Standardizer\Interfaces\ParserInterface;

/**
 * Defines the base methods for a converter
 */
class Converter
{
    protected $outputFolder;
    protected $outputFile;
    protected $outputFilePath;

    protected $tempFilePath;

    protected $parser;
    protected $exporter;

    protected $delimiter;

    /**
     * Class constructor.
     */
    public function __construct(ParserInterface $parser, Exporter $exporter)
    {
        // Store the references
        $this->parser = $parser;
        $this->exporter = $exporter;

        // Get the output folder from config
        $this->outputFolder = config('global')->get('output_folder');

        // Create the output file path
        $this->outputFilePath = str_replace(
            $this->exporter->getInputFileInfo()['extension'],
            config('global')->get('output_type'),
            $this->exporter->getInputFilePath()
        );

        // Delimiter
        $this->delimiter = config('global')->get('delimiter');
    }

    /**
     * Get this outputFilePath
     *
     * @return string Output path
     **/
    public function getOutputFilePath()
    {
        return $this->outputFilePath;
    }

    /**
     * Execute the conversor and generates output
     *
     * @return void
     * @throws conditon
     **/
    public function run() : void
    {
        // Parser options bind
        $options = $this->parser->options;

        // Create file for converted output
        $this->outputFile = Filesystem::createResource(
            basename($this->outputFilePath)
        );

        // Write header to converted output file based on standard
        Filesystem::writeLine(
            $this->outputFile,
            implode($this->delimiter, $this->getFieldsToImplode())
        );

        // Execute exporter
        $this->exporter->run();

        // Get the raw csv lines
        $lines = Filesystem::getLines($this->exporter->getTempFilePath());

        //Execute default steps
        $lines = self::cutTop($lines, $options->get('discard_top'));
        $lines = self::cutBottom($lines, $options->get('discard_bottom'));
        $lines = self::cutContains($lines, $options->get('discard_contains'));

        //Execute line sumarization rule
        $lines = $this->parser->summarizeLines($lines);

        //Run the parser logic implemented by the child
        $parsedLines = [];

        foreach ($lines as $lineSet) {
            // Detects the enf of file if set and exit
            if ($options->get('end_file_string') != '') {
                foreach ($lineSet as $line) {
                    // Finalize file processing when string found
                    if (Str::create($line)->contains($options->get('end_file_string'))) {
                        // Exit run function
                        return;
                    }
                }
            }

            // if ($this->parser->options->get('mode') == 'dinamic') {
            //     dd($lineSet);
            // }
            
            $parsedLines = $this->parser->parseLines($lineSet);

            // Detect if the line has contents parsed
            if ($parsedLines != []) {
                Filesystem::writeLine($this->outputFile, "\n".$parsedLines);
            }
        }
    }

    /**
     * Discart top lines
     *
     * @param array $lines The lines to check
     * @param int $cut The number of lines to cut
     * @return array New lines array
     **/
    public static function cutTop(array $lines, int $cut)
    {
        // Discard N top lines
        foreach ($lines as $key => $line) {
            // Advance key to start from 1 and match cut target
            if (($key+1) <= $cut) {
                unset($lines[$key]);
            }
        }

        // Reorder and return lines
        return array_values($lines);
    }

    /**
     * Discart bottom lines
     *
     * @param array $lines The lines to check
     * @param int $cut The number of lines to cut
     * @return array New lines array
     **/
    public static function cutBottom(array $lines, int $cut)
    {
        // Discart N bottom lines
        foreach ($lines as $key => $line) {
            // Advance key to start from 1 and match cut target
            if (($key+1) > (count($lines) - $cut)) {
                unset($lines[$key]);
            }
        }

        // Reorder and return lines
        return array_values($lines);
    }

    /**
     * Discard lines that contains
     *
     * @param array $lines The lines to check
     * @param array $needles The array of strings to find
     * @return array New lines array
     **/
    public function cutContains(array $lines, array $needles)
    {
        // Check for discard lines with unwanted text
        foreach ($lines as $key => $line) {
            foreach ($needles as $needle) {
                if (Str::create($line)->contains($needle)) {
                    unset($lines[$key]);
                }
            }
        }
        
        return array_values($lines);
    }

    /**
     * Fields to generate the title line
     *
     * @return array
     */
    public function getFieldsToImplode(): array
    {
        return array_keys($this->parser->options->get('mapper'));
    }
}
