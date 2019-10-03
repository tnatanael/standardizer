<?php namespace Standardizer;

use Standardizer\Objects\ParserOptions;

/**
 * Document parser class
 */
class Parser
{
    public $options;

    /**
     * Class constructor.
     */
    public function __construct(ParserOptions $options)
    {
        $this->options = $options;
    }

    /**
     * Implements the line parser
     *
     * @param array $lineArray Line to be parsed as array of data
     * @return string $line Line after parsing
     **/
    public function parseLine(array $linesArray) : array
    {
        // Explode the lines to parse text
        foreach($linesArray as $line) {
            $linesToParse[] = $this->split(config('global')->get('delimiter'), $line);
        }

        //Create empty indexed array from config fields to output
        foreach($this->options->get('mapper') as $field => $parseString) {
            // Decode the parse string to find the steps and execute them
            $stepsData = explode(';', $parseString);
            foreach($stepsData as $stepString) {
                // Decode step data to find parâmeters
                $stepInfo = $this->split(':', $stepString);
                $stepName = $stepInfo[0];
                $stepParamsString = $stepInfo[1];
                $stepParamsString = $stepInfo[1];
            }

            $parsedLine[$field] = '';
        }

        return $parsedLine;
    }

    /**
     * Implements the divider (div) step
     *
     * @param string $line Line to execute step
     * @return string Returns the line if not found
     **/
    public function positionStep(string $line, int $index, string $letter)
    {
        # code...
    }

    /**
     * Implements the split step
     *
     * @param string $line Line to execute step
     * @return string Returns the line if not found
     **/
    public function splitStep(string $line, string $separator, int $position)
    {
        return @explode($separator, $line)[$position];
    }

    /**
     * Cadastro line mapper
     *
     * @return array
     */
    private function cobrancaMapper(array $lineToParse, array $parsedLine): array
    {
        // Start line parsing
        $parsedLine['bloco'] = @explode('/', $lineToParse[1])[0];
        $parsedLine['bloco'] = ($parsedLine['bloco'] == 0) ? '' : $parsedLine['bloco'];
        $parsedLine['unidade'] = @explode('/', $lineToParse[1])[1];
        $parsedLine['proprietario_nome'] = @explode('CPF:', $lineToParse[3])[0];
        $parsedLine['proprietario_cpf/cnpj'] = @explode('CPF:', $lineToParse[3])[1];
        $parsedLine['proprietario_rg'] = @explode('RG:', $lineToParse[8])[1];
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
        $regex = '~,(?=([^\"]*\"[^\"]*\")*[^\"]*$)~';
        return preg_split($regex, $string);
    }
}
