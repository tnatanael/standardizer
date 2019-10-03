<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Standardizer\Converter;
use Standardizer\Exporter;
use Standardizer\Factories\ExporterFactory;
use Standardizer\Factories\ParserFactory;
use Standardizer\Filesystem;

final class ConverterTest extends TestCase
{
    private $fileToCutTest = 'tests/assets/cut.txt';

    public function testCanICutRandomTopLines() : void
    {
        $toCut = random_int(1,19);
        $lines = Filesystem::getLines($this->fileToCutTest);
        $originalLinesCount = Filesystem::countLines($this->fileToCutTest);
        $resultLines = Converter::cutTop($lines, $toCut);

        $this->assertEquals(($originalLinesCount - $toCut), count($resultLines));

        $extracted = array_slice($lines, 0, $toCut);
        $this->assertEquals([], array_intersect($extracted, $resultLines));
    }

    public function testCanICutRandomBottomLines() : void
    {
        $toCut = random_int(1,19);
        $lines = Filesystem::getLines($this->fileToCutTest);
        $originalLinesCount = Filesystem::countLines($this->fileToCutTest);
        $resultLines = Converter::cutBottom($lines, $toCut);

        $this->assertEquals(($originalLinesCount - $toCut), count($resultLines));

        $extracted = array_slice($lines, -$toCut);
        $this->assertEquals([], array_intersect($extracted, $resultLines));
    }

    public function testCanICutLineThatContains() : void
    {
        $toCut = ["6"]; //This will is contained in 2 lines
        $lines = Filesystem::getLines($this->fileToCutTest);
        $resultLines = Converter::cutContains($lines, $toCut);

        // Need to result in 18 lines after cut
        $this->assertEquals(18, count($resultLines));
    }

    public function testCanICutLineEquals() : void
    {
        $toCut = ["6"]; //This will is contained in 2 lines
        $lines = Filesystem::getLines($this->fileToCutTest);
        $resultLines = Converter::cutContains($lines, $toCut);

        // Need to result in 19 lines after cut
        $this->assertEquals(18, count($resultLines));
    }

    public function testCanIGetAConcatenatedLinesResult() : void
    {
        $every = 2;
        $lines = Filesystem::getLines($this->fileToCutTest);
        $resultLines = Converter::concatenateLines($lines, $every);

        // Need to result in 10 lines after concat
        $this->assertEquals(10, count($resultLines));

        // First line need to contains 12
        $this->assertEquals($resultLines[0], "12");
    }
}