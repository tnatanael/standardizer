<?php namespace Standardizer;

use Closure;
use Standardizer\Objects\ParserOptions;

/**
 * Document parser class
 */
class Parser
{
    public $options;

    private $delimiter;
    private $current_mapper_field;

    /**
     * Class constructor.
     */
    public function __construct(ParserOptions $options)
    {
        // Stora options in current object
        $this->options = $options;

        // Get delimiter configuration from config
        $this->delimiter = config('global')->get('delimiter');
    }

    /**
     * Implements the line parser
     *
     * @param array $lineArray Line to be parsed as array of data
     * @return string $line Line after parsing
     **/
    public function parseLines(array $linesArray) : array
    {
        // Explode the lines to parse text
        foreach($linesArray as $line) {
            $linesToParse[] = $this->split($this->delimiter, $line);
        }

        //Create empty indexed array from config fields to output
        foreach($this->options->get('mapper') as $field => $parseString) {
            // Set the current mapper field
            $this->current_mapper_field = $field;
            // Decode the parse string to find the steps and execute them
            $stepsData = $this->split(';', $parseString);
            // Define the line param to empty
            $lineOutput = '';
            // Execute the steps
            foreach($stepsData as $stepString) {
                $stepData = [];
                // Decode step data to find parâmeters
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

                // Call the step passing the array of parâmeters
                $stepData['function'] = $stepData['name'].'Step';

                // The first position call receives array, the others receive string
                if ($lineOutput == '') {
                    $lineOutput = $linesToParse;
                }

                // Execute step and store the return
                $fn = $stepData['function'];
                $lineOutput = $this->$fn($lineOutput, $stepData['params']);
            }

            $parsedLine[$field] = $lineOutput;
        }

        return $parsedLine;
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
        // Trim quotes from line to return
        return trim($lines[$lineIndex][$columnInt],'"');    
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
                "Wrong number of parameters in equals step field ".$this->current_mapper_field." params: ".implode(',', $params)
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

        $value_if_true = ($params[1] == 'SELF') ? $string:$params[1];
        $value_if_false = ($params[2] == 'SELF') ? $string:$params[2];

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
     * Implements the custom step
     *
     * @param string $string String to execute step
     * @param array $params The custom step can receive any number of extra parâmeters
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
     * Cadastro line mapper
     *
     * @return array
     */
    private function cobrancaMapper(array $lineToParse, array $parsedLine): array
    {
        // Start line parsing
        $parsedLine['bloco'] = @explode('/', $lineToParse[1])[0]; //split
        $parsedLine['bloco'] = ($parsedLine['bloco'] == 0) ? '' : $parsedLine['bloco']; //equals
        $parsedLine['unidade'] = @explode('/', $lineToParse[1])[1];//split
        $parsedLine['proprietario_nome'] = @explode('CPF:', $lineToParse[3])[0]; //split
        $parsedLine['proprietario_cpf/cnpj'] = @explode('CPF:', $lineToParse[3])[1]; //split
        $parsedLine['proprietario_rg'] = @explode('RG:', $lineToParse[8])[1]; //split
        $phones = explode('  ', $lineToParse[13]);
        foreach($phones as $key => $phone) {
            $phoneCleaned = preg_replace('/[^0-9]/', '', $phone);
            if ($phoneCleaned == '') {
                unset($phones[$key]);
            } else {
                $phones[$key] = $phoneCleaned;
            }
        }
        $parsedLine['proprietario_telefone'] = implode(";", $phones);
        $parsedLine['proprietario_email'] = @explode('e-mail:', $lineToParse[18])[1];
        $parsedLine['proprietario_endereco'] = @$lineToParse[22];
        $address = explode('  ', @$lineToParse[26]);
        $city = @explode(' - ', @$address[0])[0];
        $state = @explode(' - ', @$address[0])[1];

        $parsedLine['proprietario_cep'] = @$address[1];
        $parsedLine['proprietario_cidade'] = $city;
        $parsedLine['proprietario_bairro'] = @$address[2];
        $parsedLine['proprietario_estado'] = $state;

        return $parsedLine;
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
