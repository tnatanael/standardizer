<?php namespace Standardizer\Parsers;

use Standardizer\Objects\ParserOptions;
use Standardizer\Exporter;

/**
 * Document parser class
 */
class Parser
{
    public $options;

    protected $delimiter;
    protected $current_mapper_field;

    protected $previous_values;

    /**
     * Class constructor.
     */
    public function __construct(ParserOptions $options)
    {
        // Store options in current object
        $this->options = $options;

        // Get delimiter configuration from config
        $this->delimiter = config('global')->get('delimiter');
    }

    /**
     * The position step returns the desired line position to parse
     *
     * @param array $lines Lines to get the position and index
     * @param array $params Parameters to step
     * [
     *      string params[0] - Letter of the spreadsheet column
     *      int params[1] - Optional: Wanted line index default: 1
     * ]
     * @return string Returns the cell content
     * @throws \Exception When parameters where not found
     **/
    public function positionStep(array $lines, array $params): string
    {
        // Default line index if parameter was not set
        if (!isset($params[1])) {
            $params[1] = 1;
        }
        // Validate parameters
        if (!is_numeric($params[1])) {
            throw new \Exception(
                "Wrong line index in position step field ".$this->current_mapper_field." value: ".$params[1]
            );
        }
        $lineIndex = $params[1];
        
        // Subtract 1 to match the array index
        $lineIndex--;

        //Confirm if line index exists
        if (!isset($lines[$lineIndex])) {
            throw new \Exception(
                "Not found line index in position step field ".$this->current_mapper_field." value: ".$params[1]
            );
        }

        // Decode the column letter to integer
        $columnInt = Exporter::columnIndex($params[0]);

        // Subtract 1 from column to match array index
        $columnInt--;
        
        // Check if the wanted line column is set
        if (!isset($lines[$lineIndex][$columnInt])) {
            throw new \Exception(
                "Wrong column in position step field ".$this->current_mapper_field." value: ".$params[0]
            );
        }
        // Trim quotes from position to return
        return trim($lines[$lineIndex][$columnInt], '"');
    }

    /**
     * Implements the split step
     *
     * @param string $string String to execute step
     * @param array $params Parameters to step
     * [
     *      string $params[0] - Separator for explode string
     *      string $params[1] - Position to return from exploded string
     * ]
     * @return string Returns the splited selected position
     **/
    public function splitStep(string $string, array $params)
    {
        // Trim the separator parameter
        $separator = trim($params[0], '"');

        // Validate parameters
        if (!is_numeric($params[1])) {
            throw new \Exception(
                "Wrong split index in split step field ".$this->current_mapper_field." value: ".$params[1]
            );
        }
        $splitIndex = $params[1];

        // Subtract the split index to match splited array index
        $splitIndex--;

        return @explode($separator, $string)[$splitIndex] ?? '';
    }

    /**
     * Implements the equals step
     *
     * @param string $string String to execute step
     * @param array $params Parameters to step
     * [
     *      string $params[0] - Value to check
     *      string $params[1] - Value to assert if true
     *      string $params[2] - Value to assert if false
     * ]
     * @return string Returns the splited selected position
     **/
    public function equalsStep(string $string, array $params)
    {
        // Validate parameters needed
        if (count($params) > 3) {
            throw new \Exception(
                "Wrong number of parameters in equals step field ".$this->current_mapper_field
                ." params: ".implode(',', $params)
            );
        }
        if (!isset($params[0]) || !isset($params[1])) {
            throw new \Exception(
                "Wrong parameters in equals step field ".$this->current_mapper_field." params: ".implode(',', $params)
            );
        }
        if (!isset($params[2])) {
            $params[2] = '';
        }
        // Trim the separator parameter
        $check = trim($params[0], '"');

        // Check for SELF keyword
        $value_if_true = ($params[1] == 'SELF') ? $string:$params[1];
        $value_if_false = ($params[2] == 'SELF') ? $string:$params[2];

        // Check for the PREV keyword
        if ($params[1] == 'PREV') {
            $value_if_true = @$this->previous_values[$this->current_mapper_field];
        }

        if ($params[2] == 'PREV') {
            $value_if_false = @$this->previous_values[$this->current_mapper_field];
        }

        return ($check == $string) ? trim($value_if_true, '"') : trim($value_if_false, '"');
    }

