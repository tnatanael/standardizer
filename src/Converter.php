<?php namespace Standardizer;

// Thyrd party lib's
use Stringy\Stringy as Str;

use Standardizer\Filesystem;
use Standardizer\Parser;

/**
 * Defines the base methods for a converter
 */
class Converter
{
    protected $inputFilePath;
    protected $inputFileInfo;

    protected $outputFile;

    protected $tempFilePath;

    protected $parser;
    protected $exporter;

    /**
     * Class constructor.
     */
    public function __construct(Parser $parser, Exporter $exporter)
    {
        // Store the references
        $this->parser = $parser;
        $this->exporter = $exporter;

        // Get the output folder from config
        $this->outputFolder = config('global')->get('output_folder');
    }

    /**
     * Execute the conversor and generates output
     *
     * @return void
     * @throws conditon
     **/
    public function run() : void
    {
        // Create file for converted output
        $this->outputFile = Filesystem::createResource(
            $this->outputFolder.basename($this->outputFilePath)
        );

        // Write header to converted output file based on standard
        Filesystem::writeLine(
            $this->outputFile,
            implode(',', $this->getFieldsToImplode())
        );

        // Get the raw csv lines
        $lines = Filesystem::getLines($this->rawFilePath);

        //Execute default steps
        $lines = self::cutTop($lines, $this->getCutTop());
        $lines = self::cutBottom($lines, $this->getCutBottom());
        $lines = self::cutContains($lines, $this->standard['discard']);
        $lines = self::cutEquals($lines, $this->standard['discard_equals']);

        //Execute line concatenation rule
        $lines = self::concatenateLines($lines, $this->getConcatEvery());

        //Run the parser logic implemented by the child
        $lines = $this->parse($lines);
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
        foreach($lines as $key => $line)
        {
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
        foreach($lines as $key => $line)
        {
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
        foreach($lines as $key => $line)
        {
            foreach($needles as $needle) {
                if (Str::create($line)->contains($needle)) {
                    unset($lines[$key]);
                }
            }
        }
        
        return array_values($lines);
    }

    /**
     * Discard lines that text is equals
     *
     * @param array $lines The lines to check
     * @param array $needles The array of strings to find
     * @return array New lines array
     **/
    public function cutEquals(array $lines, array $needles)
    {
        // Check for discard lines with unwanted text
        foreach($lines as $key => $line)
        {
            foreach($needles as $needle) {
                if ($line == $needle) {
                    unset($lines[$key]);
                }
            }
        }
        
        return array_values($lines);
    }

    /**
     * Concatenate lines to simplify parsing
     *
     * @param array $var Description
     * @param int $every Description
     * @return array New lines array
     **/
    public static function concatenateLines(array $lines, int $every)
    {
        // No need to run concatenation in this case
        if ($every == 1) return $lines;

        // Concatenation result
        $result = [];
        // Line concatenation index
        $index = 0;
        // Line concatenation buffer
        $toWrite = '';
        foreach($lines as $key => $line) {
            // Increade key to start from 1 instead of 0
            $key++;

            // Concatenate line to write
            $toWrite .= $line;

            // Check for line concatenation moment
            if (($index+$every) == $key) {
                $result[] = $toWrite;
                // Set the current position as concatenation index
                $index = $key;
                // Clean concatenation buffer
                $toWrite = '';
            }
        }
        return $result;
    }

}
