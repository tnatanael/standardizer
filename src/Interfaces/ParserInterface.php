<?php namespace Standardizer\Interfaces;

interface ParserInterface 
{
    // Configuration Getters
    public function getCutTop() : int; // Lines to discard at top
    public function getCutBottom() : int; // Lines to discard at bottom
    public function getConcatEvery() : int; // Concatenate every X lines

    /**
     * Implements the line parser
     *
     * @param string $line Line to be parsed
     * @return string $line Line after parsing
     **/
    public function parseLine(string $line) : array;
}