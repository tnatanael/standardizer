<?php namespace Standardizer\Parsers;

use Standardizer\Objects\ParserOptions;
use Standardizer\Interfaces\ParserInterface;

/**
 * Document parser class
 */
class FixedParser extends Parser implements ParserInterface
{
    /**
     * Class constructor.
     */
    public function __construct(ParserOptions $options)
    {
        // Constrtuct base parser
        parent::__construct($options);
    }

    /**
     * Implements the line parser
     *
     * @param array $lineArray Line to be parsed as array of data
     * @return string $line Line after parsing
     **/
    public function parseLines(array $lines) : string
    {
        // Explode the lines to parse text
        foreach ($lines as $line) {
            // Detects empty lines and discards
            if (str_replace('""', '', str_replace(',', '', $line)) == '') {
                return '';
            }
            $linesToParse[] = $this->split($this->delimiter, $line);
        }

        //Create empty indexed array from config fields to output
        foreach ($this->options->get('mapper') as $field => $parseString) {
            // Set the current mapper field
            $this->current_mapper_field = $field;

            // Decode the parse string to find the steps and execute them
            $stepsData = $this->split(';', $parseString);

            // Clean the position output
            $positionOutput = '';

            // Execute the steps
            foreach ($stepsData as $stepString) {
                $stepData = [];
                // Decode step data to find par�meters
                $stepData['info'] = $this->split(':', $stepString);
                // If step info returned more than 2 itens, configuration is wrong
                if (count($stepData['info']) > 2) {
                    throw new \Exception(
                        "Wrong step configuration in ".$this->current_mapper_field
                    );
                }
                $stepData['name'] = $stepData['info'][0];

                // If step contains parameters, parse them
                $stepData['params'] = [];
                if (isset($stepData['info'][1])) {
                    // Parse the params string to an array of params
                    $stepData['params'] = $this->split(',', $stepData['info'][1]);
                }

                // Call the step passing the array of par�meters
                $stepData['function'] = $stepData['name'].'Step';

                // Execute step and store the return
                $fn = $stepData['function'];

                // Set the input for the position step, the other steps receives the result string
                if ($fn == 'positionStep') {
                    $positionOutput = $linesToParse;
                }

                // Run the step and get the result
                $positionOutput = $this->$fn($positionOutput, $stepData['params']);
            }

            // Trim whitespaces
            $parsedLines[$field] = trim($positionOutput, ' ');

            // Save value to previous_values
            $this->previous_values[$field] = $positionOutput;
        }

        // Implode and return the lines
        return implode($this->delimiter, $parsedLines);
    }

    /**
     * Summarize lines to simplify parsing
     *
     * @param array $var Description
     * @return array New lines array
     **/
    public function summarizeLines(array $lines) : array
    {
        $every = $this->options->get('line_counter');
        // Concatenation result
        $result = [];
        // Line concatenation index
        $index = 0;
        // Line concatenation buffer
        $toWrite = [];
        foreach ($lines as $key => $line) {
            // Increade key to start from 1 instead of 0
            $key++;

            // Concatenate line to write
            $toWrite[] = $line;

            // Check for line concatenation moment
            if (($index+$every) == $key) {
                $result[] = $toWrite;
                // Set the current position as concatenation index
                $index = $key;
                // Clean summarization buffer
                $toWrite = [];
            }
        }
        return $result;
    }
}
