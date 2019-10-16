<?php namespace Standardizer\Parsers;

use Stringy\Stringy as Str;

use Standardizer\Objects\ParserOptions;
use Standardizer\Interfaces\ParserInterface;

/**
 * Document parser class
 */
class DinamicParser extends Parser implements ParserInterface
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
        // Parse the mapper
        foreach ($this->options->get('custom_steps') as $functionName => $parseFunction) {
            // Reflect the function to validate
            $reflection = new \ReflectionFunction($parseFunction);

            // Validate if the custom step is a closure
            if (!$reflection->isClosure()) {
                throw new \Exception(
                    "The custom step defined is not a function: ".$functionName
                );
            }

            // Execute the step passing extra parameters and return
            $parsedLineReturn = $parseFunction($lines);

            // Validate the return must be an string
            if (!is_string($parsedLineReturn)) {
                throw new \Exception(
                    "The return of custom function must be string in function: ".$functionName
                );
            }
        }
        
        return $parsedLineReturn;
    }

    /**
     * Summarize lines to simplify parsing
     *
     * @param array $var Description
     * @return array New lines array
     **/
    public function summarizeLines(array $lines) : array
    {
        // Get the divisor text config
        $divisor_text = $this->options->get('divisor_text');

        // Create result buffer
        $result = [];

        // Concatenation buffer
        $buffer = [];

        // To ignore the first group before the divisor
        $found = false;

        foreach ($lines as $line) {
            if (Str::create($line)->contains($divisor_text)) {
                if ($found) {
                    $result[] = $buffer;
                    $buffer = [];
                }
                $found = true;
            }
            if ($found) {
                $buffer[] = $line;
            }
        }

        // Add the last buffer
        $result[] = $buffer;

        return $result;
    }
}
