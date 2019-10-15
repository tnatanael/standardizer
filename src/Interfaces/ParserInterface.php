<?php namespace Standardizer\Interfaces;

use Standardizer\Objects\ParserOptions;

interface ParserInterface
{
    public function __construct(ParserOptions $options);
    public function summarizeLines(array $lines) : array;
    public function parseLines(array $lines) : string;
}