    /**
     * Implements the numbers filter step
     *
     * @param string $string String to execute step
     * @param array $params Parameters to step
     * [
     *      // This step receives no params
     * ]
     * @return string Returns the string filtered
     **/
    public function numbersStep(string $string, array $params)
    {
        // Validate parameters needed
        if (count($params) > 0) {
            throw new \Exception(
                "The numbers step accepts no params in field ".$this->current_mapper_field
            );
        }

        return preg_replace('/[^0-9]/', '', $string);
    }

    /**
     * Implements the replace step
     *
     * @param string $string String to execute step
     * @param array $params Parameters to step
     * [
     *      string $params[0] - Value to search
     *      string $params[1] - Value to replace
     * ]
     * @return string Returns the string filtered
     **/
    public function replaceStep(string $string, array $params)
    {
        // Validate parameters needed
        if (count($params) != 2) {
            throw new \Exception(
                "The replace step needs two params in field ".$this->current_mapper_field
            );
        }
        // Trim the param quotes and replace
        return str_replace(trim($params[0], '"'), trim($params[1], '"'), $string);
    }

    /**
     * Implements the substr step
     *
     * @param string $string String to execute step
     * @param array $params Parameters to step
     * [
     *      string $params[0] - Start index
     *      string $params[1] - Number of chars to subtract
     * ]
     * @return string Returns the string filtered
     **/
    public function substrStep(string $string, array $params)
    {
        // Validate parameters needed
        if (!isset($params[0])) {
            throw new \Exception(
                "The substr step needs at least one param in field ".$this->current_mapper_field
            );
        }

        $start = trim($params[0], '"');

        // Validate for first parameter numeric
        if (!is_numeric($start)) {
            throw new \Exception(
                "Wrong start index in substr step field ".$this->current_mapper_field." value: ".$params[1]
            );
        }

        // Reduce start index to get the first position
        $start--;

        // Validate for second parameter numeric
        if (isset($params[1])) {
            if (!is_numeric($params[0])) {
                throw new \Exception(
                    "Wrong lenght in substr step field ".$this->current_mapper_field." value: ".$params[1]
                );
            }
            return substr($string, $start, trim($params[1], '"'));
        }

        // Trim the param quotes and replace
        return substr($string, $start);
    }
    
    /**
     * Implements the custom step
     *
     * @param string $string String to execute step
     * @param array $params The custom step can receive any number of extra par�meters
     * @return string Returns the string value
     **/
    public function customStep(string $string, array $params)
    {
        // Validate first parameter must contain a valid defined function
        if (!array_key_exists($params[0], $this->options->get('custom_steps'))) {
            throw new \Exception(
                "The custom step function is not found in field ".$this->current_mapper_field
            );
        }
        $fn = $this->options->get('custom_steps')[$params[0]];
        //dd($fn);
        $reflection = new \ReflectionFunction($fn);

        // Validate if the custom step is a closure
        if (!$reflection->isClosure()) {
            throw new \Exception(
                "The custom step defined is not a function in field ".$this->current_mapper_field
            );
        }
        // Unset the param[0] to not pass it to custom function
        unset($params[0]);
        // Execute the step passing extra parameters and return
        return $fn($string, array_values($params));
    }

    /**
     * Split a string ignoring when delimiter is inside double quotes
     *
     * @param string $string The string to split
     * @return array
     **/
    public function split(string $delimiter, string $string)
    {
        // Create a regex to parse string but ignore when inside double quotes
        $regex = '~'.$delimiter.'(?=([^\"]*\"[^\"]*\")*[^\"]*$)~';
        return preg_split($regex, $string);
    }
}
