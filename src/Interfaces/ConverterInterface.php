<?php namespace Standardizer\Interfaces;

interface ConverterInterface 
{
    // Configuration Getters
    public function getFieldsToImplode(): array; // Get the headers values for output

    /**
     * Implements the converter logic
     *
     * @param array $lines Lines to be parsed
     * @return array $lines Lines after parsing
     **/
    public function convertLines(array $lines) : array;
}