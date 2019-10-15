<?php declare(strict_types=1);

namespace Standardizer\Tests;

use PHPUnit\Framework\TestCase;
use Standardizer\Factories\ExporterFactory;
use Standardizer\Factories\ParserFactory;
use Standardizer\Factories\ConverterFactory;
use Standardizer\Converter;

final class ConverterFactoryTest extends TestCase
{
    private $emptyFileToExport = 'tests/assets/empty.xls';

    public function testCanICreateAConverterFromFactory(): void
    {
        // Create the parser instance
        $parser = ParserFactory::create('testing');

        // Create the exporter instance
        $exporter = ExporterFactory::create($parser, $this->emptyFileToExport);

        $this->assertInstanceOf(
            Converter::class,
            ConverterFactory::create($parser, $exporter)
        );
    }
}