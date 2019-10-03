<?php namespace Standardizer\Parsers;

use Standardizer\Parser;

use Standardizer\Interfaces\ParserInterface;

/**
 * Document parser class
 */
class CarsoftParser extends Parser implements ParserInterface
{
    // Configuration Getters
    public function getCutTop(): int { return $this->config['defaults']['cut_top']; }
    public function getCutBottom(): int { return $this->config['defaults']['cut_bottom']; }
    public function getConcatEvery(): int { return $this->config['defaults']['concat_every']; }

    /**
     * Implements the line parser
     *
     * @param string $line Line to be parsed
     * @return string $line Line after parsing
     **/
    public function parseLine(string $line) : array
    {
        //Create empty indexed array from config fields to implode
        foreach($this->converter->getFieldsToImplode() as $field) {
            $parsedLine[$field] = '';
        }

        // Explode the line to parse
        $lineToParse = explode($this->getDelimiter(), $line);

        // Call the line parser mapper based on conversor type
        $mapper = $this->converter->getType().'Mapper';

        // Execute mapper and return
        return $this->$mapper($lineToParse, $parsedLine);
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
     * Cobranca line mapper
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function FunctionName(Type $var = null)
    {
        # code...
    }
}
