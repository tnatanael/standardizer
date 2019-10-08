<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Standardizer\Factories\ParserFactory;
use Standardizer\Exporter;
use Standardizer\Filesystem;

final class ExporterTest extends TestCase
{
    private $emptyFileToExport = 'tests/assets/empty.xls';
    private $txtFileToExport = 'tests/assets/test_tab_separated.txt';
    private $expectedRawOutput = 'temp/raw.csv';

    public function testCanICreateAExporterFromClass() : Exporter
    {
        $parser = ParserFactory::create('testing');
        $exporter = new Exporter(
            $parser,
            $this->emptyFileToExport
        );

        $this->assertInstanceOf(Exporter::class, $exporter);

        return $exporter;
    }

    /**
     * @depends testCanICreateAExporterFromClass
     */
    public function testCanIGetTheInputFilePath(Exporter $exporter) : void
    {
        $this->assertEquals($this->emptyFileToExport, $exporter->getInputFilePath());
    }


    /**
     * @depends testCanICreateAExporterFromClass
     */
    public function testCanIGetTheRawFilePath(Exporter $exporter) : void
    {
        $this->assertEquals($this->expectedRawOutput, $exporter->getTempFilePath());
    }

    /**
     * @depends testCanICreateAExporterFromClass
     */
    public function testCanIExportAXlsToCsv(Exporter $exporter) : void
    {
        $exporter->run();

        $this->assertFileExists($this->expectedRawOutput);

        // Erase generated raw output file
        unlink($this->expectedRawOutput);
    }

    public function testCanIExportATxtTabSeparatedToCsv() : void
    {
        $parser = ParserFactory::create('testing');
        $exporter = new Exporter($parser, $this->txtFileToExport);

        $exporter->run();

        $this->assertFileExists($this->expectedRawOutput);
        $this->assertFileEquals(
            'tests/assets/tab_separated_equals.csv', 
            $this->expectedRawOutput
        );

        // Erase generated raw output file
        unlink($this->expectedRawOutput);
    }
}
