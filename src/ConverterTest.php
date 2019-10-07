<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Standardizer\Converter;
use Standardizer\Factories\ExporterFactory;
use Standardizer\Factories\ParserFactory;
use Standardizer\Filesystem;

final class ConverterTest extends TestCase
{
    private $emptyFileToExport = 'tests/assets/empty.xls';
    private $fileToCutTest = 'tests/assets/cut.txt';

    public function testCanICreateAConverterInstance(): Converter
    {
        // Create the parser instance
        $parser = ParserFactory::create('testing');

        // Create the exporter instance
        $exporter = ExporterFactory::create($this->emptyFileToExport);

        // Create the converter instance
        $converter = new Converter($parser, $exporter);

        $this->assertInstanceOf(
            Converter::class,
            $converter
        );

        return $converter;
    }

    public function testCanIConvertAXlsFile(): void
    {
        // Create the parser instance
        $parser = ParserFactory::create('testing');

        // Create the exporter instance
        $exporter = ExporterFactory::create('tests/assets/test.xls');

        // Create the converter instance
        $converter = new Converter($parser, $exporter);

        $converter->run();

        $this->assertFileExists('output/test.csv');
        $this->assertFileEquals('tests/assets/test.csv', 'output/test.csv');

        //Remore generated test file
        unlink('output/test.csv');
    }

    public function testCanIConvertAXlsFileWithEndOfFile(): void
    {
        // Create the parser instance
        $parser = ParserFactory::create('testing');

        // Create the exporter instance
        $exporter = ExporterFactory::create('tests/assets/test_end_of_file.xls');

        // Create the converter instance
        $converter = new Converter($parser, $exporter);

        $converter->run();

        $this->assertFileExists('output/test_end_of_file.csv');
        
        //Remore generated test file
        unlink('output/test_end_of_file.csv');
    }

    /**
     * @depends testCanICreateAConverterInstance
     */
    public function testCanIGetFieldsToImplode(Converter $converter): void
    {
        $this->assertEquals([
            "split_test",
            "equals_test",
            "number_test",
            "phone_test",
        ], $converter->getFieldsToImplode());
    }

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

    public function testCanIGetASummarizedLinesResult() : void
    {
        $every = 2;
        $lines = Filesystem::getLines($this->fileToCutTest);
        $resultLines = Converter::summarizeLines($lines, $every);

        // Need to result in 10 lines after concat
        $this->assertEquals(10, count($resultLines));

        // First line need to contains 1 and 2
        $this->assertEquals($resultLines[0], ["1","2"]);
    }
}