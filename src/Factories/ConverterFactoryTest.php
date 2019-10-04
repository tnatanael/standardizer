<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Standardizer\Factories\ExporterFactory;
use Standardizer\Factories\ParserFactory;

final class ConverterFactoryTest extends TestCase
{
    private $emptyFileToExport = 'tests/assets/empty.xls';

    public function testCanICreateAConverterFromFactory(): void
    {
        // Create the parser instance
        $parser = ParserFactory::create('testing');

        // Create the exporter instance
        $exporter = ExporterFactory::create($this->emptyFileToExport);

        $this->assertInstanceOf(
            Standardizer\Converter::class,
            Standardizer\Factories\ConverterFactory::create($parser, $exporter)
        );
    }
}